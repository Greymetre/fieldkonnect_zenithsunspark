<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Illuminate\Http\Request;
use App\Http\Requests\SalesRequest;
use App\Models\SalesDetails;
use App\Models\Wallet;
use App\Models\WalletDetail;
use App\Models\Product;
use App\Models\Customers;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\SalesDataTable;
use App\Imports\SalesImport;
use App\Exports\SalesExport;
use App\Exports\SalesTemplate;
use App\Models\Category;
use App\Models\CustomerType;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->sales = new Sales();
    }

    public function index(SalesDataTable $dataTable)
    {
        abort_if(Gate::denies('sale_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $sellers_ids = $this->sales->distinct()->pluck('seller_id');
        $buyer_ids = $this->sales->distinct()->pluck('buyer_id');
        $divisions = Category::where('active', 'Y')->get();
        $retailers = Customers::whereIn("id", $buyer_ids)->get();
        $distributors = Customers::whereIn("id", $sellers_ids)->get();
        $customer_types = CustomerType::where('active', 'Y')->get();
        return $dataTable->render('sales.index', compact('divisions', 'retailers', 'distributors', 'customer_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('sale_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $products = Product::where('active', '=', 'Y')->select('id', 'display_name', 'product_image')->get();
        $userids = getUsersReportingToAuth();
        $sellers = Customers::whereHas('customertypes', function ($query) {
            $query->where('type_name', '=', 'distributor');
        })
            ->where(function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('executive_id', $userids);
                }
            })
            ->where('active', '=', 'Y')
            ->select('id', 'name', 'mobile')
            ->get();
        $buyers = Customers::whereIn('customertype', ['2', '3', '4', '5', '6'])
            ->where(function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('executive_id', $userids);
                }
            })
            ->where('active', '=', 'Y')
            ->select('id', 'name', 'mobile')
            ->get();

        return view('sales.create', compact('products', 'sellers', 'buyers'))->with('sales', $this->sales);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SalesRequest $request)
    {
        abort_if(Gate::denies('sale_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $request['active'] = 'Y';
            $request['created_by'] = Auth::user()->id;
            $validator = Validator::make($request->all(), [
                'buyer_id' => 'required',
                'seller_id' => 'required',
                'invoice_no' => 'required',
                //'invoice_date' => 'required',
                'grand_total' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $data = collect([$request]);
            $response = insertSales($data);
            if ($response['status'] == 'success') {
                return Redirect::to('sales')->with('message_success', 'Sales Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Sales Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('sale_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $sales = $this->sales->with('saledetails', 'saledetails.products', 'saledetails.productdetails')->find($id);
        return view('sales.show')->with('sales', $sales);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('sale_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $products = Product::where('active', '=', 'Y')->select('id', 'display_name', 'product_image')->get();
        $userids = getUsersReportingToAuth();
        $sales = $this->sales->with('saledetails', 'saledetails.products', 'saledetails.products.productdetails')->find($id);
        $sellers = Customers::where('active', '=', 'Y')
            ->where('id', $sales->seller_id)
            ->select('id', 'name', 'mobile')
            ->get();
        $buyers = Customers::where('active', '=', 'Y')
            ->where('id', $sales->buyer_id)
            ->select('id', 'name', 'mobile')
            ->get();
        return view('sales.create', compact('products', 'sellers', 'buyers'))->with('sales', $sales);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function update(Sales $sales, Request $request)
    {
        try {
            abort_if(Gate::denies('sale_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            
            $sales = Sales::find($request->sales_id);
            $sales->transport_details = $request->transport_details;
            $sales->lr_no = $request->lr_no;
            $sales->dispatch_date = $request->dispatch_date;
            if ($sales->save()) {
                return Redirect::to('sales')->with('message_success', 'Sales Update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Sales Update')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('sale_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        SalesDetails::where('sales_id', '=', $id)->delete();
        $walletid = Wallet::where('sales_id', '=', $id)->pluck('id');
        WalletDetail::whereIn('wallet_id', $walletid)->delete();
        Wallet::where('sales_id', '=', $id)->delete();
        $sale = Sales::find($id);
        if ($sale->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Sale deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Sale Delete!']);
    }

    public function active(Request $request)
    {
        if (Sales::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y'])) {
            SalesDetails::where('sales_id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y']);
            $walletid = Wallet::where('sales_id', '=', $request['id'])->pluck('id');
            WalletDetail::whereIn('wallet_id', $walletid)->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y']);
            Wallet::where('sales_id', '=', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y']);
            $message = ($request['active'] == 'Y') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'Sale ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }
    public function saleApproval($id)
    {
        abort_if(Gate::denies('sale_active'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        salesApproval($id);
        return redirect()->back()->with('message_success', 'Sales Approved Successfully');
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('sale_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new SalesImport, request()->file('import_file'));
        return back();
    }
    public function download(Request $request)
    {
        abort_if(Gate::denies('sale_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SalesExport($request), 'sales.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('sale_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SalesTemplate, 'sales.xlsx');
    }

    public function salesInfo(Request $request)
    {
        if ($request->ajax()) {
            $data = Sales::with('sellers', 'buyers')
                ->latest();
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($data) {
                    return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                })
                ->editColumn('invoice_date', function ($data) {
                    return isset($data->invoice_date) ? showdateformat($data->invoice_date) : '';
                })
                ->addColumn('action', function ($query) {
                    $btn = '';
                    if (auth()->user()->can(['order_show'])) {
                        $btn = $btn . '<a href="' . url("sales/" . encrypt($query->id)) . '" class="btn btn-theme btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.sales.title_singular') . '">
                                            <i class="material-icons">visibility</i>
                                        </a>';
                    }
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                        ' . $btn . '
                                    </div>';
                })
                ->filter(function ($query) use ($request) {
                    if (!empty($request['buyer_id'])) {
                        $query->where('buyer_id', $request['buyer_id']);
                    }
                    if (!empty($request['seller_id'])) {
                        $query->where('seller_id', $request['seller_id']);
                    }
                    if (!empty($request['created_by'])) {
                        $query->where('created_by', $request['created_by']);
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
}
