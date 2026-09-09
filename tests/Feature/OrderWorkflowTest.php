<?php

namespace Tests\Feature;

use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SalesOrderController;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Use an isolated database; never migrate or modify the application's database.
        config(['database.default' => 'order_workflow_test', 'database.connections.order_workflow_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::statement('CREATE TABLE purchase_orders (id INTEGER PRIMARY KEY, status TEXT, deleted_at TEXT, created_at TEXT, updated_at TEXT)');
        DB::statement('CREATE TABLE sales_orders (id INTEGER PRIMARY KEY, status TEXT, grand_total NUMERIC, updated_by INTEGER, deleted_at TEXT, created_at TEXT, updated_at TEXT)');
        DB::statement('CREATE TABLE sales_order_payments (id INTEGER PRIMARY KEY AUTOINCREMENT, sales_order_id INTEGER, receipt_number TEXT, payment_date TEXT, amount_received NUMERIC, payment_mode TEXT, reference_number TEXT, received_by INTEGER, created_at TEXT, updated_at TEXT)');
        Gate::shouldReceive('denies')->andReturn(false);
        Auth::shouldReceive('id')->andReturn(1);
    }

    /** @dataProvider deletableStatuses */
    public function test_unreceived_purchase_orders_can_be_deleted(string $status): void
    {
        $order = PurchaseOrder::create(['status' => $status]);
        $response = (new PurchaseOrderController)->destroy($order);

        $this->assertSoftDeleted('purchase_orders', ['id' => $order->id]);
        $this->assertSame(0, $response->getData(true)['pending_receive_count']);
    }

    public static function deletableStatuses(): array
    {
        return [['draft'], ['approved']];
    }

    /** @dataProvider receivedStatuses */
    public function test_received_purchase_orders_cannot_be_deleted(string $status): void
    {
        $order = PurchaseOrder::create(['status' => 'approved']);
        // Simulate receipt after the controller's route-bound model was loaded.
        PurchaseOrder::whereKey($order->id)->update(['status' => $status]);
        try {
            (new PurchaseOrderController)->destroy($order);
            $this->fail('Received order deletion should be rejected.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
            $this->assertNotNull(PurchaseOrder::find($order->id));
        }
    }

    public static function receivedStatuses(): array
    {
        return [['received'], ['partially_received']];
    }

    public function test_zero_payment_allows_confirmation_and_later_balance_collection(): void
    {
        $order = SalesOrder::create(['status' => 'payment_pending', 'grand_total' => 100]);
        $controller = new SalesOrderController;
        $controller->storePayment($this->paymentRequest(0), $order);

        $this->assertSame('payment_partial', $order->fresh()->status);
        $this->assertSame(0.0, (float) $order->payments()->sum('amount_received'));
        $this->assertSame('RCPT-000001', $order->payments()->first()->receipt_number);
        $controller->confirm($order->fresh());
        $this->assertSame('confirmed', $order->fresh()->status);

        $controller->storePayment($this->paymentRequest(100), $order->fresh());
        $this->assertSame(100.0, (float) $order->payments()->sum('amount_received'));
        $this->assertSame('confirmed', $order->fresh()->status);
    }

    public function test_zero_value_order_can_record_a_zero_receipt(): void
    {
        $order = SalesOrder::create(['status' => 'payment_pending', 'grand_total' => 0]);
        (new SalesOrderController)->storePayment($this->paymentRequest(0), $order);
        $this->assertSame('payment_received', $order->fresh()->status);
        $this->assertSame(1, $order->payments()->count());
    }

    public function test_negative_payment_is_rejected(): void
    {
        $order = SalesOrder::create(['status' => 'payment_pending', 'grand_total' => 100]);
        $this->expectException(ValidationException::class);
        (new SalesOrderController)->storePayment($this->paymentRequest(-1), $order);
    }

    public function test_overpayment_is_rejected(): void
    {
        $order = SalesOrder::create(['status' => 'payment_pending', 'grand_total' => 100]);
        $this->expectException(HttpException::class);
        (new SalesOrderController)->storePayment($this->paymentRequest(101), $order);
    }

    public function test_confirmation_requires_a_receipt(): void
    {
        $order = SalesOrder::create(['status' => 'payment_partial', 'grand_total' => 100]);
        $this->expectException(HttpException::class);
        (new SalesOrderController)->confirm($order);
    }

    private function paymentRequest(float $amount): Request
    {
        return Request::create('/', 'POST', [
            'payment_date' => '2026-09-09', 'amount_received' => $amount, 'payment_mode' => 'cash',
        ]);
    }
}
