<?php

namespace App\Http\Controllers;

use App\Models\UnitMeasure;
use Illuminate\Http\Request;
use App\Http\Requests\UnitRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\DataTables\UnitDataTable;
use App\Imports\UnitImport;
use App\Exports\UnitExport;
use App\Exports\UnitTemplate;

class UnitController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->units = new UnitMeasure();
        
    }
    
    public function index(UnitDataTable $dataTable)
    {
        abort_if(Gate::denies('unit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('units.index');
    }

    public function store(UnitRequest $request)
    {
        try
        { 
            $permission = !empty($request['id']) ? 'unit_edit' : 'unit_create' ;
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if(!empty($request['id']))
            {
                $status = UnitMeasure::where('id',$request['id'])->update($request->except(['_token','id']));
            }
            else
            {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = UnitMeasure::create($request->except(['_token']));
            } 
            if($status)
            {
              return Redirect::to('units')->with('message_success', 'Unit Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
        }        
        catch(\Exception $e)
        {

          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $id = decrypt($id);
        $unit = UnitMeasure::find($id);
        return response()->json($unit);
    }

    public function edit($id)
    {
        abort_if(Gate::denies('unit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $units = UnitMeasure::find($id);
         return view('units.create')->with('units',$units);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('unit_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $unit = UnitMeasure::find($id);
        if($unit->delete())
        {
            return response()->json(['status' => 'success','message' => 'Data deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Data deleted successfully!']);
    }

    public function active(Request $request)
    {
        if(UnitMeasure::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Unit '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
        abort_if(Gate::denies('unit_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new UnitImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('unit_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UnitExport, 'units.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('unit_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UnitTemplate, 'units.xlsx');
    }
}
