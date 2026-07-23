<?php

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use Illuminate\Http\Request;
use App\DataTables\SalesTargetDatatable;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;



class TargetController extends Controller
{
   

    public function index(SalesTargetDatatable $dataTable)
    {
        $userids = getUsersReportingToAuth();
        $users= User::where('active','=','Y')->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name')->get();
        return $dataTable->render('targets.index',compact('users'));
    }

    public function store(Request $request)
    {
        try
        { 
            $status = '';
            if(!empty($request['id']))
            {
                $status = SalesTarget::where('id',$request['id'])->update($request->except(['_token','id']));
            }
            else
            {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = SalesTarget::create($request->except(['_token']));
            } 
            if($status)
            {
              return Redirect::to('targets')->with('message_success', 'Lead Stages Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Lead Stages')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $leadstage = SalesTarget::find($id);
        return response()->json($leadstage);
    }

    public function destroy($id)
    {
        $status = SalesTarget::find($id);
        if($status->delete())
        {
            return response()->json(['status' => 'success','message' => 'SalesTarget deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in SalesTarget Delete!']);
    }
}
