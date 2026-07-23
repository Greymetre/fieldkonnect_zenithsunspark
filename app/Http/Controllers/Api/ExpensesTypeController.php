<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\ExpensesType;
use App\Models\Expenses;
use App\Models\ExpenseLog;
use App\Models\Media;
use App\Models\TourProgramme;
use App\Models\User;
use Validator;
use Auth;
use Carbon\Carbon;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpensesTypeController extends Controller
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


    public function getExpensesType(Request $request)
    {
        // $expenses_type = ExpensesType::all();
        // return response()->json(['status'=>'success', 'data'=>$expenses_type], 200); 

        try {

            $validator = Validator::make($request->all(), [
                'payroll_id'  => "required",
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $payroll_id = $request->payroll_id;
            $expense_types = ExpensesType::where('payroll_id', $payroll_id)->get();
            if (!empty($expense_types)) {

                $datas = array();
                foreach ($expense_types as $expense_type) {
                    $datas[] = array(
                        'id' => $expense_type->id ?? "",
                        'name' => $expense_type->name ?? "",
                        'rate' => $expense_type->rate ?? "",
                        'allowance_type_id' => $expense_type->allowance_type_id ?? "",
                        'payroll_id' => $expense_type->payroll_id ?? "",
                    );
                }

                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $datas], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $expense], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }



    public function createExpense(Request $request)
    {
        try {
            ini_set('memory_limit', '-1');

            $dates = Carbon::now();
            $current_date_time = $dates->setTimezone('Asia/Kolkata');

            $userid = $request->user()->id;
            $validator = Validator::make(
                $request->all(),
                [
                    // 'customer_id'   => 'nullable|exists:customers,id',
                    'expenses_type'  => "required",
                    'claim_amount'  => "required",
                    'date'  => "required",
                    'expense_file.*' => 'mimes:jpeg,jpg,png,pdf,doc,webp',
                ]
            );


            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }


            if ($expenses = Expenses::create([
                'user_id' => $userid,
                'expenses_type' => isset($request->expenses_type) ? $request->expenses_type : null,
                'date' => isset($request->date) ? $request->date : null,
                'claim_amount' => isset($request->claim_amount) ? $request->claim_amount : null,
                'start_km' => isset($request->start_km) ? $request->start_km : null,
                'stop_km' => isset($request->stop_km) ? $request->stop_km : null,
                'total_km' => isset($request->total_km) ? $request->total_km : null,
                'note' => isset($request->note) ? $request->note : null,
                'created_by' => $userid,
                'created_at' => $current_date_time
            ])) {

                $logdata = array(
                    'log_date' => date('Y-m-d'),
                    'expense_id' => $expenses->id,
                    'created_by' => $userid,
                    'status_type' => 'generated',
                    'created_at' => $current_date_time
                );

                ExpenseLog::create($logdata);
                if ($request->hasFile('expense_file')) {
                    $files = $request->file('expense_file');
                    foreach ($files as $file) {
                        $customname = time() . '.' . $file->getClientOriginalExtension();
                        $expenses->addMedia($file)
                            ->usingFileName($customname)
                            ->toMediaCollection('expense_file');
                    }
                }

                return response()->json(['status' => 'success', 'message' => 'Data inserted successfully.', 'data' => $expenses], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'Error in No Record Found.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }


    public function expenseListing(Request $request)
    {
        try {
            $payroll_id = $request->payroll_id ?? '';
            $userids = getUsersReportingToAuth();
            $pageSize = $request->input('pageSize');
            // $query = Expenses::with('media','expense_type')->whereIn('user_id',$userids)->orderBy('id','desc');
            $query = Expenses::with('media', 'expense_type')->where(['user_id' => Auth::Id()])->orderBy('id', 'desc');
            $expence_types = ExpensesType::where('payroll_id', $payroll_id)->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            })->toArray();
            //$expenses = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            if (!empty($request['start_date']) && !empty($request['end_date'])) {
                $query->whereBetween('date', [$request['start_date'], $request['end_date']]);
            }
            if (!empty($request['expenses_type'])) {
                $query->where('expenses_type',$request['expenses_type']);
            }
            if ((!empty($request['status']) || $request['status'] == 0) && $request['status'] != null) {
                $query->where('checker_status',$request['status']);
            }
            $all_status = [['id' => '0', 'name' => 'Pending'], ['id' => '1', 'name' => 'Approved'], ['id' => '2', 'name' => 'Rejected'], ['id' => '3', 'name' => 'Checked'] ,['id' => '4', 'name' => 'Checked By Reporting']];
            $expenses = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->paginate(100);
            $datas = array();
            if ($expenses->isNotEmpty()) {

               
                foreach ($expenses as $expense) {

                    // $image = '';  
                    // if(isset($expense) && $expense->getMedia('expense_file')->count() > 0 && file_exists($expense->getFirstMedia('expense_file')->getPath())){
                    //  $image = $expense->getFirstMedia('expense_file')->getFullUrl();
                    // }

                    $image = array();

                    if (isset($expense) && $expense->getMedia('expense_file')->count() > 0 && Storage::disk('s3')->exists($expense->getFirstMedia('expense_file')->getPath())) {
                        foreach ($expense->getMedia('expense_file') as $expense_image) {
                            $image[] = $expense_image->getFullUrl();
                        }
                    }


                    if ($expense->checker_status == '1') {
                        $exp_status = 'Approved';
                    } elseif ($expense->checker_status == '2') {
                        $exp_status = 'Rejected';
                    } elseif ($expense->checker_status == '3') {
                        $exp_status = 'Checked';
                    } elseif ($expense->checker_status == '4') {
                        $exp_status = 'Checked By Reporting';
                    } else {
                        $exp_status = 'Pending';
                    }


                    $datas[] = array(
                        'id' => $expense->id ?? "",
                        'expenses_type' => $expense->expenses_type ?? "",
                        'expenses_type_name' => $expense->expense_type->name ?? "",
                        'user_id' => $expense->user_id ?? "",
                        'date' => date("d-m-Y", strtotime($expense->date)),
                        'note' => $expense->note ?? "",
                        'start_km' => $expense->start_km ?? "",
                        'stop_km' => $expense->stop_km ?? "",
                        'total_km' => $expense->total_km ?? "",
                        'claim_amount' => $expense->claim_amount ?? NULL,
                        'approve_amount' => $expense->approve_amount ?? "",
                        'status' => $exp_status,
                        // 'claim_amount' => '$'.number_format($expense->claim_amount ?? 0,2),
                        //'expense_image' =>  $expense->getFirstMedia('expense_file')->getFullUrl(),
                        'expense_image' =>  $image,

                    );
                }

                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $datas , 'expence_types' => $expence_types , 'all_status' => $all_status], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $datas , 'expence_types' => $expence_types , 'all_status' => $all_status], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function expenseDetails(Request $request)
    {
        try {
            $userid = $request->user()->id;
            $validator = Validator::make($request->all(), [
                'expense_id'  => "required",
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $expense_id = $request->expense_id;
            $expense = Expenses::with('media', 'expense_type')->where('id', $expense_id)->first();
            if (!empty($expense)) {

                $image = array();
                $image_id = array();
                if (isset($expense) && $expense->getMedia('expense_file')->count() > 0 && Storage::disk('s3')->exists($expense->getFirstMedia('expense_file')->getPath())) {
                    foreach ($expense->getMedia('expense_file') as $expense_image) {
                        $image[] = $expense_image->getFullUrl();
                        $image_id[] = $expense_image->id;
                    }
                }


                if ($expense->checker_status == '1') {
                    $exp_status = 'Approved';
                } elseif ($expense->checker_status == '2') {
                    $exp_status = 'Rejected';
                } elseif ($expense->checker_status == '3') {
                    $exp_status = 'Checked';
                } elseif ($expense->checker_status == '4') {
                    $exp_status = 'Checked By Reporting';
                } else {
                    $exp_status = 'Pending';
                }

                // $datas[] = array(
                // 'id' => $expense->id ?? "",
                // 'expenses_type' => $expense->expenses_type ?? "",
                // 'expenses_type_name' => $expense->expense_type->name?? "",
                // 'user_id' => $expense->user_id ?? "",
                // 'date' => date("d-m-Y", strtotime($expense->date)),
                // 'note' => $expense->note ?? "",
                // 'start_km' => $expense->start_km ?? "",
                // 'stop_km' => $expense->stop_km ?? "",
                // 'total_km' => $expense->total_km ?? "",
                // 'claim_amount' => $expense->claim_amount ?? "",
                // 'approve_amount' => $expense->approve_amount ?? "",
                // 'status' => $exp_status,
                // 'expense_image' =>  $image,
                //    );

                $datas = array();
                $datas['id'] = $expense->id ?? "";
                $datas['expenses_type'] = $expense->expenses_type ?? "";
                $datas['expenses_type_name'] = $expense->expense_type->name ?? "";
                $datas['rate'] = $expense->expense_type->rate ?? "";
                $datas['allowance_type_id'] = $expense->expense_type->allowance_type_id ?? "";
                $datas['user_id'] = $expense->user_id ?? "";
                $datas['user_name'] = $expense->users->name ?? "";
                $datas['date'] = date("d-m-Y", strtotime($expense->date));
                $datas['note'] = $expense->note ?? "";
                $datas['start_km'] = $expense->start_km ?? "";
                $datas['stop_km'] = $expense->stop_km ?? "";
                $datas['total_km'] = $expense->total_km ?? "";
                $datas['claim_amount'] = $expense->claim_amount ?? "";
                $datas['expense_image'] = $image;
                $datas['image_id'] = $image_id;

                $datas['approve_amount'] = $expense->approve_amount ?? "";
                $datas['status'] = $exp_status;
                $datas['reason'] = $expense->reason ?? "";

                $plan = TourProgramme::where('userid', $expense->user_id)->where('date', $expense->date)->first();
                $total_visit = count(CheckIn::where('user_id', $expense->user_id)->where('checkin_date', $expense->date)->groupBy('customer_id')->get());

                $checkins = CheckIn::where('user_id', $expense->user_id)
                    ->where('checkin_date', $expense->date)
                    ->orderBy('checkin_time', 'asc')
                    ->get();
                $total_dis = 0;                
                foreach ($checkins as $checkin) {
                    if (!empty($checkin->checkin_latitude) && !empty($checkin->checkin_longitude) && !empty($checkin->checkout_latitude) && !empty($checkin->checkout_longitude)) {
                        $total_dis += haversineGreatCircleDistance($checkin->checkin_latitude, $checkin->checkin_longitude, $checkin->checkout_latitude, $checkin->checkout_longitude);
                    }
                }

                $datas['plan'] = $plan;
                $datas['total_visit'] = (string)$total_visit;
                $datas['total_dis'] = (string)number_format($total_dis, '2');


                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $datas], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $expense], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }


    public function updateExpense(Request $request)
    {
        try {
            ini_set('memory_limit', '-1');

            $dates = Carbon::now();
            $current_date_time = $dates->setTimezone('Asia/Kolkata');

            $userid = $request->user()->id;
            $validator = Validator::make(
                $request->all(),
                [
                    'expense_id'  => "required",
                    'expense_file.*' => 'mimes:jpeg,jpg,png,pdf,doc,webp',
                ]
            );
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $expense_detail = Expenses::where('id', $request['expense_id'])->first();
            if ($expenses = Expenses::where('id', $request['expense_id'])->update([
                'user_id' => $userid,
                'expenses_type' => isset($request['expenses_type']) ? $request['expenses_type'] : $expense_detail->expenses_type,
                //'date' => isset($request['date'])? $request['date']:$expense_detail->date,
                'note' => isset($request['note']) ? $request['note'] : $expense_detail->note,
                'start_km' => isset($request['start_km']) ? $request['start_km'] : $expense_detail->start_km,
                'stop_km' => isset($request['stop_km']) ? $request['stop_km'] : $expense_detail->stop_km,
                'total_km' => isset($request['total_km']) ? $request['total_km'] : $expense_detail->total_km,
                'claim_amount' => isset($request['claim_amount']) ? $request['claim_amount'] : $expense_detail->claim_amount,
                'created_at' => $current_date_time
            ])) {

                $logdata = array(
                    'log_date' => date('Y-m-d'),
                    'expense_id' => $request['expense_id'],
                    'created_by' => $userid,
                    'status_type' => 'updated',
                    'created_at' => $current_date_time
                );

                ExpenseLog::create($logdata);

                if ($request->hasFile('expense_file')) {
                    $files = $request->file('expense_file');
                    foreach ($files as $file) {
                        $customname = time() . '.' . $file->getClientOriginalExtension();
                        $expense_detail->addMedia($file)
                            ->usingFileName($customname)
                            ->toMediaCollection('expense_file');
                    }
                }


                if (!empty($request->image_id)) {
                    Media::where('id', $request->image_id)->delete();
                }

                return response()->json(['status' => 'success', 'message' => 'Data updated successfully.'], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Updated.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function allExpenseListing(Request $request)
    {
        try {

            $user = $request->user();
            $user_id = $user->id;
            $userids = getUsersReportingToAuth($user_id);
            $search_branches = $request->input('search_branches');
            if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
                $userids = User::whereIn('id', $userids)->whereIn('branch_id', $search_branches)->pluck('id')->toArray();
            }
            $pageSize = $request->input('pageSize');
            $query = Expenses::with('media', 'expense_type', 'users')->orderBy('id', 'desc');
            // $query = Expenses::with('media','expense_type')->where(['user_id'=>Auth::Id()])->orderBy('id','desc');
            //$expenses = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            if (!empty($request['start_date']) && !empty($request['end_date'])) {
                $query->whereBetween('date', [$request['start_date'], $request['end_date']]);
            }
            if ((!empty($request['status']) || $request['status'] == 0) && $request['status'] != null) {
                $query->where('checker_status',$request['status']);
            }
            if (!empty($request['user_id'])) {
                $query->where('user_id', $request['user_id']);
            } else {
                $query->whereIn('user_id', $userids);
            }
            // dd($query->toSql());
            $expenses = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->paginate(100);

            $all_status = [['id' => '0', 'name' => 'Pending'], ['id' => '1', 'name' => 'Approved'], ['id' => '2', 'name' => 'Rejected'], ['id' => '3', 'name' => 'Checked'] ,['id' => '4', 'name' => 'Checked By Reporting']];
            $datas = array();
            $all_user_branches = User::with('getbranch')->whereDoesntHave('roles', function ($query) {
                $query->where('id', 29);
            })->whereIn('id', getUsersReportingToAuth($user_id))->orderBy('branch_id')->get();
            $branches = array();
            $all_branch = array();
            $bkey = 0;
            foreach ($all_user_branches as $k => $val) {
                if ($val->getbranch) {
                    if (!in_array($val->getbranch->id, $all_branch)) {
                        array_push($all_branch, $val->getbranch->id);
                        $branches[$bkey]['id'] = $val->getbranch->id;
                        $branches[$bkey]['name'] = $val->getbranch->branch_name;
                        $bkey++;
                    }
                }
            }

            $all_user_details = User::with('getbranch')->whereDoesntHave('roles', function ($query) {
                $query->where('id', 29);
            })->whereIn('id', $userids)->orderBy('name', 'asc')->get();
            $all_users = array();
            foreach ($all_user_details as $k => $val) {
                $all_users[$k]['id'] = $val->id;
                $all_users[$k]['name'] = $val->name;
            }
            if ($expenses->isNotEmpty()) {

               
                foreach ($expenses as $expense) {
                    // $image = '';  
                    // if(isset($expense) && $expense->getMedia('expense_file')->count() > 0 && file_exists($expense->getFirstMedia('expense_file')->getPath())){
                    //  $image = $expense->getFirstMedia('expense_file')->getFullUrl();
                    // }

                    $image = array();

                    if (isset($expense) && $expense->getMedia('expense_file')->count() > 0 && Storage::disk('s3')->exists($expense->getFirstMedia('expense_file')->getPath())) {
                        foreach ($expense->getMedia('expense_file') as $expense_image) {
                            $image[] = $expense_image->getFullUrl();
                        }
                    }


                    if ($expense->checker_status == '1') {
                        $exp_status = 'Approved';
                    } elseif ($expense->checker_status == '2') {
                        $exp_status = 'Rejected';
                    } elseif ($expense->checker_status == '3') {
                        $exp_status = 'Checked';
                    } elseif ($expense->checker_status == '4') {
                        $exp_status = 'Checked By Reporting';
                    } else {
                        $exp_status = 'Pending';
                    }


                    $datas[] = array(
                        'id' => $expense->id ?? "",
                        'expenses_type' => $expense->expenses_type ?? "",
                        'expenses_type_name' => $expense->expense_type->name ?? "",
                        'user_id' => $expense->user_id ?? "",
                        'date' => date("d-m-Y", strtotime($expense->date)),
                        'note' => $expense->note ?? "",
                        'start_km' => $expense->start_km ?? "",
                        'stop_km' => $expense->stop_km ?? "",
                        'total_km' => $expense->total_km ?? "",
                        'claim_amount' => $expense->claim_amount ?? "",
                        'approve_amount' => $expense->approve_amount ?? "",
                        'status' => $exp_status,
                        // 'claim_amount' => '$'.number_format($expense->claim_amount ?? 0,2),
                        //'expense_image' =>  $expense->getFirstMedia('expense_file')->getFullUrl(),
                        'expense_image' =>  $image,
                        'user_name' =>  $expense->users->name,
                        'self' =>  $expense->is_self,

                    );
                }

               

                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'users' => $all_users, 'branches' => $branches,'all_status'=> $all_status ,'data' => $datas], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.',  'users' => $all_users, 'branches' => $branches,'all_status'=> $all_status ,'data' => $datas , 'dummy' =>$request['status']], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function approveExpense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_id'  => "required",
            'approve_amnt'  => "required",
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }
        $dates = Carbon::now();
        $current_date_time = $dates->setTimezone('Asia/Kolkata');
        $expense_detail = Expenses::where('id', $request->expense_id)->first();
        $approve_amnt = $request->approve_amnt;
        $expense_id = $request->expense_id;
        $reason = $request->reasons ?? NULL;

        if ($expense_detail->claim_amount < $approve_amnt) {
            return response()->json(['status' => 'error', 'message' => 'Approve amount greater than to claim amount']);
        }

        Expenses::where('id', $expense_id)->update(['reason' => $reason, 'checker_status' => '4', 'approve_reject_by' => Auth::user()->id, 'approve_amount' => $approve_amnt]);

        if ($expense_id) {
            $logdata = array(
                'log_date' => date('Y-m-d'),
                'expense_id' => $expense_id,
                'created_by' => Auth::user()->id,
                'status_type' => 'approved'
            );
            ExpenseLog::create($logdata);
        }

        return response()->json(['status' => 'success', 'message' => 'Approve amount.']);
    }

    public function rejectExpense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_id'  => "required",
            'reasons'  => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }
        $dates = Carbon::now();
        $current_date_time = $dates->setTimezone('Asia/Kolkata');
        $expense_id = $request->expense_id;
        $reason = $request->reason ?? NULL;
        Expenses::where('id', $expense_id)->update(['reason' => $reason, 'checker_status' => '2', 'approve_reject_by' => Auth::user()->id, 'approve_amount' => NULL]);
        //return redirect(route('expenses.index')); 

        if ($expense_id) {
            $logdata = array(
                'log_date' => date('Y-m-d'),
                'expense_id' => $expense_id,
                'created_by' => Auth::user()->id,
                'status_type' => 'rejected'
            );
            ExpenseLog::create($logdata);
        }
        return response()->json(['status' => 'success', 'message' => 'Expense reject successfully']);
    }
}
