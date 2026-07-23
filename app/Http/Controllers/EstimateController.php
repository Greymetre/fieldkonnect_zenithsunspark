<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\CurrentTaxInvoiceNo;
use App\Models\Customers;
use App\Models\CustomPdfValue;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\InvoiceSetting;
use App\Models\PaymentTerm;
use App\Models\Product;
use App\Models\State;
use App\Models\TaxInvoiceTax;
use App\Models\TaxInvoiceTds;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class EstimateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Estimate::with('customer');

            if ($request->start_date && $request->end_date && !empty($request->start_date) && !empty($request->end_date)) {
                $invoices = $invoices->whereBetween('estimate_date', [$request->start_date, $request->end_date]);
            }
            if ($request->searchInput && !empty($request->searchInput)) {
                //Useing $query orwher using bracket 
                $invoices = $invoices->where(function ($query) use ($request) {
                    $query->where('estimate_no', 'like', '%' . $request->searchInput . '%')
                        ->orWhere('order_no', 'like', '%' . $request->searchInput . '%')
                        ->orWhereHas('customer', function ($subQuery) use ($request) {
                            $subQuery->where('name', 'like', '%' . $request->searchInput . '%')
                                ->orWhere('mobile', 'like', '%' . $request->searchInput . '%');
                        });
                });
            }
            $invoices = $invoices->latest();
            return DataTables::of($invoices)
                ->addIndexColumn()
                ->editColumn('status', function ($data) {
                    if ($data->status == 0) {
                        return '<span class="badge badge-warning">Pending</span>';
                    } else {
                        return '<span class="badge badge-paid">Converted</span>';
                    }
                })
                ->editColumn('estimate_no', function ($data) {
                    return '<a href="' . route('estimate.show', $data->id) . '">' . $data->estimate_no . '</a>';
                })
                ->editColumn('estimate_date', function ($data) {
                    return date('d M Y', strtotime($data->estimate_date));
                })
                ->editColumn('due_date', function ($data) {
                    return date('d M Y', strtotime($data->due_date));
                })
                ->rawColumns(['status', 'estimate_no', 'estimate_date', 'due_date'])
                ->make(true);
        }
        return view('estimate.index');
    }
    public function create(Request $request)
    {
        $payment_terms = PaymentTerm::all();
        $products = Product::where('active', 'Y')->get();
        $customers = Customers::where('active', 'Y')->select('id', 'name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $users = User::where('active', 'Y')->select('id', 'name')->get();

        $lastInvoice = Estimate::orderBy('id', 'desc')->first();
        if ($lastInvoice) {
            $parts = explode('/', $lastInvoice->estimate_no);
            $prefix = $parts[0];
            $lastNumberPart = end($parts);
            $digitLength = strlen($lastNumberPart);
            $nextNumber = intval($lastNumberPart) + 1;
            $formattedNumber = str_pad($nextNumber, $digitLength, '0', STR_PAD_LEFT);
        } else {
            $currentYear = date('y'); // e.g. 25
            if (date('m') >= 4) {
                $startYear = $currentYear;
                $endYear   = $currentYear + 1;
            } else {
                $startYear = $currentYear - 1;
                $endYear   = $currentYear;
            }
            $prefix = 'EST-' . str_pad($startYear, 2, '0', STR_PAD_LEFT) . '-' . str_pad($endYear, 2, '0', STR_PAD_LEFT);
            $formattedNumber = '01';
        }
        $invoiceNumber = $prefix . '/' . $formattedNumber;
        if (strpos($invoiceNumber, '/') !== false) {
            [$prefixValue, $nextNumberValue] = explode('/', $invoiceNumber);
        } else {
            $prefixValue = $invoiceNumber;
            $nextNumberValue = '';
        }

        $all_tax = TaxInvoiceTax::all();
        $all_tds = TaxInvoiceTds::all();
        $settings = InvoiceSetting::with('labels')->first();

        return view('estimate.create', compact(
            'payment_terms',
            'products',
            'customers',
            'states',
            'invoiceNumber',
            'prefixValue',
            'nextNumberValue',
            'users',
            'all_tax',
            'all_tds',
            'settings'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'estimate_no' => 'required|unique:estimates,estimate_no',
            'hsn_sac_type.*' => 'nullable|in:HSN,SAC',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $invoice = new Estimate();
        $invoice->customer_id          =   $request->customer_id;
        $invoice->place_of_supply      =   $request->place_of_supply;
        $invoice->estimate_no           =   $request->estimate_no;
        $invoice->order_no             =   $request->order_no;
        $invoice->estimate_date         =   $request->estimate_date;
        $invoice->payment_term         =   $request->payment_term;
        $invoice->due_date             =   $request->due_date;
        $invoice->user_id              =   $request->user_id;
        $invoice->sub_total            =   $request->sub_total;
        $invoice->discount_type        =   $request->discount_type;
        $invoice->discount             =   $request->discount;
        $invoice->discount_amount      =   $request->discount_amount;
        $invoice->tds                  =   $request->tds ?? 0.00;
        $invoice->tds_amount           =   $request->tds_amount ?? 0.00;
        $invoice->adjustment           =   $request->adjustment ?? 0.00;
        $invoice->grand_total          =   $request->grand_total;
        $invoice->customer_notes       =   $request->customer_notes;
        $invoice->t_c                  =   $request->t_c;
        $invoice->save();

        if ($request->hasFile('files') && count($request->file('files')) > 0) {
            foreach ($request->file('files') as $file) {
                $invoice->addMedia($file)->toMediaCollection('estimate_files');
            }
        }

        if (isset($request->custom_pdf) && $request->custom_pdf == 'on') {
            foreach ($request->custom_labels as $label_id => $value) {
                CustomPdfValue::updateOrCreate(
                    ['estimate_id' => $invoice->id, 'label_id' => $label_id],
                    ['value' => $value]
                );
            }
        }

        foreach ($request->product_id as $k => $product) {
            $invoice->details()->create([
                'product_id' => $request->product_id[$k],
                'product_dec' => $request->product_dec[$k],
                'hsn_sac' => $request->hsn_sac[$k],
                'hsn_sac_type' => $request->hsn_sac_type[$k],
                'quantity' => $request->quantity[$k],
                'mrp' => $request->mrp[$k],
                'tax' => $request->tax[$k] ?? 0.00,
                'tax_amount' => $request->tax_amount[$k] ?? 0.00,
                'amount' => $request->amount[$k]
            ]);
        }
        return redirect()->route('estimate.show', $invoice->id);
    }

    public function show(Estimate $estimate)
    {
        $request = new Request(['customer_id' => $estimate->customer_id]);
        $customer_address_class = new AjaxController();
        $customer_address = $customer_address_class->getCustomerAddress($request);
        $address = $customer_address->getData(true)['data'];

        $settings = InvoiceSetting::with('address', 'labels')->first();

        $placeOfSupply = $estimate->place_of_supply;

        $taxSummary = $estimate->details
            ->groupBy('tax')
            ->map(function ($items, $taxId) use ($placeOfSupply, $settings) {
                $taxName = optional($items->first()->tax_details)->tax_name;
                $taxRate = optional($items->first()->tax_details)->tax_percentage;
                $totalAmount = $items->sum('tax_amount');
                $summary = [];

                if ($placeOfSupply == ($settings->address ? $settings->address->state_id : 1)) {
                    $halfRate = $taxRate / 2;
                    $halfAmount = $totalAmount / 2;
                    $summary[] = [
                        'tax_id' => $taxId . '_cgst',
                        'tax_name' => "CGST[{$halfRate}%]",
                        'total_tax_amount' => $halfAmount,
                    ];
                    $summary[] = [
                        'tax_id' => $taxId . '_sgst',
                        'tax_name' => "SGST[{$halfRate}%]",
                        'total_tax_amount' => $halfAmount,
                    ];
                } else {
                    $summary[] = [
                        'tax_id' => $taxId,
                        'tax_name' => "IGST[{$taxRate}%]",
                        'total_tax_amount' => $totalAmount,
                    ];
                }
                return $summary;
            })
            ->flatten(1)
            ->values();

        $hsnSacSummary = $estimate->details
            ->whereNotNull('hsn_sac')
            ->groupBy('hsn_sac')
            ->map(function ($items) use ($placeOfSupply) {
                $taxRate = optional($items->first()->tax_details)->tax_percentage;
                $totalAmount = $items->sum('amount');
                $totalTaxAmount = $items->sum('tax_amount');
                $summary = [];

                $summary[] = [
                    'hsn_sac' => $items[0]['hsn_sac'] . ' - ' . $items[0]['hsn_sac_type'],
                    'total_amount' => $totalAmount,
                    'tax_name' => $taxRate,
                    'total_tax_amount' => $totalTaxAmount,
                ];
                return $summary;
            })
            ->flatten(1)
            ->values();

        return view('estimate.show', compact('estimate', 'address', 'taxSummary', 'settings', 'hsnSacSummary'));
    }

    public function download(Request $request)
    {
        $filename = 'Estimate-' . now()->format('d-m-Y') . '.xlsx';

        $invoices = Estimate::with('customer');

        if ($request->start_date && $request->end_date && !empty($request->start_date) && !empty($request->end_date)) {
            $invoices = $invoices->whereBetween('estimate_date', [$request->start_date, $request->end_date]);
        }
        if ($request->searchInput && !empty($request->searchInput)) {
            //Useing $query orwher using bracket 
            $invoices = $invoices->where(function ($query) use ($request) {
                $query->where('estimate_no', 'like', '%' . $request->searchInput . '%')
                    ->orWhere('order_no', 'like', '%' . $request->searchInput . '%')
                    ->orWhereHas('customer', function ($subQuery) use ($request) {
                        $subQuery->where('name', 'like', '%' . $request->searchInput . '%')
                            ->orWhere('mobile', 'like', '%' . $request->searchInput . '%');
                    });
            });
        }
        $invoices = $invoices->latest()->get();

        // Build rows
        $rows = [];
        foreach ($invoices as $invoice) {
            $rows[] = [
                date('d M Y', strtotime($invoice->estimate_date)),
                $invoice->estimate_no,
                $invoice->order_no,
                $invoice->customer->name,
                $invoice->status == 0 ? 'Pending' : 'Converted',
                date('d M Y', strtotime($invoice->due_date)),
                $invoice->sub_total,
                $invoice->grand_total,
            ];
        }

        // Build headers
        $headers = [
            'Date',
            'Estimate #',
            'Order Number',
            'Customer Name',
            'STATUS',
            'Due Date',
            'ESTIMATE AMOUNT',
            'Sub Total',
        ];

       
        // ✅ Export
        $export = new ExcelExport($headers, $rows);
        return Excel::download($export, $filename);
    }
}
