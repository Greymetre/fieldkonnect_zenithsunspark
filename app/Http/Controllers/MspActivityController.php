<?php

namespace App\Http\Controllers;

use App\Exports\MSPActivityExport;
use App\Exports\MSPActivityTemplate;
use App\Imports\MSPActivityImport;
use App\Models\Branch;
use App\Models\MspActivity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use Excel;
use Yajra\DataTables\DataTables;

class MspActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $branches = Branch::where('active', 'Y')->latest()->get();
        $FinancialYears = getFinancialYears();

        if ($request->ajax()) {
            
            $data = MspActivity::with('user');
            if($request->branch_id && !empty($request->branch_id)){
                $data->whereHas('user', function($query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                });
            }

            if($request->financial_year && !empty($request->financial_year)){
                $parts = explode('-', $request->financial_year);
                $financial_year = $parts[0] . '-' . substr($parts[1], -2);
                $data->where('fyear', $financial_year);
            }

            if($request->month && !empty($request->month)){
                $data->whereIn('month', $request->month);
            }
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('first_slot', function ($data) {
                    $day_wise_amount_array = json_decode($data->day_amount_pairs, true);
                    return $day_wise_amount_array['0-30']??'0';
                })
                ->addColumn('second_slot', function ($data) {
                    $day_wise_amount_array = json_decode($data->day_amount_pairs, true);
                    return $day_wise_amount_array['31-60']??'0';
                })
                ->addColumn('thired_slot', function ($data) {
                    $day_wise_amount_array = json_decode($data->day_amount_pairs, true);
                    return $day_wise_amount_array['61-90']??'0';
                })
                

                ->rawColumns(['first_slot','second_slot','thired_slot','fourth_slot','fifth_slot'])
                ->make(true);
        }

        return view('msp_activity.index', compact('branches','FinancialYears'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MspActivity  $mspActivity
     * @return \Illuminate\Http\Response
     */
    public function show(MspActivity $mspActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MspActivity  $mspActivity
     * @return \Illuminate\Http\Response
     */
    public function edit(MspActivity $mspActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MspActivity  $mspActivity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MspActivity $mspActivity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MspActivity  $mspActivity
     * @return \Illuminate\Http\Response
     */
    public function destroy(MspActivity $mspActivity)
    {
        //
    }

    public function msp_activity_template(Request $request){        
        abort_if(Gate::denies('msp_activity_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new MSPActivityTemplate, 'MSP Activity Template.xlsx');
    }

    public function msp_activity_upload(Request $request)
    {
        abort_if(Gate::denies('msp_activity_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new MSPActivityImport, request()->file('import_file'));

        return back()->with('success', 'MSP Activity Import successfully !!');
    }

    public function msp_activity_download(Request $request)
    {
        abort_if(Gate::denies('msp_activity_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new MSPActivityExport($request), 'MSP Activity.xlsx');
    }
}
