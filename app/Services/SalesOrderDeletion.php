<?php

namespace App\Services;

use App\Models\InventoryLedger;
use App\Models\SalesOrder;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesOrderDeletion
{
    public function delete(SalesOrder $salesOrder, int $userId): void
    {
        DB::transaction(function () use ($salesOrder, $userId) {
            // Dispatch, receipt of payment and return acceptance lock this same order.
            $order = SalesOrder::whereKey($salesOrder->id)->lockForUpdate()->firstOrFail();
            $movements = InventoryLedger::where('reference_type', SalesOrder::class)
                ->where('reference_id', $order->id)
                ->orderBy('warehouse_id')->orderBy('product_id')->lockForUpdate()->get();

            foreach ($movements->groupBy(fn ($row) => $row->warehouse_id . ':' . $row->product_id) as $rows) {
                // Accepted returns have already restored stock; only reverse the net OUT.
                $quantity = round((float) $rows->sum('quantity_out') - (float) $rows->sum('quantity_in'), 3);
                if ($quantity < 0) {
                    throw ValidationException::withMessages(['order' => 'Order stock movements are inconsistent. Review its inventory ledger before deleting.']);
                }
                if ($quantity == 0) {
                    continue;
                }
                $movement = $rows->first();
                $stock = WarehouseStock::firstOrCreate([
                    'warehouse_id' => $movement->warehouse_id, 'product_id' => $movement->product_id,
                ], ['quantity' => 0]);
                $stock = WarehouseStock::whereKey($stock->id)->lockForUpdate()->firstOrFail();
                $stock->quantity = round((float) $stock->quantity + $quantity, 3);
                $stock->save();

                InventoryLedger::create([
                    'warehouse_id' => $movement->warehouse_id,
                    'product_id' => $movement->product_id,
                    'transaction_type' => 'sales_delete_reversal',
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $order->id,
                    'quantity_in' => $quantity,
                    'quantity_out' => 0,
                    'balance_quantity' => $stock->quantity,
                    'remark' => 'Stock restored on deletion of ' . $order->order_number,
                    'created_by' => $userId,
                ]);
            }

            // Remove every dispatch/return and its item rows when the entire SO is deleted.
            foreach ($order->dispatches()->lockForUpdate()->get() as $dispatch) {
                $dispatch->items()->delete();
                $dispatch->delete();
            }
            $order->update(['status' => 'cancelled', 'updated_by' => $userId]);
            $order->delete();
        });
    }
}
