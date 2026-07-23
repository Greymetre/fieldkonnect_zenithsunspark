<?php

namespace App\Http\Controllers;

use App\Exports\ExcelExport;
use App\Models\Address;
use App\Models\CurrentTaxInvoiceNo;
use App\Models\Customers;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\InvoiceLabel;
use App\Models\InvoiceSetting;
use App\Models\PaymentTerm;
use App\Models\Product;
use App\Models\State;
use App\Models\TaxInvoiceTax;
use App\Models\TaxInvoiceTds;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TaxInvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Invoice::with('customer');

            if ($request->start_date && $request->end_date && !empty($request->start_date) && !empty($request->end_date)) {
                $invoices = $invoices->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
            }
            if ($request->searchInput && !empty($request->searchInput)) {
                //Useing $query orwher using bracket 
                $invoices = $invoices->where(function ($query) use ($request) {
                    $query->where('invoice_no', 'like', '%' . $request->searchInput . '%')
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
                // ->editColumn('status', function ($data) {
                //     return '<span class="badge badge-paid">Paid</span>';
                // })
                ->editColumn('status', function ($data) {
                    $today = \Carbon\Carbon::today();
                    $dueDate = \Carbon\Carbon::parse($data->due_date);

                    if ($dueDate->isToday()) {
                        return '<span class="badge badge-warning">Due Today</span>';
                    } elseif ($dueDate->isPast()) {
                        $days = $dueDate->diffInDays($today);
                        return '<span class="badge badge-danger">Overdue by ' . $days . ' days</span>';
                    } else {
                        $days = $today->diffInDays($dueDate);
                        return '<span class="badge badge-info">Due in ' . $days . ' days</span>';
                    }
                })
                ->editColumn('invoice_no', function ($data) {
                    return '<a href="' . route('tax_invoice.show', $data->id) . '">' . $data->invoice_no . '</a>';
                })
                ->editColumn('invoice_date', function ($data) {
                    return date('d M Y', strtotime($data->invoice_date));
                })
                ->editColumn('due_date', function ($data) {
                    return date('d M Y', strtotime($data->due_date));
                })
                ->rawColumns(['status', 'invoice_no', 'invoice_date', 'due_date'])
                ->make(true);
        }
        return view('taxinvoice.index');
    }
    public function create(Request $request)
    {
        $payment_terms = PaymentTerm::all();
        $products = Product::where('active', 'Y')->orderBy('id', 'desc')->get();
        
        $customers = Customers::where('active', 'Y')->select('id', 'name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $users = User::where('active', 'Y')->select('id', 'name')->get();

        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        if ($lastInvoice) {
            $parts = explode('/', $lastInvoice->invoice_no);
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
            $prefix = 'INV-' . str_pad($startYear, 2, '0', STR_PAD_LEFT) . '-' . str_pad($endYear, 2, '0', STR_PAD_LEFT);
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

        return view('taxinvoice.create', compact(
            'payment_terms',
            'products',
            'customers',
            'states',
            'invoiceNumber',
            'prefixValue',
            'nextNumberValue',
            'users',
            'all_tax',
            'all_tds'
        ));
    }
    public function convert_to_tax_invoice(Request $request, Estimate $convert_estimate)
    {
        $payment_terms = PaymentTerm::all();
        $products = Product::where('active', 'Y')->get();
        $customers = Customers::where('active', 'Y')->select('id', 'name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $users = User::where('active', 'Y')->select('id', 'name')->get();

        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        if ($lastInvoice) {
            $parts = explode('/', $lastInvoice->invoice_no);
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
            $prefix = 'INV-' . str_pad($startYear, 2, '0', STR_PAD_LEFT) . '-' . str_pad($endYear, 2, '0', STR_PAD_LEFT);
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

        return view('taxinvoice.convert', compact(
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
            'convert_estimate'
        ));
    }
    public function add_payment_term(Request $request)
    {
        $payment_term = new PaymentTerm();
        $payment_term->term_name = $request->term_name;
        $payment_term->number_of_days = $request->number_of_days;
        $payment_term->save();
        return response()->json(['status' => true, 'message' => 'Payment Term Added Successfully!', 'data' => $payment_term]);
    }
    public function add_tax(Request $request)
    {
        $tax = new TaxInvoiceTax();
        $tax->tax_name = $request->tax_name;
        $tax->tax_percentage = $request->tax_percentage;
        $tax->save();
        return response()->json(['status' => true, 'message' => 'Tax Added Successfully!', 'data' => $tax]);
    }
    public function add_tds(Request $request)
    {
        $tds = new TaxInvoiceTds();
        $tds->tax_name = $request->tax_name;
        $tds->rate = $request->rate;
        $tds->section = $request->section;
        $tds->save();
        return response()->json(['status' => true, 'message' => 'TDS Added Successfully!', 'data' => $tds]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'invoice_no' => 'required|unique:invoices',
            'hsn_sac_type.*' => 'nullable|in:HSN,SAC',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        // dd($request->all());
        $invoice = new Invoice();
        $invoice->customer_id          =   $request->customer_id;
        $invoice->place_of_supply      =   $request->place_of_supply;
        $invoice->invoice_no           =   $request->invoice_no;
        $invoice->order_no             =   $request->order_no;
        $invoice->invoice_date         =   $request->invoice_date;
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

        if (isset($request->convert_estimate_id) && !empty($request->convert_estimate_id)) {
            Estimate::where('id', $request->convert_estimate_id)->update(['invoice_id' => $invoice->id, 'status' => 1]);
        }

        if ($request->hasFile('files') && count($request->file('files')) > 0) {
            foreach ($request->file('files') as $file) {
                $invoice->addMedia($file)->toMediaCollection('invoice_files');
            }
        }

        foreach ($request->product_id as $k => $product) {
            if (empty($request->product_id[$k])) continue;
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
        // return redirect()->route('tax_invoice.index');
        return redirect()->route('tax_invoice.show', $invoice->id);
    }

    public function show(Invoice $tax_invoice, Request $request)
    {
        $request = new Request(['customer_id' => $tax_invoice->customer_id]);
        $customer_address_class = new AjaxController();
        $customer_address = $customer_address_class->getCustomerAddress($request);
        $address = $customer_address->getData(true)['data'];

        $settings = InvoiceSetting::with('address', 'labels')->first();

        $placeOfSupply = $tax_invoice->place_of_supply;

        $taxSummary = $tax_invoice->details
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

        $hsnSacSummary = $tax_invoice->details
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

        return view('taxinvoice.show', compact('tax_invoice', 'address', 'taxSummary', 'settings', 'hsnSacSummary'));
    }

    public function invoice_setting(Request $request)
    {
        $invoice_setting = InvoiceSetting::with('labels')->first();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        return view('taxinvoice.invoice_setting', compact('invoice_setting', 'states'));
    }

    public function invoice_setting_store(Request $request)
    {
        $request->validate([
            'invoice_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:9048',
            'invoice_esign' => 'nullable|image|mimes:png,jpg,jpeg|max:9048',
            'company_name' => 'required|string|max:255',
            'gst_number' => 'nullable|string|max:255',
            'pan_number' => 'nullable|string|max:255',
            'labels.*.name' => 'required|string|max:255',
            'labels.*.page' => 'required|in:2,3,4,5',
            'labels.*.icon' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $pages = [];

        foreach ($request->labels as $labelData) {
            $page = $labelData['page'] ?? null;
            $heading = $labelData['page_heading'] ?? null;

            if ($page && $heading) {
                if (isset($pages[$page]) && $pages[$page] !== $heading) {
                    return back()->withErrors([
                        "Page {$page} can only have one unique heading across all labels."
                    ]);
                }
                $pages[$page] = $heading;
            }
        }

        // Fetch or create first invoice setting
        $invoiceSetting = InvoiceSetting::first() ?? new InvoiceSetting();
        $invoiceSetting->company_name = $request->company_name;
        $invoiceSetting->gst_number = $request->gst_number;
        $invoiceSetting->pan_number = $request->pan_number;
        $invoiceSetting->save();

        if ($request->company_address && !empty($request->company_address)) {
            Address::updateOrCreate([
                'model_type' => 'App\Models\InvoiceSetting',
                'model_id' => $invoiceSetting->id,
            ], [
                'address1' => $request->company_address ?? 'N/A',
                'country_id' => 1,
                'pincode_id' => $request->pincode_id ?? null,
                'state_id' => $request->state_id ?? null,
                'city_id' => $request->city_id ?? null,
                'district_id' => $request->district_id ?? null,
                'created_by' => Auth::id()
            ]);
        }

        // ✅ Upload invoice logo
        if ($request->hasFile('invoice_logo')) {
            $invoiceSetting->clearMediaCollection('invoice_logo');
            $invoiceSetting->addMedia($request->file('invoice_logo'))
                ->toMediaCollection('invoice_logo');
        }

        // ✅ Upload invoice e-sign
        if ($request->hasFile('invoice_esign')) {
            $invoiceSetting->clearMediaCollection('invoice_esign');
            $invoiceSetting->addMedia($request->file('invoice_esign'))
                ->toMediaCollection('invoice_esign');
        }

        // ✅ Handle labels
        if ($request->has('labels')) {
            foreach ($request->labels as $index => $labelData) {
                // Skip empty rows
                if (empty($labelData['name']) && empty($labelData['page']) && empty($request->file("labels.$index.icon"))) {
                    continue;
                }

                // Update existing or create new
                $label = isset($labelData['id'])
                    ? InvoiceLabel::find($labelData['id'])
                    : new InvoiceLabel();

                $label->invoice_setting_id = $invoiceSetting->id;
                $label->name = $labelData['name'] ?? '';
                $label->page = $labelData['page'] ?? null;
                $label->page_heading = $labelData['page_heading'] ?? null;
                $label->save();

                // ✅ Upload label icon
                if ($request->hasFile("labels.$index.icon")) {
                    $label->clearMediaCollection('label_icon');
                    $label->addMedia($request->file("labels.$index.icon"))
                        ->toMediaCollection('label_icon');
                }
            }
        }

        return redirect()->back()->with('success', 'Invoice settings updated successfully!');
    }

    public function destroy_label($id)
    {
        $label = InvoiceLabel::find($id);

        if (!$label) {
            return response()->json(['message' => 'Label not found'], 404);
        }

        $label->delete();

        return response()->json(['message' => 'Label deleted successfully']);
    }

    public function download(Request $request)
    {
        $filename = 'Invoice-' . now()->format('d-m-Y') . '.xlsx';

        $invoices = Invoice::with('customer', 'state');

        if ($request->searchInput && !empty($request->searchInput)) {
            //Useing $query orwher using bracket 
            $invoices = $invoices->where(function ($query) use ($request) {
                $query->where('invoice_no', 'like', '%' . $request->searchInput . '%')
                    ->orWhere('order_no', 'like', '%' . $request->searchInput . '%')
                    ->orWhereHas('customer', function ($subQuery) use ($request) {
                        $subQuery->where('name', 'like', '%' . $request->searchInput . '%')
                            ->orWhere('mobile', 'like', '%' . $request->searchInput . '%');
                    });
            });
        }

        if ($request->start_date && $request->end_date && !empty($request->start_date) && !empty($request->end_date)) {
            $invoices = $invoices->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
        }
        $invoices = $invoices->latest()->get();

        // Build rows
        $rows = [];
        $settings = InvoiceSetting::with('address', 'labels')->first();
        foreach ($invoices as $invoice) {
            $today = \Carbon\Carbon::today();
            $dueDate = \Carbon\Carbon::parse($invoice->due_date);

            if ($dueDate->isToday()) {
                $status = 'Due Today';
            } elseif ($dueDate->isPast()) {
                $days = $dueDate->diffInDays($today);
                $status = 'Overdue by ' . $days . ' days';
            } else {
                $days = $today->diffInDays($dueDate);
                $status = 'Due in ' . $days . ' days';
            }
            $igst = 0;
            $cgst = 0;
            $sgst = 0;
            if($settings->address){
                if($settings->address->state_id && $invoice->place_of_supply == $settings->address->state_id){
                    $cgst = $invoice->details->sum('tax_amount')/2;
                    $sgst = $invoice->details->sum('tax_amount')/2;
                }else{
                    $igst = $invoice->details->sum('tax_amount');
                }
            }else{
                $igst = $invoice->details->sum('tax_amount');
            }
            $rows[] = [
                date('d M Y', strtotime($invoice->invoice_date)),
                $invoice->invoice_no,
                $status,
                $invoice->customer->id,
                $invoice->customer->name,
                $invoice->customer->address ? $invoice->customer->address->cityname?->city_name : 'N/A',
                $invoice->state ? $invoice->state->gst_code.' - '.$invoice->state->state_name : 'N/A',
                $invoice->customer->customerdetails ? $invoice->customer->customerdetails->gstin_no : 'N/A',
                $invoice->order_no,
                $invoice->sub_total,
                $invoice->grand_total,
                date('d M Y', strtotime($invoice->due_date)),
                $invoice->grand_total,
                $cgst,
                $sgst,
                $igst                
            ];
        }

        // Build headers
        $headers = [
            'Invoice Date',
            'Invoice Number',
            'Invoice Status',
            'Customer ID',
            'Customer Name',
            'City',
            'Place of Supply',
            'GST Number',
            'Order Number',
            'SubTotal',
            'Total',
            'Due Date',
            'Balance',
            'CGST',
            'SGST',
            'IGST',
        ];


        // ✅ Export
        $export = new ExcelExport($headers, $rows);
        return Excel::download($export, $filename);
    }

    public function edit(Invoice $tax_invoice)
    {
        $payment_terms = PaymentTerm::all();
        $products = Product::where('active', 'Y')->orderBy('id', 'desc')->get();
        
        $customers = Customers::where('active', 'Y')->select('id', 'name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $users = User::where('active', 'Y')->select('id', 'name')->get();

        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        if ($lastInvoice) {
            $parts = explode('/', $lastInvoice->invoice_no);
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
            $prefix = 'INV-' . str_pad($startYear, 2, '0', STR_PAD_LEFT) . '-' . str_pad($endYear, 2, '0', STR_PAD_LEFT);
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

        return view('taxinvoice.create', compact('tax_invoice', 'payment_terms', 'products', 'customers', 'states', 'users', 'prefixValue', 'nextNumberValue', 'all_tax', 'all_tds', 'invoiceNumber'));
    }
}
