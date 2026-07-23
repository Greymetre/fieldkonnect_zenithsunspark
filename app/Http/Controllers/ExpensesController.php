<?php

namespace App\Http\Controllers;

use App\DataTables\ExpensesDataTable;
use App\Http\Requests\StoreExpensesRequest;
use App\Http\Requests\UpdateExpensesRequest;
use App\Models\Expenses;
use App\Models\User;
use App\Models\ExpensesType;
use App\Models\ExpenseLog;
use App\Models\Media;
use Illuminate\Http\Request;
use Carbon\Carbon;

use DataTables;
use Validator;
use DB;
use Auth;
use Excel;
use App\Exports\ExcelExport;
use App\Models\Attendance;
use App\Models\CheckIn;
use App\Models\Customers;
use App\Models\TourProgramme;
use App\Models\UserLiveLocation;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ExpensesDataTable $dataTable, Request $request)
    {
        $userids = getUsersReportingToAuth();

        if ($request->executive_id && !empty(session('executive_id'))) {
            $request->session()->put('executive_id', $request->executive_id);
        }
        $all_user_branches = User::with('getbranch')->whereIn('id', $userids)->orderBy('branch_id')->get();
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



        $all_user_divisions = User::with('getdivision')->whereIn('id', $userids)->orderBy('branch_id')->get();
        $divisions = array();
        $all_division = array();
        $dkey = 0;


        foreach ($all_user_divisions as $dv => $div_val) {
            if ($div_val->getdivision) {
                if (!in_array($div_val->getdivision->id, $all_division)) {
                    array_push($all_division, $div_val->getdivision->id);
                    $divisions[$dkey]['id'] = $div_val->getdivision->id;
                    $divisions[$dkey]['name'] = $div_val->getdivision->division_name;
                    $dkey++;
                }
            }
        }



        $pending_count = Expenses::where('checker_status', '0')->count();
        $approve_count = Expenses::where('checker_status', '1')->count();
        $reject_count = Expenses::where('checker_status', '2')->count();
        $checked_count = Expenses::where('checker_status', '3')->count();

        $pay_rolls = Config('constants.pay_roll');

        return $dataTable->render('expenses.index', compact('branches', 'pay_rolls', 'divisions', 'pending_count', 'approve_count', 'reject_count', 'checked_count'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userids = getUsersReportingToAuth();

        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Sub_Admin') || Auth::user()->hasRole('HR_Admin') || Auth::user()->hasRole('HO_Account')) {

            $users = User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 29);
            })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Sub_Admin') && !Auth::user()->hasRole('HR_Admin') && !Auth::user()->hasRole('HO_Account')) {
                    $query->whereIn('id', $userids);
                }
            })->select('id', 'name')->get();
        } else {
            $users = User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 29);
            })->where('active', '=', 'Y')->where('id', Auth::user()->id)->select('id', 'name')->get();
        }

        $expensestypes = ExpensesType::get();

        return view('expenses.create', compact('users', 'expensestypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreExpensesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dates = Carbon::now();
        $current_date_time = $dates->setTimezone('Asia/Kolkata');

        $subdays = Carbon::now()->subDays(1)->format('Y-m-d');
        $adddays = Carbon::now()->addDays(1)->format('Y-m-d');
        $current_date = Carbon::now()->format('Y-m-d');

        $rules = [
            'expenses_type'    => 'required',
            'user_id'          => 'required',
            'claim_amount'    => 'required',
            'date'            => 'required',
        ];


        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $data = $request->all();

            if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Sub_Admin') || Auth::user()->hasRole('HR_Admin') || Auth::user()->hasRole('HO_Account')) {

                $data = array(
                    'expenses_type' => $request->expenses_type ?? NULL,
                    'user_id' => $request->user_id ?? NULL,
                    'date' => $request->date ?? NULL,
                    'claim_amount' => $request->claim_amount ?? NULL,
                    'start_km' => $request->start_km ?? NULL,
                    'stop_km' => $request->stop_km ?? NULL,
                    'total_km' => $request->total_km ?? NULL,
                    'note' => $request->note ?? NULL,
                    'created_by' => Auth::user()->id ?? NULL,
                    'created_at' => $current_date_time
                );

                $expenses = Expenses::create($data);
                if ($expenses) {
                    $logdata = array(
                        'log_date' => date('Y-m-d'),
                        'expense_id' => $expenses->id,
                        'created_by' => Auth::user()->id,
                        'status_type' => 'generated',
                        'created_at' => $current_date_time
                    );
                    ExpenseLog::create($logdata);
                }

                if ($request->hasFile('expense_file')) {

                    $files = $request->file('expense_file');
                    foreach ($files as $file) {
                        $customname = time() . '.' . $file->getClientOriginalExtension();

                        $expenses->addMedia($file)
                            ->usingFileName($customname)
                            ->toMediaCollection('expense_file', 's3');
                    }
                }
            } else {

                $endter_date = $request->date;
                if ($endter_date == $current_date || $endter_date == $subdays) {

                    $data = array(
                        'expenses_type' => $request->expenses_type ?? NULL,
                        'user_id' => $request->user_id ?? NULL,
                        'date' => $request->date ?? NULL,
                        'claim_amount' => $request->claim_amount ?? NULL,
                        'start_km' => $request->start_km ?? NULL,
                        'stop_km' => $request->stop_km ?? NULL,
                        'total_km' => $request->total_km ?? NULL,
                        'note' => $request->note ?? NULL,
                        'created_by' => Auth::user()->id ?? NULL,
                        'created_at' => $current_date_time
                    );

                    $expenses = Expenses::create($data);
                    if ($expenses) {
                        $logdata = array(
                            'log_date' => date('Y-m-d'),
                            'expense_id' => $expenses->id,
                            'created_by' => Auth::user()->id,
                            'status_type' => 'generated',
                            'created_at' => $current_date_time
                        );
                        ExpenseLog::create($logdata);
                    }

                    if ($request->hasFile('expense_file')) {

                        $files = $request->file('expense_file');
                        foreach ($files as $file) {
                            $customname = time() . '.' . $file->getClientOriginalExtension();
                            $expenses->addMedia($file)
                                ->usingFileName($customname)
                                ->toMediaCollection('expense_file', 's3');
                        }
                    }
                } else {

                    return redirect()->back()->withErrors('enter current date or 1 day before')->withInput();
                }
            }

            return redirect(route('expenses.index'))->with('message', 'expense added successfully');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function show(Expenses $expense, Request $request)
    {

        $exce_session = session('executive_id');
        if (!empty($exce_session) && $exce_session != $expense->user_id) {
            $request->session()->put('executive_id', $expense->user_id);
        }

        $paln = TourProgramme::where('userid', $expense->user_id)->where('date', $expense->date)->first();
        $total_visit = count(CheckIn::where('user_id', $expense->user_id)->where('checkin_date', $expense->date)->groupBy('customer_id')->get());

        $checkins = CheckIn::where('user_id', $expense->user_id)
            ->where('checkin_date', $expense->date)
            ->orderBy('checkin_time', 'asc')
            ->get();
        $total_dis = 0;
        foreach ($checkins as $k => $checkin) {
            if ($k <= (count($checkins) - 2)) {
                if (!empty($checkin->checkin_latitude) && !empty($checkin->checkin_longitude) && !empty($checkin->checkout_latitude) && !empty($checkin->checkout_longitude)) {
                    $total_dis += haversineGreatCircleDistance($checkin->checkin_latitude, $checkin->checkin_longitude, $checkins[$k + 1]->checkin_latitude, $checkins[$k + 1]->checkin_longitude,);
                }
            }
        }

        $logdetails = ExpenseLog::with('logusers')->where('expense_id', $expense->id)->orderBy('id', 'desc')->get();
        //$expense->update(['accountant_status'=>'3','checker_status'=>'3']);
        return view('expenses.show', compact('expense', 'logdetails', 'paln', 'total_visit', 'total_dis'))->render();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function edit(Expenses $expense)
    {
        $userids = getUsersReportingToAuth();
        // $users= User::where('active','=','Y')->where(function($query) use($userids){
        //                     if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
        //                     {
        //                         $query->whereIn('id',$userids);
        //                     }
        //                     })->select('id','name')->get();

        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Sub_Admin') || Auth::user()->hasRole('HR_Admin') || Auth::user()->hasRole('HO_Account')) {

            $users = User::where('active', '=', 'Y')->where(function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Sub_Admin') && !Auth::user()->hasRole('HR_Admin') && !Auth::user()->hasRole('HO_Account')) {
                    $query->whereIn('id', $userids);
                }
            })->select('id', 'name')->get();
        } else {
            $users = User::where('active', '=', 'Y')->where('id', Auth::user()->id)->select('id', 'name')->get();
        }

        $expensestypes = ExpensesType::get();
        return view('expenses.edit', compact('users', 'expensestypes', 'expense'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExpensesRequest  $request
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expenses $expense)
    {
        $dates = Carbon::now();
        $current_date_time = $dates->setTimezone('Asia/Kolkata');

        $rules = [
            'expenses_type'    => 'required',
            'user_id'          => 'required',
            'claim_amount'    => 'required',
            'date'            => 'required',
        ];


        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $data = $request->all();



            $expense_details = ExpensesType::where('id', $request->expenses_type)->first();
            $expense_details->allowance_type_id;


            if ($expense_details->allowance_type_id == '1') {

                $data = array(
                    'expenses_type' => $request->expenses_type ?? NULL,
                    'user_id' => $request->user_id ?? NULL,
                    'date' => $request->date ?? NULL,
                    'claim_amount' => $request->claim_amount ?? NULL,
                    'start_km' => $request->start_km ?? NULL,
                    'stop_km' => $request->stop_km ?? NULL,
                    'total_km' => $request->total_km ?? NULL,
                    'reason' => $request->reason ?? NULL,
                    'note' => $request->note ?? NULL,
                    'approve_amount' => $request->approve_amount ?? NULL,
                    'created_by' => Auth::user()->id ?? NULL

                );
                $expense->update($data);
            } else {

                $data = array(
                    'expenses_type' => $request->expenses_type ?? NULL,
                    'user_id' => $request->user_id ?? NULL,
                    'date' => $request->date ?? NULL,
                    'claim_amount' => $request->claim_amount ?? NULL,
                    'start_km' => NULL,
                    'stop_km' => NULL,
                    'total_km' => NULL,
                    'note' => $request->note ?? NULL,
                    'approve_amount' => $request->approve_amount ?? NULL,
                    'reason' => $request->reason ?? NULL,
                    'created_by' => Auth::user()->id ?? NULL
                );
                $expense->update($data);
            }

            if ($expense) {
                $logdata = array(
                    'log_date' => date('Y-m-d'),
                    'expense_id' => $expense->id,
                    'created_by' => Auth::user()->id,
                    'status_type' => 'updated'
                );
                ExpenseLog::create($logdata);
            }


            if ($request->hasFile('expense_file')) {

                // $expense->clearMediaCollection('expense_file');

                $files = $request->file('expense_file');
                foreach ($files as $file) {
                    $customname = time() . '.' . $file->getClientOriginalExtension();
                    $expense->addMedia($file)
                        ->usingFileName($customname)
                        ->toMediaCollection('expense_file', 's3');
                }
            }
            return redirect(route('expenses.index'))->with('message', 'expense updated successfully');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }


    public function expenseDownload(Request $request)
    {

        $filename = 'expense-report.xlsx';
        $executive_id = $request->executive_id;
        $expenses_type = $request->expenses_type;
        $branch_id = $request->branch_id;
        $status = $request->status;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $payroll = $request->payroll;
        $division_id = $request->division_id;
        $expense_id = $request->expense_id;

        $expenses = Expenses::with(['expense_type', 'users', 'approve_reject', 'get_time_history']);

        if (!empty($payroll)) {
            $userid = User::where('payroll', $payroll)->pluck('id');
            $expenses = $expenses->whereIn('user_id', $userid);
        }
        if (!empty($executive_id)) {
            $expenses = $expenses->where(['user_id' => $executive_id]);
        }

        if (!empty($expenses_type)) {
            $expenses = $expenses->where(['expenses_type' => $expenses_type]);
        }

        if (!empty($branch_id)) {
            $branch_user_id = User::where('branch_id', $branch_id)->pluck('id');
            if (!empty($branch_user_id)) {
                $expenses->whereIn('user_id', $branch_user_id);
            }
        }

        if (!empty($start_date) && !empty($end_date)) {
            $expenses->whereBetween('date', [$start_date, $end_date]);
        }

        if (!empty($status)) {
            $expenses->where('checker_status', $status);
        }

        if (!empty($division_id)) {
            $division_user_id = User::where('division_id', $division_id)->pluck('id');
            if (!empty($division_user_id)) {
                $expenses->whereIn('user_id', $division_user_id);
            }
        }

        if (!empty($expense_id)) {
            $expenses->where('id', $expense_id);
        }






        $expenses = $expenses->with('get_time_history')->orderBy('id', 'desc')->get();

        $data = $expenses->map(function ($item, $key) {

            // if ($item->checker_status == '1') {
            //     $status = "Approved";
            // } elseif ($item->checker_status == '2') {
            //     $status = "Rejected";
            // } else {
            //     $status = "Pending";
            // }
            $status = "Pending";
            if ($item->checker_status == '1') {
                $status = "Approved";
            } elseif ($item->checker_status == '2') {
                $status = "Rejected";
            } elseif ($item->checker_status == '3') {
                $status = "Checked";
            } elseif ($item->checker_status == '4') {
                $status = "Checked By Reporting";
            } elseif ($item->checker_status == '5') {
                $status = "Hold";
            } elseif ($item->checker_status == '0') {
                $status = "Pending";
            }


            $checke_by = array();
            $approved_by = array();
            $rejected_by = array();
            $cd_date = '-';
            $ap_date = '-';
            $rj_date = '-';
            if (!empty($item->get_time_history)) {
                foreach ($item->get_time_history as $key_new => $datas) {

                    if ($datas->status_type == 'checked' || $datas->status_type == 'Checked') {
                        $checke_by[0] = $datas->logusers->name ?? '';
                        $cd_date =  date("d/m/Y", strtotime($datas->created_at));
                    }

                    if ($datas->status_type == 'approved' || $datas->status_type == 'Approved') {
                        $approved_by[0] = $datas->logusers->name ?? '';
                        $ap_date =  date("d/m/Y", strtotime($datas->created_at));
                    }

                    if ($datas->status_type == 'rejected' || $datas->status_type == 'Rejected') {
                        $rejected_by[0] = $datas->logusers->name ?? '';
                        $rj_date =  date("d/m/Y", strtotime($datas->created_at));
                    }
                }
            }

            $genrate = $item->get_time_history->where('status_type', 'generated')->first();
            if ($genrate) {
                $cd_at =  date("d/m/Y", strtotime($genrate->created_at));
            } else {
                $cd_at =  date("d/m/Y", strtotime($item->created_at));
            }



            return [

                $item->id ?? "",
                isset($item->date) ? date("d-m-Y", strtotime($item->date)) : '',
                $item->users->employee_codes ?? "",
                $item->users->name ?? "",
                $item->users->getdesignation->designation_name ?? '',
                $item->users->getbranch->branch_name ?? '',
                $item->users->getdivision->division_name ?? '',
                $item->expense_type->name ?? "",
                ($item->expense_type->rate && $item->expense_type->rate > 0) ? $item->expense_type->rate : "0",
                ($item->claim_amount && $item->claim_amount > 0) ? $item->claim_amount : "0",
                ($item->approve_amount && $item->approve_amount > 0) ? $item->approve_amount : "0",
                $item->note ?? "",
                $item->total_km ?? "",
                $item->reason ?? "",
                $status,
                implode(',', $checke_by),
                implode(',', $approved_by),
                $cd_at,
                $cd_date,
                $ap_date,
                $rj_date,
                implode(',', $rejected_by),
            ];
        })->toArray();

        $export = new ExcelExport([
            '#Expense Id',
            'Expense Date',
            'Emp Code',
            'User Name',
            'Designation',
            'Branch',
            'Division',
            'Expense Type',
            'Rate',
            'Claim Amount',
            'Approve Amount',
            'Note',
            'Total km',
            'Reason',
            'Expense Status',
            // 'Status BY'
            'Checked By Name',
            'Approved BY Name',
            'Created At',
            'Checked Date',
            'Approved Date',
            'Rejected Date',
            'Rejected By Name',
        ], $data);

        return Excel::download($export, $filename);
    }


    public function rejectExpense(Request $request)
    {
        $rules = [
            'reason'          => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
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
            return response()->json(['status' => 'success', 'message' => 'Expense reject successfully.']);
            // return redirect(route('expenses.show', ["expense" => $expense_id]))->with('danger', 'Expense rejected');
        } else {
            return response()->json(['status' => 'error', 'message' => 'Please add reason if you want reject the expens.']);
        }
    }

    public function approveExpense(Request $request)
    {
        $dates = Carbon::now();
        $current_date_time = $dates->setTimezone('Asia/Kolkata');
        $expense_detail = Expenses::where('id', $request->expense_new_id)->first();
        $approve_amnt = $request->approve_amnt;
        $expense_id = $request->expense_new_id;
        $reason = $request->reasons ?? NULL;

        if ($expense_detail->claim_amount < $approve_amnt) {
            return response()->json(['status' => 'error', 'message' => 'Approve amount greater than to claim amount']);
            // return redirect(route('expenses.show', ["expense" => $expense_id]))->with('success', 'Approve amount greater than to claim amount');
        }

        Expenses::where('id', $expense_id)->update(['reason' => $reason, 'checker_status' => '1', 'approve_reject_by' => Auth::user()->id, 'approve_amount' => $approve_amnt]);

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
        // return redirect(route('expenses.show', ["expense" => $expense_id]))->with('success', 'Approved amount');
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            ExpenseLog::where('expense_id', $id)->delete();
            $expenses = Expenses::find($id);
            if ($expenses->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Expense deleted successfully!']);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in Expense Delete!']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function changeStatus(Request $request)
    {
        $status = $request->status;
        $dates = Carbon::now();
        $current_date_time = $dates->setTimezone('Asia/Kolkata');
        $expenses = Expenses::find($request->id);
        $expenses->checker_status = $status;
        $expenses->approve_reject_by = Auth::user()->id;
        $expenses->save();
        if ($status == '3') {
            $status_type = 'Checked';
        } elseif ($status == '4') {
            $status_type = 'Checked By Reporting';
        } elseif ($status == '5') {
            $status_type = 'Hold';
        }

        if ($request->id) {
            $logdata = array(
                'log_date' => date('Y-m-d'),
                'expense_id' => $request->id,
                'created_by' => Auth::user()->id,
                'status_type' => $status_type,
            );
            ExpenseLog::create($logdata);
        }

        return response()->json(['status' => 'success', 'message' => 'Status checked successfully']);
        // return redirect(route('expenses.index'));
    }


    public function uncheckStatus(Request $request)
    {
        $dates = Carbon::now();
        $current_date_time = $dates->setTimezone('Asia/Kolkata');
        $expenses = Expenses::find($request->id);
        $expenses->checker_status = '0';
        $expenses->approve_amount = null;
        $expenses->approve_reject_by = Auth::user()->id;
        $expenses->save();

        if ($request->id) {
            $logdata = array(
                'log_date' => date('Y-m-d'),
                'expense_id' => $request->id,
                'created_by' => Auth::user()->id,
                'status_type' => 'unchecked'
            );
            ExpenseLog::create($logdata);
        }

        return response()->json(['status' => 'success', 'message' => 'Status unchecked successfully']);
        // return redirect(route('expenses.index'));
    }






    public function getexpenseType(Request $request)
    {
        $payroll = $request->payroll;
        $expenseTypes = ExpensesType::where('payroll_id', $payroll)->get();
        $html = "";
        $html .= "<option value='' >Select Expense Type</option>";
        foreach ($expenseTypes as $expenseType) {
            $html .= "<option value='" . $expenseType->id . "'>" . ucwords($expenseType->name) . "</option>";
        }
        return $html;
    }


    public function getexpenseUserType(Request $request)
    {
        $user_id = $request->user_id;
        $userDetail = User::where('id', $user_id)->first();
        $expenseTypes = ExpensesType::where('payroll_id', $userDetail->payroll)->get();
        $html = "";
        $html .= "<option value=''>Select Expense Type</option>";
        if (!empty($userDetail->payroll)) {
            foreach ($expenseTypes as $expenseType) {
                $html .= "<option value='" . $expenseType->id . "' data-allowtype='" . $expenseType->allowance_type_id . "' data-rate='" . $expenseType->rate . "'  >" . ucwords($expenseType->name) . "</option>";
            }
        }

        return $html;
    }




    public function getexpenseUserTypeEdit(Request $request)
    {

        $user_id = $request->user_id;
        $expenses_type = $request->expenses_type;
        $userDetail = User::where('id', $user_id)->first();
        $expenseTypes = ExpensesType::where('payroll_id', $userDetail->payroll)->get();
        $selected = '';

        $html = "";
        //$html .= "<option value=''>Select Expense Type</option>";

        if (!empty($userDetail->payroll) && $expenseTypes->count() > 0) {
            foreach ($expenseTypes as $expenseType) {
                if ($expenses_type == $expenseType->id) {
                    $selected = 'selected';
                } else {
                    $selected = "";
                }

                $html .= "<option value='" . $expenseType->id . "' data-allowtype='" . $expenseType->allowance_type_id . "' data-rate='" . $expenseType->rate . "' " . $selected . ">" . ucwords($expenseType->name) . "</option>";
            }
        }

        return $html;
    }



    public function deletImages(Request $request)
    {
        $id = $request->id;
        $expense_id = $request->expense_id;
        Media::where('id', $id)->delete();
        //$media = Media::find($id);
        // $model = Model::find($media->id);
        //$media->deleteMedia($media->id);

        return redirect()->route('expenses.edit', ['expense' => $expense_id]);
    }


    public function deleteview(Request $request)
    {

        $id = $request->id;
        $expense_id = $request->expense_id;
        Media::where('id', $id)->delete();
        //$media = Media::find($id);
        // $model = Model::find($media->id);
        //$media->deleteMedia($media->id);
        return redirect()->route('expenses.show', ['expense' => $expense_id]);
    }

    // public function all_map(Request $request)
    // {

    //     $rules = [
    //         'user_id'          => 'required',
    //         'date'            => 'required',
    //     ];

    //     $validator = Validator::make($request->all(), $rules);
    //     if ($validator->passes()) {
    //         $coordinates = [];

    //         $attan = Attendance::where('user_id', $request->user_id)->where('punchin_date', $request->date)->first();
    //         $checks = CheckIn::with('customers')->where('user_id', $request->user_id)->where('checkin_date', $request->date)->get();

    //         if ($attan) {

    //             $coordinates[0]['latitude'] = $attan->punchin_latitude;
    //             $coordinates[0]['longitude'] = $attan->punchin_longitude;
    //             $coordinates[0]['name'] = 'Punch In';
    //             $coordinates[0]['time'] = $attan->punchin_time ? Carbon::createFromFormat('H:i:s', $attan->punchin_time)->format('g:i A') : '-';
    //             $i = 1;
    //             foreach ($checks as $check) {
    //                 $coordinates[$i]['latitude'] = $check->checkin_latitude;
    //                 $coordinates[$i]['longitude'] = $check->checkin_longitude;
    //                 $coordinates[$i]['name'] = ($check->customers ? $check->customers->name : '-') . ' : Check In';
    //                 $coordinates[$i]['time'] = $check->checkin_time ? Carbon::createFromFormat('H:i:s', $check->checkin_time)->format('g:i A') : '-';
    //                 $i++;
    //                 // $coordinates[$i]['latitude'] = $check->checkout_latitude;
    //                 // $coordinates[$i]['longitude'] = $check->checkout_longitude;
    //                 // $coordinates[$i]['name'] = ($check->customers ? $check->customers->name : '-') . ' : Check Out';
    //                 // $coordinates[$i]['time'] = $check->checkout_time?Carbon::createFromFormat('H:i:s', $check->checkout_time)->format('g:i A'):'-';
    //                 // $i++;
    //             }
    //             if ($attan->punchout_latitude && !empty($attan->punchout_latitude)) {
    //                 $coordinates[$i]['latitude'] = $attan->punchout_latitude;
    //                 $coordinates[$i]['longitude'] = $attan->punchout_longitude;
    //                 $coordinates[$i]['name'] = 'Punch Out';
    //                 $coordinates[$i]['time'] = $attan->punchout_time ? Carbon::createFromFormat('H:i:s', $attan->punchout_time)->format('g:i A') : '-';
    //             }

    //             return view('map.route', compact('coordinates'));
    //         } else {
    //             return redirect()->back()->withErrors('No Activity Found');
    //         }
    //     } else {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }
    // }

    public function all_map(Request $request)
    {
        if ($request->submit == 'Track') {
            $rules = [
                'user_id'   => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $coordinates = [];

            $all_data = UserLiveLocation::where('userid', $request->user_id)->whereDate('created_at', Carbon::today())->orderBy('id', 'asc')->get();

            foreach ($all_data as $check) {
                // Add Check-In Data
                $coordinates[] = [
                    'latitude' => $check->latitude,
                    'longitude' => $check->longitude,
                    'time' => $check->time,
                ];
            }

            return view('map.track', compact('coordinates'));

        } else {
            $rules = [
                'user_id'   => 'required',
                'date'      => 'required|date|before_or_equal:to_date',
                'to_date'   => 'required|date|after_or_equal:date',
            ];

            $validator = Validator::make($request->all(), $rules);

            // Additional Custom Validation
            $validator->after(function ($validator) use ($request) {
                $date = Carbon::parse($request->date);
                $toDate = Carbon::parse($request->to_date);

                if ($date->diffInDays($toDate) > 30) {
                    $validator->errors()->add('to_date', 'The date range must not exceed 30 days.');
                }
            });
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $coordinates = [];
            $fromDate = Carbon::parse($request->date);
            $toDate = Carbon::parse($request->to_date);

            while ($fromDate->lte($toDate)) {
                $date = $fromDate->format('Y-m-d');

                $attan = Attendance::where('user_id', $request->user_id)
                    ->where('punchin_date', $date)
                    ->first();

                $checks = CheckIn::with('customers')
                    ->where('user_id', $request->user_id)
                    ->where('checkin_date', $date)
                    ->get();

                if ($attan) {
                    $dayCoordinates = [];
                    $dayCoordinates[] = [
                        'latitude' => $attan->punchin_latitude,
                        'longitude' => $attan->punchin_longitude,
                        'name' => 'Punch In',
                        'date' => $date,
                        'time' => $attan->punchin_time ? Carbon::createFromFormat('H:i:s', $attan->punchin_time)->format('g:i A') : '-',
                    ];

                    foreach ($checks as $check) {
                        // Add Check-In Data
                        $dayCoordinates[] = [
                            'latitude' => $check->checkin_latitude,
                            'longitude' => $check->checkin_longitude,
                            'name' => ($check->customers ? $check->customers->name : '-') . ' : Check In (' . $date . ')',
                            'time' => $check->checkin_time ? Carbon::createFromFormat('H:i:s', $check->checkin_time)->format('g:i A') : '-',
                        ];

                        // Add Check-Out Data if available
                        if (!empty($check->checkout_latitude) && !empty($check->checkout_longitude)) {
                            $dayCoordinates[] = [
                                'latitude' => $check->checkout_latitude,
                                'longitude' => $check->checkout_longitude,
                                'name' => ($check->customers ? $check->customers->name : '-') . ' : Check Out (' . $date . ')',
                                'time' => $check->checkout_time ? Carbon::createFromFormat('H:i:s', $check->checkout_time)->format('g:i A') : '-',
                            ];
                        }
                    }


                    if ($attan->punchout_latitude && !empty($attan->punchout_latitude)) {
                        $dayCoordinates[] = [
                            'latitude' => $attan->punchout_latitude,
                            'longitude' => $attan->punchout_longitude,
                            'name' => 'Punch Out',
                            'date' => $date,
                            'time' => $attan->punchout_time ? Carbon::createFromFormat('H:i:s', $attan->punchout_time)->format('g:i A') : '-',
                        ];
                    }

                    $coordinates[$date] = $dayCoordinates;
                }

                $fromDate->addDay();
            }

            return view('map.route', compact('coordinates'));
        }
    }




    public function checkExpenses(Request $request)
    {
        try {
            $ids = explode(',', $request['id']);

            $reason = $request['reason'] ?? 'Checked';

            foreach ($ids as $key => $value) {
                $update = Expenses::where('id', $value)->update(['reason' => $reason, 'checker_status' => '3']);

                if ($update) {
                    $logdata = array(
                        'log_date' => date('Y-m-d'),
                        'expense_id' => $value,
                        'created_by' => Auth::user()->id,
                        'status_type' => 'checked'
                    );
                    ExpenseLog::create($logdata);
                }
            }
            return  response()->json(['status' => 'success', 'message' => 'Expense Approved Successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Expense Not Approved Successfully']);
        }
    }
    public function approveExpenses(Request $request)
    {
        try {
            $ids = explode(',', $request['id']);

            $reason = $request['reason'] ?? 'Approved';

            foreach ($ids as $key => $value) {
                $update = Expenses::where('id', $value)->update(['reason' => $reason, 'checker_status' => '1', 'approve_reject_by' => Auth::user()->id, 'approve_amount' => DB::raw('claim_amount')]);

                if ($update) {
                    $logdata = array(
                        'log_date' => date('Y-m-d'),
                        'expense_id' => $value,
                        'created_by' => Auth::user()->id,
                        'status_type' => 'approved'
                    );
                    ExpenseLog::create($logdata);
                }
            }
            return  response()->json(['status' => 'success', 'message' => 'Expense Approved Successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Expense Not Approved Successfully']);
        }
    }

    public function rejectExpenses(Request $request)
    {
        try {
            $ids = explode(',', $request['id']);
            $reason = $request['reason'] ?? 'Rejected';
            foreach ($ids as $key => $value) {
                $update = Expenses::where('id', $value)->update(['reason' => $reason, 'checker_status' => '2', 'approve_reject_by' => Auth::user()->id, 'approve_amount' => NULL]);

                if ($update) {
                    $logdata = array(
                        'log_date' => date('Y-m-d'),
                        'expense_id' => $value,
                        'created_by' => Auth::user()->id,
                        'status_type' => 'rejected'
                    );
                    ExpenseLog::create($logdata);
                }
            }
            return  response()->json(['status' => 'success', 'message' => 'Expense Rejected Successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Expense Not Rejected Successfully']);
        }
    }
}
