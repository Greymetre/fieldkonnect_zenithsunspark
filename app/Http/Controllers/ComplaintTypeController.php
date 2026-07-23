<?php

namespace App\Http\Controllers;

use App\DataTables\ComplaintTypeDataTable;
use App\Models\ComplaintType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Gate;

class ComplaintTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ComplaintTypeDataTable $dataTable)
    {
        abort_if(Gate::denies('complaint_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('complaint_type.index');
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
        {
            try
            { 
                $useraccess = !empty($request['id']) ? 'complaint_type_edit' : 'complaint_type_create' ;
                abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');
                if(!empty($request['id']))
                {
                    $city = ComplaintType::where('id',$request['id'])->first();
                    $city->name = isset($request['name']) ? $request['name'] :'';
                    $city->active = isset($request['active']) ? $request['active'] :'Y';
                    $city->save();
                }
                else
                {
                    $request['active'] = isset($request['active']) ? $request['active'] :'Y';
                    $city = ComplaintType::create($request->except(['_token']));
                } 
                if($city)
                {
                  return Redirect::to('complaint-type')->with('message_success', 'Complaint Type Store Successfully');
                }
                return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
            }     
            catch(\Exception $e)
            {
              return redirect()->back()->withErrors($e->getMessage())->withInput();
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ComplaintType  $complaintType
     * @return \Illuminate\Http\Response
     */
    public function show(ComplaintType $complaintType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ComplaintType  $complaintType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('complaint_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $ComplaintType = ComplaintType::find($id);
        return response()->json($ComplaintType);
    }

    public function active(Request $request)
    {
        if(ComplaintType::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Complaint Type '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ComplaintType  $complaintType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComplaintType $complaintType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComplaintType  $complaintType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('complaint_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ComplaintType = ComplaintType::find($id);
        if($ComplaintType->delete())
        {
            return response()->json(['status' => 'success','message' => 'Complaint Type deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in User Delete!']);
    }
}
