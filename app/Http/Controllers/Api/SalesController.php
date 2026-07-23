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
use App\Models\Sales;
use App\Models\Attachment;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->sales = new Sales();


        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
        $this->path = 'sales';
    }

    public function getSales(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $buyer_id = $request->input('buyer_id');
            $status_id = $request->input('status_id');
            $customers = $request->input('customers');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $user_ids = getUsersReportingToAuth($user_id);
            $query = $this->sales
                ->select('id', 'buyer_id', 'seller_id', 'order_id', 'grand_total', 'invoice_date', 'invoice_no', 'status_id', 'created_by', 'orderno','sub_total','total_gst')
                ->with([
                    'createdbyname:id,name',
                    'status:id,status_name'
                ])
                ->withSum('saledetails', 'quantity')
                ->where(function ($query) use ($user_ids, $buyer_id, $status_id, $customers, $start_date, $end_date) {
                    if (!empty($buyer_id)) {
                        $query->where('buyer_id', '=', $buyer_id);
                    }
                    if (!empty($status_id)) {
                        $query->where('status_id', '=', $status_id);
                    }
                    if (!empty($customers)) {
                        $query->whereIn('buyer_id', $customers);
                    }
                    if (empty($buyer_id) && empty($seller_id) && empty($customers)) {
                        $query->whereIn('created_by', $user_ids);
                    }
                    if ($start_date) {
                        $query->whereDate('created_at', '>=', $start_date);
                    }
                    if ($end_date) {
                        $query->whereDate('created_at', '<=', $end_date);
                    }
                })
                ->latest();

            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();

            $data = collect([]);
            $all_status = [['id' => '1', 'name' => 'Dispatched'], ['id' => '2', 'name' => 'Partially Dispatched'],];
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    
                    $data->push([
                        'sales_id' => isset($value['id']) ? $value['id'] : 0,
                        'buyer_id' => isset($value['buyer_id']) ? $value['buyer_id'] : 0,
                        'buyer_name' => isset($value['buyers']['name']) ? $value['buyers']['name'] : '',
                        'seller_id' => isset($value['seller_id']) ? $value['seller_id'] : 0,
                        'seller_name' => isset($value['sellers']['name']) ? $value['sellers']['name'] : '',
                        'order_id' => isset($value['order_id']) ? $value['order_id'] : 0,
                        'orderno' => isset($value['orderno']) ? $value['orderno'] : 0,
                        'sub_total' => isset($value['sub_total']) ? $value['sub_total'] : 0.00,
                        'total_gst' => isset($value['total_gst']) ? $value['total_gst'] : 0.00,
                        'grand_total' => isset($value['grand_total']) ? $value['grand_total'] : 0.00,
                        'invoice_date' => isset($value['invoice_date']) ? $value['invoice_date'] : '',
                        'invoice_no' => isset($value['invoice_no']) ? $value['invoice_no'] : '',
                        'quantity' => isset($value['saledetails_sum_quantity']) ? $value['saledetails_sum_quantity'] : 0,
                        'status' => isset($value['status']) ? $value['status']['status_name'] : 'Dispatched',
                        'point_type' => isset($value['point_type']) ? $value['point_type'] : '',
                        'createdbyname' => isset($value['createdbyname']) ? $value['createdbyname']['name'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'all_status' => $all_status], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getSalesDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sales_id' => 'required|exists:sales,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $user = $request->user();
            $user_id = $user->id;
            $sales_id = $request->input('sales_id');
            $db_data = $this->sales->with('saledetails.products.brands')->where('id', $sales_id)->select('id', 'buyer_id', 'seller_id', 'order_id', 'total_gst', 'sub_total', 'grand_total', 'invoice_date', 'invoice_no', 'fiscal_year', 'sales_no', 'status_id')->first();
            if (!empty($db_data)) {
                $data = collect([
                    'id' => isset($db_data['id']) ? $db_data['id'] : 0,
                    'buyer_id' => isset($db_data['buyer_id']) ? $db_data['buyer_id'] : 0,
                    'buyer_name' => isset($db_data['buyers']['name']) ? $db_data['buyers']['name'] : '',
                    'seller_id' => isset($db_data['seller_id']) ? $db_data['seller_id'] : 0,
                    'seller_name' => isset($db_data['sellers']['name']) ? $db_data['sellers']['name'] : '',
                    'order_id' => isset($db_data['order_id']) ? $db_data['order_id'] : '',
                    'total_gst' => isset($db_data['total_gst']) ? $db_data['total_gst'] : 0.00,
                    'sub_total' => isset($db_data['sub_total']) ? $db_data['sub_total'] : 0.00,
                    'grand_total' => isset($db_data['grand_total']) ? $db_data['grand_total'] : 0.00,
                    'invoice_date' => isset($db_data['invoice_date']) ? $db_data['invoice_date'] : '',
                    'invoice_no' => isset($db_data['invoice_no']) ? $db_data['invoice_no'] : '',
                    'transport_name' => isset($db_data['transport_name']) ? $db_data['transport_name'] : '',
                    'lr_no' => isset($db_data['lr_no']) ? $db_data['lr_no'] : '',
                    'dispatch_date' => isset($db_data['dispatch_date']) ? $db_data['dispatch_date'] : '',
                    'transport_details' => isset($db_data['transport_details']) ? $db_data['transport_details'] : '',
                    'fiscal_year' => isset($db_data['fiscal_year']) ? $db_data['fiscal_year'] : '',
                    'sales_no' => isset($db_data['sales_no']) ? $db_data['sales_no'] : '',
                    'status_id' => isset($db_data['status_id']) ? $db_data['status_id'] : 0,
                    'saledetails' => $db_data['saledetails']
                ]);
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }

            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data = collect()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }


    public function insertSales(Request $request)
    {
        try {

            $user = $request->user();
            $request['created_by'] = $user->id;
            $validator = Validator::make($request->all(), $this->sales->insertrules(), $this->sales->message());
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->messages()->all()], $this->badrequest);
            }
            $data = collect([$request]);
            $response = insertSales($data);
            if ($response['status'] == 'success' && $response['sales_id'] !== null) {
                $invicesimages = collect([]);
                if ($request->file('image_1')) {
                    $image = $request->file('image_1');
                    $filename = 'image_1_' . $response['sales_id'];
                    $image_1 = fileupload($image, $this->path, $filename);
                    $invicesimages->push([
                        'sales_id' => $response['sales_id'],
                        'file_path' => $image_1
                    ]);
                }
                if ($request->file('image_2')) {
                    $image = $request->file('image_2');
                    $filename = 'image_2_' . $response['sales_id'];
                    $image_1 = fileupload($image, $this->path, $filename);
                    $invicesimages->push([
                        'sales_id' => $response['sales_id'],
                        'file_path' => $image_1
                    ]);
                }
                if ($request->file('image_3')) {
                    $image = $request->file('image_3');
                    $filename = 'image_3_' . $response['sales_id'];
                    $image_1 = fileupload($image, $this->path, $filename);
                    $invicesimages->push([
                        'sales_id' => $response['sales_id'],
                        'file_path' => $image_1
                    ]);
                }
                if ($request->file('image_4')) {
                    $image = $request->file('image_4');
                    $filename = 'image_4_' . $response['sales_id'];
                    $image_1 = fileupload($image, $this->path, $filename);
                    $invicesimages->push([
                        'sales_id' => $response['sales_id'],
                        'file_path' => $image_1
                    ]);
                }
                if ($request->file('image_5')) {
                    $image = $request->file('image_5');
                    $filename = 'image_5_' . $response['sales_id'];
                    $image_1 = fileupload($image, $this->path, $filename);
                    $invicesimages->push([
                        'sales_id' => $response['sales_id'],
                        'file_path' => $image_1
                    ]);
                }
                if ($invicesimages->isNotEmpty()) {
                    Attachment::insert($invicesimages->toArray());
                }
                return response()->json($response, $this->successStatus);
            }
            return response()->json($response, $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function couponScans(Request $request)
    {
        try {
            $user = $request->user();
            $request['created_by'] = $user->id;
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'coupon_code' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->noContent);
            }
            $data = collect([$request]);
            $response = couponScans($data);
            if ($response['status'] == 'success') {
                return response()->json($response, $this->successStatus);
            }
            return response()->json($response, $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
