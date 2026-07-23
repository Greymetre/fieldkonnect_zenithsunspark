<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\PaymentDetail;
use App\Models\Customers;
use App\Models\Sales;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\PaymentDataTable;
use App\Imports\PaymentImport;
use App\Exports\PaymentExport;
use App\Exports\PaymentTemplate;
use App\Http\Requests\PaymentRequest;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() 
    {       
        $this->payment = new Payment();
        
    }

    public function index(PaymentDataTable $dataTable)
    {
        //abort_if(Gate::denies('payments_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('payments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //abort_if(Gate::denies('payments_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $customers = Customers::where('active','=','Y')
                            ->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('executive_id',$userids);
                                }
                            })
                            ->select('id', 'name','mobile')
                            ->get();

        return view('payments.create',compact('customers'))->with('payment',$this->payment);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        { 
            //abort_if(Gate::denies('payments_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['user_id'] = isset($request['user_id']) ? $request['user_id'] : Auth::user()->id;
            if($payment = Payment::create([
                'active' => 'Y',
                'user_id' => isset($request['user_id']) ? $request['user_id'] :null,
                'customer_id' => isset($request['customer_id']) ? $request['customer_id'] :null,
                'customer_name' => isset($request['customer_name']) ? $request['customer_name']:'',
                'payment_date' => isset($request['payment_date']) ? $request['payment_date'] :date('Y-m-d'),
                'payment_mode' => isset($request['payment_mode']) ? $request['payment_mode']:'',
                'payment_type' => isset($request['payment_type']) ? $request['payment_type']:'',
                'bank_name' => isset($request['bank_name']) ? $request['bank_name']:'',
                'reference_no' => isset($request['reference_no']) ? $request['reference_no']:'',
                'amount' => isset($request['amount']) ? $request['amount']:0.00,
                'response' => isset($request['response']) ? $request['response']:'',
                'description' => isset($request['description']) ? $request['description']:'',
                'status_id' => isset($request['status_id']) ? $request['status_id'] :null,
            ]))
            {
                $detail = collect([]);
                foreach ($request['detail'] as $key => $rows) {
                    if(!empty($rows['amount']))
                    {
                        $sales = Sales::find($rows['sales_id']);
                        $sales->status_id = ($sales['grand_total'] == $sales['paid_amount'] + $rows['amount']) ? 6 : 5;
                        $sales->increment('paid_amount',$rows['amount']);
                        $sales->save();
                        $detail->push([
                            'active' => 'Y',
                            'payment_id' => isset($payment['id']) ? $payment['id'] :null,
                            'sales_id' => isset($rows['sales_id']) ? $rows['sales_id'] :null,
                            'invoice_no' => isset($rows['invoice_no']) ? $rows['invoice_no'] :null,
                            'amount' => isset($rows['amount']) ? $rows['amount'] :0.00,
                            'created_at' => getcurentDateTime(),
                        ]);
                    }
                }
                if($detail->isNotEmpty())
                {
                    PaymentDetail::insert($detail->toArray());
                }
                return Redirect::to('payments')->with('message_success', 'Payment Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Payment Store')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //abort_if(Gate::denies('payments_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $payment = Payment::with('paymentdetails')->where('id',$id)->first();
        return view('payments.show')->with('payment',$payment);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //abort_if(Gate::denies('payments_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $customers = Customers::where('active','=','Y')
                            ->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('executive_id',$userids);
                                }
                            })
                            ->select('id', 'name','mobile')
                            ->get();
        $id = decrypt($id);
        $payment = Payment::with('paymentdetails')->where('id',$id)->first();
        return view('payments.create',compact('customers'))->with('payment',$payment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       //abort_if(Gate::denies('payments_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try
        { 
            //abort_if(Gate::denies('payments_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['user_id'] = isset($request['user_id']) ? $request['user_id'] : Auth::user()->id;
            if($payment = Payment::where('id',$id)->update([
                'customer_id' => isset($request['customer_id']) ? $request['customer_id'] :null,
                'customer_name' => isset($request['customer_name']) ? $request['customer_name']:'',
                'payment_date' => isset($request['payment_date']) ? $request['payment_date'] :date('Y-m-d'),
                'payment_mode' => isset($request['payment_mode']) ? $request['payment_mode']:'',
                'payment_type' => isset($request['payment_type']) ? $request['payment_type']:'',
                'bank_name' => isset($request['bank_name']) ? $request['bank_name']:'',
                'reference_no' => isset($request['reference_no']) ? $request['reference_no']:'',
                'amount' => isset($request['amount']) ? $request['amount']:0.00,
                'response' => isset($request['response']) ? $request['response']:'',
                'description' => isset($request['description']) ? $request['description']:'',
                'status_id' => isset($request['status_id']) ? $request['status_id'] :null,
            ]))
            {
                if(!empty($request['detail']))
                {
                    foreach ($request['detail'] as $key => $rows) {
                        if(!empty($rows['amount']))
                        {
                            $paymentdetail = PaymentDetail::find($rows['detail_id']);
                            if($paymentdetail['amount'] != $rows['amount'])
                            {
                                $sales = Sales::find($rows['sales_id']);
                                $diffamount = $rows['amount']- $paymentdetail['amount']  ;
                                $sales->increment('paid_amount',$diffamount);
                                $paidamount = $sales['paid_amount'] + $diffamount;

                                // if($paymentdetail['amount'] > $rows['amount'] )
                                // {
                                //     $diffamount = $paymentdetail['amount']- $rows['amount'];
                                //     $sales->decrement('paid_amount',$diffamount);
                                //     $paidamount = $sales['paid_amount'] - $diffamount;
                                    
                                // }
                                // else
                                // {
                                //     $diffamount = $rows['amount']- $paymentdetail['amount']  ;
                                //     $sales->increment('paid_amount',$diffamount);
                                //     $paidamount = $sales['paid_amount'] + $diffamount; 
                                // }
                                $sales->status_id = ($sales['grand_total'] == $paidamount + $rows['amount']) ? 6 : 5;
                                $sales->save();
                                PaymentDetail::where('id', $rows['detail_id'])->update([
                                    'payment_id' => isset($id) ? $id :null,
                                    'sales_id' => isset($rows['sales_id']) ? $rows['sales_id'] :null,
                                    'invoice_no' => isset($rows['invoice_no']) ? $rows['invoice_no'] :null,
                                    'amount' => isset($rows['amount']) ? $rows['amount'] :0.00,
                                ]);
                            }
                        }
                    }
                }
                
                return Redirect::to('payments')->with('message_success', 'Payment Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Payment Store')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //abort_if(Gate::denies('payments_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $paymentdetails = PaymentDetail::where('payment_id',$id)->get();
        if(!empty($paymentdetails))
        {
            foreach ($paymentdetails as $key => $rows) {
                $sales = Sales::find($rows['sales_id']);
                $sales->decrement('paid_amount',$rows['amount']);
                if(PaymentDetail::where('sales_id',$rows['sales_id'])->count() < 2)
                {
                    $sales->status_id = 5;
                }
                else
                {
                    $sales->status_id = 4;
                }
                $sales->save();
            }
        }
        PaymentDetail::where('payment_id',$id)->delete();
        $payment = Payment::find($id);
        if($payment->delete())
        {
            return response()->json(['status' => 'success','message' => 'Payment deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Payment Delete!']);
    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('payments_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new PaymentImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('payments_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PaymentExport, 'payments.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('payments_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PaymentTemplate, 'payments.xlsx');
    }

    public function paymentsInfo(Request $request)
    {
        if ($request->ajax()) {
            $data = Payment::latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function($data)
                    {
                        return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                    })
                    ->editColumn('payment_date', function($data)
                    {
                        return isset($data->payment_date) ? '<a href="'.url("payments/".encrypt($data->id)).'">'.showdateformat($data->payment_date).'</a>' : '';
                    })
                    ->addColumn('action', function ($query) {
                          return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group"><a href="'.url("payments/".encrypt($query->id)).'" class="btn btn-warning btn-just-icon btn-sm" title="Payment Info">
                                    <i class="material-icons">visibility</i>
                                </a>
                            </div>';
                    })
                    ->filter(function ($query) use ($request) {
                        if(!empty($request['customer_id']))
                        {
                            $query->where('customer_id', $request['customer_id']);
                        }
                    })
                    ->rawColumns(['action','payment_date'])
                    ->make(true);
        }
    }
}
