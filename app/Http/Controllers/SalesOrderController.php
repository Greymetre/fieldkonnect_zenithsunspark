<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesOrderRequest;
use App\Models\Customers;
use App\Models\FirmType;
use App\Models\InventoryLedger;
use App\Models\InvoiceSetting;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderDispatch;
use App\Models\SalesOrderPayment;
use App\Models\WareHouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use PDF;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('sales_order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = SalesOrder::with(['customer', 'warehouse', 'items', 'latestReturn'])
                ->when($request->filled('search_text'), function ($query) use ($request) {
                    $search = $request->search_text;
                    $query->where(function ($q) use ($search) {
                        $q->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
                ->when($request->filled('from_date'), fn ($q) => $q->whereDate('order_date', '>=', $request->from_date))
                ->when($request->filled('to_date'), fn ($q) => $q->whereDate('order_date', '<=', $request->to_date))
                ->latest('id');

            return datatables()->eloquent($query)
                ->addColumn('action', function ($row) {
                    $buttons = '';
                    $return = $row->latestReturn;
                    $returnData = $return ? $return->returnData() : [];
                    $returnPending = $return && ($returnData['return_status'] ?? 'pending') === 'pending';
                    if ($row->status === 'payment_pending' && Gate::allows('sales_order_payment')) {
                        $buttons .= '<button type="button" class="btn btn-success btn-sm mr-1 receive-sales-payment" data-url="' . e(route('sales-orders.payment.modal', $row)) . '">Receive Payment</button>';
                    }
                    if (in_array($row->status, ['payment_partial', 'payment_received'], true) && Gate::allows('sales_order_confirm')) {
                        $buttons .= '<button type="button" class="btn btn-theme btn-sm mr-1 confirm-sales-order" data-id="' . $row->id . '">Confirm Sale</button>';
                    }
                    if (in_array($row->status, ['confirmed', 'partially_dispatched'], true)) {
                        $buttons .= '<a class="btn btn-warning btn-sm mr-1" href="' . e(route('dispatch.index')) . '">Dispatch</a>';
                    }
                    if ($returnPending && Gate::allows('sales_order_confirm')) {
                        $buttons .= '<button type="button" class="btn btn-outline-success btn-sm mr-1 accept-sales-return" data-url="'
                            . e(route('sales-orders.return.accept.modal', [$row, $return])) . '">Accept Return</button>';
                    }
                    if (Gate::allows('sales_order_show')) {
                        $buttons .= '<button type="button" class="btn btn-link text-primary view-sales-order" data-url="' . e(route('sales-orders.show', $row)) . '">View</button>';
                    }
                    if ($row->status === 'payment_pending' && Gate::allows('sales_order_delete')) {
                        $buttons .= '<button type="button" class="btn btn-danger btn-sm delete-sales-order" data-id="' . $row->id . '">Delete</button>';
                    }
                    return $buttons;
                })
                ->addColumn('customer_name', fn ($row) => optional($row->customer)->name)
                ->addColumn('warehouse_name', fn ($row) => optional($row->warehouse)->warehouse_name)
                ->editColumn('order_date', fn ($row) => optional($row->order_date)->format('d M Y'))
                ->editColumn('order_type', fn ($row) => '<span class="badge badge-info">' . strtoupper($row->order_type) . '</span>')
                ->editColumn('grand_total', fn ($row) => '₹' . number_format($row->grand_total, 2))
                ->addColumn('status_badge', function ($row) {
                    if ($row->latestReturn) {
                        $returnStatus = $row->latestReturn->returnData()['return_status'] ?? 'pending';
                        return $returnStatus === 'accepted'
                            ? '<span class="badge badge-success">Completed</span>'
                            : '<span class="badge badge-danger">Return Requested</span>';
                    }
                    $labels = [
                        'payment_pending' => ['warning', 'Payment Pending'],
                        'payment_partial' => ['info', 'Partial'],
                        'payment_received' => ['success', 'Paid'],
                        'confirmed' => ['info', 'Ready to Dispatch'],
                        'partially_dispatched' => ['info', 'Partially Dispatched'],
                        'dispatched' => ['success', 'Dispatched'],
                        'cancelled' => ['danger', 'Cancelled'],
                    ];
                    [$color, $label] = $labels[$row->status] ?? ['secondary', ucfirst($row->status)];
                    return '<span class="badge badge-' . $color . '">' . e($label) . '</span>';
                })
                ->rawColumns(['action', 'order_type', 'status_badge'])
                ->make(true);
        }

        return view('sales_orders.index');
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('sales_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $orderType = strtolower($request->input('type', 'b2c'));
        abort_unless(in_array($orderType, ['b2b', 'b2c'], true), Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid order type.');

        $customers = $this->customerQuery()
            ->with(['customeraddress.cityname', 'customeraddress.statename', 'customerdetails'])
            ->orderBy('name')->get();
        $products = Product::where('active', 'Y')
            ->with(['productdetails' => fn ($q) => $q->orderByDesc('isprimary')])
            ->orderBy('product_name')->get();
        $warehouses = WareHouse::orderBy('warehouse_name')->get();
        $stocks = WarehouseStock::get()->groupBy('warehouse_id')->map(fn ($rows) => $rows->pluck('quantity', 'product_id'));
        $customerFirmType = FirmType::whereRaw('LOWER(firmtype_name) = ?', ['customer'])->first();

        return view('sales_orders.partials.form_modal', compact(
            'orderType', 'customers', 'products', 'warehouses', 'stocks', 'customerFirmType'
        ));
    }

    public function store(SalesOrderRequest $request)
    {
        abort_if(Gate::denies('sales_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_unless($this->customerQuery()->whereKey($request->customer_id)->exists(), Response::HTTP_UNPROCESSABLE_ENTITY, 'Selected entry must have Firm Type Customer.');

        $salesOrder = DB::transaction(function () use ($request) {
            $items = [];
            $subtotal = 0;
            $totalGst = 0;
            foreach ($request->items as $row) {
                $quantity = round((float) $row['quantity'], 3);
                $rate = round((float) $row['rate'], 2);
                $gst = round((float) $row['gst_percent'], 2);
                $taxable = round($quantity * $rate, 2);
                $gstAmount = round($taxable * $gst / 100, 2);
                $subtotal += $taxable;
                $totalGst += $gstAmount;
                $items[] = [
                    'product_id' => $row['product_id'], 'quantity' => $quantity, 'rate' => $rate,
                    'gst_percent' => $gst, 'taxable_amount' => $taxable,
                    'gst_amount' => $gstAmount, 'total_amount' => $taxable + $gstAmount,
                ];
            }

            $order = SalesOrder::create([
                'order_type' => $request->order_type,
                'customer_id' => $request->customer_id,
                'warehouse_id' => $request->warehouse_id,
                'order_date' => now()->toDateString(),
                'status' => 'payment_pending',
                'subtotal' => round($subtotal, 2),
                'total_gst' => round($totalGst, 2),
                'grand_total' => round($subtotal + $totalGst, 2),
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            $order->update(['order_number' => 'SO-' . str_pad($order->id, 6, '0', STR_PAD_LEFT)]);
            $order->items()->createMany($items);
            return $order;
        });

        return response()->json(['status' => 'success', 'message' => 'Sales Order created successfully.', 'id' => $salesOrder->id]);
    }

    public function show(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $salesOrder->load(['customer.customeraddress', 'customer.customerdetails', 'warehouse', 'items.product', 'latestReturn.items']);
        return view('sales_orders.partials.show_modal', compact('salesOrder'));
    }

    public function returnModal(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->assertReturnableOrder($salesOrder);
        abort_if($salesOrder->returns()->exists(), Response::HTTP_UNPROCESSABLE_ENTITY, 'A return has already been submitted for this Sales Order.');

        $salesOrder->load(['customer', 'warehouse', 'items.product']);
        return view('sales_orders.partials.return_modal', compact('salesOrder'));
    }

    public function storeReturn(Request $request, SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $validated = $this->validateReturnItems($request, true);

        $return = DB::transaction(function () use ($validated, $salesOrder) {
            $order = SalesOrder::whereKey($salesOrder->id)->lockForUpdate()->firstOrFail();
            $this->assertReturnableOrder($order);
            abort_if((bool) $order->returns()->lockForUpdate()->first(), Response::HTTP_UNPROCESSABLE_ENTITY, 'A return has already been submitted for this Sales Order.');

            $orderItems = $order->items()->lockForUpdate()->get()->keyBy('id');
            $this->assertSubmittedReturnItems($validated['items'], $orderItems);
            $returnRows = $this->returnItemRows($validated['items'], $orderItems, 'dispatched_quantity', $order->warehouse_id);

            $return = SalesOrderDispatch::create([
                'sales_order_id' => $order->id,
                'dispatch_date' => now()->toDateString(),
                'remark' => json_encode([
                    'return_status' => 'pending',
                    'reason' => $validated['reason'],
                    'requested_by' => Auth::id(),
                    'requested_at' => now()->toDateTimeString(),
                ]),
                'dispatched_by' => Auth::id(),
            ]);
            $return->update(['dispatch_number' => 'RTR-' . str_pad($return->id, 6, '0', STR_PAD_LEFT)]);
            $return->items()->createMany($returnRows);
            return $return;
        });

        return response()->json([
            'status' => 'success',
            'message' => "Return {$return->dispatch_number} submitted for acceptance.",
        ]);
    }

    public function acceptReturnModal(SalesOrder $salesOrder, SalesOrderDispatch $return)
    {
        abort_if(Gate::denies('sales_order_confirm'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->assertOrderReturn($salesOrder, $return);
        abort_if($return->isAcceptedReturn(), Response::HTTP_UNPROCESSABLE_ENTITY, 'This return has already been accepted.');

        $salesOrder->load(['customer', 'warehouse']);
        $return->load(['items.product']);
        return view('sales_orders.partials.accept_return_modal', compact('salesOrder', 'return'));
    }

    public function acceptReturn(Request $request, SalesOrder $salesOrder, SalesOrderDispatch $return)
    {
        abort_if(Gate::denies('sales_order_confirm'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $validated = $this->validateReturnItems($request, false);

        DB::transaction(function () use ($validated, $salesOrder, $return) {
            $order = SalesOrder::whereKey($salesOrder->id)->lockForUpdate()->firstOrFail();
            $lockedReturn = SalesOrderDispatch::whereKey($return->id)->lockForUpdate()->firstOrFail();
            $this->assertReturnableOrder($order);
            $this->assertOrderReturn($order, $lockedReturn);
            abort_if($lockedReturn->isAcceptedReturn(), Response::HTTP_UNPROCESSABLE_ENTITY, 'This return has already been accepted.');

            $requestedItems = $lockedReturn->items()->lockForUpdate()->get()->keyBy('id');
            $this->assertSubmittedReturnItems($validated['items'], $requestedItems);
            $acceptedRows = $this->returnItemRows($validated['items'], $requestedItems, 'quantity', $order->warehouse_id);

            foreach ($acceptedRows as $row) {
                $quantity = (float) $row['quantity'];
                $stock = WarehouseStock::firstOrCreate(
                    ['warehouse_id' => $order->warehouse_id, 'product_id' => $row['product_id']],
                    ['quantity' => 0]
                );
                $stock = WarehouseStock::whereKey($stock->id)->lockForUpdate()->first();
                $stock->quantity = round((float) $stock->quantity + $quantity, 3);
                $stock->save();

                InventoryLedger::create([
                    'warehouse_id' => $order->warehouse_id,
                    'product_id' => $row['product_id'],
                    'transaction_type' => 'sales_return',
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $order->id,
                    'quantity_in' => $quantity,
                    'quantity_out' => 0,
                    'balance_quantity' => $stock->quantity,
                    'remark' => 'Return accepted: ' . ($lockedReturn->returnData()['reason'] ?? ''),
                    'created_by' => Auth::id(),
                ]);
            }

            $returnData = $lockedReturn->returnData();
            $returnData['return_status'] = 'accepted';
            $returnData['accepted_by'] = Auth::id();
            $returnData['accepted_at'] = now()->toDateTimeString();
            $returnData['accepted_items'] = collect($acceptedRows)
                ->mapWithKeys(fn ($row) => [(string) $row['sales_order_item_id'] => (float) $row['quantity']])
                ->all();
            $lockedReturn->update(['remark' => json_encode($returnData)]);
            $order->update(['updated_by' => Auth::id()]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Return accepted. Stock and inventory ledger updated successfully.',
        ]);
    }

    public function downloadPdf(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $salesOrder->load([
            'customer.customerdetails',
            'customer.customeraddress.cityname',
            'customer.customeraddress.districtname',
            'customer.customeraddress.statename',
            'customer.customeraddress.pincodename',
            'warehouse',
            'items.product.unitmeasures',
        ]);
        $settings = InvoiceSetting::with([
            'address.cityname',
            'address.districtname',
            'address.statename',
            'address.pincodename',
        ])->first();

        return view('orders.order_pdf', [
            'order' => $salesOrder,
            'settings' => $settings,
            'documentTitle' => 'Sales Order',
            'documentNumber' => $salesOrder->order_number,
            'documentDate' => $salesOrder->order_date,
            'party' => $salesOrder->customer,
            'partyLabel' => $salesOrder->order_type === 'b2b' ? 'Client' : 'Customer',
            'warehouse' => $salesOrder->warehouse,
        ]);

        return PDF::loadView('orders.order_pdf', [
            'order' => $salesOrder,
            'settings' => $settings,
            'documentTitle' => 'Sales Order',
            'documentNumber' => $salesOrder->order_number,
            'documentDate' => $salesOrder->order_date,
            'party' => $salesOrder->customer,
            'partyLabel' => $salesOrder->order_type === 'b2b' ? 'Client' : 'Customer',
            'warehouse' => $salesOrder->warehouse,
        ])->setPaper('a4', 'portrait')
            ->download(($salesOrder->order_number ?: 'sales-order') . '.pdf');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($salesOrder->status !== 'payment_pending', Response::HTTP_UNPROCESSABLE_ENTITY, 'Only Payment Pending orders can be deleted.');
        $salesOrder->delete();
        return response()->json(['status' => 'success', 'message' => 'Sales Order deleted successfully.']);
    }

    public function paymentModal(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_payment'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($salesOrder->status === 'cancelled', Response::HTTP_UNPROCESSABLE_ENTITY, 'Payment cannot be received for a cancelled order.');
        $salesOrder->load(['customer', 'payments']);
        $paidAmount = round((float) $salesOrder->payments->sum('amount_received'), 2);
        $balanceDue = max(0, round((float) $salesOrder->grand_total - $paidAmount, 2));
        abort_if($balanceDue <= 0, Response::HTTP_UNPROCESSABLE_ENTITY, 'This order has no outstanding balance.');
        return view('sales_orders.partials.payment_modal', compact('salesOrder', 'paidAmount', 'balanceDue'));
    }

    public function storePayment(Request $request, SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_payment'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount_received' => 'required|numeric|gt:0',
            'payment_mode' => 'required|in:cash,upi,bank_transfer,card,cheque',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $payment = DB::transaction(function () use ($validated, $salesOrder) {
            $order = SalesOrder::whereKey($salesOrder->id)->lockForUpdate()->firstOrFail();
            abort_if($order->status === 'cancelled', Response::HTTP_UNPROCESSABLE_ENTITY, 'Payment cannot be received for a cancelled order.');
            $paidBefore = round((float) $order->payments()->sum('amount_received'), 2);
            $balance = max(0, round((float) $order->grand_total - $paidBefore, 2));
            $received = round((float) $validated['amount_received'], 2);
            abort_if($received > $balance, Response::HTTP_UNPROCESSABLE_ENTITY, "Amount received cannot exceed balance due ₹" . number_format($balance, 2) . '.');

            $payment = SalesOrderPayment::create([
                'sales_order_id' => $order->id,
                'payment_date' => $validated['payment_date'],
                'amount_received' => $received,
                'payment_mode' => $validated['payment_mode'],
                'reference_number' => $validated['reference_number'] ?? null,
                'received_by' => Auth::id(),
            ]);
            $payment->update(['receipt_number' => 'RCPT-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT)]);
            $orderUpdate = ['updated_by' => Auth::id()];
            if (in_array($order->status, ['payment_pending', 'payment_partial', 'payment_received'], true)) {
                $orderUpdate['status'] = round($paidBefore + $received, 2) >= round((float) $order->grand_total, 2)
                    ? 'payment_received'
                    : 'payment_partial';
            }
            $order->update($orderUpdate);
            return $payment;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Payment confirmed. Receipt ' . $payment->receipt_number . ' generated.',
            'payment_complete' => (float) $payment->salesOrder->payments()->sum('amount_received') >= (float) $payment->salesOrder->grand_total,
            'pending_sales_order_count' => SalesOrder::whereIn('status', ['payment_pending', 'payment_partial'])->count(),
        ]);
    }

    public function confirm(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('sales_order_confirm'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_unless(in_array($salesOrder->status, ['payment_partial', 'payment_received'], true), Response::HTTP_UNPROCESSABLE_ENTITY, 'A payment is required before confirming the sale.');
        abort_if((float) $salesOrder->payments()->sum('amount_received') <= 0, Response::HTTP_UNPROCESSABLE_ENTITY, 'A payment is required before confirming the sale.');
        $salesOrder->update(['status' => 'confirmed', 'updated_by' => Auth::id()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sale confirmed and sent to Dispatch.',
            'redirect_url' => route('dispatch.index'),
        ]);
    }

    public function dispatchIndex(Request $request)
    {
        abort_if(Gate::denies('dispatch_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $query = SalesOrder::with(['customer', 'warehouse', 'items', 'latestPayment'])
                ->whereIn('status', ['confirmed', 'partially_dispatched'])
                ->when($request->filled('search_text'), function ($query) use ($request) {
                    $search = $request->search_text;
                    $query->where(function ($q) use ($search) {
                        $q->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($request->filled('from_date'), fn ($q) => $q->whereDate('order_date', '>=', $request->from_date))
                ->when($request->filled('to_date'), fn ($q) => $q->whereDate('order_date', '<=', $request->to_date))
                ->latest('id');

            return datatables()->eloquent($query)
                ->editColumn('order_date', fn ($row) => optional($row->order_date)->format('d M Y'))
                ->editColumn('order_type', fn ($row) => '<span class="badge badge-info">' . strtoupper($row->order_type) . '</span>')
                ->addColumn('customer_name', fn ($row) => optional($row->customer)->name)
                ->addColumn('warehouse_name', fn ($row) => optional($row->warehouse)->warehouse_name)
                ->addColumn('item_count', fn ($row) => $row->items->count())
                ->addColumn('payment_mode', fn ($row) => '<span class="badge badge-success">' . strtoupper(str_replace('_', ' ', optional($row->latestPayment)->payment_mode ?: '-')) . '</span>')
                ->addColumn('action', function ($row) {
                    if (Gate::allows('dispatch_create')) {
                        return '<button type="button" class="btn btn-warning btn-sm open-dispatch" data-url="' . e(route('dispatch.modal', $row)) . '">Dispatch → Stock OUT</button>';
                    }
                    return '';
                })
                ->rawColumns(['order_type', 'payment_mode', 'action'])
                ->make(true);
        }
        return view('sales_orders.dispatch');
    }

    public function dispatchModal(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('dispatch_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_unless(in_array($salesOrder->status, ['confirmed', 'partially_dispatched'], true), Response::HTTP_UNPROCESSABLE_ENTITY, 'This order is not ready for dispatch.');
        $salesOrder->load(['customer', 'warehouse', 'items.product']);
        $stocks = WarehouseStock::where('warehouse_id', $salesOrder->warehouse_id)
            ->whereIn('product_id', $salesOrder->items->pluck('product_id'))
            ->pluck('quantity', 'product_id');
        return view('sales_orders.partials.dispatch_modal', compact('salesOrder', 'stocks'));
    }

    public function storeDispatch(Request $request, SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('dispatch_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|distinct',
            'items.*.quantity' => 'required|numeric|min:0',
            'remark' => 'nullable|string|max:2000',
        ]);
        if (collect($validated['items'])->sum(fn ($item) => (float) $item['quantity']) <= 0) {
            throw ValidationException::withMessages(['items' => 'Enter a dispatch quantity for at least one product.']);
        }

        $result = DB::transaction(function () use ($validated, $salesOrder) {
            $order = SalesOrder::whereKey($salesOrder->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($order->status, ['confirmed', 'partially_dispatched'], true), Response::HTTP_UNPROCESSABLE_ENTITY, 'This order is no longer available for dispatch.');

            $pendingItems = $order->items()->whereColumn('dispatched_quantity', '<', 'quantity')->lockForUpdate()->get()->keyBy('id');
            $submitted = collect($validated['items']);
            if ($submitted->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all() !== $pendingItems->keys()->sort()->values()->all()) {
                throw ValidationException::withMessages(['items' => 'Submit every pending order item.']);
            }

            $dispatchRows = [];
            foreach ($validated['items'] as $index => $row) {
                $item = $pendingItems->get((int) $row['id']);
                $quantity = round((float) $row['quantity'], 3);
                $pending = round((float) $item->quantity - (float) $item->dispatched_quantity, 3);
                if ($quantity > $pending) {
                    throw ValidationException::withMessages(["items.{$index}.quantity" => "{$item->product->product_name}: dispatch quantity cannot exceed pending quantity {$pending}."]);
                }
                if ($quantity <= 0) {
                    continue;
                }

                $stock = WarehouseStock::where('warehouse_id', $order->warehouse_id)
                    ->where('product_id', $item->product_id)->lockForUpdate()->first();
                $available = round((float) optional($stock)->quantity, 3);
                if (!$stock || $quantity > $available) {
                    throw ValidationException::withMessages(["items.{$index}.quantity" => "{$item->product->product_name}: only {$available} is available in {$order->warehouse->warehouse_name}."]);
                }

                $stock->quantity = round($available - $quantity, 3);
                $stock->save();
                $item->dispatched_quantity = round((float) $item->dispatched_quantity + $quantity, 3);
                $item->save();

                InventoryLedger::create([
                    'warehouse_id' => $order->warehouse_id,
                    'product_id' => $item->product_id,
                    'transaction_type' => 'sales_dispatch',
                    'reference_type' => SalesOrder::class,
                    'reference_id' => $order->id,
                    'quantity_in' => 0,
                    'quantity_out' => $quantity,
                    'balance_quantity' => $stock->quantity,
                    'remark' => $validated['remark'] ?? null,
                    'created_by' => Auth::id(),
                ]);
                $dispatchRows[] = [
                    'sales_order_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'warehouse_id' => $order->warehouse_id,
                    'quantity' => $quantity,
                ];
            }

            $dispatch = SalesOrderDispatch::create([
                'sales_order_id' => $order->id,
                'dispatch_date' => now()->toDateString(),
                'remark' => $validated['remark'] ?? null,
                'dispatched_by' => Auth::id(),
            ]);
            $dispatch->update(['dispatch_number' => 'DSP-' . str_pad($dispatch->id, 6, '0', STR_PAD_LEFT)]);
            $dispatch->items()->createMany($dispatchRows);

            $hasPending = $order->items()->whereColumn('dispatched_quantity', '<', 'quantity')->exists();
            $order->update([
                'status' => $hasPending ? 'partially_dispatched' : 'dispatched',
                'updated_by' => Auth::id(),
            ]);
            return ['complete' => !$hasPending, 'dispatch_number' => $dispatch->dispatch_number];
        });

        return response()->json([
            'status' => 'success',
            'complete' => $result['complete'],
            'message' => $result['complete']
                ? "Dispatch {$result['dispatch_number']} completed. Order fully dispatched."
                : "Dispatch {$result['dispatch_number']} saved. Remaining quantities stay pending.",
            'pending_dispatch_count' => SalesOrder::whereIn('status', ['confirmed', 'partially_dispatched'])->count(),
        ]);
    }

    private function customerQuery()
    {
        return Customers::where('active', 'Y')->whereHas('firmtypes', function ($query) {
            $query->whereRaw('LOWER(firmtype_name) = ?', ['customer']);
        });
    }

    private function assertReturnableOrder(SalesOrder $salesOrder): void
    {
        abort_unless(
            $salesOrder->order_type === 'b2c' && $salesOrder->status === 'dispatched',
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Only fully dispatched B2C Sales Orders can be returned.'
        );
    }

    private function assertOrderReturn(SalesOrder $salesOrder, SalesOrderDispatch $return): void
    {
        abort_unless(
            (int) $return->sales_order_id === (int) $salesOrder->id
                && str_starts_with((string) $return->dispatch_number, 'RTR-'),
            Response::HTTP_NOT_FOUND,
            'Return not found for this Sales Order.'
        );
    }

    private function validateReturnItems(Request $request, bool $reasonRequired): array
    {
        $validated = $request->validate([
            'reason' => ($reasonRequired ? 'required' : 'nullable') . '|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|distinct',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);
        if (collect($validated['items'])->sum(fn ($item) => (float) $item['quantity']) <= 0) {
            throw ValidationException::withMessages(['items' => 'Enter a return quantity for at least one product.']);
        }
        return $validated;
    }

    private function assertSubmittedReturnItems(array $submitted, $availableItems): void
    {
        $submittedIds = collect($submitted)->pluck('id')->map(fn ($id) => (int) $id)->sort()->values();
        $availableIds = $availableItems->keys()->map(fn ($id) => (int) $id)->sort()->values();
        if ($submittedIds->all() !== $availableIds->all()) {
            throw ValidationException::withMessages(['items' => 'Submit every return item.']);
        }
    }

    private function returnItemRows(array $submitted, $availableItems, string $maximumField, int $warehouseId): array
    {
        $rows = [];
        foreach ($submitted as $index => $row) {
            $item = $availableItems->get((int) $row['id']);
            abort_unless($item, Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid return item.');
            $quantity = round((float) $row['quantity'], 3);
            $maximum = round((float) $item->{$maximumField}, 3);
            if ($quantity > $maximum) {
                $name = optional($item->product)->product_name ?: 'Product';
                throw ValidationException::withMessages([
                    "items.{$index}.quantity" => "{$name}: return quantity cannot exceed {$maximum}.",
                ]);
            }
            if ($quantity <= 0) {
                continue;
            }
            $salesOrderItemId = $maximumField === 'quantity' ? $item->sales_order_item_id : $item->id;
            $rows[] = [
                'sales_order_item_id' => $salesOrderItemId,
                'product_id' => $item->product_id,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
            ];
        }
        return $rows;
    }
}
