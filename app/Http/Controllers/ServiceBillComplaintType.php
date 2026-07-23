<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceBillComplaintType as ServiceBillComplaintTypes;
use App\Models\Subcategory;
use App\Models\ServiceComplaintReason;
use App\Models\ServiceGroupComplaint;

use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

use App\Exports\ServiceBillComplaintTypeExport;


use Gate;
use DB;
use Excel;
use Validator;
use DataTables;
use Carbon\Carbon;
use Auth;

class ServiceBillComplaintType extends Controller
{

    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->complaint_type = new ServiceBillComplaintTypes();
        $this->path = 'service_bill_complaint_types';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('service-bill-complaint.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $service_complaint_types = ServiceBillComplaintTypes::all();
        $product_groups = Subcategory::all();
        return view('service-bill-complaint.edit', compact('service_complaint_types', 'product_groups'))
       ->with('complaint_type', $this->complaint_type);
    }


    public function getServiceComplaintType(Request $request){
        $query = ServiceBillComplaintTypes::with([
            'service_complaint_reasons',
            'service_group_complaints.subcategory'
        ])->latest()->newQuery();

        if(isset($request->group_name)){
            $query->whereHas('service_group_complaints.subcategory' , function($subquery) use($request){
                   $subquery->where('subcategory_name', 'like', '%' . $request->group_name . '%');
            });
        }

        if(isset($request->complaint_type)){
            $query->where('service_bill_complaint_type_name', 'like', '%' . $request->complaint_type . '%');
        }
       
         return DataTables::of($query)
            ->addIndexColumn()
            ->addIndexColumn()
            ->addColumn('action', function ($query) {
                  $btn = '';
                  $activebtn ='';
                  $btn = $btn.'<a href="'.route('service-bills-complaints-type.edit', encrypt($query->id)).'" class="btn btn-info btn-just-icon btn-sm edit mr-2" id="'.encrypt($query->id).'" title="'.trans('panel.global.edit').' Complaint Type">
                      <i class="material-icons">edit</i>
                    </a>';

                 $btn .= '<form action="' . route('service-bills-complaints-type.destroy', encrypt($query->id)) . '" method="POST" class="delete-form-' .$query->id . '" style="display:inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '

                                <button type="button" class="btn btn-danger btn-just-icon btn-sm delete-coplaint-Type " data-id="' . $query->id . '" title="Delete Service Bill Complaint Type">
                                    <i class="material-icons">delete</i>
                                </button>
                            </form>';

                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>';


            })
            ->addColumn('subcategory', function ($query) {
                return $query->service_group_complaints->subcategory->subcategory_name ?? '';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "subcategory_id" => "required",
            'service_bill_complaint_type_name' => "required",
            "complaints_reasons"   => "required|array|min:1",  // Ensures it's an array and not empty
            "complaints_reasons.*" => "required|string|max:255", // Ensures each value is a valid string
        ]);
        try{
            $serviceBillComplaintTypes = ServiceBillComplaintTypes::create([
                   'service_bill_complaint_type_name' => $request->service_bill_complaint_type_name ?? '' , 
            ]);
            if (!empty($request->complaints_reasons) && is_array($request->complaints_reasons)) {
                foreach ($request->complaints_reasons as $reason) {
                    ServiceComplaintReason::create([
                        'service_bill_complaint_id' => $serviceBillComplaintTypes->id,
                        'service_complaint_reasons' => $reason
                    ]);
                }
            }
            $serviceGroupComplaint = ServiceGroupComplaint::create([
                "subcategory_id" => $request->subcategory_id,
                "service_bill_complaint_id"  => $serviceBillComplaintTypes->id                
            ]);
            return Redirect::to('service-bills-complaints-type')->with('message_success', 'Service Complaint Types added Sucessfully.');
        }
        catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function service_bill_complaint_type_download(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ServiceBillComplaintTypeExport($request), 'ServiceBillComplaintTypes.xlsx');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $complaint_type = ServiceBillComplaintTypes::find($id);
        if(!$complaint_type){
          return redirect()->back()->with('message_error', 'Record not found');
        }
        $service_complaint_types = ServiceBillComplaintTypes::all();
        $product_groups = Subcategory::all();
        return view('service-bill-complaint.edit' , compact('complaint_type' , 'service_complaint_types' , 'product_groups'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $serviceBillComplaintTypes = ServiceBillComplaintTypes::find($id);
        if(!$serviceBillComplaintTypes){
          return redirect()->back()->with('message_error', 'Record not found');
        }
        try{
            $update = false;
            if($request->service_bill_complaint_type_name != $serviceBillComplaintTypes->service_bill_complaint_type_name){
                $serviceBillComplaintTypes->update(['service_bill_complaint_type_name' => $request->service_bill_complaint_type_name]);
                $update = true;
            }
            if (!empty($request->complaints_reasons) && is_array($request->complaints_reasons)) {
                // Fetch existing reasons for the given service_bill_complaint_id
                $existingReasons = ServiceComplaintReason::where('service_bill_complaint_id', $serviceBillComplaintTypes->id)
                    ->pluck('service_complaint_reasons')
                    ->toArray();
                // Sort both arrays for accurate comparison (order-independent)
                sort($existingReasons);
                $newReasons = $request->complaints_reasons;
                sort($newReasons);
                // Check if there is any change in data
                if ($existingReasons !== $newReasons) {
                    $update = true;
                    // Delete all existing records
                    ServiceComplaintReason::where('service_bill_complaint_id', $serviceBillComplaintTypes->id)->delete();

                    // Insert new records
                    foreach ($newReasons as $reason) {
                        ServiceComplaintReason::create([
                            'service_bill_complaint_id' => $serviceBillComplaintTypes->id,
                            'service_complaint_reasons' => $reason
                        ]);
                    }
                }
            }
            if($update != true){
                return redirect()->back()->with('message_error', 'No changes detected in Service complaint types');
            }
            return Redirect::to('service-bills-complaints-type')->with('message_success', 'Service complaint types Updated Sucessfully.');
        }
        catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {  
        try{
            $id = decrypt($id);
            $complaint_type = ServiceBillComplaintTypes::find($id);
            if(!$complaint_type){
              return redirect()->back()->with('message_error', 'Record not found');
            }
            $complaint_type->delete();
            return redirect()->back()->with('message_success', 'Service bill complaint type and there reason delete Sucessfully');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
}
