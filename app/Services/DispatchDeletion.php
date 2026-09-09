<?php

namespace App\Services;

use App\Models\InventoryLedger;
use App\Models\SalesOrder;
use App\Models\SalesOrderDispatch;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

class DispatchDeletion
{
    public function delete(SalesOrder $salesOrder, SalesOrderDispatch $dispatch, int $userId): SalesOrder
    {
        return DB::transaction(function () use ($salesOrder, $dispatch, $userId) {
            $order = SalesOrder::whereKey($salesOrder->id)->lockForUpdate()->firstOrFail();
            $dispatch = SalesOrderDispatch::where('sales_order_id', $order->id)
                ->where('dispatch_number', 'like', 'DSP-%')->whereKey($dispatch->id)
                ->lockForUpdate()->firstOrFail();
            abort_unless(in_array($order->status, ['confirmed', 'partially_dispatched', 'dispatched'], true), 422, 'This order is not available for dispatch deletion.');
            // Returns belong to the SO, not a specific dispatch. Do not invalidate their quantities.
            abort_if($order->returns()->exists(), 422, 'This SO has a return request. Individual dispatch deletion is unavailable; delete the SO to reverse its net stock.');

            $items = $order->items()->lockForUpdate()->get()->keyBy('id');
            $dispatchItems = $dispatch->items()->orderBy('warehouse_id')->orderBy('product_id')->lockForUpdate()->get();
            abort_if($dispatchItems->isEmpty(), 422, 'This dispatch has no item history to reverse.');
            foreach ($dispatchItems as $row) {
                $item = $items->get($row->sales_order_item_id);
                $quantity = round((float) $row->quantity, 3);
                abort_unless($item && (int) $item->product_id === (int) $row->product_id
                    && $quantity > 0 && round((float) $item->dispatched_quantity, 3) >= $quantity,
                    422, 'Dispatch quantities are inconsistent. Review the order before deleting.');

                $stock = WarehouseStock::firstOrCreate([
                    'warehouse_id' => $row->warehouse_id, 'product_id' => $row->product_id,
                ], ['quantity' => 0]);
                $stock = WarehouseStock::whereKey($stock->id)->lockForUpdate()->firstOrFail();
                $stock->quantity = round((float) $stock->quantity + $quantity, 3);
                $stock->save();
                $item->dispatched_quantity = round((float) $item->dispatched_quantity - $quantity, 3);
                $item->save();

                InventoryLedger::create([
                    'warehouse_id' => $row->warehouse_id, 'product_id' => $row->product_id,
                    'transaction_type' => 'dispatch_delete_reversal',
                    'reference_type' => SalesOrder::class, 'reference_id' => $order->id,
                    'quantity_in' => $quantity, 'quantity_out' => 0, 'balance_quantity' => $stock->quantity,
                    'remark' => 'Stock restored on deletion of dispatch ' . $dispatch->dispatch_number . ' / ' . $order->order_number,
                    'created_by' => $userId,
                ]);
            }
            $dispatch->items()->delete();
            $dispatch->delete();

            $hasDispatched = $items->contains(fn ($item) => (float) $item->dispatched_quantity > 0);
            $hasPending = $items->contains(fn ($item) => (float) $item->dispatched_quantity < (float) $item->quantity);
            $order->update([
                'status' => !$hasDispatched ? 'confirmed' : ($hasPending ? 'partially_dispatched' : 'dispatched'),
                'updated_by' => $userId,
            ]);
            return $order->load('items');
        });
    }
}
