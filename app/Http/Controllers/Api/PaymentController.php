<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Customers;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->path = 'payment'; 
    }

    public function getUnpaidInvoice(Request $request)
    {
        try
        {
            $data = Sales::where('buyer_id', $request->customer_id)
                            ->whereIn('status_id', [1, 2, 4,5])
                            ->select('id','invoice_date','invoice_no','grand_total','order_id','status_id','paid_amount')
                            ->get();
            $sales = collect([]);
            if(!empty($data))
            {
                foreach ($data as $key => $rows) {
                    $sales->push([
                        'id' => isset($rows['id']) ? $rows['id'] :'',
                        'invoice_date' => isset($rows['invoice_date']) ? $rows['invoice_date'] :'',
                        'invoice_no' => isset($rows['invoice_no']) ? $rows['invoice_no'] :'',
                        'grand_total' => isset($rows['grand_total']) ? $rows['grand_total'] :'',
                        'amount_unpaid' => isset($rows['paid_amount']) ? (double) $rows['grand_total']-$rows['paid_amount'] : (double) $rows['grand_total'],
                        'order_id' => isset($rows['order_id']) ? $rows['order_id'] :'',
                        'status_id' => isset($rows['status_id']) ? $rows['status_id'] :'',
                    ]);
                }
            }
            return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $sales ], 200);
        }
        catch(\Exception $e){
            return response()->json(['status' => 'error','message' => $e->getMessage() ], 500);
        }       
    }
    public function paymentReceived(Request $request)
    {
        try
        { 
            //return response()->json(['status' => 'success','message' => 'Payment Store Successfully','data' => $request->all() ], 200);
            $request['user_id'] = isset($request['user_id']) ? $request['user_id'] : $request->user()->id;
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'payment';
                $request['file_path'] = fileupload($image, $this->path, $filename);
            }
            $request['customer_name'] = isset($request['customer_name']) ? $request['customer_name']: Customers::where('id',$request['customer_id'])->pluck('name')->first();
            if($payment = Payment::create([
                'active' => 'Y',
                'user_id' => isset($request['user_id']) ? $request['user_id'] :null,
                'customer_id' => isset($request['customer_id']) ? $request['customer_id'] :null,
                'customer_name' => isset($request['customer_name']) ? $request['customer_name']:'',
                'payment_date' => isset($request['payment_date']) ? $request['payment_date'] :date('Y-m-d'),
                'payment_mode' => isset($request['payment_mode']) ? $request['payment_mode']:'',
                'payment_type' => isset($request['payment_type']) ? $request['payment_type']:'',
                'amount' => isset($request['amount']) ? $request['amount']:0.00,
                'description' => isset($request['description']) ? $request['description']:'',
                'bank_name' => isset($request['bank_name']) ? $request['bank_name']:'',
                'reference_no' => isset($request['reference_no']) ? $request['reference_no']:'',
                'file_path' => isset($request['file_path']) ? $request['file_path']:'',
            ]))
            {
                $detail = collect([]);
                $paymentdetail = json_decode($request['detail'], true);
                foreach ($paymentdetail as $key => $rows) {
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
                return response()->json(['status' => 'success','message' => 'Payment Store Successfully','data' => $payment ], 200);
            }
            return response()->json(['status' => 'error','message' => 'Error in Payment Store','data' => $sales ], 200); 
        }         
        catch(\Exception $e)
        {
          return response()->json(['status' => 'error','message' => $e->getMessage() ], 500);
        }
    }

    public function getPaymentList(Request $request)
    {
        try
        {
            $data = Payment::where('customer_id',$request->customer_id)->select('id','customer_id','payment_date','payment_type','reference_no','amount','payment_mode','bank_name')->paginate(15);
            return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data->items() , 'last_page' => $data->lastPage() , 'total' => $data->total(), 'per_page' => $data->perPage() ], 200);
        }
        catch(\Exception $e){
            return response()->json(['status' => 'error','message' => $e->getMessage() ], 500);
        } 
    }

    public function getPaymentInfo(Request $request)
    {
        try
        {
            $data = Payment::with('paymentdetails:id,payment_id,sales_id,invoice_no,amount')->where('id',$request->payment_id)->select('id','user_id', 'customer_id', 'customer_name', 'payment_date', 'payment_mode', 'payment_type', 'bank_name', 'reference_no', 'amount', 'response', 'description', 'file_path')->first();
            return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data ], 200);
        }
        catch(\Exception $e){
            return response()->json(['status' => 'error','message' => $e->getMessage() ], 500);
        }

    }

}
