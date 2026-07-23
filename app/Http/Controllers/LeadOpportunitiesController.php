<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

use DataTables;

use App\Models\Lead;
use App\Models\User;
use App\Models\LeadOpportunity;
use App\Models\LeadContact;
use App\Models\OpportunitieStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LeadOpportunitiesController extends Controller
{



    public function index(Request $request)
    {
        // abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('name')->get();

        $lead_contacts = LeadContact::get();
        $opportunity_status = OpportunitieStatus::orderBy('ordering', 'asc')->pluck('status_name', 'id')->toArray();
        return view('leads-opportunities.index', compact('users', 'lead_contacts', 'opportunity_status'));
    }


    public function getCardData(Request $request)
    {

        $assigned_to = $request->assigned_to;
        $all_opportunities = LeadOpportunity::with('lead', 'assignUser');
        if ($assigned_to) {
            $all_opportunities->where('assigned_to', $assigned_to);
        }
        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $user_ids = getUsersReportingToAuth();
            $lead_ids = Lead::where('assign_to', $user_ids)->pluck('id');
            $all_opportunities->where('assigned_to', $user_ids);
        }
        $all_opportunities = $all_opportunities->get();

        $opportunity_status = OpportunitieStatus::orderBy('ordering', 'asc')->get();

        $view = view('leads-opportunities.inc_card_data', compact(
            'all_opportunities',
            'opportunity_status'
        ))->render();

        $total_annualised_value = ($all_opportunities->sum('amount') ?? 0);
        return response()->json([
            'status' => true,
            'view' => $view,
            'total_annualised_value' => $total_annualised_value,
        ]);
    }

    public function updateCardStatus(Request $request)
    {

        $card_id = $request->card_id;
        $new_status = $request->new_status;
        $lead_opportunity = LeadOpportunity::where(['id' => $card_id])->first();
        if ($lead_opportunity) {
            $oppo_status = OpportunitieStatus::orderBy('ordering', 'desc')->first();
            if ($new_status == $oppo_status->id) {
                Lead::where(['id' => $lead_opportunity->lead_id])->update(['conversion_date' => date('Y-m-d')]);
            }
            $lead_opportunity->update(['status' => $new_status]);

            $cur_status = OpportunitieStatus::where(['id' => $lead_opportunity->status])->first();
            $msg = '🎯 Lead move to opportunity ' . $cur_status->status_name .
                    ': ' . Str::limit($lead_opportunity->lead->company_name, 10, '...') .
                    ' by ' . Auth::user()->name;
            SendPushNotification($lead_opportunity->lead->created_by, $msg, 'opportunity');
            StoreLeadNotification($lead_opportunity->id, 'New Opportunity', $msg, $lead_opportunity->lead->created_by, 'opportunity');
            return response()->json(['status' => true, 'message' => '']);
        } else {
            return response()->json(['status' => false, 'message' => 'data not found.']);
        }
    }

    public function getsingleData(Request $request)
    {
        $id = $request->id;
        $lead_opportunity = LeadOpportunity::where(['id' => $id])->first();
        return response()->json(['status' => true, 'message' => '', 'data' => $lead_opportunity]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'lead_id' => 'required',
            'assigned_to' => 'required',
            'lead_contact_id' => 'required',
            'amount' => 'required',
            //'type'=>'required',
            'estimated_close_date' => 'required',
            'confidence' => 'required',
            'note' => 'required',
            'status' => 'required',

        ];

        $request->validate($rules);
        $created_by = Auth::id();
        $opportunity_id = $request->opportunity_id;
        $lead_opportunity = LeadOpportunity::where(['id' => $opportunity_id])->first();
        if ($lead_opportunity) {
            if ($lead_opportunity->status != $request->status) {
                $oppo_status = OpportunitieStatus::orderBy('ordering', 'desc')->first();
                if ($request->status == $oppo_status->id) {
                    Lead::where(['id' => $lead_opportunity->lead_id])->update(['conversion_date' => date('Y-m-d')]);
                }
            }
            $lead_opportunity->update(['note' => $request->note, 'created_by' => $created_by, 'assigned_to' => $request->assigned_to, 'lead_contact_id' => $request->lead_contact_id, 'estimated_close_date' => $request->estimated_close_date, 'confidence' => $request->confidence, 'status' => $request->status, 'amount' => $request->amount]);
            $request->session()->flash('message_success', __('Lead Opportunity update successfully.'));
        } else {
            $lead_opportunity = LeadOpportunity::create(['note' => $request->note, 'lead_id' => $request->lead_id, 'created_by' => $created_by, 'assigned_to' => $request->assigned_to, 'lead_contact_id' => $request->lead_contact_id, 'amount' => $request->amount, 'type' => $request->type, 'estimated_close_date' => $request->estimated_close_date, 'confidence' => $request->confidence, 'status' => $request->status]);
            $request->session()->flash('message_success', __('Lead Opportunity successfully.'));
        }
        $cur_status = OpportunitieStatus::where(['id' => $lead_opportunity->status])->first();
        $msg = '🎯 Lead move to opportunity ' . $cur_status->status_name .
                ': ' . Str::limit($lead_opportunity->lead->company_name, 10, '...') .
                ' by ' . Auth::user()->name;
        SendPushNotification($lead_opportunity->lead->created_by, $msg, 'opportunity');
        StoreLeadNotification($lead_opportunity->id, 'New Opportunity', $msg, $lead_opportunity->lead->created_by, 'opportunity');


        return redirect()->back();
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, LeadOpportunity $leadOpportunity)
    // {
    //     $rules = [
    //         'lead_id'=>'required',
    //         'assigned_to'=>'required',
    //         'lead_contact_id'=>'required',
    //         'amount'=>'required',
    //         'type'=>'required',
    //         'estimated_close_date'=>'required',
    //         'confidence'=>'required',
    //         'note'=>'required',

    //     ];

    //     $request->validate($rules);
    //     $data = $request->all();
    //     $created_by = Auth::id(); 
    //     $lead_opportunity = LeadOpportunity::where(['id'=>$leadOpportunity->id])->first();
    //     if($lead_opportunity){
    //          $lead_opportunity->update(['note'=>$request->note,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'assigned_to'=>$request->assigned_to,'lead_contact_id'=>$request->lead_contact_id,'amount'=>$request->amount,'type'=>$request->type,'estimated_close_date'=>$request->estimated_close_date,'confidence'=>$request->confidence]);
    //          $request->session()->flash('message_success',__('Lead Opportunity Added successfully.'));
    //     }else{
    //          $request->session()->flash('message_info',__('something went wrong.'));

    //     }

    //     return redirect()->back();
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, LeadOpportunity $leadOpportunity)
    {
        $leadOpportunity->delete();
        $request->session()->flash('message_success', __('Lead Opportunity deleted successfully.'));
        return redirect()->back();
    }
}
