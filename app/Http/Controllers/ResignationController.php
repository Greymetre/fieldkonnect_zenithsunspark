<?php

namespace App\Http\Controllers;

use App\DataTables\ResignationDataTable;
use App\Exports\ExcelExport;
use App\Models\Branch;
use App\Models\Division;
use App\Models\Resignation;
use App\Models\ResignationCheckList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use Auth;
use Excel;

class ResignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ResignationDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('resignation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $divisions = Division::where('active', 'Y')->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->get();
        return $dataTable->render('resignation.index', compact('branches', 'divisions', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $all_users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->get();
        if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Sub_Admin') && !auth()->user()->hasRole('HR_Admin')) {
            $userid = !empty($userid) ? $userid : Auth::user()->id;
            $all_ids_array = array($userid);
            $test = getAllChild(array($userid), $all_users);
            while (count($test) > 0) {
                $all_ids_array = array_merge($all_ids_array, $test);
                $test = getAllChild($test, $all_users);
            }
            $branches = User::where('id', $all_ids_array)->pluck('branch_id');
            $divisionss = User::where('id', $all_ids_array)->pluck('division_id');
            $branches = Branch::where('active', 'Y')->whereIn('id', $branches)->get();
            $divisions = Division::where('active', 'Y')->whereIn('id', $divisionss)->get();
        } else {
            $branches = Branch::where('active', 'Y')->get();
            $divisions = Division::where('active', 'Y')->get();
        }
        return view('resignation.form', compact('branches', 'divisions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $resignation = Resignation::create($request->all());
        return redirect(route('resignations.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resignation  $resignation
     * @return \Illuminate\Http\Response
     */
    public function show(Resignation $resignation)
    {
        return view('resignation.show', compact('resignation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resignation  $resignation
     * @return \Illuminate\Http\Response
     */
    public function edit(Resignation $resignation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resignation  $resignation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Resignation $resignation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resignation  $resignation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resignation $resignation)
    {
        ResignationCheckList::where('resignation_id', $resignation->id)->delete();
        $delete = $resignation->delete();

        if ($delete) {
            return redirect()->back()->with('message_success', 'Resignation deleted Successfully');
        } else {
            return redirect()->back()->with('message_info', 'Somthing went wrong');
        }
    }

    public function download(Request $request)
    {
        $filename = 'Resignation.xlsx';
        $branch_id = $request->branch_id;
        $status = $request->status;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $division_id = $request->division_id;
        $user_id = $request->user_id;

        $resignations = Resignation::with(['user','division','branch']);

        if (!empty($branch_id)) {
            $resignations->where('branch_id', $branch_id);
        }

        if (!empty($start_date) && !empty($end_date)) {
            $resignations->whereBetween('submit_date', [$start_date, $end_date]);
        }

        if (!empty($status)) {
            $resignations->where('status', $status);
        }

        if (!empty($division_id)) {
            $resignations->whereIn('division_id', $division_id);
        }

        if (!empty($user_id)) {
            $resignations->where('user_id', $user_id);
        }






        $resignations = $resignations->orderBy('id', 'desc')->get();

        $data = $resignations->map(function ($item, $key) {

            $status = "Pending";
            if ($item->status == '0') {
                $status = 'Pending';
            } else if ($item->status == '1') {
                $status = 'Accepted';
            } else if ($item->status == '2') {
                $status = 'Rejected';
            } else if ($item->status == '3') {
                $status = 'Revoke';
            } else if ($item->status == '4') {
                $status = 'Approved';
            } else if ($item->status == '5') {
                $status = 'Hold';
            }

            return [

                $item->id ?? "",
                isset($item->submit_date) ? date("d-M-Y", strtotime($item->submit_date)) : '',
                $status,
                $item->division->division_name ?? '',
                $item->branch->branch_name ?? '',
                $item->user->employee_codes ?? "",
                $item->user->name ?? "",
                $item->user->getdesignation->designation_name ?? '',
                $item->user->mobile ?? '',
                $item->user->reportinginfo->name ?? '',
                $item->date_of_joining ?? "",
                $item->notice ?$item->notice." Month" : "",
                $item->last_working_date ?? "",
                $item->reason ?? "",
                $item->persoanla_email ?? "",
                $item->persoanla_mobile ?? "",
                $item->remark ?? "",
            ];
        })->toArray();

        $export = new ExcelExport([
            '#Resignation Id',
            'Resignation Date',
            'Status',
            'Division',
            'Branch',
            'Employee Code',
            'Employee Name',
            'Designation',
            'Mobile',
            'Reporting Manager',
            'Date Of joing',
            'Notice Period',
            'Last Working Date',
            'Reason',
            'Personal Email ID',
            'Personal Mobile Number',
            'Remark'
        ], $data);

        return Excel::download($export, $filename);
    }

    public function update_checklist(Request $request)
    {
        $update = ResignationCheckList::updateOrCreate(['resignation_id' => $request->data['resignation_id']], [
            'document_file' => $request->data['document_file'],
            'exit_interview' => $request->data['exit_interview'],
            'advance' => $request->data['advance'],
            'laptop' => $request->data['laptop'],
            'sim_card' => $request->data['sim_card'],
            'keys' => $request->data['keys'],
            'visiting_card' => $request->data['visiting_card'],
            'income_tax' => $request->data['income_tax'],
            'laptop_bag' => $request->data['laptop_bag'],
            'expense_voucher' => $request->data['expense_voucher'],
            'crm_id' => $request->data['crm_id'],
            'unpaid_salary' => $request->data['unpaid_salary'],
            'data_email' => $request->data['data_email'],
            'id_card' => $request->data['id_card'],
            'payable_expense' => $request->data['payable_expense'],
            'pen_drive' => $request->data['pen_drive'],
            'bouns' => $request->data['bouns'],
        ]);

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'Checklist updated succussfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Somthing went wrong.']);
        }
    }

    public function resignation_status_change(Request $request)
    {
        $update = Resignation::where('id', $request->id)->update(['status' => $request->status, 'remark' => $request->remark]);

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'Status updated succussfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Somthing went wrong.']);
        }
    }

    public function resignation_last_working_date_change(Request $request)
    {
        $update = Resignation::where('id', $request->id)->update(['last_working_date' => $request->date]);

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'Last working date updated succussfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Somthing went wrong.']);
        }
    }
}
