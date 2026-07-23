<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CompOffLeave;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use DateTime;
use Illuminate\Support\Facades\Log;

class LeaveController extends Controller
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

    public function addLeaves(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'from_date' => 'required|before_or_equal:to_date',
                'to_date' => 'required|after_or_equal:from_date',
                'type' => 'required',
                'bal_type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $fromDate = new DateTime($request->from_date);
            $toDate = new DateTime($request->to_date);

            $dates = [];
            $days = 0;
            $currentDate = clone $fromDate;
            while ($currentDate <= $toDate) {
                $days++;
                $dates[] = $currentDate->format('Y-m-d');
                $currentDate->modify('+1 day');
            }

            foreach ($dates as $date) {
                Attendance::updateOrCreate(['user_id' => $request['user_id'], 'punchin_date' => date('Y-m-d', strtotime($date))], [
                    'user_id' => $request['user_id'],
                    'active' => 'Y',
                    'punchin_date' => date('Y-m-d', strtotime($date)),
                    'punchin_time' => date('G:i', strtotime('10:00:00')),
                    'punchin_summary' => !empty($request['reason']) ? $request['reason'] : '',
                    'working_type' => !empty($request['type']) ? $request['type'] : '',
                    'punchin_from' => 'App',
                    'created_at' => getcurentDateTime(),
                    'updated_at' => getcurentDateTime(),
                ]);
            }

            $leave = Leave::create([
                'user_id' => $request['user_id'],
                'active' => 'Y',
                'from_date' => date('Y-m-d', strtotime($request['from_date'])),
                'to_date' => date('Y-m-d', strtotime($request['to_date'])),
                'reason' => !empty($request['reason']) ? $request['reason'] : '',
                'type' => !empty($request['type']) ? $request['type'] : '',
                'bal_type' => !empty($request['bal_type']) ? $request['bal_type'] : NULL,
                'created_by' => auth()->user()->id,
                'created_at' => getcurentDateTime(),
                'updated_at' => getcurentDateTime(),
            ]);

            if ($request['bal_type'] === 'Comp-off Balance') {
                if ($request['type'] == 'First Half Leave' || $request['type'] == 'Second Half Leave') {
                    $compOff = CompOffLeave::where('user_id', $request['user_id'])
                        ->where('is_used', false)
                        ->where('expiry_date', '>=', now())
                        ->first();
                } else {
                    $compOff = CompOffLeave::where('user_id', $request['user_id'])
                        ->where('is_used', false)
                        ->where('expiry_date', '>=', now())
                        ->where('balance', '>', 0.6)
                        ->get();
                }

                if ($compOff) {

                    if ($request['type'] == 'First Half Leave' || $request['type'] == 'Second Half Leave') {
                        $compOff->balance = $compOff->balance - 0.50;
                        if (!empty($compOff->leave_id)) {
                            $compOff->leave_id = $compOff->leave_id . ',' . $leave->id;
                        } else {
                            $compOff->leave_id = $leave->id;
                        }
                        $compOff->is_used = false;
                        $compOff->save();
                        if ($compOff->balance == 0.00) {
                            $compOff->update(['is_used' => true, 'balance' => 0.00]);
                        }
                    } else {
                        if ($compOff->count() >= $days) {
                            $compOff->take($days)->each(function ($comp) use ($leave) {
                                $comp->update([
                                    'is_used'  => true,
                                    'leave_id' => $leave->id,
                                    'balance'  => 0.00
                                ]);
                            });
                        } else {
                            $leave->delete();
                            foreach ($dates as $date) {
                                Attendance::where(['user_id' => $leave->user_id, 'punchin_date' => date('Y-m-d', strtotime($date))])->delete();
                            }
                            return response()->json(['status' => 'error', 'message' => 'No Comp Off Balance', 'data' => $leave], 200);
                        }
                    }
                } else {
                    $leave->delete();
                    foreach ($dates as $date) {
                        Attendance::where(['user_id' => $leave->user_id, 'punchin_date' => date('Y-m-d', strtotime($date))])->delete();
                    }
                    return response()->json(['status' => 'error', 'message' => 'No Comp Off Balance', 'data' => $leave], 200);
                }
            } else {
                if ($request['type'] == 'First Half Leave' || $request['type'] == 'Second Half Leave') {
                    $user = User::find($request['user_id']);
                    if($user->leave_balance >= 0.5) {
                        $user->leave_balance = $user->leave_balance - 0.5;
                    }else{
                        $user->leave_balance = 0;
                    }
                    $user->save();
                } elseif ($request['type'] == 'Full Day Leave' || $request['type'] == 'Leave') {
                    $user = User::find($request['user_id']);
                    if($user->leave_balance >= $days) {
                        $user->leave_balance = $user->leave_balance - $days;
                    }else {
                        $user->leave_balance = 0;
                    }
                    $user->save();
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Leave Added Successfully', 'data' => $leave], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getLeaves(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $data = Leave::with('users', 'createdbyname')->where('user_id', $request['user_id'])->get();
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
