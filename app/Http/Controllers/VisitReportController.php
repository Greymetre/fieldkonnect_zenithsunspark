<?php

namespace App\Http\Controllers;

use App\Models\VisitReport;
use Illuminate\Http\Request;
use App\Http\Requests\VisitReportRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\DataTables\VisitReportDataTable;
use App\DataTables\MasterVisitReportDataTable;
use App\Imports\VisitReportImport;
use App\Exports\VisitReportExport;
use App\Exports\VisitReportTemplate;
use App\Exports\MasterVisitReportExport;
use Excel;

class VisitReportController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->visitreports = new VisitReport();
        
    }
    
    public function index(VisitReportDataTable $dataTable)
    {
        ////abort_if(Gate::denies('visitreport_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('visitreports.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        ////abort_if(Gate::denies('visitreport_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('visitreports.create')->with('visitreports',$this->visitreports);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VisitReportRequest $request)
    {
        try
        { 
            ////abort_if(Gate::denies('visitreport_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $validator = Validator::make($request->all(), [
                'checkin_id' => 'nullable|exists:check_in,id',
                'customer_id' => 'required|exists:customers,id',
                'visit_type_id' => 'nullable|exists:visit_types,id',
                'description' => 'required|string|max:440',
            ]); 
            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
            if($report_id = VisitReport::insertGetId([
                'active'    => 'Y',
                'checkin_id' => isset($request['checkin_id']) ? $request['checkin_id'] : null, 
                'user_id' => Auth::user()->id, 
                'customer_id' => isset($request['customer_id']) ? $request['customer_id'] : null, 
                'visit_type_id' => isset($request['visit_type_id']) ? $request['visit_type_id'] : null, 
                'report_title' => isset($request['report_title']) ? $request['report_title'] : null, 
                'checkin_id' => isset($request['checkin_id']) ? $request['checkin_id'] : null, 
                'description' => isset($request['description']) ? $request['description'] : '',
                'created_by' => Auth::user()->id,
                'created_at' => getcurentDateTime()
            ]))
            {
                 return Redirect::to('visitreports')->with('message_success', 'Report Submited Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Report Submit')->withInput();  
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VisitReport  $visitReport
     * @return \Illuminate\Http\Response
     */
    public function show(VisitReport $visitReport)
    {
        ////abort_if(Gate::denies('visitreport_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VisitReport  $visitReport
     * @return \Illuminate\Http\Response
     */
     public function edit($id)
    {
        ////abort_if(Gate::denies('visitreport_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $visitreports = VisitReport::find($id);
         return view('visitreports.create')->with('visitreports',$visitreports);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VisitReport  $visitReport
     * @return \Illuminate\Http\Response
     */
    public function update(VisitReportRequest $request, $id)
    {
        try
        { 
            ////abort_if(Gate::denies('visitreport_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $validator = Validator::make($request->all(), [
                'visit_type_id' => 'nullable|exists:visit_types,id',
                'description' => 'required|string|max:440',
            ]); 
            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
            $id = decrypt($id);
            $reports = VisitReport::find($id);
            $reports->report_title = isset($request['report_title'])? $request['report_title'] :'';
            $reports->description = isset($request['description'])? $request['description'] :'';
            if($reports->save())
            {
                 return Redirect::to('visitreports')->with('message_success', 'Report Submited Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Report Submit')->withInput();  
        }
        catch(\Exception $e)
        {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VisitReport  $visitReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(VisitReport $visitReport)
    {
        ////abort_if(Gate::denies('visitreport_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function upload(Request $request) 
    {
        ////abort_if(Gate::denies('visitreport_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new VisitReportImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
        ////abort_if(Gate::denies('visitreport_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new VisitReportExport, 'visitreports.xlsx');
    }
    public function template()
    {
        ////abort_if(Gate::denies('visitreport_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new VisitReportTemplate, 'visitreports.xlsx');
    }
    public function masterVisitreport(MasterVisitReportDataTable $dataTable)
    {
        ////abort_if(Gate::denies('visitreport_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('visitreports.mastervisitreport');
    }

    public function masterVisitreportsDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new MasterVisitReportExport, 'mastervisitreport.xlsx');
    }
}
