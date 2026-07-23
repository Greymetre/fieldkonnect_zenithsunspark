<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PaymentLedgerController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('payment_ledger_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $paidExpression = '(SELECT COALESCE(SUM(sop.amount_received), 0) FROM sales_order_payments sop WHERE sop.sales_order_id = sales_orders.id)';
            $query = SalesOrder::with('customer')->withSum('payments as paid_amount', 'amount_received');

            switch ($request->input('filter_status')) {
                case 'pending':
                    $query->whereRaw("{$paidExpression} = 0");
                    break;
                case 'partial':
                    $query->whereRaw("{$paidExpression} > 0")
                        ->whereRaw("{$paidExpression} < sales_orders.grand_total");
                    break;
                case 'paid':
                    $query->whereRaw("{$paidExpression} >= sales_orders.grand_total");
                    break;
            }

            $query->latest('id');

            return datatables()->eloquent($query)
                ->addColumn('customer_name', fn ($row) => optional($row->customer)->name)
                ->editColumn('grand_total', fn ($row) => '₹' . number_format($row->grand_total, 2))
                ->addColumn('paid', fn ($row) => '₹' . number_format((float) $row->paid_amount, 2))
                ->addColumn('balance', fn ($row) => '₹' . number_format(max(0, (float) $row->grand_total - (float) $row->paid_amount), 2))
                ->addColumn('payment_status', function ($row) {
                    if ((float) $row->paid_amount <= 0) return '<span class="badge badge-warning">Pending</span>';
                    if ((float) $row->paid_amount < (float) $row->grand_total) return '<span class="badge badge-info">Partial</span>';
                    return '<span class="badge badge-success">Paid</span>';
                })
                ->addColumn('action', fn ($row) => '<button type="button" class="btn btn-link text-primary view-payment-ledger" data-url="' . e(route('payment-ledger.show', $row)) . '">View</button>')
                ->rawColumns(['payment_status', 'action'])
                ->make(true);
        }
        return view('payment_ledger.index');
    }

    public function show(SalesOrder $salesOrder)
    {
        abort_if(Gate::denies('payment_ledger_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $salesOrder->load(['customer', 'payments']);
        $paidAmount = round((float) $salesOrder->payments->sum('amount_received'), 2);
        $balanceDue = max(0, round((float) $salesOrder->grand_total - $paidAmount, 2));
        return view('payment_ledger.show_modal', compact('salesOrder', 'paidAmount', 'balanceDue'));
    }
}
