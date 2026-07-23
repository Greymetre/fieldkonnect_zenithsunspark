<?php

namespace App\Http\Controllers;

use App\Models\VisitType;
use Illuminate\Http\Request;
use App\Http\Requests\VisitTypeRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\DataTables\VisitTypeDataTable;
use App\Imports\BeatImport;
use App\Exports\BeatExport;
use App\Exports\BeatTemplate;

class VisitTypeController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->visittypes = new VisitType();
        
    }


    public function index(VisitTypeDataTable $dataTable)
    {
        abort_if(Gate::denies('visittype_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('visittypes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         abort_if(Gate::denies('visittype_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('visittypes.create')->with('visittypes',$this->visittypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VisitTypeRequest $request)
    {
        try
        { 
            $permission = !empty($request['id']) ? 'visittype_edit' : 'visittype_create' ;
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $status = '';
            if(!empty($request['id']))
            {
                $status = VisitType::where('id',$request['id'])->update($request->except(['_token','id']));
            }
            else
            {
                $status = VisitType::create($request->except(['_token']));
            } 
            if($status)
            {
              return Redirect::to('visittypes')->with('message_success', 'Data Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VisitType  $visitType
     * @return \Illuminate\Http\Response
     */
    public function show(VisitType $visitType)
    {
        abort_if(Gate::denies('visittype_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VisitType  $visitType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('visittype_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $visittypes = VisitType::find($id);
        return response()->json($visittypes);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VisitType  $visitType
     * @return \Illuminate\Http\Response
     */
    public function update(VisitTypeRequest $request, $id)
    {
        try
        { 
             abort_if(Gate::denies('visittype_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
           $validator = Validator::make($request->all(), [
                'type_name' => 'required',
            ]); 
            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
            $id = decrypt($id);
            $visit = VisitType::find($id);
            $visit->type_name = isset($request['type_name'])? $request['type_name'] :'';
            if($visit->save())
            {
              return Redirect::to('visittypes')->with('message_success', 'Visit Type Update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Visit Update')->withInput();
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VisitType  $visitType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('visittype_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $priority = VisitType::find($id);
        if($priority->delete())
        {
            return response()->json(['status' => 'success','message' => 'VisitType deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in VisitType Delete!']);
    }
    
    public function active(Request $request)
    {
        //abort_if(Gate::denies('visittype_active'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if(VisitType::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'VisitType '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('beat_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new BeatImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('beat_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new BeatExport, 'beats.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('beat_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new BeatTemplate, 'beats.xlsx');
    }
}
