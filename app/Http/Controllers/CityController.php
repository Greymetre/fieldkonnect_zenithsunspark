<?php

namespace App\Http\Controllers;
ini_set('max_execution_time', 18000);
set_time_limit(18000);

use App\Models\City;
use Illuminate\Http\Request;
use App\Models\District;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\CityDataTable;
use App\Imports\CityImport;
use App\Exports\CityExport;
use App\Exports\CityTemplate;
use App\Http\Requests\CityRequest;

class CityController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->city = new City();
        
    }

    public function index(CityDataTable $dataTable)
    {
        abort_if(Gate::denies('city_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $districts = District::where('active','=','Y')->select('id', 'district_name')->get();
        return $dataTable->render('city.index',compact('districts'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {
        try
        { 
            $useraccess = !empty($request['id']) ? 'city_edit' : 'city_create' ;
            abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if(!empty($request['id']))
            {
                $city = City::where('id',$request['id'])->first();
                $city->city_name = isset($request['city_name']) ? $request['city_name'] :'';
                $city->grade = isset($request['grade']) ? $request['grade'] :'';
                $city->district_id = isset($request['district_id']) ? $request['district_id'] :'';
                $city->updated_by = isset($request['updated_by']) ? $request['updated_by'] :Auth::user()->id;
                $city->save();
            }
            else
            {
                $request['created_by'] = Auth::user()->id;
                $request['active'] = 'Y';
                $city = City::create($request->except(['_token']));
            } 
            if($city)
            {
              return Redirect::to('city')->with('message_success', 'City Store Successfully');
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
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        abort_if(Gate::denies('city_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('city_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $city = City::find($id);
        $city['district_name'] = isset($city['districtname']['district_name'])? $city['districtname']['district_name'] :'';
        return response()->json($city);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('city_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $city = City::find($id);
        if($city->delete())
        {
            return response()->json(['status' => 'success','message' => 'City deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in User Delete!']);
    }
    
    public function active(Request $request)
    {
        if(City::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'City '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
      //abort_if(Gate::denies('city_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new CityImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      //abort_if(Gate::denies('city_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CityExport, 'cities.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('city_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CityTemplate, 'cities.xlsx');
    }
}
