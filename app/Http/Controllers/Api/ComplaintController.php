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
use App\Models\Complaint;
use App\Models\ComplaintType;
use App\Models\Customers;
use Carbon\Carbon;

class ComplaintController extends Controller
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

    public function getComplaintType(Request $request)
    {
        try {
            
            $data = ComplaintType::all();
            if ($data) {
                return response()->json(['status' => 'success', 'data' => $data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'data not found', 'data' => null], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addComplaint(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'product_serail_number' => 'required',
                'product_id' => 'required',
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
            $newComplaintNumber = $this->getComplaintNumber();
            $data = Complaint::create([
                'complaint_number' => $newComplaintNumber,
                'complaint_date' => $request->complaint_date ?? Carbon::now()->toDateString(),
                'claim_amount' => $request->claim_amount ?? NULL,
                'seller' => $request->seller ?? NULL,
                'end_user_id' => $request->end_user_id ?? NULL,
                'party_name' => $request->party_name ?? NULL,
                'product_laying' => $request->product_laying ?? NULL,
                'service_center' => $request->service_center ?? NULL,
                'assign_user' => $request->assign_user ?? NULL,
                'product_id' => $request->product_id ?? NULL,
                'product_serail_number' => $request->product_serail_number ?? NULL,
                'product_code' => $request->product_code ?? NULL,
                'product_name' => $request->product_name ?? NULL,
                'category' => $request->category ?? NULL,
                'specification' => $request->specification ?? NULL,
                'product_no' => $request->product_no ?? NULL,
                'phase' => $request->phase ?? NULL,
                'seller_branch' => $request->seller_branch ?? NULL,
                'purchased_branch' => $request->purchased_branch ?? NULL,
                'product_group' => $request->product_group ?? NULL,
                'company_sale_bill_no' => $request->company_sale_bill_no ?? NULL,
                'company_sale_bill_date' => $request->company_sale_bill_date ?? NULL,
                'customer_bill_date' => $request->customer_bill_date ?? NULL,
                'customer_bill_no' => $request->customer_bill_no ?? NULL,
                'company_bill_date_month' => $request->company_bill_date_month ?? NULL,
                'under_warranty' => $request->under_warranty ?? NULL,
                'service_type' => $request->service_type ?? NULL,
                'customer_bill_date_month' => $request->customer_bill_date_month ?? NULL,
                'warranty_bill' => $request->warranty_bill ?? NULL,
                'fault_type' => $request->fault_type ?? NULL,
                'service_centre_remark' => $request->service_centre_remark ?? NULL,
                'complaint_status' => $request->complaint_status ?? 0,
                'description' => $request->remark ?? NULL,
                'division' => $request->division ?? NULL,
                'register_by' => $request->register_by ?? NULL,
                'complaint_type' => $request->complaint_type ?? NULL,
                // 'description' => $request->description ?? NULL,
                'created_by_device' => 'customer',
                'created_by' => $request->customer_id ?? NULL
            ]);
            if($request->images && count($request->images) > 0){
                foreach($request->images as $file){
                    $customname = time() . '.' . $file->getClientOriginalExtension();
                    $data->addMedia($file)
                        ->usingFileName($customname)
                        ->toMediaCollection('complaint_attach');
                }
            }
            $noti_data = [
                'fcm_token' =>  $customer->customerdetails->fcm_token,
                'title' => 'Complaint open Successfully ✅',
                'msg' => $customer->name . ' your Complaint '.$newComplaintNumber.' is successful in Silver Saarthi.',
            ];
            $send_notification = SendNotifications::send($noti_data);
            return response()->json(['status' => 'success', 'complaint_number' => $newComplaintNumber, 'message' => 'Your complaint open Successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getComplaintNumber(){
        $currentYear = date('y');
        $nextYear = $currentYear + 1;
        $financialYear = "$currentYear-$nextYear";
        $latestComplaint = Complaint::where('complaint_number', 'like', "SEC/HO/$financialYear/%")
            ->orderBy('complaint_number', 'desc')
            ->first();

        if ($latestComplaint) {
            $lastComplaintNumber = explode('/', $latestComplaint->complaint_number);
            $nextComplaintNumber = intval(end($lastComplaintNumber)) + 1;
        } else {
            $nextComplaintNumber = 1;
        }

        $nextComplaintNumberPadded = str_pad($nextComplaintNumber, 3, '0', STR_PAD_LEFT);

        return "SEC/HO/$financialYear/$nextComplaintNumberPadded";
    }

    public function getComplaintCounts(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $query = Complaint::where('created_by', $request->customer_id);
            if (!empty($request['start_date']) && !empty($request['end_date'])) {
                $query->whereBetween('complaint_date', [date('Y-m-d', strtotime($request['start_date'])), date('Y-m-d', strtotime($request['end_date']))]);
            }
            $data['total'] = $query->count();
            $data['pending'] = $query->where('complaint_status', '0')->count();
            $data['cancel'] = $query->where('complaint_status', '3')->count();
            $data['inproccess'] = $query->where('complaint_status', '2')->count();
            $data['closed'] = $query->where('complaint_status', '1')->count();
            $data['unassigned'] = $query->where('assign_user', NULL)->count();
            if ($data) {
                return response()->json(['status' => 'success', 'data' => $data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'data not found', 'data' => null], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getComplaints(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            
            $data = Complaint::with('customer', 'complaint_type_details','product_details')->where('created_by', $request->customer_id)->where('created_by_device', 'customer');
            if($request->warranty_start_date && $request->warranty_start_date != '' && $request->warranty_start_date != NULL){
                $data->where('customer_bill_date', $request->warranty_start_date);
            }

            if($request->complaint_status != '' && $request->complaint_status != NULL){
                $data->where('complaint_status', $request->complaint_status);
            }
            $data = $data->get();
            if ($data) {
                $main_data = array();
                foreach ($data as $key => $value) {
                    $main_data[$key]['complaint_number'] = $value->complaint_number;
                    $main_data[$key]['complaint_status'] = $value->complaint_status;
                    $main_data[$key]['product_serail_number'] = $value->product_serail_number;
                    $main_data[$key]['remark'] = $value->remark;
                    $main_data[$key]['product_name'] = $value->product_name??($value->product_details?$value->product_details->product_name:'');
                    $main_data[$key]['warranty_start_date'] = $value->customer_bill_date;
                    $main_data[$key]['complaint_type'] = $value->complaint_type_details->name;
                    $main_data[$key]['end_user'] = $value->customer;
                }
                return response()->json(['status' => 'success', 'data' => $main_data], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'data not found', 'data' => null], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}