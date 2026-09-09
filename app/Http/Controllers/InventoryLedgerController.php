<?php

namespace App\Http\Controllers;

use App\Exports\InventoryLedgerExport;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class InventoryLedgerController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (!$request->ajax()) {
            return view('inventory.inventory_ledger');
        }

        $query = $this->ledgerQuery($request);

        return datatables()->query($query)
            ->editColumn('movement_date', function ($row) {
                $date = \Carbon\Carbon::parse($row->movement_date);

                return '<strong>' . $date->format('d M y') . '</strong>'
                    . '<small class="d-block text-muted">' . $date->format('h:i A') . '</small>';
            })
            ->addColumn('movement_type', function ($row) {
                return (float) $row->quantity_in > 0
                    ? '<span class="movement-badge movement-in"><span></span>IN</span>'
                    : '<span class="movement-badge movement-out"><span></span>OUT</span>';
            })
            ->addColumn('before_quantity', fn ($row) => $this->quantity(
                (float) $row->balance_quantity - (float) $row->quantity_in + (float) $row->quantity_out
            ))
            ->addColumn('change_quantity', function ($row) {
                $isIn = (float) $row->quantity_in > 0;
                $change = $isIn ? (float) $row->quantity_in : -(float) $row->quantity_out;

                return '<strong class="' . ($isIn ? 'text-success' : 'text-danger') . '">'
                    . ($change > 0 ? '+' : '') . $this->quantity($change) . '</strong>';
            })
            ->editColumn('balance_quantity', fn ($row) => '<strong>' . $this->quantity($row->balance_quantity) . '</strong>')
            ->editColumn('person_name', fn ($row) => $row->transaction_type === 'sales_return'
                ? 'Return Accepted'
                : ($row->person_name ?: 'System'))
            ->addColumn('reference', fn ($row) => $this->reference($row))
            ->rawColumns(['movement_date', 'movement_type', 'change_quantity', 'balance_quantity'])
            ->make(true);
    }

    public function export(Request $request)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rows = $this->ledgerQuery($request)
            ->get()
            ->map(function ($row) {
                $row->reference = $this->reference($row);
                return $row;
            });

        return Excel::download(
            new InventoryLedgerExport($rows),
            'inventory-ledger-' . now()->format('Y-m-d-His') . '.xlsx'
        );
    }

    private function ledgerQuery(Request $request): Builder
    {
        $query = DB::table('inventory_ledgers as il')
            ->join('products as p', 'p.id', '=', 'il.product_id')
            ->join('ware_houses as wh', 'wh.id', '=', 'il.warehouse_id')
            ->leftJoin('users as u', 'u.id', '=', 'il.created_by')
            ->leftJoin('purchase_orders as po', function ($join) {
                $join->on('po.id', '=', 'il.reference_id')
                    ->where('il.reference_type', PurchaseOrder::class);
            })
            ->leftJoin('sales_orders as so', function ($join) {
                $join->on('so.id', '=', 'il.reference_id')
                    ->where('il.reference_type', SalesOrder::class);
            })
            ->select([
                'il.id',
                'il.created_at as movement_date',
                'il.transaction_type',
                'il.reference_type',
                'il.reference_id',
                'il.quantity_in',
                'il.quantity_out',
                'il.balance_quantity',
                'il.remark',
                'p.product_name',
                'wh.warehouse_name',
                'u.name as person_name',
                'po.po_number',
                'so.order_number',
            ]);

        if ($request->filled('search_text')) {
            $search = trim($request->search_text);
            $query->where(function ($builder) use ($search) {
                $builder->where('p.product_name', 'like', "%{$search}%")
                    ->orWhere('wh.warehouse_name', 'like', "%{$search}%")
                    ->orWhere('u.name', 'like', "%{$search}%")
                    ->orWhere('po.po_number', 'like', "%{$search}%")
                    ->orWhere('so.order_number', 'like', "%{$search}%")
                    ->orWhere('il.remark', 'like', "%{$search}%");
            });
        }

        $query->when($request->filled('date_from'), fn ($builder) => $builder->whereDate('il.created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($builder) => $builder->whereDate('il.created_at', '<=', $request->date_to));

        if ($request->movement === 'in') {
            $query->where('il.quantity_in', '>', 0);
        } elseif ($request->movement === 'out') {
            $query->where('il.quantity_out', '>', 0);
        }

        return $query
            ->orderByDesc('il.id');
    }

    private function reference($row): string
    {
        if (!empty($row->po_number)) {
            return $row->po_number;
        }
        if (!empty($row->order_number)) {
            if ($row->transaction_type === 'sales_delete_reversal') {
                return $row->order_number . ' (Deletion reversal)';
            }
            if ($row->transaction_type === 'dispatch_delete_reversal') {
                return $row->order_number . ' (Dispatch deletion reversal)';
            }
            return $row->order_number . ($row->transaction_type === 'sales_return' ? ' ↩' : '');
        }

        return $row->remark ?: 'Opening Balance';
    }

    private function quantity($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
    }
}
