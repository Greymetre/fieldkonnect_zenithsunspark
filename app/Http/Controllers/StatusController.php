<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use App\Http\Requests\StatusRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\StatusDataTable;
use App\Imports\StatusImport;
use App\Exports\StatusExport;
use App\Exports\StatusTemplate;

class StatusController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->status = new Status();
        
    }
    
    public function index(StatusDataTable $dataTable)
    {
        abort_if(Gate::denies('status_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('status.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('status_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('status.create')->with('status',$this->status);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StatusRequest $request)
    {
        try
        { 
            $useraccess = !empty($request['id']) ? 'status_edit' : 'status_create' ;
            abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if(!empty($request['id']))
            {
                $status = Status::where('id',$request['id'])->first();
                $status->status_name = isset($request['status_name']) ? $request['status_name'] :'';
                $status->display_name = isset($request['display_name']) ? $request['display_name'] :'';
                $status->status_message = isset($request['status_message']) ? $request['status_message'] :'';
                $status->module = isset($request['module']) ? $request['module'] :'';
                $status->updated_by = isset($request['updated_by']) ? $request['updated_by'] :Auth::user()->id;
                $status->save();
            }
            else
            {
                $request['created_by'] = Auth::user()->id;
                $request['active'] = 'Y';
                $status = Status::create($request->except(['_token']));
            } 
            if($status)
            {
              return Redirect::to('status')->with('message_success', 'Status Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
        }     
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

     public function edit($id)
    {
        abort_if(Gate::denies('status_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $status = Status::find($id);
        return response()->json($status);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('status_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $status = Status::find($id);
        if($status->delete())
        {
            return response()->json(['status' => 'success','message' => 'Status deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Delete!']);
    }
    
    public function active(Request $request)
    {
        //abort_if(Gate::denies('status_active'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if(Status::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Status '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
        abort_if(Gate::denies('status_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new StatusImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('status_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new StatusExport, 'statuss.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('status_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new StatusTemplate, 'statuss.xlsx');
    }
}
