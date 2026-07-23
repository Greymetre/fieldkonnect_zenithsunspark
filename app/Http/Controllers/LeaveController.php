<?php

namespace App\Http\Controllers;

use App\DataTables\LeaveDataTable;
use App\Models\Attendance;
use App\Models\CompOffLeave;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use DateTime;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LeaveDataTable $dataTable)
    {
        abort_if(Gate::denies('leave_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_details = User::with('getbranch')->whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $all_users = array();
        foreach ($all_user_details as $k => $val) {
            $users[$k]['id'] = $val->id;
            $users[$k]['name'] = $val->name;
        }
        return $dataTable->render('leave.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
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
                            return redirect()->back()->with('message_danger', 'No Comp Off Balance');
                        }
                    }
                } else {
                    $leave->delete();
                    foreach ($dates as $date) {
                        Attendance::where(['user_id' => $leave->user_id, 'punchin_date' => date('Y-m-d', strtotime($date))])->delete();
                    }
                    return redirect()->back()->with('message_danger', 'No Comp Off Balance');
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

            return Redirect::to('leaves')->with('message_success', 'Leave Added Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Leave $leave)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit(Leave $leave)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Leave $leave)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // try {
            $leave = Leave::find($id);
            $fromDate = new DateTime($leave->from_date);
            $toDate = new DateTime($leave->to_date);
            $dates = [];
            $currentDate = clone $fromDate;
            $days = 0;
            while ($currentDate <= $toDate) {
                $days++;
                $dates[] = $currentDate->format('Y-m-d');
                $currentDate->modify('+1 day');
            }

            foreach ($dates as $date) {
                Attendance::where(['user_id' => $leave->user_id, 'punchin_date' => date('Y-m-d', strtotime($date))])->delete();
            }

            if ($leave->type == 'First Half Leave' || $leave->type == 'Second Half Leave') {
                if ($leave->bal_type == 'Comp-off Balance') {
                    $compOffs = CompOffLeave::whereRaw("FIND_IN_SET(?, leave_id)", [$id])->get();

                    foreach ($compOffs as $compOff) {
                        $compOff->balance += 0.50;

                        $leaveIds = explode(',', $compOff->leave_id);
                        $leaveIds = array_filter($leaveIds, fn($ids) => $ids != $id);
                        $compOff->leave_id = implode(',', $leaveIds);
                        $compOff->is_used = false;
                        $compOff->save();
                    }
                }else {
                    $user = User::find($leave->user_id);
                    $user->leave_balance = $user->leave_balance + 0.50;
                    $user->save();
                }
            } elseif ($leave->type == 'Full Day Leave' || $leave->type == 'Leave') {
                if ($leave->bal_type == 'Comp-off Balance') {
                    $compOffs = CompOffLeave::whereRaw("FIND_IN_SET(?, leave_id)", [$id])->get();
                    foreach ($compOffs as $compOff) {
                        if ($compOff) {
                            $compOff->balance = $compOff->balance + 1.00;
                            $compOff->is_used = false;
                            $compOff->save();
                        }
                    }
                }else{
                    $user = User::find($leave->user_id);
                    $user->leave_balance = $user->leave_balance + $days;
                    $user->save();
                }
            }

            if ($leave->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Leave deleted successfully!']);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in Attendance Delete!']);
        // } catch (\Exception $e) {
        //     return redirect()->back()->withErrors($e->getMessage())->withInput();
        // }
    }

    public function approveLeave(Request $request)
    {
        try {
            if (Leave::where('id', '=', $request['id'])->update([
                'status' => 1,
                'remark_status' => null
            ])) {
                return redirect()->back()->with('message_success', 'Leave Approved Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Leave Approved')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }


    public function rejectLeave(Request $request)
    {
        $remark_status  = $request['remark_status'] ?? null;
        try {
            if (Leave::where('id', '=', $request['leave_id'])->update([
                'status' => 2,
                'remark_status' => $remark_status ?? null,
            ])) {
                return Redirect::to('leaves')->with('message_success', 'Leave Rejected Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Leave Rejected')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    // comboOffLeave 
    public function comboOffLeave(Request $request){
        $expiryDate = Carbon::parse($request['combo_off_date'])->addDays(60);
        $isSunday = Carbon::parse($request['combo_off_date'])->isSunday();
        if(!$isSunday){
            return redirect()->back()->with('message_danger', 'Combo of leave apply only on sunday.')->withInput();
        }
        $compOffLeave = CompOffLeave::where(['user_id' => $request->user_id , 'comp_off_date' => $request['combo_off_date']])->first();
        
        if(isset($compOffLeave)){
            return redirect()->back()->with('message_danger', 'This date has already been added as a comp-off date for this user.')->withInput();
        }
        CompOffLeave::create([
            'user_id' => $request['user_id'],
            'comp_off_date' => $request['combo_off_date'],
            'expiry_date' => $expiryDate,
            'is_used' => false,
        ]);
        return redirect()->route('leaves.index')->with('message_success', 'A comp-off date added for this user.')->withInput();
    }
}
