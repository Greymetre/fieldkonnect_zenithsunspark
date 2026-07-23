<?php

namespace App\Http\Controllers;

use App\Models\OpportunitieStatus;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;

class LeadOpportunitiesStatusController extends Controller
{
    public function index(Request $request)
    {
        if(request()->ajax()) {
            $data = OpportunitieStatus::with('createbyname')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($data){
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editStatus"><i class="fa fa-edit fa-sm text-white"></i></a>';
                    $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$data->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteStatus"><i class="fa fa-trash fa-sm text-white"></i></a>';
                    return $btn;
                })
                ->editColumn('created_at', function($data){
                    //Conver Date Format in DD-MM-YYY H:i AM/PM
                    return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                })
                ->rawColumns(['action'])
                ->make(true);
            
        }
        return view('lead-opportunities-status.index');
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token', '_method', 'submit', 'lead_id']);
        // dd($data);
        if($request->lead_id == null){
            $data['created_by'] = auth()->user()->id;
            $lead_opportunities_status = OpportunitieStatus::create($data);            
        }else{
            OpportunitieStatus::where('id', $request->lead_id)->update($data);
        }
        return redirect()->back();
    }

    public function edit(OpportunitieStatus $lead_opportunities_status)
    {
        return response()->json($lead_opportunities_status);
    }

    public function destroy(OpportunitieStatus $lead_opportunities_status)
    {
        $lead_opportunities_status->delete();
        return response()->json(['status' => true]);
    }
}