<?php

namespace App\Http\Controllers;

use App\Models\FirmType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\Imports\FirmTypeImport;
use App\Exports\FirmTypeExport;
use App\Exports\FirmTypeTemplate;
use App\Http\Requests\FirmTypeRequest;
use App\DataTables\FirmTypeDataTable;

class FirmTypeController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->firmtype = new FirmType();
        
    }
    
    public function index(FirmTypeDataTable $dataTable)
    {
        abort_if(Gate::denies('firmtype_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('firmtype.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('firmtype_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('firmtype.create')->with('firmtype',$this->firmtype);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FirmTypeRequest $request)
    {
         try
        { 
            $useraccess = !empty($request['id']) ? 'firmtype_edit' : 'firmtype_create' ;
            abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if(!empty($request['id']))
            {
                $firmtype = FirmType::where('id',$request['id'])->first();
                $firmtype->firmtype_name = isset($request['firmtype_name']) ? $request['firmtype_name'] :'';
                $firmtype->updated_by = isset($request['updated_by']) ? $request['updated_by'] :Auth::user()->id;
                $firmtype->save();
            }
            else
            {
                $request['created_by'] = Auth::user()->id;
                $request['active'] = 'Y';
                $firmtype = FirmType::create($request->except(['_token']));
            } 
            if($firmtype)
            {
              return Redirect::to('firmtype')->with('message_success', 'CustomerType Store Successfully');
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
     * @param  \App\Models\FirmType  $firmType
     * @return \Illuminate\Http\Response
     */
    public function show(FirmType $firmType)
    {
        abort_if(Gate::denies('firmtype_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FirmType  $firmType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('firmtype_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $firmtype = FirmType::find($id);
        return response()->json($firmtype);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FirmType  $firmType
     * @return \Illuminate\Http\Response
     */
    public function update(FirmTypeRequest $request, $id)
    {
        try
        { 
            abort_if(Gate::denies('firmtype_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $id = decrypt($id);
            $firmtype = FirmType::find($id);
            $firmtype->firmtype_name = isset($request['firmtype_name'])? $request['firmtype_name'] :'';
            $firmtype->updated_by = Auth::user()->id;
            if($firmtype->save())
            {
              return Redirect::to('firmtype')->with('message_success', 'Firm Type Update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Firm Type Update')->withInput();
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FirmType  $firmType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('firmtype_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $firmtype = FirmType::find($id);
        if($firmtype->delete())
        {
            return response()->json(['status' => 'success','message' => 'FirmType deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in CustomerType Delete!']);
    }
    
    public function active(Request $request)
    {
        if(FirmType::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'FirmType '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
        abort_if(Gate::denies('firmtype_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new FirmTypeImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('firmtype_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new FirmTypeExport, 'firmtypes.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('firmtype_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new FirmTypeTemplate, 'firmtypes.xlsx');
    }
}
