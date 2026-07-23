<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SendNotifications;
use App\Models\Address;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Validator;
use Gate;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BeatSchedule;
use App\Models\BeatCustomer;
use App\Models\BeatUser;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Sales;
use App\Models\CheckIn;
use App\Models\CompOffLeave;
use App\Models\CustomerDetails;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\SalesTarget;
use App\Models\Customers;
use App\Models\DealerAppointment;
use App\Models\Division;
use App\Models\Expenses;
use App\Models\LoyaltyAppSetting;
use App\Models\ParentDetail;
use App\Models\Pincode;
use App\Models\Redemption;
use App\Models\SalesTargetUsers;
use App\Models\State;
use App\Models\TourProgramme;
use App\Models\TransactionHistory;
use Carbon\Carbon;
use App\Models\FieldKonnectAppSetting;
use App\Models\PrimarySales;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->users = new User();


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

    public function dashboard(Request $request)
    {
        try {
            $user_id = $request->user()->id;
            $filter_date = $request->input('filter_date');
            $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d');
            $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d');
            $year = (date('m') > 3) ? date("Y", strtotime("+ 1 year")) : date('Y');
            $lastyear = (date('m') < 4) ? date("Y", strtotime("- 1 year")) : date('Y');
            switch ($filter_date) {
                case 'Today':
                    $fromdate = date('Y-m-d');
                    $todate = date('Y-m-d');
                    break;
                case 'This Month':
                    $fromdate = date('Y-m-01');
                    $todate = date('Y-m-t');
                    break;
                case 'Last Month':
                    $fromdate = date('Y-m-d', strtotime('first day of last month'));
                    $todate = date('Y-m-t', strtotime('last month'));
                    break;
                case 'Quarter 1':
                    $fromdate = date('Y-m-d', strtotime(date('Y') . '-04-01'));
                    $todate = date('Y-m-d', strtotime(date('Y') . '-06-30'));
                    break;
                case 'Quarter 2':
                    $fromdate = date('Y-m-d', strtotime(date('Y') . '-07-01'));
                    $todate = date('Y-m-d', strtotime(date('Y') . '-09-30'));
                    break;
                case 'Quarter 3':
                    $fromdate = date('Y-m-d', strtotime(date('Y') . '-10-01'));
                    $todate = date('Y-m-d', strtotime(date('Y') . '-12-31'));
                    break;
                case 'Quarter 4':
                    $fromdate = date('Y-m-d', strtotime($year . '-01-01'));
                    $todate = date('Y-m-d', strtotime($year . '-03-31'));
                    break;
                case 'YTM':
                    $fromdate = date('Y-m-d', strtotime($lastyear . '-04-01'));
                    $todate = date('Y-m-t', strtotime('last month'));
                    break;
                case 'Last Year':
                    $fromdate = date('Y-m-d', strtotime($lastyear . '-04-01'));
                    $todate = date('Y-m-d', strtotime($year . '-03-31'));
                    break;
                default:
                    $fromdate = date('Y-m-d');
                    $todate = date('Y-m-d');
                    break;
            }
            $punchin = Attendance::where('user_id', $user_id)->whereDate('punchin_date', getcurentDate())->select('id', 'punchin_time', 'punchout_time', 'working_type', 'flag')->first();
            $orders = Order::where(function ($query) use ($user_id, $fromdate, $todate) {
                $query->where('created_by', '=', $user_id);
                if (!empty($fromdate) && !empty($todate)) {
                    $query->whereDate('order_date', '>=', $fromdate);
                    $query->whereDate('order_date', '<=', $todate);
                    //$query->whereBetween('order_date', [$fromdate, $todate]);
                }
            })
                ->select('grand_total', 'id', 'buyer_id', 'beatscheduleid')->get();

            $achievement = !empty($orders) ? $orders->sum('grand_total') : 0;
            $target = SalesTarget::where('userid', '=', $user_id)
                ->whereYear('startdate', '=', date('Y', strtotime($fromdate)))
                ->whereMonth('startdate', '=', date('m', strtotime($fromdate)))
                ->sum('amount');

            $sales = Sales::where(function ($query) use ($user_id, $fromdate, $todate) {
                $query->where('created_by', '=', $user_id);
                if (!empty($fromdate) && !empty($todate)) {
                    $query->whereDate('invoice_date', '>=', $fromdate);
                    $query->whereDate('invoice_date', '<=', $todate);
                    //$query->whereBetween('invoice_date', [$fromdate, $todate]);
                }
            })->select('grand_total', 'order_id', 'id')->get();
            $sales_amount = !empty($sales) ? $sales->sum('grand_total') : 0;

            $beatschedule = BeatSchedule::with('beatcustomers')->where(function ($query) use ($fromdate, $todate, $user_id) {
                if (!empty($fromdate) && !empty($todate)) {
                    $query->whereDate('beat_date', '>=', $fromdate);
                    $query->whereDate('beat_date', '<=', $todate);
                    //$query->whereBetween('beat_date', [$fromdate, $todate]);
                }
                $query->where('user_id', '=', $user_id);
            })
                ->select('id', 'beat_id')
                ->get();
            $total_beat_counter = 0;
            $total_visited_counter = 0;
            if (!empty($beatschedule)) {
                foreach ($beatschedule as $key => $counter) {
                    $total_visited_counter += $counter['beatcheckininfo']->unique('customer_id', 'checkin_date')->count();
                    $total_beat_counter += count($counter['beatcustomers']);
                }
            }
            //Assign Counter
            $assign_counter = Customers::where('executive_id', '=', $user_id)->count();
            $new_added_counter = Customers::where(function ($query) use ($fromdate, $todate, $user_id) {
                if (!empty($fromdate) && !empty($todate)) {
                    $query->whereDate('created_at', '>=', $fromdate);
                    $query->whereDate('created_at', '<=', $todate);

                    //$query->whereBetween('created_at', [$fromdate, $todate]);
                }
                $query->where('created_by', '=', $user_id);
            })->count();
            $active_counter = $orders->unique('buyer_id')->count();
            // Payment 
            $collectionamount = PaymentDetail::whereIn('sales_id', $sales->pluck('id'))->sum('amount');
            //Working Days
            $workings = Attendance::where(function ($query) use ($user_id, $fromdate, $todate) {
                $query->where('user_id', '=', $user_id);
                $query->where('working_type', '=', 'fields');
                if (!empty($fromdate) && !empty($todate)) {
                    $query->whereDate('punchin_date', '>=', $fromdate);
                    $query->whereDate('punchin_date', '<=', $todate);
                    //$query->whereBetween('punchin_date', [$fromdate, $todate]);
                }
            })
                ->select('id', 'worked_time')->get();

            $attendances = $workings->map(function ($item) {
                $days = 0;
                switch ($item->worked_time) {
                    case (date('H', strtotime($item->worked_time))  >= 4 && date('H', strtotime($item->worked_time)) < 7):
                        $days = 0.5;
                        break;
                    case (date('H', strtotime($item->worked_time)) >= 7):
                        $days = 1;
                        break;
                    default:
                        break;
                }
                $item['working_days'] = $days;
                return $item;
            });
            $avgsales = ($achievement > 0 && $attendances->sum('working_days') > 0) ? $achievement / $attendances->sum('working_days') : 0;
            $data = collect([
                'punchin_id' => (!empty($punchin['id'])) ? $punchin['id'] : '',
                'punchin' => (!empty($punchin['punchin_time'])) ? true : false,
                'punchin_flag' => (!empty($punchin['flag'])) ? true : false,
                'punchout' => (!empty($punchin['punchout_time'])) ? true : false,
                'working_type' => (!empty($punchin['working_type'])) ? $punchin['working_type'] : '',
                'buyer' => 'Retailer',
                'seller' => 'Distributor',
                'totalcounter' => $total_beat_counter,
                'visitcounter' => $total_visited_counter,
                'adherence' => ($total_beat_counter >= 1) ? number_format((float)($total_visited_counter * 100) / $total_beat_counter, 1, '.', '') . ' %'  : '',
                'productive_counter' => (string)$active_counter,
                'productivity' => ($total_visited_counter >= 1) ? number_format((float)($orders->unique('buyer_id', 'beatscheduleid')->count() * 100) / $total_visited_counter, 1, '.', '') . ' %'  : '',
                'target_amount' => amountConversion($target),
                'achievement_amount' => amountConversion($achievement),
                'achievement_percent' => ($target >= 1) ? ($achievement * 100) / $target : 0,
                'target' => $target,
                'achievement' => $achievement,
                'orders_count' => $orders->count(),
                'outstanding_amount' => (string)($sales_amount - $collectionamount),
                'new_added_counter' => $new_added_counter,
                'active_counter_percent' => ($active_counter >= 1 && $assign_counter >= 1) ? number_format((float)($active_counter * 100) / $assign_counter, 1, '.', '') . ' %'  : '',
                'uniquesku_qty' => 0,
                'sales_amount' => amountConversion($sales_amount),
                'average_sales' => amountConversion($avgsales),
                'collection_amount' => amountConversion($collectionamount),
            ]);
            return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getKyc(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $customer = Customers::with('customerdocuments')->with('customerdetails')->find($request->id);

            $data['aadhar']['number'] = $customer->customerdetails ? $customer->customerdetails->aadhar_no : '';
            $data['aadhar']['front_image'] = !empty($customer['customerdocuments']->where('document_name', 'aadhar')->pluck('file_path')->first()) ? asset('uploads/' . $customer['customerdocuments']->where('document_name', 'aadhar')->pluck('file_path')->first()) : url('/') . '/' . asset('assets/img/placeholder.jpg');
            $data['aadhar']['back_image'] = !empty($customer['customerdocuments']->where('document_name', 'aadharback')->pluck('file_path')->first()) ? asset('uploads/' . $customer['customerdocuments']->where('document_name', 'aadharback')->pluck('file_path')->first()) : url('/') . '/' . asset('assets/img/placeholder.jpg');
            $data['aadhar']['status'] = $customer->customerdetails ? $customer->customerdetails->aadhar_no_status : 0;

            $data['pan']['number'] = $customer->customerdetails ? $customer->customerdetails->pan_no : '';
            $data['pan']['image'] = !empty($customer['customerdocuments']->where('document_name', 'pan')->pluck('file_path')->first()) ? asset('uploads/' . $customer['customerdocuments']->where('document_name', 'pan')->pluck('file_path')->first()) : url('/') . '/' . asset('assets/img/placeholder.jpg');
            $data['pan']['status'] = $customer->customerdetails ? $customer->customerdetails->pan_no_status : 0;

            $data['bank_details']['account_holder'] = $customer->customerdetails ? $customer->customerdetails->account_holder : '';
            $data['bank_details']['account_number'] = $customer->customerdetails ? $customer->customerdetails->account_number : '';
            $data['bank_details']['bank_name'] = $customer->customerdetails ? $customer->customerdetails->bank_name : '';
            $data['bank_details']['ifsc_code'] = $customer->customerdetails ? $customer->customerdetails->ifsc_code : '';
            $data['bank_details']['passbook_image'] = !empty($customer['customerdocuments']->where('document_name', 'bankpass')->pluck('file_path')->first()) ? asset('uploads/' . $customer['customerdocuments']->where('document_name', 'bankpass')->pluck('file_path')->first()) : url('/') . '/' . asset('assets/img/placeholder.jpg');
            $data['bank_details']['status'] = $customer->customerdetails ? $customer->customerdetails->bank_status : 0;

            $data['gstin']['number'] = $customer->customerdetails ? $customer->customerdetails->gstin_no : '';
            $data['gstin']['image'] = !empty($customer['customerdocuments']->where('document_name', 'gstin')->pluck('file_path')->first()) ? asset('uploads/' . $customer['customerdocuments']->where('document_name', 'gstin')->pluck('file_path')->first()) : url('/') . '/' . asset('assets/img/placeholder.jpg');
            $data['gstin']['status'] = $customer->customerdetails ? $customer->customerdetails->gstin_no_status : 0;

            return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addKyc(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $customer = Customers::with('customerdocuments', 'customerdetails')->find($request->id);
            $id = $request->id;
            $docimages = collect([]);
            if ($request->file('imggstin')) {
                $path = 'customers';
                $image = $request->file('imggstin');
                $filename = 'gstin_' . $id;
                unset($request['imggstin']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $path, $filename),
                    'document_name' =>  'gstin',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['gstin_no_status' => '0']);
            }
            if ($request->file('imgpan')) {
                $path = 'customers';
                $image = $request->file('imgpan');
                $filename = 'pan_' . $id;
                unset($request['imgpan']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $path, $filename),
                    'document_name' =>  'pan',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['pan_no_status' => '0']);
            }
            if ($request->file('imgaadhar')) {
                $path = 'customers';
                $image = $request->file('imgaadhar');
                $filename = 'aadhar_' . $id;
                unset($request['image']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $path, $filename),
                    'document_name' =>  'aadhar',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['aadhar_no_status' => '0']);
            }
            if ($request->file('imgaadharback')) {
                $path = 'customers';
                $image = $request->file('imgaadharback');
                $filename = 'aadharback_' . $id;
                unset($request['image']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $path, $filename),
                    'document_name' =>  'aadharback',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['aadhar_no_status' => '0']);
            }
            if ($request->file('imgbankpass')) {
                $path = 'customers';
                $image = $request->file('imgbankpass');
                $filename = 'bankpass_' . $id;
                unset($request['image']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $path, $filename),
                    'document_name' =>  'bankpass',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['bank_status' => '0']);
            }

            foreach ($docimages as $docimage) {
                $existingAttachment = Attachment::where('document_name', $docimage['document_name'])
                    ->where('customer_id', $request->id)
                    ->first();

                if ($existingAttachment) {
                    $existingAttachment->update($docimage);
                } else {
                    Attachment::create(array_merge($docimage, ['customer_id' => $id]));
                }
            }
            $request['customer_id'] = $request->id;
            $customerdetails = new CustomerDetails();
            $customerdetails->save_data($request);
            $noti_data = [
                'fcm_token' =>  trim($customer->customerdetails->fcm_token),
                'title' => 'KYC Sent for verification 🧐',
                'msg' => $customer->first_name . ' your KYC details have sent for verification in Silver Saarthi.',
            ];
            $send_notification = SendNotifications::send($noti_data);
            return response(['status' => 'success', 'message' => 'Data save successfully.', 'data' => $customerdetails, 'push_notification' => $send_notification], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function updateprofile(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $customer = Customers::with('customeraddress', 'customerdetails')->find($request->id);
            if ($request->file('image')) {
                $path = 'customers';
                $image = $request->file('image');
                $filename = 'shop_' . $request->id;
                unset($request['image']);
                $request['profile_image'] = fileupload($image, $path, $filename);
            }
            if ($request->file('profileImage')) {
                $path = 'customers';
                $image = $request->file('profileImage');
                $filename = 'profile_' . $request->id;
                unset($request['image']);
                $request['shop_image'] = fileupload($image, $path, $filename);
            }
            $request['customer_id'] = $request->id;
            $response = $customer->update_data($request);
            if ($response['status'] == 'success') {
                $pincodes = Pincode::with('cityname', 'cityname.districtname')->where('pincode', '=', $request['zipcode'])->first();
                $request['state_id'] = !empty($pincodes['cityname']['districtname']['state_id']) ? $pincodes['cityname']['districtname']['state_id'] : $request['state_id'];
                $request['district_id'] = !empty($pincodes['cityname']['district_id']) ? $pincodes['cityname']['district_id'] : $request['district_id'];
                $request['city_id'] = !empty($pincodes['city_id']) ? $pincodes['city_id'] : $request['city_id'];
                $request['zipcode'] = !empty($request['pincode_id']) ? $request['pincode_id'] : $request['zipcode'];
                $request['pincode_id'] = !empty($pincodes['id']) ? $pincodes['id'] : $request['pincode_id'];

                $request['country_id'] = !empty($request['country_id']) ? $request['country_id'] : State::where('id', $request['state_id'])->pluck('country_id')->first();
                $request['landmark'] = !empty($request['landmark']) ? $request['landmark'] : '';
                Address::updateOrCreate(['customer_id' => $request->id], [
                    'active'    => 'Y',
                    'customer_id'   =>  $request['customer_id'],
                    'address1' => !empty($request['address1']) ? $request['address1'] : '',
                    'address2' => !empty($request['address2']) ? $request['address2'] : '',
                    'landmark' => !empty($request['landmark']) ? $request['landmark'] : '',
                    'locality' => !empty($request['locality']) ? $request['locality'] : $request['landmark'],
                    'country_id' => !empty($request['country_id']) ? $request['country_id'] : null,
                    'state_id' => !empty($request['state_id']) ? $request['state_id'] : null,
                    'district_id' => !empty($request['district_id']) ? $request['district_id'] : null,
                    'city_id' => !empty($request['city_id']) ? $request['city_id'] : null,
                    'pincode_id' => !empty($request['pincode_id']) ? $request['pincode_id'] : null,
                    'zipcode' => !empty($request['zipcode']) ? $request['zipcode'] : '',
                    'created_by' => !empty($request['created_by']) ? $request['created_by'] : 0,
                    'created_at' => getcurentDateTime(),
                    'updated_at' => getcurentDateTime()
                ]);
                if (!empty($request['parent_id'])) {
                    ParentDetail::where('customer_id', $request['id'])->delete();
                    foreach ($request['parent_id'] as $key => $rows) {
                        $parentDetail = ParentDetail::create(
                            [
                                'customer_id' => $request['id'],
                                'parent_id' => $rows,
                            ]
                        );
                    }
                }
                $customer_updated = Customers::with('customeraddress', 'getparentdetail', 'customerdetails')->find($request->id);
                $profile_image = $customer_updated->shop_image;
                $customer_updated->shop_image = $customer_updated->profile_image;
                $customer_updated->profile_image = $profile_image;
                return response(['status' => 'success', 'message' => 'Data Update successfully.', 'data' => $customer_updated], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getsettings()
    {
        try {
            $data = LoyaltyAppSetting::with('media')->first();
            return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getpoints(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $thistorys = TransactionHistory::where('customer_id', $request->id)->get();
            $data['total_points'] = (int)TransactionHistory::where('customer_id', $request->id)->sum('point') ?? 0;
            $data['active_points'] = TransactionHistory::where('customer_id', $request->id)->where('status', '1')->sum('point') ?? 0;
            $data['provision_points'] = TransactionHistory::where('customer_id', $request->id)->where('status', '0')->sum('point') ?? 0;
            $data['active_points'] = 0;
            $data['provision_points'] = 0;
            foreach ($thistorys as $thistory) {
                if ($thistory->status == '1') {
                    $data['active_points'] += $thistory->point;
                } else {
                    $data['active_points'] += $thistory->active_point;
                    $data['provision_points'] += $thistory->provision_point;
                }
            }
            $data['total_redemption'] = Redemption::where('customer_id', $request->id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
            $data['total_rejected'] = Redemption::where('customer_id', $request->id)->where('status', '2')->sum('redeem_amount') ?? 0;
            $data['total_balance'] = (int)$data['active_points'] - (int)$data['total_redemption'];
            return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function pendingCounts(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $userids = getUsersReportingToAuth($request->id);
            $data['pending_attendance'] = Attendance::where('user_id', $request->id)->where('attendance_status', '0')->count();
            $data['pending_tour_plan'] = TourProgramme::where('userid', $request->id)->where('status', '0')->count();
            $data['pending_expense'] = Expenses::where('user_id', $request->id)->where('checker_status', '0')->count();
            $data['pending_all_expense'] = Expenses::whereNot('user_id', $request->id)->whereIn('user_id', $userids)->where('checker_status', '0')->count();
            // dd($data['pending_all_expense']);
            $data['pending_order_discount'] = Order::where('created_by', $request->id)->where('cluster_discount', '!=', NULL)->where('discount_status', '0')->count();
            $data['pending_orders'] = Order::where('status_id', NULL)->count();
            $data['pending_appointment'] = DealerAppointment::where('approval_status', '0')->count();

            return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getUserDashboardData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }
        $slected_user = User::find($request->user_id);
        $login_user = $request->user();
        $todayDate = Carbon::today()->toDateString();
        $todayBeatSchedule = BeatSchedule::where('user_id', $login_user['id'])->where('beat_date', $todayDate)->get();
        $beatUser = BeatUser::where('user_id', $login_user['id'])->get();

        $user_ids = getUsersReportingToAuth($request->user_id);

        $month = Carbon::now()->format('M');
        $year = Carbon::now()->format('Y');

        if ($request->start_date && !empty($request->start_date) && $request->end_date && !empty($request->end_date)) {
            $startOfWeek = $request->start_date;
            $endOfWeek = $request->end_date;
        } else {
            $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
            $endOfWeek = Carbon::now()->endOfWeek()->toDateString();
        }

        if ($request->division_id && !empty($request->division_id)) {
            $user_ids = User::where('division_id', $request->division_id)->pluck('id');
        }

        $query = SalesTargetUsers::with('user');
        if (!$slected_user->hasRole('superadmin') && !$slected_user->hasRole('Admin')) {
            $query->whereIn('user_id', $user_ids);
        }

        if ($request->branch_id && !empty($request->branch_id)) {
            $query->where(['branch_id' => $request->branch_id]);
        }


        if ($request->tamonth && !empty($request->tamonth) && count($request->tamonth) > 0) {
            $query->where('year', $request->tayear)->whereIn('month', $request->tamonth);
        } elseif ($request->tayear && !empty($request->tayear)) {
            $query->where(['year' => $request->tayear]);
        } else {
            $query->where(['month' => $month, 'year' => $year]);
        }
        
        $total_data = $query->get();
        $target = 0;
        $achievement = 0;
        foreach ($total_data as $key => $value) {
            if ($value->user_id == $request->user_id) {
                $target += $value->target;
                $achievement += $value->achievement;
            } else if ($value->type == 'primary') {
                $target += $value->target;
                $achievement += $value->achievement;
            }
        }
        $order_value = Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])->whereIn('created_by', $user_ids)->sum('sub_total');
        $order_ids = Order::whereBetween('order_date', [$startOfWeek, $endOfWeek])->whereIn('created_by', $user_ids)->pluck('id');
        $order_qty = OrderDetails::whereIn('order_id', $order_ids)->sum('quantity');
        $customer_visit = CheckIn::whereBetween('checkin_date', [$startOfWeek, $endOfWeek])->whereIn('user_id', $user_ids)->count();
        if ($target > 0) {
            $data['target'] = (string)$target;
            if ($achievement < 1) {
                $all_emp_codes = User::whereIn('id', $user_ids)->pluck('employee_codes');
                if ($request->tamonth && !empty($request->tamonth) && count($request->tamonth) > 0) {
                    $monthNumbers = array_map(function ($month) {
                        return Carbon::parse($month)->month;
                    }, $request->tamonth);

                    $firstMonthNumber = min($monthNumbers);
                    $lastMonthNumber = max($monthNumbers);

                    $firstDate = Carbon::createFromDate($request->tayear, $firstMonthNumber, 1)->startOfMonth();
                    $lastDate = Carbon::createFromDate($request->tayear, $lastMonthNumber, 1)->endOfMonth();

                    $firstDateFormatted = $firstDate->toDateString();
                    $lastDateFormatted = $lastDate->toDateString();

                    $achievement = PrimarySales::whereIn('emp_code', $all_emp_codes)->where('invoice_date', '>=', $firstDateFormatted)->where('invoice_date', '<=', $lastDateFormatted);

                    if ($request->branch_id && !empty($request->branch_id)) {
                        $selected_branch = Branch::find($request->branch_id);
                        $achievement->where(['final_branch' => $selected_branch->branch_name]);
                    }
                    $achievement = $achievement->sum('net_amount');
                    if ($achievement > 0) {
                        $achievement = number_format(($achievement / 100000), 2, '.', '');
                        $data['achievement'] = $achievement;
                    } else {
                        $data['achievement'] = "0";
                    }
                } elseif ($request->tayear && !empty($request->tayear)) {
                    $firstDate = Carbon::createFromDate($request->tayear, 1, 1)->startOfYear();

                    $lastDate = Carbon::createFromDate($request->tayear, 12, 31)->endOfYear();

                    $firstDateFormatted = $firstDate->toDateString();
                    $lastDateFormatted = $lastDate->toDateString();
                    $achievement = PrimarySales::whereIn('emp_code', $all_emp_codes)->where('invoice_date', '>=', $firstDateFormatted)->where('invoice_date', '<=', $lastDateFormatted);
                    if ($request->branch_id && !empty($request->branch_id)) {
                        $selected_branch = Branch::find($request->branch_id);
                        $achievement->where(['final_branch' => $selected_branch->branch_name]);
                    }
                    $achievement = $achievement->sum('net_amount');
                    if ($achievement > 0) {
                        $achievement = number_format(($achievement / 100000), 2, '.', '');
                        $data['achievement'] = $achievement;
                    } else {
                        $data['achievement'] = "0";
                    }
                } else {
                    $achievement = PrimarySales::where('invoice_date', '>=', date('Y-m') . '-01')
                        ->where('invoice_date', '<=', date('Y-m') . '-31')
                        ->whereIn('emp_code', User::where('sales_type', 'Primary')->pluck('employee_codes'))
                        ->whereIn('emp_code', $all_emp_codes);

                    // PrimarySales::whereIn('emp_code', $all_emp_codes)->whereIn('division', ['PUMP', 'MOTOR'])->where('invoice_date', '>=', date('Y-m') . '-01');
                    if ($request->branch_id && !empty($request->branch_id)) {
                        $selected_branch = Branch::find($request->branch_id);
                        $achievement->where(['final_branch' => $selected_branch->branch_name]);
                    }
                    $achievement = $achievement->sum('net_amount');
                    if ($achievement > 0) {
                        $achievement = number_format(($achievement / 100000), 2, '.', '');
                        $data['achievement'] = (string)$achievement;
                    } else {
                        $data['achievement'] = "0";
                    }
                }
            } else {
                $data['achievement'] = $achievement;
            }
            if ($achievement > 0) {
                $data['achiv_per'] = number_format((($achievement / $target) * 100), 2, '.', '');
                $data['target_per'] = number_format((100 - $data['achiv_per']), 2, '.', '');
            } else {
                $data['achiv_per'] = "0";
                $data['target_per'] = "100";
            }
        } else {
            $data['target'] = "";
            $data['achievement'] = "";
            $data['achiv_per'] = "";
            $data['target_per'] = "";
        }
        
        $data['order_value'] = $order_value > 0 ? number_format(($order_value / 100000), 2, '.', '') : "";
        $data['order_qty'] = $order_qty > 0 ? $order_qty : "";
        $data['customer_visit'] = $customer_visit > 0 ? (string)$customer_visit : "";
        $data['todayBeatSchedule'] = count($todayBeatSchedule) > 0 ? true : false;
        $data['beatUser'] = count($beatUser) > 0 ? true : false;

        $branches = Branch::where('active', 'Y')->select('id', 'branch_name')->get();
        $divisions = Division::where('active', 'Y')->select('id', 'division_name')->get();

        return response()->json(['status' => 'success', 'data' => $data, 'Branches' => $branches, 'divisions' => $divisions, 'user_status' => $login_user->active], 200);
    }

    //field connect version
    public function getVersion()
    {
        $fieldConnect = FieldKonnectAppSetting::with('media')->first();
        $data["app_version"] = isset($fieldConnect) ? (isset($fieldConnect->app_version) ? $fieldConnect->app_version : '') : '';
        $data["media"] = $fieldConnect->media->toArray();

        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
    }

    public function getSarthiPoints(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }

        $thistorys = TransactionHistory::where('customer_id', $request->customer_id)->get();
        $total_points = TransactionHistory::where('customer_id', $request->customer_id)->sum('point') ?? 0;
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
        $total_rejected = Redemption::where('customer_id', $request->customer_id)->where('status', '2')->sum('redeem_amount') ?? 0;
        $total_balance = (int)$active_points - (int)$total_redemption;

        $data = [
            'total_points' => $total_points,
            'active_points' => $active_points,
            'provision_points' => $provision_points,
            'total_redemption' => $total_redemption,
            'total_rejected' => $total_rejected,
            'total_balance' => $total_balance,
        ];

        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
    }

    public function getUserSataus(Request $request)
    {
        $login_user = $request->user();
        return response()->json(['status' => 'success', 'user_status' => $login_user->active], 200);
    }

    public function getLeaveBalance(Request $request)
    {
        try {
            $user = $request->user();
            $data['leaveBalance'] = $user->leave_balance;
            $last60Days = Carbon::now()->subDays(60);
            $sundayPunchinCount = CompOffLeave::where('comp_off_date', '>=', $last60Days)->where('is_used', false)
                ->where('user_id', $user->id)
                ->sum('balance');
            $data['comb_off'] = $sundayPunchinCount > 0 ? $sundayPunchinCount : '0';
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
