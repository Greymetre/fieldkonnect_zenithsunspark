<?php

namespace Tests\Feature;

use App\Models\InventoryLedger;
use App\Models\SalesOrder;
use App\Models\SalesOrderDispatch;
use App\Models\WarehouseStock;
use App\Services\DispatchDeletion;
use App\Services\SalesOrderDeletion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DispatchDeletionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'dispatch_delete_test', 'database.connections.dispatch_delete_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ]]);
        DB::statement('CREATE TABLE sales_orders (id INTEGER PRIMARY KEY, order_number TEXT, warehouse_id INTEGER, status TEXT, grand_total NUMERIC, updated_by INTEGER, deleted_at TEXT, created_at TEXT, updated_at TEXT)');
        DB::statement('CREATE TABLE sales_order_items (id INTEGER PRIMARY KEY, sales_order_id INTEGER REFERENCES sales_orders(id), product_id INTEGER, quantity NUMERIC, dispatched_quantity NUMERIC, created_at TEXT, updated_at TEXT)');
        DB::statement('CREATE TABLE sales_order_dispatches (id INTEGER PRIMARY KEY, sales_order_id INTEGER REFERENCES sales_orders(id), dispatch_number TEXT, dispatch_date TEXT, remark TEXT, created_at TEXT, updated_at TEXT)');
        DB::statement('CREATE TABLE sales_order_dispatch_items (id INTEGER PRIMARY KEY, sales_order_dispatch_id INTEGER REFERENCES sales_order_dispatches(id), sales_order_item_id INTEGER REFERENCES sales_order_items(id), product_id INTEGER, warehouse_id INTEGER, quantity NUMERIC, created_at TEXT, updated_at TEXT)');
        DB::statement('CREATE TABLE warehouse_stocks (id INTEGER PRIMARY KEY, warehouse_id INTEGER, product_id INTEGER, quantity NUMERIC, created_at TEXT, updated_at TEXT, UNIQUE(warehouse_id, product_id))');
        DB::statement('CREATE TABLE inventory_ledgers (id INTEGER PRIMARY KEY, warehouse_id INTEGER, product_id INTEGER, transaction_type TEXT, reference_type TEXT, reference_id INTEGER, quantity_in NUMERIC, quantity_out NUMERIC, balance_quantity NUMERIC, remark TEXT, created_by INTEGER, created_at TEXT, updated_at TEXT)');
        DB::statement('CREATE TABLE sales_order_payments (id INTEGER PRIMARY KEY, sales_order_id INTEGER, amount_received NUMERIC)');
    }

    public function test_deleting_only_partial_dispatch_reopens_full_quantity(): void
    {
        [$order, $dispatches] = $this->scenario([4]);
        $result = (new DispatchDeletion)->delete($order, $dispatches[0], 1);
        $this->assertSame('confirmed', $result->status);
        $this->assertSame(0.0, (float) $result->items->first()->dispatched_quantity);
        $this->assertSame(10.0, (float) ($result->items->sum('quantity') - $result->items->sum('dispatched_quantity')));
        $this->assertSame(20.0, (float) WarehouseStock::first()->quantity);
        $this->assertSame(0, SalesOrderDispatch::count());
        $this->assertSame(0, DB::table('sales_order_dispatch_items')->count());
        $this->assertSame(40, DB::table('sales_order_payments')->sum('amount_received'));
        $this->assertSame(100.0, (float) $result->grand_total);
        $this->assertNull($result->deleted_at);
    }

    public function test_deleting_one_of_multiple_dispatches_keeps_remaining_dispatch(): void
    {
        [$order, $dispatches] = $this->scenario([3, 4]);
        $result = (new DispatchDeletion)->delete($order, $dispatches[0], 1);
        $this->assertSame('partially_dispatched', $result->status);
        $this->assertSame(4.0, (float) $result->items->first()->dispatched_quantity);
        $this->assertSame(6.0, (float) ($result->items->sum('quantity') - $result->items->sum('dispatched_quantity')));
        $this->assertSame(16.0, (float) WarehouseStock::first()->quantity);
        $this->assertSame([$dispatches[1]->id], SalesOrderDispatch::pluck('id')->all());
    }

    public function test_fully_dispatched_order_becomes_partial_when_one_dispatch_is_deleted(): void
    {
        [$order, $dispatches] = $this->scenario([4, 6]);
        $this->assertSame('dispatched', $order->status);
        $result = (new DispatchDeletion)->delete($order, $dispatches[1], 1);
        $this->assertSame('partially_dispatched', $result->status);
        $this->assertSame(4.0, (float) $result->items->first()->dispatched_quantity);
    }

    public function test_only_full_dispatch_deletion_sets_ready_to_dispatch(): void
    {
        [$order, $dispatches] = $this->scenario([10]);
        $result = (new DispatchDeletion)->delete($order, $dispatches[0], 1);
        $this->assertSame('confirmed', $result->status);
        $this->assertSame(20.0, (float) WarehouseStock::first()->quantity);
    }

    public function test_later_so_delete_does_not_restore_deleted_dispatch_twice(): void
    {
        [$order, $dispatches] = $this->scenario([4, 6]);
        (new DispatchDeletion)->delete($order, $dispatches[0], 1);
        (new SalesOrderDeletion)->delete($order, 1);
        $this->assertSame(20.0, (float) WarehouseStock::first()->quantity);
        $this->assertSame(6.0, (float) InventoryLedger::where('transaction_type', 'sales_delete_reversal')->sum('quantity_in'));
        $this->assertSame(0, SalesOrderDispatch::count());
        $this->assertSame(0, DB::table('sales_order_dispatch_items')->count());
        $this->assertSame(40, DB::table('sales_order_payments')->sum('amount_received'));
        $this->assertSoftDeleted('sales_orders', ['id' => $order->id]);
    }

    public function test_so_delete_removes_all_dispatches_and_keeps_payment_history(): void
    {
        [$order] = $this->scenario([4, 6]);
        (new SalesOrderDeletion)->delete($order, 1);
        $this->assertSame(0, SalesOrderDispatch::count());
        $this->assertSame(0, DB::table('sales_order_dispatch_items')->count());
        $this->assertSame(20.0, (float) WarehouseStock::first()->quantity);
        $this->assertSame(40, DB::table('sales_order_payments')->sum('amount_received'));
    }

    public function test_duplicate_dispatch_delete_does_not_add_stock_twice(): void
    {
        [$order, $dispatches] = $this->scenario([4]);
        (new DispatchDeletion)->delete($order, $dispatches[0], 1);
        try {
            (new DispatchDeletion)->delete($order, $dispatches[0], 1);
            $this->fail('Repeated dispatch deletion must fail.');
        } catch (ModelNotFoundException $exception) {
            $this->assertSame(20.0, (float) WarehouseStock::first()->quantity);
            $this->assertSame(1, InventoryLedger::where('transaction_type', 'dispatch_delete_reversal')->count());
        }
    }

    public function test_dispatch_from_another_so_cannot_be_deleted(): void
    {
        [$order, $dispatches] = $this->scenario([4]);
        $other = SalesOrder::create(['order_number' => 'SO-OTHER', 'status' => 'confirmed']);
        $this->expectException(ModelNotFoundException::class);
        (new DispatchDeletion)->delete($other, $dispatches[0], 1);
    }

    public function test_multi_product_dispatch_reverses_each_original_warehouse(): void
    {
        [$order, $dispatches] = $this->scenario([4]);
        $item = $order->items()->create(['product_id' => 2, 'quantity' => 5, 'dispatched_quantity' => 2.125]);
        $dispatches[0]->items()->create(['sales_order_item_id' => $item->id, 'product_id' => 2, 'warehouse_id' => 2, 'quantity' => 2.125]);
        WarehouseStock::create(['warehouse_id' => 2, 'product_id' => 2, 'quantity' => 7.875]);
        $result = (new DispatchDeletion)->delete($order, $dispatches[0], 1);
        $this->assertSame('confirmed', $result->status);
        $this->assertSame(0.0, (float) $result->items->sum('dispatched_quantity'));
        $this->assertSame(15.0, (float) $result->items->sum('quantity'));
        $this->assertSame(10.0, (float) WarehouseStock::where('warehouse_id', 2)->value('quantity'));
        $this->assertSame(20.0, (float) WarehouseStock::where('warehouse_id', 1)->value('quantity'));
    }

    public function test_invalid_later_item_rolls_back_prior_stock_and_quantity_changes(): void
    {
        [$order, $dispatches] = $this->scenario([4]);
        $item = $order->items()->create(['product_id' => 2, 'quantity' => 5, 'dispatched_quantity' => 0]);
        $dispatches[0]->items()->create(['sales_order_item_id' => $item->id, 'product_id' => 2, 'warehouse_id' => 2, 'quantity' => 3]);
        try {
            (new DispatchDeletion)->delete($order, $dispatches[0], 1);
            $this->fail('Inconsistent dispatch must not partially reverse.');
        } catch (HttpException $exception) {
            $this->assertSame(16.0, (float) WarehouseStock::first()->quantity);
            $this->assertSame(4.0, (float) $order->items()->where('product_id', 1)->value('dispatched_quantity'));
            $this->assertSame(1, SalesOrderDispatch::count());
            $this->assertSame(0, InventoryLedger::where('transaction_type', 'dispatch_delete_reversal')->count());
        }
    }

    public function test_dispatch_cannot_be_deleted_again_after_parent_so_deletion(): void
    {
        [$order, $dispatches] = $this->scenario([4]);
        (new SalesOrderDeletion)->delete($order, 1);
        $this->expectException(ModelNotFoundException::class);
        (new DispatchDeletion)->delete($order, $dispatches[0], 1);
    }

    public function test_return_records_cannot_be_deleted_as_dispatches(): void
    {
        [$order] = $this->scenario([10]);
        $return = SalesOrderDispatch::create(['sales_order_id' => $order->id, 'dispatch_number' => 'RTR-1']);
        $this->expectException(ModelNotFoundException::class);
        (new DispatchDeletion)->delete($order, $return, 1);
    }

    public function test_dispatch_delete_with_return_request_does_not_change_stock(): void
    {
        [$order, $dispatches] = $this->scenario([10]);
        SalesOrderDispatch::create(['sales_order_id' => $order->id, 'dispatch_number' => 'RTR-1']);
        try {
            (new DispatchDeletion)->delete($order, $dispatches[0], 1);
            $this->fail('Returns require the whole-SO net stock reversal.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
            $this->assertSame(10.0, (float) WarehouseStock::first()->quantity);
            $this->assertSame(2, SalesOrderDispatch::count());
        }
    }

    private function scenario(array $quantities): array
    {
        $total = array_sum($quantities);
        $order = SalesOrder::create(['order_number' => 'SO-1', 'warehouse_id' => 1,
            'status' => $total == 10 ? 'dispatched' : 'partially_dispatched', 'grand_total' => 100]);
        $item = $order->items()->create(['product_id' => 1, 'quantity' => 10, 'dispatched_quantity' => $total]);
        WarehouseStock::create(['warehouse_id' => 1, 'product_id' => 1, 'quantity' => 20 - $total]);
        DB::table('sales_order_payments')->insert(['id' => 1, 'sales_order_id' => $order->id, 'amount_received' => 40]);
        $dispatches = [];
        foreach ($quantities as $index => $quantity) {
            $dispatch = SalesOrderDispatch::create(['sales_order_id' => $order->id, 'dispatch_number' => 'DSP-' . ($index + 1)]);
            $dispatch->items()->create(['sales_order_item_id' => $item->id, 'product_id' => 1, 'warehouse_id' => 1, 'quantity' => $quantity]);
            InventoryLedger::create(['warehouse_id' => 1, 'product_id' => 1,
                'transaction_type' => 'sales_dispatch', 'reference_type' => SalesOrder::class,
                'reference_id' => $order->id, 'quantity_in' => 0, 'quantity_out' => $quantity]);
            $dispatches[] = $dispatch;
        }
        return [$order, $dispatches];
    }
}
