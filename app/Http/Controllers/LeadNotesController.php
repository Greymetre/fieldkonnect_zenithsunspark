<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

use DataTables;
use Auth;

use App\Models\Lead;
use App\Models\LeadNote;

class LeadNotesController extends Controller
{
    

  


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
    {
        $rules = [
            'lead_id'=>'required',
            'note'=>'required',
            
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id(); 
        $note_id = $request->note_id;
        $lead_note = LeadNote::where(['id'=>$note_id])->first();
        if($lead_note){
            $lead_note->update(['note'=>$request->note,'lead_id'=>$request->lead_id,'created_by'=>$created_by]);
            $request->session()->flash('message_success',__('Lead Note Updated successfully.'));
        }else{
            $lead_note = LeadNote::create(['note'=>$request->note,'lead_id'=>$request->lead_id,'created_by'=>$created_by]);
            $request->session()->flash('message_success',__('Lead Note Added successfully.'));
        }
        

        return redirect()->back();
    }

    


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, LeadNote $leadNote)
    // {
    //      $rules = [
    //         'lead_id'=>'required',
    //         'note'=>'required',
            
    //     ];

    //     $request->validate($rules);
    //     $data = $request->all();
    //     $created_by = Auth::id(); 
    //     $lead_note = LeadNote::where(['id'=>$leadNote->id])->first();
    //     if($lead_note){
    //          $lead_note->update(['note'=>$request->note,'lead_id'=>$request->lead_id,'created_by'=>$created_by]);
    //          $request->session()->flash('message_success',__('Lead Note Added successfully.'));
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
    public function destroy(Request $request, LeadNote $leadNote)
    {
        $leadNote->delete();
        $request->session()->flash('message_success',__('Lead Note deleted successfully.'));
        return redirect()->back();
    }
}
