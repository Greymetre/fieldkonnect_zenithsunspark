<?php

namespace App\DataTables;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Services\DataTable;

class PurchaseOrderDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()->eloquent($query)
            ->editColumn('po_date', fn ($row) => optional($row->po_date)->format('d M Y'))
            ->editColumn('grand_total', fn ($row) => '₹' . number_format($row->grand_total, 2))
            ->addColumn('supplier_name', fn ($row) => optional($row->supplier)->name)
            ->addColumn('warehouses', fn ($row) => $row->items->pluck('warehouse.warehouse_name')->filter()->unique()->implode(', '))
            ->addColumn('item_count', fn ($row) => $row->items->count())
            ->addColumn('status_badge', function ($row) {
                $colors = ['draft' => 'secondary', 'approved' => 'info', 'partially_received' => 'warning', 'received' => 'success', 'cancelled' => 'danger'];
                return '<span class="badge badge-' . ($colors[$row->status] ?? 'secondary') . '">' .
                    ucwords(str_replace('_', ' ', $row->status)) . '</span>';
            })
            ->addColumn('action', function ($row) {
                $buttons = '';
                if (Auth::user()->can('purchase_order_show')) {
                    $buttons .= '<button type="button" class="btn btn-theme btn-just-icon btn-sm view-po" data-id="' . $row->id . '" title="View"><i class="material-icons">visibility</i></button>';
                }
                if ($row->status === 'draft' && Auth::user()->can('purchase_order_edit')) {
                    $buttons .= '<a href="' . route('purchase-orders.edit', $row) . '" class="btn btn-info btn-just-icon btn-sm" title="Edit"><i class="material-icons">edit</i></a>';
                }
                if (in_array($row->status, ['draft', 'approved'], true) && Auth::user()->can('purchase_order_delete')) {
                    $buttons .= '<button type="button" class="btn btn-danger btn-just-icon btn-sm delete-po" data-id="' . $row->id . '" title="Delete"><i class="material-icons">clear</i></button>';
                }
                return '<div class="btn-group btn-group-sm">' . $buttons . '</div>';
            })
            ->rawColumns(['status_badge', 'action']);
    }

    public function query(PurchaseOrder $model)
    {
        $request = request();
        return $model->newQuery()
            ->with(['supplier', 'items.warehouse'])
            ->when($request->filled('search_text'), function ($query) use ($request) {
                $search = $request->search_text;
                $query->where(function ($q) use ($search) {
                    $q->where('po_number', 'like', "%{$search}%")
                        ->orWhereHas('supplier', fn ($supplier) => $supplier->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('supplier_id'), fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->when($request->filled('warehouse_id'), fn ($q) => $q->whereHas('items', fn ($item) => $item->where('warehouse_id', $request->warehouse_id)))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('from_date'), fn ($q) => $q->whereDate('po_date', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn ($q) => $q->whereDate('po_date', '<=', $request->to_date))
            ->latest('id');
    }
}
