<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Validator;
use Gate;
use App\Models\Wallet;

class WalletController extends Controller
{
    public function __construct()
    {
        
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }

    public function pointsCollection(Request $request)
    {
        try
        { 
            $userid = $request->user()->id;
            $validator = Validator::make($request->all(), [
                'collection'  => "required",
                'customer_id' => 'required|exists:customers,id',
                'checkinid' => 'required|exists:check_in,id',
                'collection.*.points' => 'required',
                'collection.*.point_type' => 'required',
                'collection.*.quantity' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }
            if(is_array($request['collection']))
            {
                $collection = array();
                foreach ($request['collection'] as $key => $row) {
                    array_push($collection,array(
                        "userid" => $userid, 
                        'points' => $row['points'], 
                        'customer_id' => $request['customer_id'], 
                        'point_type' => $row['point_type'],
                        'quantity' => $row['quantity'],
                        'checkinid' => $request['checkinid'],
                        'transaction_type' => 'Cr',
                        'created_at' => date('Y-m-d H:i:s') ));
                }
            }
            if(Wallet::insert($collection))
            {
                return response()->json(['status' => 'success','message' => 'Data inserted successfully.' ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'Error in No Record Found.'],200); 
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }  
    }

    public function getCollectedPoints(Request $request)
    {
        try
        { 
            $userid = $request->user()->id;
            $customer_id = $request->input('customer_id');
            $data = Wallet::with('customers')->where(function ($query) use($userid, $customer_id) {
                            if(!empty($customer_id))
                            {
                                $query->where('customer_id', '=', $customer_id);
                            }
                            $query->where('userid', '=', $userid);
                        })
                        ->select('id','customer_id', 'points', 'point_type', 'transaction_at', 'checkinid', 'quantity')->latest()->get();
            if($data->isNotEmpty())
            {
                return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data ], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data ],200);
        }
        catch(\Exception $e)
        {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }  
    }
}

