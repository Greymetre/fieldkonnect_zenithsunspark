<?php

namespace App\Http\Controllers;

use App\Exports\SerialNumberHistoryExport;
use App\Exports\SerialNumberTransactionExport;
use App\Imports\SerialNumberTransactionImport;
use App\Models\Services;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use DataTables;
use Excel;
use DB;
use Carbon\Carbon;

class ServicesController extends Controller
{
    public function serial_number_transaction(Request $request)
    {
        $branches = Branch::latest()->get();
        $products = Product::latest()->get();
        abort_if(Gate::denies('serial_number_transaction'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('services.serial_number_transaction', compact('branches', 'products'));
    }

    public function serial_number_transaction_upload(Request $request)
    {
        abort_if(Gate::denies('serial_number_transaction_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $rows = Excel::toCollection([], request()->file('import_file'))->first();
        $PCKey = 0;
        $PCKeyCheck = false;
        $BCKey = 0;
        $BCKeyCheck = false;
        $SNKey = 0;
        $SNKeyCheck = false;
        foreach ($rows as $k => $row) {
            if ($k == 0) {
                foreach ($row as $ks => $heads) {
                    if ($heads == 'Product Code') {
                        $PCKey =  $ks;
                        $PCKeyCheck = true;
                    }
                    if ($heads == 'Branch Code') {
                        $BCKey =  $ks;
                        $BCKeyCheck = true;
                    }
                    if ($heads == 'Serial No.') {
                        $SNKey =  $ks;
                        $SNKeyCheck = true;
                    }
                }
                if($SNKeyCheck == false){
                    return back()->with('error', 'Serial No. column not found in uploaded file.');
                }
                if($PCKeyCheck == false){
                    return back()->with('error', 'Product Code column not found in uploaded file.');
                }
                if($BCKeyCheck == false){
                    return back()->with('error', 'Branch Code column not found in uploaded file.');
                }
            } else {
                $productCode = $row[$PCKey];
                $branchCode = $row[$BCKey];
                // if ($branchCode != 'HO0000') {
                //     $serialNumbers = explode(',', $row[$SNKey]);
                //     foreach ($serialNumbers as $serialNumber) {
                //         $exists = DB::table('services')
                //             ->where('serial_no', $serialNumber)
                //             ->where('product_code', $productCode)
                //             ->exists();
                //         if (!$exists) {
                //             return back()->with('error', 'The serial number ' . $serialNumber . ' with product code ' . $productCode . ' does not exist or not uploaded by Head Office. Please remove them and try again');
                //         }
                //     }
                // }
            }
        }
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new SerialNumberTransactionImport, request()->file('import_file'));
        return back()->with('success', 'Serial Number Transaction Import successfully !!');
    }

    public function serial_number_transaction_download(Request $request)
    {
        abort_if(Gate::denies('serial_number_transaction_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SerialNumberTransactionExport($request), 'SerialNumberTransaction.xlsx');
    }

    public function serial_number_transaction_list(Request $request)
    {
        $data = Services::select(
            'product_code',
            'invoice_no',
            'invoice_date',
            'branch_code',
            'party_name',
            'product_name',
            'qty',
            'narration',
            'group'
        );
        if ($request->branch_id && $request->branch_id != null && $request->branch_id != '') {
            $branche_code = Branch::where('id', $request->branch_id)->value('branch_code');
            $data = $data->where('branch_code', $branche_code);
        }
        if ($request->product_id && $request->product_id != null && $request->product_id != '') {
            $product_code = Product::where('id', $request->product_id)->value('product_code');
            $data = $data->where('product_code', $product_code);
        }
        if ($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != '') {
            $data = $data->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
        }
        $data = $data->groupBy('product_code', 'invoice_no', 'invoice_date', 'branch_code', 'party_name', 'product_name', 'group', 'qty')->with('createdbyname');
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn = '';
                $activebtn = '';
                if (auth()->user()->can(['brand_delete'])) {
                    $btn = $btn . ' <a href="#" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $data->invoice_no . '" title="' . trans('panel.global.delete') . ' Serial Number Transaction">
                              <i class="material-icons">clear</i>
                            </a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                              ' . $btn . '
                          </div>';
            })
            ->addColumn('invoice_date', function ($data) {
                return date("d/m/Y", strtotime($data->invoice_date));
            })
            ->rawColumns(['action','invoice_date'])
            ->make(true);
    }

    public function serial_number_history(Request $request)
    {
        // return true;
        abort_if(Gate::denies('serial_number_history'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('services.serial_number_history');
    }

    public function serial_number_history_list(Request $request)
    {
        $data = Services::with('product', 'warrantyDetails');
        if($request->search['value'] && $request->search['value'] != '' && $request->search['value'] != NULL){
            $data = $data->where('serial_no', $request->search['value']);
        }
        if($request->start_date && $request->start_date != '' && $request->start_date != NULL && $request->end_date && $request->end_date != '' && $request->end_date != NULL){
            $data->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $data = $data->orderBy('invoice_date', 'desc');
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('expiry_date', function ($data) {
                $product = Product::where('product_code', $data->product_code)->first();
                if ($product && $product->expiry_interval && $product->expiry_interval != null && $product->expiry_interval != '' && $product->expiry_interval_preiod && $product->expiry_interval_preiod > 0 && $product->expiry_interval_preiod != null) {
                    $initialDate = Carbon::parse($data->invoice_date);

                    $expiryDate = $initialDate->add($product->expiry_interval_preiod, strtolower($product->expiry_interval));

                    return date("d/m/Y", strtotime($expiryDate)) ?? '';

                    // return $expiryDate->toDateString();
                } else {
                    return 'No Expiry Date';
                }
            })
            ->addColumn('invoice_date', function ($data) {
               
                    return $data->invoice_date?date("d/m/Y", strtotime($data->invoice_date)) : '';
            })
            ->addColumn('warranty_status', function ($data) {
               
                    if($data->warrantyDetails){
                        return '<a href="/warranty_activation/'.encrypt($data->warrantyDetails->id).'">Active</a>';
                    }else{
                        return 'Not Active';
                    }
            })
            ->addColumn('action', function ($data) {
                $btn = '';
                 if(auth()->user()->can(['leave_delete'])){
                 $btn = $btn. '<a href="'.url("services/serial_number_history/edit", $data->id).'" class="btn btn-success btn-just-icon btn-sm" title="Edit Serial Number history">
                                    <i class="material-icons">edit</i>
                                  </a>';
                    }  

                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                            ' . $btn . '
                        </div>';
            })

            ->rawColumns(['expiry_date','invoice_date','action', 'warranty_status'])
            ->make(true);
    }

    public function serial_number_transaction_delete(Request $request)
    {
        Services::where('invoice_no', $request->id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Serial Number Transaction Delete successfully']);
    }

    public function serial_number_history_edit($id){
        $serialNumberHistory = Services::with('product')->find($id);
        $products = Product::all();
        return view('services.serial_number_history_edit', compact('serialNumberHistory', 'products'))->with('serialNumberHistory', $serialNumberHistory);
    }

    public function serial_number_history_update(Request $request)
    {
        $product = Product::where('product_code', $request->product_code)->first();
        $service = Services::find($request->service_id);
        
        $service->serial_no = $request->serial_no;
        $service->product_code = $request->product_code;
        $service->product_name = $product->product_name;
        $service->product_description = $product->description;
        $service->new_group = $product->new_group;
        $service->group = $product->sub_group;  
        $service->party_name = $request->party_name;
        $service->branch_code = $request->branch_code;
        $service->invoice_date = $request->invoice_date;
        $service->invoice_no = $request->invoice_no;
        $service->narration = $request->narration;
        $service->save();

        return redirect()->route('service.serial_number_history')->with('message_success', 'Serial number updated successfully.');
    }
    public function serial_number_history_download(Request $request)
    {
        abort_if(Gate::denies('serial_number_history_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SerialNumberHistoryExport($request), 'SerialNumberHistory.xlsx');
    }

}
