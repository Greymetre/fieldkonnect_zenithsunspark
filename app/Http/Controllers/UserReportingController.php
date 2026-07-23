<?php

namespace App\Http\Controllers;

use App\Models\UserReporting;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\DataTables\UserReportingDataTable;
use App\Models\User;

class UserReportingController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        
    }
    
    public function index(UserReportingDataTable $dataTable)
    {
        //abort_if(Gate::denies('reportings_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::where('active','=','Y')->select('id','name','profile_image')->get();
        return $dataTable->render('reportings.index',compact('users'));
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
        try
        { 
            $request['users'] = json_encode($request['users']);
            if(!empty($request['id']))
            {
                $status = UserReporting::where('id',$request['id'])->update($request->except(['_token','id']));
            }
            else
            {
                $request['created_by'] = Auth::user()->id;
                $status = UserReporting::create($request->except(['_token','member_id']));
            } 
            if($status)
            {
              return Redirect::to('reportings')->with('message_success', 'Data Store Successfully');
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
     * @param  \App\Models\UserReporting  $userReporting
     * @return \Illuminate\Http\Response
     */
    public function show(UserReporting $userReporting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserReporting  $userReporting
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $reportings = UserReporting::find($id);
        $reportings['user_name'] = isset($reportings['reportinginfo']['name']) ? $reportings['reportinginfo']['name'] : '';
        return response()->json($reportings);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserReporting  $userReporting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserReporting $userReporting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserReporting  $userReporting
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(UserReporting::where('id',$id)->delete())
        {
            return response()->json(['status' => 'success','message' => 'UserReporting deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in UserReporting Delete!']);
    }
}
