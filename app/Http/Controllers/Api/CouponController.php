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
use App\Models\Coupons;
use App\Models\WarrantyActivation;
use App\Models\TransactionHistory;
use App\Models\EndUser;
use App\Models\Wallet;
use App\Models\InvalidCoupons;
use App\Models\Pincode;
use App\Models\Product;
use App\Models\Services;
use App\Http\Controllers\SendNotifications;
use App\Models\Customers;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->coupons = new Coupons();


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

    public function couponScans(Request $request)
    {
        try {
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'coupons.*' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $data = collect($request['coupons']);
            $data = $data->map(function ($item) use ($user) {
                return collect([
                    'customer_id'  =>  $user['id'],
                    'coupon_code'  =>   $item,
                ]);
            });
            $response = couponScans($data);
            return response()->json($response, $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getScanedCoupons(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $query = Wallet::where('customer_id', $user_id)
                ->whereNotNull('coupon_code')
                ->select('id', 'coupon_code', 'transaction_at', 'points')->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'wallet_id' => isset($value['id']) ? $value['id'] : 0,
                        'coupon_code' => isset($value['coupon_code']) ? $value['coupon_code'] : '',
                        'points' => isset($value['points']) ? $value['points'] : 0,
                        'transaction_at' => isset($value['transaction_at']) ? showdatetimeformat($value['transaction_at']) : '',
                    ]);
                }
            }

            $errorquery = InvalidCoupons::where('customer_id', $user_id)
                ->whereNotNull('coupon_code')
                ->select('id', 'coupon_code', 'created_at', 'status_id')->latest();
            $invalid_data = (!empty($pageSize)) ? $errorquery->paginate($pageSize) : $errorquery->get();
            $invalidData = collect([]);
            if ($invalid_data->isNotEmpty()) {
                foreach ($invalid_data as $key => $rows) {
                    $invalidData->push([
                        'transaction_id' => isset($rows['id']) ? $rows['id'] : 0,
                        'coupon_code' => isset($rows['coupon_code']) ? $rows['coupon_code'] : '',
                        'status' => isset($rows['status']['status_name']) ? $rows['status']['status_name'] : '',
                        'transaction_at' => isset($rows['created_at']) ? showdatetimeformat($rows['created_at']) : '',
                    ]);
                }
            }
            if (!empty($data) || !empty($invalidData)) {
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'invalid' => $invalidData], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getProductByCoupon(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'serial_no' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $serial_no = $request->serial_no;
            $serial_no_product_code = Services::where('serial_no', $serial_no)->value('product_code');
            $all_products = Product::all();
            $slected = false;
            $data['status'] = 'success';
            foreach ($all_products as $k => $product) {
                if ($serial_no_product_code && $product->product_code == $serial_no_product_code && $serial_no_product_code != null && $serial_no_product_code != '') {
                    $data['products'][0]['id'] = $product->id;
                    $data['products'][0]['name'] = $product->product_name;
                    $data['products'][0]['expiry_interval'] = $product->expiry_interval;
                    $data['products'][0]['expiry_interval_preiod'] = $product->expiry_interval_preiod;
                    $data['products'][0]['serial_no'] = $product->product_code;
                    $slected = true;
                }
            }
            if ($slected === false) {
                foreach ($all_products as $k => $product) {
                    $data['products'][$k]['id'] = $product->id;
                    $data['products'][$k]['name'] = $product->product_name;
                    $data['products'][$k]['expiry_interval'] = $product->expiry_interval;
                    $data['products'][$k]['expiry_interval_preiod'] = $product->expiry_interval_preiod;
                }
            }
            $check_warranty = warrantyActivation::with('customer', 'media')->where('product_serail_number', $serial_no)->first();
            if($check_warranty){
                $data['warranty']['status'] = true;
                $data['warranty']['details'] = $check_warranty;
            }else{
                $data['warranty']['status'] = false;
            }
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getEndUserData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_number' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $customer_number = $request->input('customer_number');
            if (isset($customer_number)) {
                $data = EndUser::where(function ($query) use ($customer_number) {
                    $query->where('customer_number', '=', $customer_number);
                })
                    ->first();
                $pincodes = Pincode::where('id', '=', $data->customer_pindcode)->first();
                $data->customer_pindcode = $pincodes ? $pincodes->pincode : '';
                if ($data) {
                    return response()->json(['status' => 'success', 'data' => $data], 200);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Customer not found', 'data' => null], 404);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Please insert number', 'data' => null], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function warrantyActivation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'product_serail_number' => 'required',
                'product_id' => 'required',
                'sale_bill_no' => 'required',
                'sale_bill_date' => 'required',
                'warranty_date' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            if (!$request->end_user_id || $request->end_user_id == NULL || $request->end_user_id == '') {
                $pincodes = Pincode::with('cityname', 'cityname.districtname')->where('pincode', '=', $request['customer_pindcode'])->first();
                $request['customer_state'] = !empty($pincodes['cityname']['districtname']['statename']) ? $pincodes['cityname']['districtname']['statename']['state_name'] : '';
                $request['state_id'] = !empty($pincodes['cityname']['districtname']['state_id']) ? $pincodes['cityname']['districtname']['state_id'] : '';
                $request['customer_district'] = !empty($pincodes['cityname']['districtname']) ? $pincodes['cityname']['districtname']['district_name'] : '';
                $request['customer_city'] = !empty($pincodes['cityname']) ? $pincodes['cityname']['city_name'] : '';
                $request['customer_country'] = !empty($pincodes['cityname']['districtname']['statename']['countryname']) ? $pincodes['cityname']['districtname']['statename']['countryname']['country_name'] : '';
                $end_user = EndUser::updateOrCreate(['customer_number' => $request->customer_number ?? ''], [
                    'customer_name' => $request->customer_name ?? '',
                    'customer_number' => $request->customer_number ?? '',
                    'customer_email' => $request->customer_email ?? '',
                    'customer_address' => $request->customer_address ?? '',
                    'customer_place' => $request->customer_place ?? '',
                    'customer_pindcode' => $pincodes ? $pincodes->id : '',
                    'customer_country' => $request->customer_country ?? '',
                    'customer_state' => $request->customer_state ?? '',
                    'customer_district' => $request->customer_district ?? '',
                    'customer_city' => $request->customer_city ?? ''
                ]);
                $request->end_user_id = $end_user->id;
            }
            $customer = Customers::with('customerdetails')->find($request->customer_id);
            // $checkTrans = TransactionHistory::where('coupon_code', $request->product_serail_number)->first();
            $data = WarrantyActivation::where('product_serail_number', $request->product_serail_number)->first();
            if($data){
                return response()->json(['status' => 'error', 'message' => 'This serial number already inWarranty Activation.', 'data' => $data], 200);
            }else{
                $data = WarrantyActivation::create([
                    'product_serail_number' => $request->product_serail_number ?? NULL,
                    'product_id' => $request->product_id ?? NULL,
                    'end_user_id' => $request->end_user_id ?? NULL,
                    'branch_id' => $request->branch_id ?? NULL,
                    'customer_id' => $request->customer_id ?? NULL,
                    'status' => 0,
                    'remark' => $request->remark ?? NULL,
                    'sale_bill_no' => $request->sale_bill_no ?? NULL,
                    'sale_bill_date' => $request->sale_bill_date ?? NULL,
                    'warranty_date' => $request->warranty_date ?? NULL
                ]);

            }
            if ($request->hasFile('warranty_activation_attach')) {
                $file = $request->file('warranty_activation_attach');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $data->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('warranty_activation_attach');
            }
            $noti_data = [
                'fcm_token' =>  $customer->customerdetails->fcm_token,
                'title' => 'Warranty Is Activated Successfully ✅',
                'msg' => $customer->name . ' your warranty activation is successful in Silver Saarthi.',
            ];
            $send_notification = SendNotifications::send($noti_data);
            return response()->json(['status' => 'success', 'message' => 'Warranty Activation Store Successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getwarranty(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $data = WarrantyActivation::with('media', 'product_details', 'customer')->where('customer_id', $request->customer_id);
            if($request->serial_number && $request->serial_number != NULL && $request->serial_number != ''){
                $data->where('product_serail_number', $request->serial_number);
            }
            if($request->product_id && $request->product_id != NULL && $request->product_id != ''){
                $data->where('product_id', $request->product_id);
            }
            if($request->status != NULL && $request->status != ''){
                $data->where('status', $request->status);
            }
            if($request->warranty_date && $request->warranty_date != NULL && $request->warranty_date != ''){
                $data->where('warranty_date', $request->warranty_date);
            }
            if($request->sale_bill_date && $request->sale_bill_date != NULL && $request->sale_bill_date != ''){
                $data->where('sale_bill_date', $request->sale_bill_date);
            }
            
            $data = $data->get();
            if ($data) {
                return response()->json(['status' => 'success', 'data' => $data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'data not found', 'data' => null], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
