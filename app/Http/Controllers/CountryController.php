<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Requests\CountryRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\CountryDataTable;
use App\Imports\CountryImport;
use App\Exports\CountryExport;
use App\Exports\CountryTemplate;


class CountryController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->countries = new Country();
        
    }

    public function index(CountryDataTable $dataTable)
    {
        abort_if(Gate::denies('country_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('country.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CountryRequest $request)
    {
        try
        { 
            $useraccess = !empty($request['id']) ? 'country_edit' : 'country_create' ;
            abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if(!empty($request['id']))
            {
                $country = Country::where('id',$request['id'])->first();
                $country->country_name = isset($request['country_name']) ? $request['country_name'] :'';
                $country->updated_by = isset($request['updated_by']) ? $request['updated_by'] :Auth::user()->id;
                $country->save();
            }
            else
            {
                $request['created_by'] = Auth::user()->id;
                $request['active'] = 'Y';
                $country = Country::create($request->except(['_token']));
            } 
            if($country)
            {
              return Redirect::to('country')->with('message_success', 'Country Store Successfully');
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
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function show(Country $country)
    {
        abort_if(Gate::denies('country_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('country_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $country = Country::find($id);
        return response()->json($country);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('country_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = Country::find($id);
        if($user->delete())
        {
            return response()->json(['status' => 'success','message' => 'Country deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Country Delete!']);
    }
    
    public function active(Request $request)
    {
        if(Country::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Country '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('country_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new CountryImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('country_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CountryExport, 'countrys.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('country_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CountryTemplate, 'countrys.xlsx');
    }
}
