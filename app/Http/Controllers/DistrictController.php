<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;
use App\Models\State;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\DistrictDataTable;
use App\Imports\DistrictImport;
use App\Exports\DistrictExport;
use App\Exports\DistrictTemplate;
use App\Http\Requests\DistrictRequest;

class DistrictController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->district = new District();
        
    }

    public function index(DistrictDataTable $dataTable)
    {
        abort_if(Gate::denies('district_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $states = State::where('active','=','Y')->select('id', 'state_name')->get();
        return $dataTable->render('district.index',compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('district_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $states = State::where('active','=','Y')->select('id', 'state_name')->get();
        return view('district.create',compact('states') )->with('district',$this->district);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DistrictRequest $request)
    {
       try
        { 
            $useraccess = !empty($request['id']) ? 'district_edit' : 'district_create' ;
            abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if(!empty($request['id']))
            {
                $district = District::where('id',$request['id'])->first();
                $district->district_name = isset($request['district_name']) ? $request['district_name'] :'';
                $district->state_id = isset($request['state_id']) ? $request['state_id'] :null;
                $district->updated_by = isset($request['updated_by']) ? $request['updated_by'] :Auth::user()->id;
                $district->save();
            }
            else
            {
                $request['created_by'] = Auth::user()->id;
                $request['active'] = 'Y';
                $district = District::create($request->except(['_token','image']));
            } 
            if($district)
            {
              return Redirect::to('district')->with('message_success', 'State Store Successfully');
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
     * @param  \App\Models\District  $district
     * @return \Illuminate\Http\Response
     */
    public function show(District $district)
    {
        abort_if(Gate::denies('district_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('district_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $district = District::find($id);
        $district['state_name'] = isset($district['statename']['state_name'])? $district['statename']['state_name'] :'';
        return response()->json($district);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('district_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $district = District::find($id);
        if($district->delete())
        {
            return response()->json(['status' => 'success','message' => 'District deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in User Delete!']);
    }
    
    public function active(Request $request)
    {
        if(District::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'District '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }
    public function upload(Request $request) 
    {
      abort_if(Gate::denies('district_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new DistrictImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      //abort_if(Gate::denies('district_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new DistrictExport, 'districts.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('district_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new DistrictTemplate, 'districts.xlsx');
    }
}
