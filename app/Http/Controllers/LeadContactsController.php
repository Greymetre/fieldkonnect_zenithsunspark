<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

use DataTables;
use Auth;

use App\Exports\ExcelExport;
use App\Exports\LeadsContactsTemplate;
use App\Imports\LeadsContactsImport;
use Excel;


use App\Models\Lead;
use App\Models\LeadContact;
use App\Models\LeadNote;

class LeadContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        return view('lead-contacts.index');
    }

    public function getLeadContacts(Request $request){
        $lead_contacts = LeadContact::with(['lead']); 
        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $user_ids = getUsersReportingToAuth();
            $lead_ids = Lead::where('assign_to', $user_ids)->pluck('id');
            $lead_contacts->where('lead_id', $lead_ids);
        }
        $lead_contacts = $lead_contacts->select(\DB::raw(with(new LeadContact)->getTable().'.*'))->groupBy('id');
        return DataTables::of($lead_contacts)
            ->editColumn('lead.company_name', function ($lead_contact) {
                    return $lead_contact->lead->company_name??'';
            })
            ->editColumn('name', function ($lead_contact) {
                $url = route('leads.show',$lead_contact->lead_id);
                return '<a href="'.$url.'">'.$lead_contact->name.'</a>';
            })
           
            ->addColumn('action', function ($lead_contact) {
                return "action";
            })
            ->addColumn('checkbox', function ($lead_contact) {
                $lead_contact_id = "'".$lead_contact->id."'";
                return '<input type="checkbox" class="lead_task-checkbox checkbox_cls" value="'.$lead_contact->id.'" name="lead_contacts_ids[]" onclick="checkboxDelete('.$lead_contact_id.')">';
            })
            ->rawColumns(['action','name','checkbox'])
            ->make(true);
    }

    function exportContacts(Request $request){
        $filename = 'contacts.xlsx';

        $results_per_page = 8000;
        $page_number = intval($request->input('page_number'));
        $page_result = ($page_number-1) * $results_per_page;

        $lead_contacts = LeadContact::with(['lead']); 
        $lead_contacts = $lead_contacts->get();
        $data = $lead_contacts->map(function ($item, $key) {

            return [
                $item->id,
                $item->name,
                $item->title,
                $item->phone_number,
                $item->email,
                $item->url,
                $item->lead->company_name??'',
               

            ];
        })->toArray();

        $export = new ExcelExport([
            'Id',
            'Name',
            'Title',
            'Phone Number',
            'Email',
            'Url',
            'Lead',
        ], $data);

        return Excel::download($export, $filename);
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
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
            'lead_id'=>'required',
            'name'=>'required',
            'title'=>'required',
            'phone_number'=>'required',
            //'contact_email'=>'required',
            //'url'=>'required',
        ];

        $request->validate($rules);
        
        $created_by = Auth::id(); 
        $contact_id = $request->contact_id;
        $lead_contact = LeadContact::where(['id'=>$contact_id])->first();
        if($lead_contact){
            $lead_contact->update(['name'=>$request->name,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'title'=>$request->title,'phone_number'=>$request->phone_number,'email'=>$request->contact_email,'url'=>$request->url]);
            $request->session()->flash('message_success',__('Lead contact Updated successfully.'));
        }else{
            $LeadContact = LeadContact::create(['name'=>$request->name,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'title'=>$request->title,'phone_number'=>$request->phone_number,'email'=>$request->contact_email,'url'=>$request->url]);
            $request->session()->flash('message_success',__('Lead contact Added successfully.'));
        }
        

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Lead $lead)
    {   
        $lead_contacts = LeadContact::where(['lead_id'=>$lead->id])->get();
        return view('leads.edit',compact('lead','lead_contacts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,Lead $lead)
    {
        //return view('leads.edit',compact('lead'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, LeadContact $leadContact)
    {
        $leadContact->delete();
        $request->session()->flash('message_success',__('Lead Contact deleted successfully.'));
        return redirect()->back();
    }

    public function checkboxAction(Request $request){
         $lead_ids = $request->lead_ids;
        $lead_id_arr = explode(",", $lead_ids);
        if(count($lead_id_arr)>0){
            LeadContact::whereIn('id',$lead_id_arr)->delete(); 
            $request->session()->flash('message_success',__('Lead Task deleted successfully.'));
            return redirect()->back();
        }
        
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('lead_contacts_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new LeadsContactsImport, request()->file('import_file'));
        return back();
    }

    public function template()
    {
        abort_if(Gate::denies('lead_contacts_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new LeadsContactsTemplate, 'LeadContactsTemplate.xlsx');
    }
}
