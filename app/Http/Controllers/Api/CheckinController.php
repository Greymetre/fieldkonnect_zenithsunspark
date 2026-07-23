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
use App\Models\CheckIn;
use App\Models\Customers;
use App\Models\VisitReport;
use App\Models\BeatSchedule;
use App\Models\CheckInDraft;

class CheckinController extends Controller
{
    public function __construct()
    {
        $this->checkin = new CheckIn();

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

    public function getCheckin(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->active == 'N') {
                return response()->json(['status' => 'error', 'message' =>  'User Inactive'], 401);
            }
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $query = $this->checkin->where(function ($query) use ($user_id) {
                $query->where('user_id', '=', $user_id);
            })
                ->select('id', 'customer_id', 'checkin_date', 'checkin_time', 'checkin_latitude', 'checkin_longitude', 'checkin_address', 'checkout_date', 'checkout_time', 'checkout_latitude', 'checkout_longitude', 'checkout_address', 'beatscheduleid')->orderBy('checkin_date', 'desc')->orderBy('checkin_time', 'desc');
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'checkin_id' => isset($value['id']) ? $value['id'] : 0,
                        'customer_id' => isset($value['customer_id']) ? $value['customer_id'] : null,
                        'customer_name' => isset($value['customers']['name']) ? $value['customers']['name'] : $value['leads']['name'],
                        'customer_type' => isset($value['customers']['customertypes']['customertype_name']) ? $value['customers']['customertypes']['customertype_name'] : '',
                        'checkin_date' => isset($value['checkin_date']) ? $value['checkin_date'] : '',
                        'checkin_time' => isset($value['checkin_time']) ? $value['checkin_time'] : '',
                        'checkin_latitude' => isset($value['checkin_latitude']) ? $value['checkin_latitude'] : '',
                        'checkin_longitude' => isset($value['checkin_longitude']) ? $value['checkin_longitude'] : '',
                        'checkin_address' => isset($value['checkin_address']) ? $value['checkin_address'] : '',
                        'checkout_date' => isset($value['checkout_date']) ? $value['checkout_date'] : '',
                        'checkout_time' => isset($value['checkout_time']) ? $value['checkout_time'] : '',
                        'checkout_latitude' => isset($value['checkout_latitude']) ? $value['checkout_latitude'] : '',
                        'checkout_longitude' => isset($value['checkout_longitude']) ? $value['checkout_longitude'] : '',
                        'checkout_address' => isset($value['checkout_address']) ? $value['checkout_address'] : '',
                        'is_lead' => isset($value['customer_id']) ? 'No' : 'Yes',
                        'beat_schedule_id' => isset($value['beatscheduleid']) ? $value['beatscheduleid'] : 0,
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function submitCheckin(Request $request)
    {
        try {

            $user = $request->user();
            if ($user->active == 'N') {
                return response()->json(['status' => 'error', 'message' =>  'User Inactive'], 401);
            }
            $validator = Validator::make($request->all(), [
                'customer_id' => 'nullable|exists:customers,id',
                'checkin_latitude' => 'required',
                'checkin_longitude' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $distance = '';
            // if(!empty($request['checkin_latitude']) && !empty($request['checkin_longitude']))
            // {
            //     $distance = distance($request['checkin_latitude'] , $request['checkin_longitude'],$request['customer_id']);
            // }

            if (!empty($request['checkin_latitude']) && !empty($request['checkin_longitude'])) {
                $distance = distance($request['checkin_latitude'], $request['checkin_longitude'], $request['customer_id']);
            }


            if (empty($request['beatScheduleId'])) {
                $request['beatScheduleId'] = BeatSchedule::where('user_id', $user->id)
                    ->whereDate('beat_date', getcurentDate())
                    ->whereHas('beatcustomers', function ($query) use ($request) {
                        $query->where('customer_id', '=', $request['customer_id']);
                    })
                    ->pluck('id')->first();
            }

            // $request['checkin_address'] = getLatLongToAddress($request['checkin_latitude'], $request['checkin_longitude']);

            $request['checkin_address'] = getLatLongToAddress($request['checkin_latitude'], $request['checkin_longitude']);

            if ($checkin_id = $this->checkin->insertGetId([
                'active' => 'Y',
                'customer_id' => isset($request['customer_id']) ? $request['customer_id'] : null,
                'user_id' => $user->id,
                'checkin_date' => getcurentDate(),
                'checkin_time' => getcurentTime(),
                'checkin_latitude' => isset($request['checkin_latitude']) ? $request['checkin_latitude'] : '',
                'checkin_longitude' => isset($request['checkin_longitude']) ? $request['checkin_longitude'] : '',
                'checkin_address' => isset($request['checkin_address']) ? $request['checkin_address'] : '',
                'distance' => $distance,
                'beatscheduleid' => isset($request['beatScheduleId']) ? $request['beatScheduleId'] : null,
            ])) {
                // $customername = Customers::where('id','=',$request['customer_id'])->pluck('name')->first();
                // $useractivity = array(
                //         'userid' => $user->id, 
                //         'latitude' => $request['checkin_latitude'], 
                //         'longitude' => $request['checkin_longitude'], 
                //         'type' => 'Checkin',
                //         'description' => $user->name.' Checkin to '.$customername,
                //     );
                // submitUserActivity($useractivity);
                // $zsmnotify = collect([
                //     'title' => $user->name.' has Checked In',
                //     'body' =>  $user->name.' has Checked In at '.$customername
                // ]);
                // sendNotification($user->reportingid,$zsmnotify);
                // $asmnotify = collect([
                //     'title' => 'Successfully Checked in',
                //     'body' =>  'You have successfully Checked In at '.$customername
                // ]);
                // sendNotification($user->id,$asmnotify);
                $customername = Customers::with(['customertypes'])->where('id', '=', $request['customer_id'])->first();
                $cutomertype = $customername['customertypes']['customertype_name'] ?? '';

                return response()->json(['status' => 'success', 'message' => 'Check In successfully', 'checkin_id' => $checkin_id, 'customer_type' => $cutomertype], $this->successStatus);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in Check In'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function submitCheckout(Request $request)
    {
        try {

            $user = $request->user();
            if ($user->active == 'N') {
                return response()->json(['status' => 'error', 'message' =>  'User Inactive'], 401);
            }
            $validator = Validator::make($request->all(), [
                'checkin_id' => 'required|exists:check_in,id',
                'checkout_latitude' => 'required',
                'checkout_longitude' => 'required',
                'description' => 'required|string|max:1540',
                'customer_id' => 'required|exists:customers,id',
                'visit_type_id' => 'nullable|exists:visit_types,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $request['checkout_address'] = getLatLongToAddress($request['checkout_latitude'], $request['checkout_longitude']);
            $check_in = CheckIn::where('id', $request['checkin_id'])->first();
            if ($this->checkin->where('id', '=', $request['checkin_id'])->update([
                'checkout_date' => getcurentDate(),
                'checkout_time' => getcurentTime(),
                'checkout_latitude' => !empty($request['checkout_latitude']) ? $request['checkout_latitude'] : '',
                'checkout_longitude' => !empty($request['checkout_longitude']) ? $request['checkout_longitude'] : '',
                'checkout_address' => !empty($request['checkout_address']) ? $request['checkout_address'] : '',
                'time_interval' => gmdate("H:i:s", strtotime(getcurentDateTime()) - strtotime($check_in->checkin_date . ' ' . $check_in->checkin_time))
            ])) {
                VisitReport::insertGetId([
                    'checkin_id' => isset($request['checkin_id']) ? $request['checkin_id'] : null,
                    'user_id' => $user->id,
                    'customer_id' => isset($request['customer_id']) ? $request['customer_id'] : null,
                    'visit_type_id' => isset($request['visit_type_id']) ? $request['visit_type_id'] : null,
                    'description' => isset($request['description']) ? $request['description'] : '',
                    'visit_image' => '',
                    'created_by' => $user->id,
                    'next_visit' => isset($request['next_visit']) ? date('Y-m-d H:i:s', strtotime($request['next_visit'])) : null,
                    'created_at' => getcurentDateTime()
                ]);
                // $customerid = $this->checkin->where('id','=',$request['checkin_id'])->pluck('customer_id')->first();
                // $customername = Customers::where('id','=',$customerid)->pluck('name')->first();
                // $useractivity = array(
                //     'userid' => $user->id, 
                //     'latitude' => $request['checkout_latitude'], 
                //     'longitude' => $request['checkout_longitude'], 
                //     'type' => 'Checkout',
                //     'description' => $user->name.' Checkout to '.$customername,
                // );
                // submitUserActivity($useractivity);
                // $zsmnotify = collect([
                //     'title' => $user->name.' has Checked Out',
                //     'body' =>  $user->name.' has Checked out from '.$customername
                // ]);
                // sendNotification($user->reportingid,$zsmnotify);
                // $asmnotify = collect([
                //     'title' => 'Successfully Checked Out',
                //     'body' =>  'You have successfully Checked out from '.$customername
                // ]);
                // sendNotification($user->id,$asmnotify);
                $checkDraft = CheckInDraft::where('checkin_id', $request->checkin_id)->first();
                if($checkDraft){
                    $checkDraft->delete();
                }
                return response()->json(['status' => 'success', 'message' => 'Check Out successfully'], $this->successStatus);
            }
            return response()->json(['status' => 'error', 'message' => 'Please submit report then checkout'], 200);
            // if(VisitReport::where('checkin_id',$request->checkin_id)->exists())
            // {
            //     // $request['checkout_address'] = getLatLongToAddress($request['checkout_latitude'], $request['checkout_longitude']);
            //     if($this->checkin->where('id','=',$request['checkin_id'])->update([
            //         'checkout_date' => getcurentDate(),
            //         'checkout_time' => getcurentTime(),
            //         'checkout_latitude' => !empty($request['checkout_latitude']) ? $request['checkout_latitude'] :'',
            //         'checkout_longitude' => !empty($request['checkout_longitude']) ? $request['checkout_longitude'] :'',
            //         'checkout_address' => !empty($request['checkout_address']) ? $request['checkout_address'] :'',
            //     ]))
            //     {
            //         // $customerid = $this->checkin->where('id','=',$request['checkin_id'])->pluck('customer_id')->first();
            //         // $customername = Customers::where('id','=',$customerid)->pluck('name')->first();
            //         // $useractivity = array(
            //         //     'userid' => $user->id, 
            //         //     'latitude' => $request['checkout_latitude'], 
            //         //     'longitude' => $request['checkout_longitude'], 
            //         //     'type' => 'Checkout',
            //         //     'description' => $user->name.' Checkout to '.$customername,
            //         // );
            //         // submitUserActivity($useractivity);
            //         // $zsmnotify = collect([
            //         //     'title' => $user->name.' has Checked Out',
            //         //     'body' =>  $user->name.' has Checked out from '.$customername
            //         // ]);
            //         // sendNotification($user->reportingid,$zsmnotify);
            //         // $asmnotify = collect([
            //         //     'title' => 'Successfully Checked Out',
            //         //     'body' =>  'You have successfully Checked out from '.$customername
            //         // ]);
            //         // sendNotification($user->id,$asmnotify);
            //         return response()->json(['status' => 'success','message' => 'Check Out successfully'], $this->successStatus); 
            //     }
            //     return response()->json(['status' => 'error','message' => 'Error in Check Out' ], $this->badrequest);
            // }
            // return response()->json(['status' => 'error','message' => 'Please submit report then checkout' ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addCheckinDraft(Request $request)
    {
        try {

            $user = $request->user();
            if ($user->active == 'N') {
                return response()->json(['status' => 'error', 'message' =>  'User Inactive'], 401);
            }
            $validator = Validator::make($request->all(), [
                'checkin_id' => 'required|exists:check_in,id',
                'draft_msg' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $draft = CheckInDraft::updateOrCreate(['checkin_id' => $request->checkin_id], [
                'draft_msg' => $request->draft_msg,
            ]);

            return response()->json(['status' => 'success','data' => $draft, 'message' => 'Data save successfully'], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getCheckinDraft(Request $request)
    {
        try {

            $user = $request->user();
            if ($user->active == 'N') {
                return response()->json(['status' => 'error', 'message' =>  'User Inactive'], 401);
            }
            $validator = Validator::make($request->all(), [
                'checkin_id' => 'required|exists:check_in,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $checkDraft = CheckInDraft::where('checkin_id', $request->checkin_id)->first();

            return response()->json(['status' => 'success','data' => $checkDraft, 'message' => 'Draft retrieved successfully'], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
