<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use App\DataTables\ServiceBillDataTable;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Complaint;
use App\Models\ComplaintTimeline;
use App\Models\Division;
use App\Models\ServiceBill;
use App\Models\ServiceGroupComplaint;
use App\Models\ServiceBillProductDetails;
use App\Models\ServiceChargeChargeType;
use App\Models\ServiceChargeProducts;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;


class ServiceBillController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->service_bill = new ServiceBill();
        $this->path = 'service_bill';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ServiceBillDataTable $dataTable)
    {
        abort_if(Gate::denies('service_bill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('service_bill.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->complaint_id) {
            $complaint = Complaint::with(['product_details.subcategories'])->find($request->complaint_id);
        } else {
            $complaint = Complaint::find(0);
        }
        
        if(isset($complaint->product_details->subcategories)){
           $categoryIds = explode(',', $complaint->product_details->subcategories->service_category_id);
            $service_charge_products = ServiceChargeProducts::whereIn('category_id', $categoryIds)
                ->distinct()
                ->pluck('charge_type_id');
            $charge_type = ServiceChargeChargeType::whereIn('id',$service_charge_products)->orWhere('id' , 4)->get();
        }else{
            $charge_type = ServiceChargeChargeType::all();
        }

        $service_bill_complaint = ServiceGroupComplaint::where('subcategory_id' , $complaint->product_details?->subcategories->id)->get() ?? [];
 
        $all_complaint_number = Complaint::with('product_details')->get();
        $lastServiceBillId = ServiceBill::max('bill_no');
        $newserviceBillNo = $lastServiceBillId ? $lastServiceBillId + 1 : 1;
        $serviceBillNo = str_pad($newserviceBillNo, 3, '0', STR_PAD_LEFT);
        $divisions = Category::where('active', 'Y')->select('id', 'category_name')->get();
        $groups = Subcategory::where('active', 'Y')->select('id', 'subcategory_name')->get();

        return view('service_bill.create', compact('complaint', 'serviceBillNo', 'all_complaint_number', 'divisions', 'charge_type' , 'groups' , 'service_bill_complaint'))->with('service_bill', $this->service_bill);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->service_bill_id && !empty($request->service_bill_id)) {
            $serviceBillNo = $request->service_bill_no;
        } else {
            $lastServiceBillId = ServiceBill::max('bill_no');
            $newserviceBillNo = $lastServiceBillId ? $lastServiceBillId + 1 : 1;
            $serviceBillNo = str_pad($newserviceBillNo, 3, '0', STR_PAD_LEFT);
        }
        if ($request->repaired_replacement == 'Replacement') {
            $replacement_tag = 'Yes';
            $replacement_tag_number = $request->replacement_tag_number;
        } else {
            $replacement_tag = 'No';
            $replacement_tag_number = NULL;
        }
        
        $new_service_bill = ServiceBill::updateOrCreate(['complaint_id' => $request->complaint_id, 'complaint_no' => $request->complaint_number], [
            'bill_no' => $serviceBillNo,
            'complaint_id' => $request->complaint_id,
            'complaint_no' => json_decode($request->complaint_details)->complaint_number,
            'division' => $request->product_division,
            'category' => $request->category,
            'complaint_type' => $request->complaint_type,
            'complaint_reason' => $request->complaint_reason,
            'condition_of_service' => $request->condition_of_service,
            'received_product' => $request->received_product,
            'nature_of_fault' => $request->nature_of_fault,
            'service_location' => $request->service_location,
            'repaired_replacement' => $request->repaired_replacement,
            'replacement_tag' => $replacement_tag,
            'replacement_tag_number' => $replacement_tag_number,
            'line_voltage' => $request->line_voltage ?? '',
            'load_voltage' => $request->load_voltage ?? '',
            'current' => $request->current ?? '',
            'water_source' => $request->water_source ?? '',
            'panel_rating_running' => $request->panel_rating_running ?? '',
            'panel_rating_starting' => $request->panel_rating_starting ?? '',
        ]);

        if ($new_service_bill) {
            if ($request->hasFile('product_sr_no')) {
                $file = $request->file('product_sr_no');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $new_service_bill->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('product_sr_no');
            }
            if ($request->hasFile('scr_job_card')) {
                $file = $request->file('scr_job_card');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $new_service_bill->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('scr_job_card');
            }
            if ($request->hasFile('photo_3')) {
                $file = $request->file('photo_3');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $new_service_bill->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('photo_3');
            }
            if ($request->hasFile('photo_4')) {
                $file = $request->file('photo_4');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $new_service_bill->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('photo_4');
            }
            if ($request->hasFile('photo_5')) {
                $file = $request->file('photo_5');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $new_service_bill->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('photo_5');
            }
            if ($request->hasFile('voltage_image')) {
                $file = $request->file('voltage_image');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $new_service_bill->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('voltage_image');
            }
            if ($request->hasFile('current_image')) {
                $file = $request->file('current_image');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $new_service_bill->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('current_image');
            }
            if ($request->service && count($request->service) > 0) {
                foreach ($request->service as $service) {
                    if (isset($service['service_bill_product_id']) && !empty($service['service_bill_product_id'])) {
                        ServiceBillProductDetails::where('id', $service['service_bill_product_id'])->update([
                            'service_type' => $service['service_type'],
                            'product_id' => $service['product_id'],
                            'quantity' => $service['quantity'],
                            'distance' => $service['distance'],
                            'appreciation' => $service['appreciation'],
                            'price' => $service['price'],
                            'subtotal' => $service['subtotal']
                        ]);
                    } else {
                        ServiceBillProductDetails::create([
                            'service_bill_id' => $new_service_bill->id,
                            'service_type' => $service['service_type'],
                            'product_id' => $service['product_id'],
                            'quantity' => $service['quantity'],
                            'distance' => $service['distance'],
                            'appreciation' => $service['appreciation'],
                            'price' => $service['price'],
                            'subtotal' => $service['subtotal']
                        ]);
                    }
                }
            }

            ComplaintTimeline::create([
                'complaint_id' => $request->complaint_id,
                'created_by' => auth()->user()->id,
                'status' => '102',
                'remark' => 'Service Bill Created',
            ]);


            return Redirect::to('service_bills')->with('message_success', 'Service Bill Store Successfully and the Service Bill number is <span title="Copy" id="copyText">' . $serviceBillNo . '</span>');
        } else {
            return Redirect::to('service_bills')->with('message_error', 'Service Bill Not store.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ServiceBill  $serviceBill
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceBill $serviceBill)
    {
        return view('service_bill.show', compact('serviceBill'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServiceBill  $serviceBill
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceBill $serviceBill, Request $request)
    {
        $complaint = $serviceBill->complaint;
        $this->service_bill = $serviceBill;
        $charge_type = ServiceChargeChargeType::all();
        $all_complaint_number = Complaint::with('product_details')->get();
        $serviceBillNo = $serviceBill->bill_no;
        $divisions = Category::where('active', 'Y')->select('id', 'category_name')->get();
        $groups = Subcategory::where('active', 'Y')->select('id', 'subcategory_name')->get();
        $service_bill_complaint = ServiceGroupComplaint::where('subcategory_id' , $complaint->product_details?->subcategories->id)->get() ?? []; 
        return view('service_bill.create', compact('complaint', 'serviceBillNo', 'all_complaint_number', 'divisions', 'charge_type' , 'groups' , 'service_bill_complaint'))->with('service_bill', $this->service_bill);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceBill  $serviceBill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceBill $serviceBill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ServiceBill  $serviceBill
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceBill $serviceBill)
    {
        //
    }

    public function company_claim(Request $request)
    {
        try {
            $service_bill = ServiceBill::find($request->id);
            $service_bill->status = '1';
            $service_bill->save();

            // ComplaintTimeline::create([
            //     'complaint_id' => $request->id,
            //     'created_by' => auth()->user()->id,
            //     'status' => '0',
            // ]);

            return response()->json(['status' => 'success', 'message' => 'Service Bill Status update successfully !']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function customer_pay(Request $request)
    {
        try {
            $service_bill = ServiceBill::find($request->id);
            $service_bill->status = '2';
            $service_bill->save();

            // ComplaintTimeline::create([
            //     'complaint_id' => $request->id,
            //     'created_by' => auth()->user()->id,
            //     'status' => '0',
            // ]);

            return response()->json(['status' => 'success', 'message' => 'Service Bill Status update successfully !']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function draftBill(Request $request)
    {
        try {
            $service_bill = ServiceBill::find($request->id);
            $service_bill->status = '0';
            $service_bill->save();

            // ComplaintTimeline::create([
            //     'complaint_id' => $request->id,
            //     'created_by' => auth()->user()->id,
            //     'status' => '0',
            // ]);

            return response()->json(['status' => 'success', 'message' => 'Service Bill Status update successfully !']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function approveBill(Request $request)
    {
        try {
            $service_bill = ServiceBill::find($request->id);
            $service_bill->status = '3';
            $service_bill->save();

            // ComplaintTimeline::create([
            //     'complaint_id' => $request->id,
            //     'created_by' => auth()->user()->id,
            //     'status' => '0',
            // ]);

            return response()->json(['status' => 'success', 'message' => 'Service Bill Approved successfully !']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function cancelBill(Request $request)
    {
        try {
            $service_bill = ServiceBill::find($request->id);
            $service_bill->status = '4';
            $service_bill->save();

            // ComplaintTimeline::create([
            //     'complaint_id' => $request->id,
            //     'created_by' => auth()->user()->id,
            //     'status' => '0',
            // ]);

            return response()->json(['status' => 'success', 'message' => 'Service Bill Status update successfully !']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function remove_product(Request $request)
    {
        if(!empty($request->id)){
            ServiceBillProductDetails::where('id', $request->id)->delete();
            return response()->json(['status'=>'success', 'msg'=>'Service bill product delete successfully.']);
        }else{
            return response()->json(['status'=>'success', 'msg'=>'Service bill product not found.']);
        }
    }
}
