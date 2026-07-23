<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Requests\StateRequest;
use App\Models\Country;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\StateDataTable;
use App\Imports\StateImport;
use App\Exports\StateExport;
use App\Exports\StateTemplate;
class StateController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->state = new State();
        
    }

    public function index(StateDataTable $dataTable)
    {
        abort_if(Gate::denies('state_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = Country::where('active','=','Y')->select('id', 'country_name')->get();
        return $dataTable->render('state.index',compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('state_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = Country::where('active','=','Y')->select('id', 'country_name')->get();
        return view('state.create',compact('countries') )->with('state',$this->state);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StateRequest $request)
    {
        try
        { 
            $useraccess = !empty($request['id']) ? 'state_edit' : 'state_create' ;
            abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if(!empty($request['id']))
            {
                $state = State::where('id',$request['id'])->first();
                $state->state_name = isset($request['state_name']) ? $request['state_name'] :'';
                $state->country_id = isset($request['country_id']) ? $request['country_id'] :null;
                $state->gst_code = isset($request['gst_code']) ? $request['gst_code'] :null;
                $state->updated_by = isset($request['updated_by']) ? $request['updated_by'] :Auth::user()->id;
                $state->save();
            }
            else
            {
                $request['created_by'] = Auth::user()->id;
                $request['active'] = 'Y';
                $state = State::create($request->except(['_token','image']));
            } 
            if($state)
            {
              return Redirect::to('state')->with('message_success', 'State Store Successfully');
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
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function show(State $state)
    {
        abort_if(Gate::denies('state_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\State  $state
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('state_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $state = State::find($id);
        $state['country_name'] = isset($state['countryname']['country_name'])? $state['countryname']['country_name'] :'';
        return response()->json($state);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('state_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $state = State::find($id);
        if($state->delete())
        {
            return response()->json(['status' => 'success','message' => 'State deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in State Delete!']);
    }
    
    public function active(Request $request)
    {
        if(State::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'State '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('state_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new StateImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('state_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new StateExport, 'states.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('state_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new StateTemplate, 'states.xlsx');
    }
}
