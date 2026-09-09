<?php

namespace App\Http\Controllers;

use App\DataTables\PurchaseOrderDataTable;
use App\Http\Requests\PurchaseOrderRequest;
use App\Models\Customers;
use App\Models\FirmType;
use App\Models\InventoryLedger;
use App\Models\InvoiceSetting;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\WareHouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use PDF;

class PurchaseOrderController extends Controller
{
    public function index(PurchaseOrderDataTable $dataTable)
    {
        abort_if(Gate::denies('purchase_order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $suppliers = $this->supplierQuery()->orderBy('name')->get();
        $warehouses = WareHouse::orderBy('warehouse_name')->get();

        return $dataTable->render('procurement.purchase_orders', compact('suppliers', 'warehouses'));
    }

    public function create()
    {
        abort_if(Gate::denies('purchase_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('procurement.purchase_order_form', $this->formData());
    }

    public function store(PurchaseOrderRequest $request)
    {
        abort_if(Gate::denies('purchase_order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->assertVendor($request->supplier_id);

        $purchaseOrder = DB::transaction(function () use ($request) {
            [$items, $totals] = $this->calculateItems($request->items);
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'po_date' => $request->po_date,
                'status' => 'draft',
                'subtotal' => $totals['subtotal'],
                'total_gst' => $totals['total_gst'],
                'grand_total' => $totals['grand_total'],
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            $purchaseOrder->update(['po_number' => 'PO-' . str_pad($purchaseOrder->id, 6, '0', STR_PAD_LEFT)]);
            $purchaseOrder->items()->createMany($items);
            return $purchaseOrder;
        });

        return redirect()->route('purchase-orders.index')
            ->with('message_success', 'Purchase Order created as Draft successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        abort_if(Gate::denies('purchase_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $purchaseOrder->load(['supplier.customerdetails', 'supplier.customeraddress.cityname', 'items.product', 'items.warehouse', 'creator', 'approver']);
        if (request()->ajax()) {
            return view('procurement.partials.purchase_order_modal', compact('purchaseOrder'));
        }
        return view('procurement.purchase_order_show', compact('purchaseOrder'));
    }

    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        abort_if(Gate::denies('purchase_order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $purchaseOrder->load([
            'supplier.customerdetails',
            'supplier.customeraddress.cityname',
            'supplier.customeraddress.districtname',
            'supplier.customeraddress.statename',
            'supplier.customeraddress.pincodename',
            'items.product.unitmeasures',
            'items.warehouse',
        ]);
        $settings = InvoiceSetting::with([
            'address.cityname',
            'address.districtname',
            'address.statename',
            'address.pincodename',
        ])->first();

        return PDF::loadView('orders.order_pdf', [
            'order' => $purchaseOrder,
            'settings' => $settings,
            'documentTitle' => 'Purchase Order',
            'documentNumber' => $purchaseOrder->po_number,
            'documentDate' => $purchaseOrder->po_date,
            'party' => $purchaseOrder->supplier,
            'partyLabel' => 'Supplier',
            'warehouse' => null,
        ])->setPaper('a4', 'portrait')
            ->download(($purchaseOrder->po_number ?: 'purchase-order') . '.pdf');
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        abort_if(Gate::denies('purchase_order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($purchaseOrder->status !== 'draft', Response::HTTP_UNPROCESSABLE_ENTITY, 'Only Draft Purchase Orders can be edited.');
        $purchaseOrder->load('items');
        return view('procurement.purchase_order_form', $this->formData($purchaseOrder));
    }

    public function update(PurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        abort_if(Gate::denies('purchase_order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($purchaseOrder->status !== 'draft', Response::HTTP_UNPROCESSABLE_ENTITY, 'Only Draft Purchase Orders can be edited.');
        $this->assertVendor($request->supplier_id);

        DB::transaction(function () use ($request, $purchaseOrder) {
            [$items, $totals] = $this->calculateItems($request->items);
            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'po_date' => $request->po_date,
                'subtotal' => $totals['subtotal'],
                'total_gst' => $totals['total_gst'],
                'grand_total' => $totals['grand_total'],
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ]);
            $purchaseOrder->items()->delete();
            $purchaseOrder->items()->createMany($items);
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('message_success', 'Purchase Order updated successfully.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        abort_if(Gate::denies('purchase_order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::transaction(function () use ($purchaseOrder) {
            $order = PurchaseOrder::whereKey($purchaseOrder->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($order->status, ['draft', 'approved'], true), Response::HTTP_UNPROCESSABLE_ENTITY, 'Only Draft or Approved Purchase Orders pending stock receipt can be deleted.');
            $order->delete();
        });
        return response()->json([
            'status' => 'success',
            'message' => 'Purchase Order deleted successfully.',
            'pending_receive_count' => PurchaseOrder::where('status', 'approved')->count(),
        ]);
    }

    public function approve(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_if(Gate::denies('purchase_order_approve'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_if($purchaseOrder->status !== 'draft', Response::HTTP_UNPROCESSABLE_ENTITY, 'Only Draft Purchase Orders can be approved.');
        $request->validate(['approval_remark' => 'nullable|string|max:2000']);
        $purchaseOrder->update([
            'status' => 'approved',
            'approval_remark' => $request->input('approval_remark'),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'updated_by' => Auth::id(),
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Purchase Order approved successfully.',
            'pending_receive_count' => PurchaseOrder::where('status', 'approved')->count(),
        ]);
    }

    public function receiveStock(Request $request)
    {
        abort_if(Gate::denies('receive_stock_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = PurchaseOrder::with(['supplier', 'items.warehouse'])
                ->whereIn('status', ['approved', 'received'])
                ->when($request->filled('search_text'), function ($query) use ($request) {
                    $search = $request->search_text;
                    $query->where(function ($q) use ($search) {
                        $q->where('po_number', 'like', "%{$search}%")
                            ->orWhereHas('supplier', fn ($supplier) => $supplier->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($request->filled('from_date'), fn ($q) => $q->whereDate('po_date', '>=', $request->from_date))
                ->when($request->filled('to_date'), fn ($q) => $q->whereDate('po_date', '<=', $request->to_date))
                ->latest('approved_at');

            return datatables()->eloquent($query)
                ->addColumn('action', function ($row) {
                    $url = route('procurement.receive-stock.modal', $row);
                    if ($row->status === 'approved' && Gate::allows('receive_stock_create')) {
                        return '<button type="button" class="btn btn-warning btn-sm receive-po" data-url="' . e($url) . '">Receive → Stock IN</button>';
                    }
                    return '<button type="button" class="btn btn-link text-primary view-receipt" data-url="' . e($url) . '">View</button>';
                })
                ->addColumn('supplier_name', fn ($row) => optional($row->supplier)->name)
                ->addColumn('warehouses', fn ($row) => $row->items->pluck('warehouse.warehouse_name')->filter()->unique()->implode(', '))
                ->addColumn('item_count', fn ($row) => $row->items->count())
                ->addColumn('total_quantity', fn ($row) => number_format($row->items->sum('quantity'), 3))
                ->editColumn('po_date', fn ($row) => optional($row->po_date)->format('d M Y'))
                ->editColumn('approved_at', fn ($row) => optional($row->approved_at)->format('d M Y'))
                ->addColumn('status_badge', fn ($row) => $row->status === 'received'
                    ? '<span class="badge badge-success">Received</span>'
                    : '<span class="badge badge-warning">Pending for Receive</span>')
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }

        return view('procurement.receive_stock');
    }

    public function receiveStockModal(PurchaseOrder $purchaseOrder)
    {
        abort_if(Gate::denies('receive_stock_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        abort_unless(in_array($purchaseOrder->status, ['approved', 'received'], true), Response::HTTP_UNPROCESSABLE_ENTITY, 'This Purchase Order is not available for receipt.');

        $purchaseOrder->load(['supplier', 'items.product', 'items.warehouse']);
        $warehouses = WareHouse::orderBy('warehouse_name')->get();

        return view('procurement.partials.receive_stock_modal', compact('purchaseOrder', 'warehouses'));
    }

    public function storeReceipt(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_if(Gate::denies('receive_stock_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.warehouse_id' => 'required|integer|exists:ware_houses,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'receive_remark' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            $lockedOrder = PurchaseOrder::whereKey($purchaseOrder->id)->lockForUpdate()->firstOrFail();
            abort_if($lockedOrder->status !== 'approved', Response::HTTP_UNPROCESSABLE_ENTITY, 'This Purchase Order has already been received.');

            $submittedItems = collect($validated['items']);
            $items = $lockedOrder->items()->whereIn('id', $submittedItems->pluck('id'))->get()->keyBy('id');
            abort_if($items->count() !== $submittedItems->count() || $items->count() !== $lockedOrder->items()->count(), Response::HTTP_UNPROCESSABLE_ENTITY, 'All Purchase Order items are required.');

            $subtotal = 0;
            $totalGst = 0;
            foreach ($validated['items'] as $row) {
                $item = $items->get((int) $row['id']);
                abort_unless($item, Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid Purchase Order item.');

                $quantity = round((float) $row['quantity'], 3);
                $taxable = round($quantity * (float) $item->rate, 2);
                $gstAmount = round($taxable * (float) $item->gst_percent / 100, 2);
                $item->update([
                    'warehouse_id' => $row['warehouse_id'],
                    'quantity' => $quantity,
                    'received_quantity' => $quantity,
                    'taxable_amount' => $taxable,
                    'gst_amount' => $gstAmount,
                    'total_amount' => $taxable + $gstAmount,
                ]);

                $stock = WarehouseStock::firstOrCreate(
                    ['warehouse_id' => $row['warehouse_id'], 'product_id' => $item->product_id],
                    ['quantity' => 0]
                );
                $stock = WarehouseStock::whereKey($stock->id)->lockForUpdate()->first();
                $stock->quantity = round((float) $stock->quantity + $quantity, 3);
                $stock->save();

                InventoryLedger::create([
                    'warehouse_id' => $row['warehouse_id'],
                    'product_id' => $item->product_id,
                    'transaction_type' => 'purchase_receipt',
                    'reference_type' => PurchaseOrder::class,
                    'reference_id' => $lockedOrder->id,
                    'quantity_in' => $quantity,
                    'quantity_out' => 0,
                    'balance_quantity' => $stock->quantity,
                    'remark' => $validated['receive_remark'] ?? null,
                    'created_by' => Auth::id(),
                ]);
                $subtotal += $taxable;
                $totalGst += $gstAmount;
            }

            $lockedOrder->update([
                'status' => 'received',
                'subtotal' => round($subtotal, 2),
                'total_gst' => round($totalGst, 2),
                'grand_total' => round($subtotal + $totalGst, 2),
                'receive_remark' => $validated['receive_remark'] ?? null,
                'received_by' => Auth::id(),
                'received_at' => now(),
                'updated_by' => Auth::id(),
            ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Stock received successfully.',
            'pending_receive_count' => PurchaseOrder::where('status', 'approved')->count(),
        ]);
    }

    private function formData(?PurchaseOrder $purchaseOrder = null): array
    {
        $suppliers = $this->supplierQuery()
            ->with(['customerdetails', 'customeraddress.cityname', 'customeraddress.statename'])
            ->orderBy('name')->get();
        $products = Product::where('active', 'Y')
            ->with(['productdetails' => fn ($q) => $q->orderByDesc('isprimary')])
            ->orderBy('product_name')->get();
        $warehouses = WareHouse::orderBy('warehouse_name')->get();
        $vendorFirmType = FirmType::whereRaw('LOWER(firmtype_name) = ?', ['vendor'])->first();

        return compact('purchaseOrder', 'suppliers', 'products', 'warehouses', 'vendorFirmType');
    }

    private function supplierQuery()
    {
        return Customers::where('active', 'Y')->whereHas('firmtypes', function ($query) {
            $query->whereRaw('LOWER(firmtype_name) = ?', ['vendor']);
        });
    }

    private function assertVendor(int $supplierId): void
    {
        abort_unless($this->supplierQuery()->whereKey($supplierId)->exists(), Response::HTTP_UNPROCESSABLE_ENTITY, 'Selected supplier must have Firm Type Vendor.');
    }

    private function calculateItems(array $rows): array
    {
        $items = [];
        $subtotal = 0;
        $totalGst = 0;
        foreach ($rows as $row) {
            $quantity = round((float) $row['quantity'], 3);
            $rate = round((float) $row['rate'], 2);
            $gstPercent = round((float) $row['gst_percent'], 2);
            $taxable = round($quantity * $rate, 2);
            $gstAmount = round($taxable * $gstPercent / 100, 2);
            $total = $taxable + $gstAmount;
            $subtotal += $taxable;
            $totalGst += $gstAmount;
            $items[] = [
                'product_id' => $row['product_id'],
                'warehouse_id' => $row['warehouse_id'],
                'quantity' => $quantity,
                'rate' => $rate,
                'gst_percent' => $gstPercent,
                'taxable_amount' => $taxable,
                'gst_amount' => $gstAmount,
                'total_amount' => $total,
            ];
        }
        return [$items, [
            'subtotal' => round($subtotal, 2),
            'total_gst' => round($totalGst, 2),
            'grand_total' => round($subtotal + $totalGst, 2),
        ]];
    }
}
