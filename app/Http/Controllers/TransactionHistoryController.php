<?php

namespace App\Http\Controllers;

use Gate;
use Excel;
use App\Exports\TransactionHistoryExport;
use Validator;
use App\Models\Branch;
use App\Models\Services;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\DataTables\TransactionHistoryDataTable;
use App\Exports\TransactionMainTemplate;
use App\Exports\TransactionTemplate;
use App\Imports\MainTransactionImport;
use App\Imports\ManualTransactionImport;
use App\Models\Designation;
use App\Models\SchemeDetails;
use App\Models\SchemeHeader;
use Carbon\Carbon;

class TransactionHistoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->transaction_history = new TransactionHistory();
        $this->path = 'transaction_history';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransactionHistoryDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('transaction_history_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        // $parent_customers = Customers::where('active', 'Y')->whereIn('customertype', ['1', '3'])->select('id', 'name')->get();
        $customer_ids = $this->transaction_history->distinct()->pluck('customer_id');
        $parent_customers = [];
        $designations = Designation::where('active', 'Y')->get();
        $customers = Customers::whereIn('id' , $customer_ids)->get();
        $scheme_names = SchemeHeader::where('active', 'Y')->select('id', 'scheme_name')->get();
        return $dataTable->render('transaction_history.index', compact('branches', 'parent_customers', 'scheme_names' , 'customers','designations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('transaction_history_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('transaction_history.create')->with('transaction_history', $this->transaction_history);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function manualcreate()
    {
        abort_if(Gate::denies('transaction_history_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('transaction_history.manualcreate')->with('transaction_history', $this->transaction_history);;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // try {
            abort_if(Gate::denies('transaction_history_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'coupon_code.*' => 'required',
            ]);
            $validator->setAttributeNames([
                'coupon_code.*' => 'coupon code',
            ]);

            $validator->setCustomMessages([
                'coupon_code.*.required' => 'All coupon code fields are required.',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $nonNullCoupenCodes = array_filter($request->coupon_code, function ($value) {
                return !is_null($value);
            });
            $expire_schemes = array();
            $notInsert = array();
            foreach ($nonNullCoupenCodes as $nonNullCoupenCode) {
                $exists = TransactionHistory::where('coupon_code', $nonNullCoupenCode)->exists();
                $notexists = Services::where('serial_no', $nonNullCoupenCode)->exists();
                // if ($exists) {
                //     throw ValidationException::withMessages([
                //         'coupon_code' => "The coupon code '$nonNullCoupenCode' already Scanned.",
                //     ]);
                // }
                // if (!$notexists) {
                //     throw ValidationException::withMessages([
                //         'coupon_code' => "The coupon code '$nonNullCoupenCode' is Invalid.",
                //     ]);
                // }
                if (!$exists && $notexists) {
                    $scheme = Services::where('serial_no', $nonNullCoupenCode)->first();
                    $scheme_details = SchemeDetails::where('product_id', $scheme->product?->id)->first();
                    $point = 0;
                    $active_point = '0';
                    $provision_point = '0';
                    if ($scheme_details) {
                        $scheme_id = $scheme_details->scheme_id;
                        $start_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->start_date);
                        $end_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->end_date);
                        $current_date = Carbon::today();
                        if ($current_date->isSameDay($start_date) || ($current_date->gte($start_date) && $current_date->lte($end_date))) {
                            $active_point = ($scheme_details) ? $scheme_details->active_point : NULL;
                            $provision_point = ($scheme_details) ? $scheme_details->provision_point : NULL;
                            $point = ($scheme_details) ? $scheme_details->points : NULL;
                        } else {
                            array_push($expire_schemes, $nonNullCoupenCode);
                            $active_point = '0';
                            $provision_point = '0';
                            $point = '0';
                        }
                    } else {
                        array_push($expire_schemes, $nonNullCoupenCode);
                        $scheme_id = null;
                    }
                    $tHistory = TransactionHistory::create([
                        'customer_id' => $request->customer_id,
                        'coupon_code' => $nonNullCoupenCode,
                        'scheme_id' => $scheme_id,
                        'active_point' => $active_point,
                        'provision_point' => $provision_point,
                        'point' => $point,
                        'remark' => 'Coupon scan',
                        'created_by' => auth()->user()->id,
                    ]);
                } else {
                    if ($exists) {
                        $push_is = $nonNullCoupenCode . ' - already Scanned ';
                    } elseif (!$notexists) {
                        $push_is = $nonNullCoupenCode . ' - Invalid ';
                    }
                    array_push($notInsert, $push_is);
                }
            }
            if (count($expire_schemes) > 0) {
                if (count($notInsert) > 0) {
                    return Redirect::to('transaction_history')->with('message_info', 'Transaction History Store Successfully but coupon code (' . implode(',', $expire_schemes) . ') scheme has either expired or has not started yet so you earned 0 point And also check (' . implode(',', $notInsert) . ').');
                }
                return Redirect::to('transaction_history')->with('message_info', 'Transaction History Store Successfully but coupon code (' . implode(',', $expire_schemes) . ') scheme has either expired or has not started yet so you earned 0 point.');
            } else {
                if (count($notInsert) > 0) {
                    return Redirect::to('transaction_history')->with('message_success', 'Transaction History Store Successfully And also check (' . implode(',', $notInsert) . ').');
                }
                return Redirect::to('transaction_history')->with('message_success', 'Transaction History Store Successfully');
            }
        // } catch (\Exception $e) {
        //     return redirect()->back()->withErrors($e->getMessage())->withInput();
        // }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function manualstore(Request $request)
    {
        try {
            abort_if(Gate::denies('transaction_history_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'point_type' => 'required',
                'points' => 'required',
                'remark' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $tHistory = TransactionHistory::create([
                'customer_id' => $request->customer_id,
                'status' => $request->point_type,
                'point' => $request->points,
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
            ]);

            return Redirect::to('transaction_history')->with('message_success', 'Transaction History Store Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransactionHistory  $transactionHistory
     * @return \Illuminate\Http\Response
     */
    public function show(TransactionHistory $transactionHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransactionHistory  $transactionHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(TransactionHistory $transactionHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransactionHistory  $transactionHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransactionHistory $transactionHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransactionHistory  $transactionHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransactionHistory $transactionHistory)
    {
        if ($transactionHistory->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Transaction History deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Transaction History Delete!']);
    }

    public function download(Request $request)
    {
        abort_if(Gate::denies('transaction_history_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TransactionHistoryExport($request), 'TransactionHistory.xlsx');
    }


    public function template(Request $request)
    {
        abort_if(Gate::denies('transaction_history_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TransactionTemplate($request), 'ManualTransactionTamplate.xlsx');
    }

    public function template_main(Request $request)
    {
        abort_if(Gate::denies('transaction_history_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TransactionMainTemplate($request), 'TransactionTamplate.xlsx');
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('transaction_history_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new ManualTransactionImport, request()->file('import_file'));
        return back()->with('message_success', 'Manual Points import successfully !');
    }

    public function upload_main(Request $request)
    {
        abort_if(Gate::denies('transaction_history_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        $result = Excel::import(new MainTransactionImport, request()->file('import_file_main'));

        return back()->with('message_success', $_SESSION['tr_msg']);
    }

    // recalculate point 

    public function point_recalculate(Request $request) {
        $ids = $request->ids ?? [];
        $transactionHistories = TransactionHistory::whereIn('id', $ids)->get();
        $update = false;
        $coupoun = [];
        $message = "";
        // $update = fa
        foreach ($transactionHistories as $transactionHistory) {
            if ($transactionHistory->scheme && $transactionHistory->scheme->product) {
                $schemeDetails = SchemeDetails::where('product_id', $transactionHistory->scheme->product->id)->first();
                if ($schemeDetails) {
                    $transactionHistory->update([
                        'active_point' => $schemeDetails->active_point,
                        'provision_point' => $schemeDetails->provision_point,
                        'point' => $schemeDetails->points,
                    ]);
                    $update = true;
                }else{
                    array_push($coupoun, $transactionHistory->coupon_code);
                }
            }
        }

        if(count($coupoun)> 0){
            $message =  implode(',', $coupoun) . ' This coupon code not present in our scheme details';
        }
    
        return response()->json(['status' => true  , 'update' => $update , 'message' => $message]);
    }
}
