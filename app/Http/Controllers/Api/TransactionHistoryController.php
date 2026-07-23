<?php

namespace App\Http\Controllers\Api;

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
use App\Models\SchemeDetails;
use App\Models\SchemeHeader;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DamageEntryController;
use App\Http\Controllers\SendNotifications;
use App\Models\DamageEntry;
use App\Models\Gifts;
use App\Models\Product;
use App\Models\Redemption;

class TransactionHistoryController extends Controller
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

    public function getcouponhistory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $pageSize = $request->input('pageSize');
            $query = TransactionHistory::with('scheme_details', 'scheme')->where(function ($query) use ($request) {
                $query->where('customer_id', $request->id);
                if ($request->search && $request->search != '' && $request->search != null) {
                    $query->where('product_name', 'LIKE', "%{$request->search}%")->orWhere('display_name', 'LIKE', "%{$request->search}%");
                }
                if ($request->status != '' && $request->status != null) {
                    $query->where('status', $request->status);
                }
                // if ($request->subcategory_id && $request->subcategory_id != '' && $request->subcategory_id != null) {
                //     $query->where('subcategory_id', $request->subcategory_id);
                // }
                if ($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != '') {
                    $startDate = date('Y-m-d', strtotime($request->start_date));
                    $endDate = date('Y-m-d', strtotime($request->end_date));
                    $data = $query->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate);
                }
            })->orderBy('created_at', 'desc');
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $total_points = TransactionHistory::where('customer_id', $request->id)->sum('point') ?? 0;
            // $active_points = TransactionHistory::where('customer_id', $request->id)->where('status', '1')->sum('point') ?? 0;
            // $provision_points = TransactionHistory::where('customer_id', $request->id)->where('status', '0')->sum('point') ?? 0;
            $thistorys = TransactionHistory::where('customer_id', $request->id)->get();
            $active_points = 0;
            $provision_points = 0;
            foreach ($thistorys as $thistory) {
                if ($thistory->status == '1') {
                    $active_points += $thistory->point;
                } else {
                    $active_points += $thistory->active_point;
                    $provision_points += $thistory->provision_point;
                }
            }
            $total_redemption = Redemption::where('customer_id', $request->id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
            $total_balance = (int)$active_points - (int)$total_redemption;

            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {

                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'scheme_name' => isset($value['scheme_details']) ? $value['scheme_details']['scheme_name'] : '',
                        'coupon_code' => (isset($value['coupon_code']) && $value['coupon_code'] != '' && $value['coupon_code'] != NULL) ? $value['coupon_code'] : 'Manual',
                        'status' => isset($value['status']) ? $value['status'] : '',
                        'active_point' => isset($value['active_point']) ? $value['active_point'] : '',
                        'provision_point' => isset($value['provision_point']) ? $value['provision_point'] : '',
                        'point' => isset($value['point']) ? $value['point'] : '',
                        'remark' => isset($value['remark']) ? $value['remark'] : '',
                        'date' => isset($value['created_at']) ? date('d M Y', strtotime($value['created_at'])) : '',
                        'product_name' => isset($value['scheme']) ? preg_replace('/\s+/', ' ', $value['scheme']['product']['product_name']) : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'total_points' => $total_points, 'active_points' => $active_points, 'provision_points' => $provision_points, 'total_balance' => $total_balance, 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'total_points' => $total_points, 'active_points' => $active_points, 'provision_points' => $provision_points, 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getredemptionhistory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $pageSize = $request->input('pageSize');
            $query = Redemption::with('product', 'neft_details')->where(function ($query) use ($request) {
                $query->where('customer_id', $request->id);
                if ($request->search && $request->search != '' && $request->search != null) {
                    $query->where('product_name', 'LIKE', "%{$request->search}%")->orWhere('display_name', 'LIKE', "%{$request->search}%");
                }
                if ($request->redeem_mode && $request->redeem_mode != '' && $request->redeem_mode != null) {
                    $query->where('redeem_mode', $request->redeem_mode);
                }
                if ($request->status != '' && $request->status != null) {
                    $query->where('status', $request->status);
                }
                if ($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != '') {
                    $startDate = date('Y-m-d', strtotime($request->start_date));
                    $endDate = date('Y-m-d', strtotime($request->end_date));
                    $data = $query->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate);
                }
            })->orderBy('created_at', 'desc');
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $total_redemption = Redemption::where('customer_id', $request->id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
            // $active_points = TransactionHistory::where('customer_id', $request->id)->where('status', '1')->sum('point') ?? 0;
            $thistorys = TransactionHistory::where('customer_id', $request->id)->get();
            $active_points = 0;
            $provision_points = 0;
            foreach ($thistorys as $thistory) {
                if ($thistory->status == '1') {
                    $active_points += $thistory->point;
                } else {
                    $active_points += $thistory->active_point;
                    $provision_points += $thistory->provision_point;
                }
            }
            $total_rejected = Redemption::where('customer_id', $request->id)->where('status', '2')->sum('redeem_amount') ?? 0;
            $total_balance = (int)$active_points - (int)$total_redemption;

            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'id' => isset($value['id']) ? $value['id'] : 0,
                        'redeem_mode' => isset($value['redeem_mode']) ? $value['redeem_mode'] : '',
                        'coupon_code' => isset($value['coupon_code']) ? $value['coupon_code'] : '',
                        'status' => isset($value['status']) ? $value['status'] : '',
                        'point' => isset($value['redeem_amount']) ? $value['redeem_amount'] : '',
                        'date' => isset($value['updated_at']) ? date('d M Y', strtotime($value['updated_at'])) : '',
                        'details' => $value->neft_details,
                        'gift_details' => ['dispatch_number' => $value->dispatch_number, 'remark' => $value->remark],
                    ]);
                }
                return response()->json(['status' => 'success', 'total_redemption' => $total_redemption, 'total_rejected' => $total_rejected, 'total_balance' => $total_balance, 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'total_redemption' => $total_redemption, 'total_rejected' => $total_rejected, 'total_balance' => $total_balance, 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getBankDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $customer = Customers::with('customerdocuments')->with('customerdetails')->find($request->id);
            $last_redemption = Redemption::where('customer_id', $request->id)->latest()->first();

            if ($last_redemption) {
                $last_redemption->date = date('d M Y', strtotime($last_redemption->created_at));
            }
            // $active_points = TransactionHistory::where('customer_id', $request->id)->where('status', '1')->sum('point') ?? 0;
            $thistorys = TransactionHistory::where('customer_id', $request->id)->get();
            $active_points = 0;
            $provision_points = 0;
            foreach ($thistorys as $thistory) {
                if ($thistory->status == '1') {
                    $active_points += $thistory->point;
                } else {
                    $active_points += $thistory->active_point;
                    $provision_points += $thistory->provision_point;
                }
            }
            $total_redemption = Redemption::where('customer_id', $request->id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
            $total_balance = (int)$active_points - (int)$total_redemption;

            // $data = collect([]);
            if ($customer) {
                $data['bank_details']['account_holder'] = $customer->customerdetails ? $customer->customerdetails->account_holder : '';
                $data['bank_details']['account_number'] = $customer->customerdetails ? $customer->customerdetails->account_number : '';
                $data['bank_details']['bank_name'] = $customer->customerdetails ? $customer->customerdetails->bank_name : '';
                $data['bank_details']['ifsc_code'] = $customer->customerdetails ? $customer->customerdetails->ifsc_code : '';
                $data['bank_details']['passbook_image'] = !empty($customer['customerdocuments']->where('document_name', 'bankpass')->pluck('file_path')->first()) ? asset('uploads/' . $customer['customerdocuments']->where('document_name', 'bankpass')->pluck('file_path')->first()) : url('/') . '/' . asset('assets/img/placeholder.jpg');
                $data['bank_details']['status'] = $customer->customerdetails ? $customer->customerdetails->bank_status : 0;
                $data['last_redemption'] = $last_redemption;
                $data['active_balance_points'] = $total_balance;
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'success', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addNeftRedemption(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'redeem_amount' => 'required|numeric',
                'account_holder' => 'required',
                'account_number' => 'required',
                'bank_name' => 'required',
                'ifsc_code' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            // $active_points = TransactionHistory::where('customer_id', $request->customer_id)->where('status', '1')->sum('point') ?? 0;
            $thistorys = TransactionHistory::where('customer_id', $request->customer_id)->get();
            $active_points = 0;
            $provision_points = 0;
            foreach ($thistorys as $thistory) {
                if ($thistory->status == '1') {
                    $active_points += $thistory->point;
                } else {
                    $active_points += $thistory->active_point;
                    $provision_points += $thistory->provision_point;
                }
            }
            $total_redemption = Redemption::where('customer_id', $request->customer_id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
            $total_balance = (int)$active_points - (int)$total_redemption;

            if ($request->redeem_amount > $total_balance) {
                return response()->json(['status' => 'error', 'message' => 'The redeem amount not be greater than to total active balance point.'], $this->successStatus);
            }
            $data = Redemption::create([
                'customer_id' => $request->customer_id,
                'redeem_mode' => '2',
                'account_holder' => $request->account_holder,
                'account_number' => $request->account_number,
                'bank_name' => $request->bank_name,
                'ifsc_code' => $request->ifsc_code,
                'redeem_amount' => $request->redeem_amount,
            ]);
            $customer = Customers::with('customerdetails')->find($request->customer_id);
            $noti_data = [
                'fcm_token' =>  $customer->customerdetails->fcm_token,
                'title' => 'Redemption Request Sent 💸',
                'msg' => $customer->first_name . ', your redemption request of ' . $request->redeem_amount . ' Points is sent successfully.',
            ];
            $send_notification = SendNotifications::send($noti_data);
            return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addGiftRedemption(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'gift_id' => 'required|array',
                'gift_id.*' => 'exists:gifts,id',
            ]);
            $validator->setCustomMessages([
                'redeem_amount.max' => 'The redeem amount must not be greater than total point.',
                'gift_id.required' => 'Please select at least one gift.',
                'gift_id.*.exists' => 'The selected gift id :input is invalid.',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            // $active_points = TransactionHistory::where('customer_id', $request->customer_id)->where('status', '1')->sum('point') ?? 0;
            $thistorys = TransactionHistory::where('customer_id', $request->customer_id)->get();
            $active_points = 0;
            $provision_points = 0;
            foreach ($thistorys as $thistory) {
                if ($thistory->status == '1') {
                    $active_points += $thistory->point;
                } else {
                    $active_points += $thistory->active_point;
                    $provision_points += $thistory->provision_point;
                }
            }
            $total_redemption = Redemption::where('customer_id', $request->customer_id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
            $total_balance = (int)$active_points - (int)$total_redemption;
            $tottal_redeem_point = Gifts::whereIn('id', $request->gift_id)->sum('points');

            if ($tottal_redeem_point > $total_balance) {
                return response()->json(['status' => 'error', 'message' => 'The redeem amount not be greater than to total active balance point.'], $this->successStatus);
            }
            $ttpoints = 0;
            foreach ($request->gift_id as $gift) {
                $redeem_point = Gifts::where('id', $gift)->value('points');
                $created_at = Carbon::now();
                $created_at = $created_at->setTimezone('Asia/Kolkata');
                $data = Redemption::create([
                    'customer_id' => $request->customer_id,
                    'redeem_mode' => '1',
                    'gift_id' => $gift,
                    'redeem_amount' => $redeem_point,
                    'created_at' => $created_at,
                ]);
                $ttpoints += $redeem_point;
            }
            $customer = Customers::with('customerdetails')->find($request->customer_id);
            $noti_data = [
                'fcm_token' =>  $customer->customerdetails->fcm_token,
                'title' => 'Redemption Request Sent 💸',
                'msg' => $customer->first_name . ', your redemption request of ' . $ttpoints . ' Points is sent successfully.',
            ];
            $send_notification = SendNotifications::send($noti_data);
            return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addSerialNumber(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'coupon_code' => 'required|array',
            ]);
            $validator->setAttributeNames([
                'coupon_code.*' => 'coupon code',
            ]);

            $validator->setCustomMessages([
                'coupon_code.*.required' => 'All coupon code fields are required.',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $nonNullCoupenCodes = array_filter($request->coupon_code, function ($value) {
                return !is_null($value);
            });
            $expire_schemes = array();
            $no_schemes = array();
            foreach ($nonNullCoupenCodes as $nonNullCoupenCode) {
                $exists = TransactionHistory::where('coupon_code', $nonNullCoupenCode)->exists();
                $notexists = Services::where('serial_no', $nonNullCoupenCode)->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'coupon_code' => "The coupon code '$nonNullCoupenCode' already Scanned.",
                    ]);
                }
                if (!$notexists) {
                    throw ValidationException::withMessages([
                        // 'coupon_code' => "The coupon code '$nonNullCoupenCode' is Invalid.",
                        'coupon_code' => "coupon code '$nonNullCoupenCode' is sent for approval, points will be credited once approved.",
                    ]);
                }
                $scheme = Services::where('serial_no', $nonNullCoupenCode)->first();
                $scheme_details = SchemeDetails::where('product_id', $scheme->product->id)->first();
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
                    array_push($no_schemes, $nonNullCoupenCode);
                    $scheme_id = null;
                }
                $created_at = Carbon::now();
                $created_at = $created_at->setTimezone('Asia/Kolkata');
                $tHistory = TransactionHistory::create([
                    'customer_id' => $request->customer_id,
                    'coupon_code' => $nonNullCoupenCode,
                    'scheme_id' => $scheme_id,
                    'active_point' => $active_point,
                    'provision_point' => $provision_point,
                    'point' => $point,
                    'remark' => 'Coupon scan',
                    'created_at' => $created_at,
                ]);
            }
            $customer = Customers::with('customerdetails')->find($request->customer_id);
            if (count($expire_schemes) > 0) {
                $noti_data = [
                    'fcm_token' =>  $customer->customerdetails->fcm_token,
                    'title' => 'Scan Successful 💸💸',
                    'msg' => $customer->name . ' you have successfully earned ' . $point . ' provisional points in Silver Saarthi.',
                ];
                $send_notification = SendNotifications::send($noti_data);
                return response(['status' => 'success', 'message' => 'Transaction History Store Successfully but coupon code (' . implode(',', $expire_schemes) . ') scheme has either expired or has not started yet so you earned 0 point.', 'point_earn' => $point, 'data' => $tHistory, 'push_notification' => $send_notification], 200);
            } elseif (!$scheme_details) {
                $noti_data = [
                    'fcm_token' =>  $customer->customerdetails->fcm_token,
                    'title' => 'Scan Successful 💸💸',
                    'msg' => $customer->name . ' you have successfully earned ' . $point . ' provisional points in Silver Saarthi.',
                ];
                $send_notification = SendNotifications::send($noti_data);
                return response(['status' => 'success', 'message' => 'Scan is successfully but the coupon code (' . implode(',', $no_schemes) . ') is not part of any scheme. So 0 points are credited.', 'point_earn' => $point, 'data' => $tHistory, 'push_notification' => $send_notification], 200);
            } else {
                $noti_data = [
                    'fcm_token' =>  $customer->customerdetails->fcm_token,
                    'title' => 'Scan Successful 💸💸',
                    'msg' => $customer->name . ' you have successfully earned ' . $point . ' provisional points in Silver Saarthi.',
                ];
                $send_notification = SendNotifications::send($noti_data);
                return response(['status' => 'success', 'message' => 'Transaction History Store Successfully', 'point_earn' => $point, 'data' => $tHistory, 'push_notification' => $send_notification], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getDamageEntry(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $data = DamageEntry::where('customer_id', $request->id)->orderby('id', 'desc')->get();
            if (count($data) > 0) {
                foreach ($data as $key => $value) {
                    $data[$key]['date'] = date('d M Y', strtotime($value->created_at));
                }
                return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
            } else {
                return response(['status' => 'error', 'message' => 'No data found'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addDamageEntry(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                // 'damageattach1' => 'required',
            ]);
            // $validator->setCustomMessages([
            //     'damageattach1.required' => 'Please attach at least one attachment.',
            // ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $expire_schemes = array();
            if ($request->coupon_code && $request->coupon_code != NULL && $request->coupon_code != '') {
                $exists = TransactionHistory::where('coupon_code', $request->coupon_code)->exists();
                // $existsDamage = DamageEntry::where('coupon_code', $request->coupon_code)->exists();
                $existsDamage = DamageEntry::where('coupon_code', $request->coupon_code)->where('status', '!=', '2')->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'coupon_code' => "The coupon code '$request->coupon_code' already Scanned.",
                    ]);
                }
                if ($existsDamage) {
                    throw ValidationException::withMessages([
                        'coupon_code' => "The coupon code '$request->coupon_code' already Scanned.",
                    ]);
                }
                $scheme = Services::where('serial_no', $request->coupon_code)->first();
                if ($scheme) {
                    $scheme_details = SchemeDetails::where('product_id', $scheme->product->id)->first();
                    $scheme_id = $scheme_details->scheme_id;
                    $start_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->start_date);
                    $end_date = Carbon::createFromFormat('Y-m-d', $scheme_details->scheme->end_date);
                    $current_date = Carbon::today();
                    if ($current_date->isSameDay($start_date) || ($current_date->gte($start_date) && $current_date->lte($end_date))) {
                        $point = ($scheme_details) ? $scheme_details->points : NULL;
                    } else {
                        array_push($expire_schemes, $request->coupon_code);
                        $point = '0';
                    }
                } else {
                    $scheme_id = NULL;
                    $point = 0;
                }
                $damageEntry = new DamageEntry();
                $damageEntry->customer_id = $request->customer_id;
                $damageEntry->coupon_code = $request->coupon_code;
                $damageEntry->scheme_id = $scheme_id;
                $damageEntry->point = $point;
                $damageEntry->created_by = auth()->user()->id;
                $damageEntry->save();
                if ($damageEntry->save()) {
                    $coupon_code_array = explode('|', $damageEntry->coupon_code);
                    $pName = trim($coupon_code_array[0]);
                    $cCode = isset($coupon_code_array[1]) ? trim($coupon_code_array[1]) : '';
                    $produ = Product::where('product_name', $pName)->first();
                    if ($produ && !empty($produ)) {
                        $data = [
                            'id' => $damageEntry->id,
                            'product_id' => $produ->id,
                            'status' => '1',
                            'coupon_code' => $cCode,
                            'remark' => 'Direct Approve'
                        ];
                        $request = new Request($data);

                        $damageEntryController = new DamageEntryController();
                        $response = $damageEntryController->changeStatus($request);
                    }
                }
            } else {
                $damageEntry = new DamageEntry();
                $damageEntry->customer_id = $request->customer_id;
                $damageEntry->created_by = auth()->user()->id;
                $damageEntry->save();
            }

            if ($request->hasFile('damageattach1')) {
                $file = $request->file('damageattach1');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $damageEntry->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('damageattach1');
            }
            if ($request->hasFile('damageattach2')) {
                $file = $request->file('damageattach2');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $damageEntry->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('damageattach2');
            }
            if ($request->hasFile('damageattach3')) {
                $file = $request->file('damageattach3');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $damageEntry->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('damageattach3');
            }
            if (count($expire_schemes) > 0) {
                return response(['status' => 'success', 'message' => 'Damage Entry Store Successfully but coupon code (' . implode(',', $expire_schemes) . ') scheme has either expired or has not started yet so you earned 0 point.'], 200);
            } else {
                return response(['status' => 'success', 'message' => 'Damage Entry Store Successfully'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
