<?php

namespace Tests\Feature;

use App\Http\Controllers\PaymentLedgerController;
use App\Http\Controllers\SalesOrderController;
use App\Models\InventoryLedger;
use App\Models\SalesOrder;
use App\Models\WarehouseStock;
use App\Services\SalesOrderDeletion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SalesOrderDeletionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sales_delete_test', 'database.connections.sales_delete_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::statement('CREATE TABLE sales_orders (id INTEGER PRIMARY KEY, order_number TEXT, customer_id INTEGER, warehouse_id INTEGER, status TEXT, grand_total NUMERIC, updated_by INTEGER, deleted_at TEXT, created_at TEXT, updated_at TEXT)');
        DB::statement('CREATE TABLE warehouse_stocks (id INTEGER PRIMARY KEY, warehouse_id INTEGER, product_id INTEGER, quantity NUMERIC, created_at TEXT, updated_at TEXT, UNIQUE(warehouse_id, product_id))');
        DB::statement('CREATE TABLE inventory_ledgers (id INTEGER PRIMARY KEY, warehouse_id INTEGER, product_id INTEGER, transaction_type TEXT, reference_type TEXT, reference_id INTEGER, quantity_in NUMERIC, quantity_out NUMERIC, balance_quantity NUMERIC, remark TEXT, created_by INTEGER, created_at TEXT, updated_at TEXT)');
        DB::statement('CREATE TABLE sales_order_payments (id INTEGER PRIMARY KEY, sales_order_id INTEGER, amount_received NUMERIC)');
        DB::statement('CREATE TABLE sales_order_dispatches (id INTEGER PRIMARY KEY, sales_order_id INTEGER, dispatch_number TEXT)');
        DB::statement('CREATE TABLE sales_order_dispatch_items (id INTEGER PRIMARY KEY, sales_order_dispatch_id INTEGER)');
        DB::statement('CREATE TABLE customers (id INTEGER PRIMARY KEY, name TEXT)');
        DB::statement('CREATE TABLE addresses (id INTEGER PRIMARY KEY, address1 TEXT, address2 TEXT, landmark TEXT, locality TEXT, customer_id INTEGER, user_id INTEGER, country_id INTEGER, state_id INTEGER, district_id INTEGER, city_id INTEGER, pincode_id INTEGER, zipcode TEXT)');
        DB::table('customers')->insert(['id' => 1, 'name' => 'Customer']);
        Gate::shouldReceive('denies')->andReturn(false);
        Auth::shouldReceive('id')->andReturn(1);
    }

    /** @dataProvider undispatchedStatuses */
    public function test_undispatched_order_deletion_does_not_change_stock(string $status): void
    {
        $order = $this->order($status);
        $stock = WarehouseStock::create(['warehouse_id' => 1, 'product_id' => 1, 'quantity' => 20]);
        $response = (new SalesOrderController)->destroy($order);
        $this->assertSoftDeleted('sales_orders', ['id' => $order->id]);
        $this->assertSame(20.0, (float) $stock->fresh()->quantity);
        $this->assertSame(0, InventoryLedger::count());
        $this->assertSame(0, $response->getData(true)['pending_dispatch_count']);
        $this->assertSame(0, $response->getData(true)['pending_sales_order_count']);
    }

    public static function undispatchedStatuses(): array
    {
        return [['payment_pending'], ['payment_partial'], ['payment_received'], ['confirmed']];
    }

    /** @dataProvider dispatchQuantities */
    public function test_deletion_restores_only_net_dispatch(string $status, float $out, float $returned): void
    {
        $order = $this->order($status);
        $stock = WarehouseStock::create(['warehouse_id' => 1, 'product_id' => 1, 'quantity' => 20 - $out + $returned]);
        $this->movement($order, 1, 0, $out);
        if ($returned > 0) $this->movement($order, 1, $returned, 0);
        (new SalesOrderDeletion)->delete($order, 1);
        $this->assertSame(20.0, (float) $stock->fresh()->quantity);
        $reversals = InventoryLedger::where('transaction_type', 'sales_delete_reversal');
        $this->assertSame($out - $returned, (float) $reversals->sum('quantity_in'));
        $this->assertSame($out == $returned ? 0 : 1, $reversals->count());
        $this->assertSame('cancelled', SalesOrder::withTrashed()->find($order->id)->status);
    }

    public static function dispatchQuantities(): array
    {
        return [['partially_dispatched', 4, 0], ['dispatched', 10, 0], ['dispatched', 10, 3], ['dispatched', 10, 10]];
    }

    public function test_duplicate_delete_does_not_restore_stock_twice(): void
    {
        $order = $this->order('dispatched');
        $this->movement($order, 1, 0, 10);
        $service = new SalesOrderDeletion;
        $service->delete($order, 1);
        try {
            $service->delete($order, 1);
            $this->fail('Duplicate deletion must fail.');
        } catch (ModelNotFoundException $exception) {
            $this->assertSame(10.0, (float) WarehouseStock::first()->quantity);
            $this->assertSame(1, InventoryLedger::where('transaction_type', 'sales_delete_reversal')->count());
        }
    }

    public function test_inconsistent_movements_roll_back_all_changes(): void
    {
        $order = $this->order('dispatched');
        $this->movement($order, 1, 0, 10);
        $this->movement($order, 2, 5, 0);
        try {
            (new SalesOrderDeletion)->delete($order, 1);
            $this->fail('Invalid movement must fail.');
        } catch (ValidationException $exception) {
            $this->assertNotNull(SalesOrder::find($order->id));
            $this->assertSame(0, WarehouseStock::count());
            $this->assertSame(0, InventoryLedger::where('transaction_type', 'sales_delete_reversal')->count());
        }
    }

    public function test_reversal_uses_original_warehouses_and_excludes_other_orders(): void
    {
        $order = $this->order('dispatched');
        $this->movement($order, 1, 0, 4.125);
        $this->movement($order, 2, 0, 3);
        InventoryLedger::where('product_id', 2)->update(['warehouse_id' => 2]);
        $this->movement($order, 3, 0, 90);
        InventoryLedger::where('product_id', 3)->update(['reference_id' => 999]);
        (new SalesOrderDeletion)->delete($order, 1);
        $this->assertSame(4.125, (float) WarehouseStock::where('warehouse_id', 1)->where('product_id', 1)->value('quantity'));
        $this->assertSame(3.0, (float) WarehouseStock::where('warehouse_id', 2)->where('product_id', 2)->value('quantity'));
        $this->assertSame(2, WarehouseStock::count());
    }

    public function test_payments_remain_visible_as_refund_pending_with_no_receivable(): void
    {
        $order = $this->order('payment_partial');
        DB::table('sales_order_payments')->insert(['id' => 1, 'sales_order_id' => $order->id, 'amount_received' => 40]);
        (new SalesOrderDeletion)->delete($order, 1);
        $this->assertSame(40, DB::table('sales_order_payments')->sum('amount_received'));
        $request = Request::create('/', 'GET', ['filter_status' => 'refund_pending'], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->app->instance('request', $request);
        $controller = (new \ReflectionClass(PaymentLedgerController::class))->newInstanceWithoutConstructor();
        $response = $controller->index($request)->getData(true);
        $this->assertCount(1, $response['data'], json_encode($response));
        $this->assertSame('₹0.00', $response['data'][0]['balance']);
        $this->assertStringContainsString('Refund Pending', $response['data'][0]['payment_status']);
        $details = $controller->show(SalesOrder::withTrashed()->find($order->id));
        $this->assertSame(0, $details->getData()['balanceDue']);
        foreach (['pending', 'partial', 'paid'] as $filter) {
            $request->merge(['filter_status' => $filter]);
            $this->assertSame([], $controller->index($request)->getData(true)['data']);
        }
    }

    public function test_stale_order_cannot_be_confirmed_after_deletion(): void
    {
        $order = $this->order('payment_received');
        (new SalesOrderDeletion)->delete($order, 1);
        $this->expectException(ModelNotFoundException::class);
        (new SalesOrderController)->confirm($order);
    }

    public function test_stale_order_cannot_receive_payment_after_deletion(): void
    {
        $order = $this->order('payment_partial');
        (new SalesOrderDeletion)->delete($order, 1);
        $this->expectException(ModelNotFoundException::class);
        (new SalesOrderController)->storePayment(Request::create('/', 'POST', [
            'payment_date' => '2026-09-09', 'amount_received' => 10, 'payment_mode' => 'cash',
        ]), $order);
    }

    private function order(string $status): SalesOrder
    {
        return SalesOrder::create(['order_number' => 'SO-000001', 'customer_id' => 1, 'warehouse_id' => 1, 'status' => $status, 'grand_total' => 100]);
    }

    private function movement(SalesOrder $order, int $productId, float $in, float $out): void
    {
        InventoryLedger::create([
            'warehouse_id' => 1, 'product_id' => $productId,
            'transaction_type' => $in ? 'sales_return' : 'sales_dispatch',
            'reference_type' => SalesOrder::class, 'reference_id' => $order->id,
            'quantity_in' => $in, 'quantity_out' => $out, 'balance_quantity' => 0,
        ]);
    }
}
