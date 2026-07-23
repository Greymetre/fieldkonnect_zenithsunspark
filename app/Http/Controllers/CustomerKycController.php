<?php

namespace App\Http\Controllers;

use App\DataTables\CustomerKycDataTable;
use App\Exports\CustomerKycExport;
use App\Models\Branch;
use App\Models\CustomerKyc;
use App\Models\CustomerType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use Excel;
use DB;

class CustomerKycController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CustomerKycDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('customer_kyc_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->select('id', 'branch_name')->get();
        $customer_types = CustomerType::where('active', 'Y')->select('id', 'customertype_name')->get();
        return $dataTable->render('customer_kyc.index', compact('branches', 'customer_types'));
    }

    public function download(Request $request)
    {
        abort_if(Gate::denies('customer_kyc_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CustomerKycExport($request), 'CustomerKYC.xlsx');
    }

    
}
