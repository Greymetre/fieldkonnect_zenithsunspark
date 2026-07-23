<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ServiceBill;
use App\Models\Complaint;
use App\Models\ServiceGroupComplaint;
use App\Models\ServiceChargeChargeType;
use App\Models\ServiceChargeProducts;
use App\Models\ServiceComplaintReason;
use App\Models\ServiceBillProductDetails;
use App\Models\ComplaintTimeline;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\AjaxController;
use Exception;

class ServiceBillCustController extends Controller
{

    public function __construct()
    {
        $this->serviceBill = new ServiceBill();


        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        try{
            if (isset($request->user()->customertype) && $request->user()->customertype ==  4){
               $user_id = $request->user()->id; 
               $query = ServiceBill::whereHas('complaint', function ($query) use ($user_id) {
                    $query->where('service_center', $user_id);
                })->select('id','bill_no','complaint_no','complaint_id','complaint_type','complaint_reason');

                $service_bills = [];
                if(isset($request->status)){
                    $query->where('status' , $request->status);
                }

                $query->chunk(100, function ($serviceBillsChunk) use (&$service_bills) {
                    foreach ($serviceBillsChunk as $service_bill) {
                        $service_bills[] = $service_bill;
                    }
                });
                if(!empty($service_bills)) {
                    return response()->json(['status' => 'success', 'data' => $service_bills], $this->successStatus);
                } else {
                return response()->json(['status' => 'success', 'data' => "No Service Bill"], $this->notFound);
                }
            }else{
                return response()->json(['status' => 'error', 'message' => 'Service Bill can access only Service Center'], $this->notFound);
            }
        }catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        try {
            $service_bill = ServiceBill::where('complaint_id', $id)->exists();
            $complaint = Complaint::with([
                'service_center_details:id,customer_code,name',
                'complaint_type_details',
                'product_details.categories',
                'product_details.subcategories',
                'createdbyname:id,name',
                'warranty_details'
            ])->where(['service_center' => $request->user()->id , 'id' => $id])
                ->first();

            if (!$complaint) {
                 return response()->json(['status' => 'error', 'message' => 'Service center don\'t have the access of this service bill'], $this->notFound);
            }

            $data = collect($complaint->only([
                'complaint_number',
                'complaint_date',
                'product_serail_number',
                'customer_bill_date',
                'under_warranty'
            ]))->mapWithKeys(function ($value, $key) {
                return [
                    match ($key) {
                        'customer_bill_date' => 'warranty_start_date',
                        default => $key,
                    } => ($value === "" ? null : $value)
                ];
            })->toArray();
            $lastServiceBillId = ServiceBill::max('bill_no');
            $newserviceBillNo = $lastServiceBillId ? $lastServiceBillId + 1 : 1;
            $serviceBillNo = str_pad($newserviceBillNo, 3, '0', STR_PAD_LEFT);
            $data["division"] = $complaint->product_details?->categories?->category_name ?? null;
            $data["group_name"] = $complaint->product_details?->subcategories?->subcategory_name ?? null;
            $data['serviceBillNo'] = $serviceBillNo ?? null;
            $data["recived_from"] = optional($complaint->createdbyname)->name ?? null;
            $data["item"] = optional($complaint->product_details)->product_name ?? null;
            $data["comments"] = $complaint ? $complaint->description : null;
            $result = app(AjaxController::class)->getProductTimeInterval(new Request([
                'product_id' => $complaint->product_id,
                'sale_bill_date' => $complaint->customer_bill_date
            ]));
            $response = $result->getData(true);
            $data['warranty_upto'] = $response['warrenty_expire_date'] ?? null;
            if (isset($complaint->product_details->subcategories)) {
                $categoryIds = explode(',', $complaint->product_details->subcategories->service_category_id);
                $service_charge_products = ServiceChargeProducts::whereIn('category_id', $categoryIds)
                    ->distinct()
                    ->pluck('charge_type_id');
                $charge_types = ServiceChargeChargeType::whereIn('id', $service_charge_products)->orWhere('id', 4)->get();
            } else {
                $charge_types = ServiceChargeChargeType::all();
            }
            if (isset($complaint->service_center)) {
                $data += [
                    'charge_types ' => $charge_types->map(fn($charge_type) => [
                        'key' => $charge_type->id,
                        'value' =>  $charge_type->charge_type
                    ])->toArray()
                ];
            }


            if ($complaint) {
                return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
            } else {
                return response()->json(['status' => 'success', 'data' => "No Service Bill"], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }


    private function getComplaintDropdowns($complaint)
    {
        $category_of_complaint = ["Electrical Fault", "Mechanical Fault", "Physical Fault"];
        $service_bill_complaint = ServiceGroupComplaint::where('subcategory_id', $complaint->product_details->subcategories->id)->get();
        $condition_of_service = ["Full Finish", "Regular Repair", "Field Visit"];
        $received_product = ["Pump", "Motor", "Pump Set", "Fan", "Heater", "Induction CookTop"];
        $nature_of_faults = ["Transit Damage", "Manufacturing Fault", "Customer Field Fault"];
        $service_locations = ["Site Visit", "At ASC"];
        $water_sources = ["Munciple Water Supply", "Well", "BoreWell", "Water Sump", "Hand Pump", "Pond / Dam", "RO Water Plant"];
        $service_bill_statuses = ["Draft", "Claimed", "Customer payble", "Approved", "Cancel"];
        $repaired_replacement = ["Replacement", "Repaired"];
        if (isset($complaint->product_details->subcategories)) {
            $categoryIds = explode(',', $complaint->product_details->subcategories->service_category_id);
            $service_charge_products = ServiceChargeProducts::whereIn('category_id', $categoryIds)
                ->distinct()
                ->pluck('charge_type_id');
            $charge_type = ServiceChargeChargeType::whereIn('id', $service_charge_products)->orWhere('id', 4)->get();
        } else {
            $charge_type = ServiceChargeChargeType::all();
        }

        return [
            'category_of_complaint' => collect($category_of_complaint)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray(),
            'complaint_type' => $service_bill_complaint->map(fn($complaint_type) => [
                'id' => $complaint_type->service_bill_complaint_type?->id,
                'key' => $complaint_type->service_bill_complaint_type->service_bill_complaint_type_name,
                'value' => $complaint_type->service_bill_complaint_type->service_bill_complaint_type_name
            ])->toArray(),
            'condition_of_service' => collect($condition_of_service)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray(),
            'received_product' => collect($received_product)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray(),
            'service_locations' => collect($service_locations)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray(),
            'nature_of_faults' => collect($nature_of_faults)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray(),
            'water_sources' => collect($water_sources)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray(),
            'service_bill_statuses' => collect($service_bill_statuses)->map(fn($item, $index) => ['key' => $index, 'value' => $item])->toArray(),
            'repaired_replacement' => collect($repaired_replacement)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray(),
            'charge_type'          => collect($charge_type)->map(fn($item) => ['key' => $item->id, 'value' => $item->charge_type])->toArray(),
        ];
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        try {

            $complaint = Complaint::with([
                'service_center_details:id,customer_code,name',
                'complaint_type_details',
                'product_details.categories',
                'product_details.subcategories',
                'createdbyname:id,name',
                'warranty_details'
            ])->where(['id' => $id , 'service_center' => $request->user()->id])
                ->first();

            if (!$complaint) {
                return response()->json(['status' => 'error', 'message' => 'Loggedin uesr don\'t have access of this service'], $this->notFound);
            }


            $validator = Validator::make($request->all(), [
                'serviceBillNo' => 'required',
                'category_of_complaint' => 'nullable|string',
                'complaint_type' => 'nullable|exists:service_bill_complaint_types,service_bill_complaint_type_name',
                'complaint_reason' => 'nullable|exists:service_complaint_reasons,service_complaint_reasons',
                'condition_of_service' => 'nullable|string',
                'received_product'     => 'nullable|string',
                'nature_of_fault' => 'nullable|string',
                'service_location'     => 'nullable|string',
                'repaired_replacement' => 'nullable|in:Replacement,Repaired',
                'line_voltage' => 'nullable|numeric|digits:3',
                'load_voltage' => 'nullable|numeric|digits:3',
                'current' => 'nullable|numeric|digits:2',
                'water_source' => 'nullable|string',
                'panel_rating_running' => 'nullable|numeric|digits:3',
                'panel_rating_starting' => 'nullable|regex:/^\d+\/\d+$/',
                'product_sr_no'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'scr_job_card'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'photo_3'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'photo_4'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'photo_5'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'voltage_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'current_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'service.*.service_type' => 'required|integer',
                'service.*.product_id' => 'nullable|integer',
                'service.*.quantity' => 'required|integer|min:1',
                'service.*.distance' => 'nullable|numeric|min:0',
                'service.*.appreciation' => 'nullable',
                'service.*.price' => 'required|numeric|min:0',
                'service.*.subtotal' => 'required|numeric|min:0',
            ]);

            $serviceTypes = collect($request->input('service'))->pluck('service_type');
            $duplicates = $serviceTypes
                ->filter(function ($type) {
                    return in_array($type, [1, 5]);
                })
                ->countBy()
                ->filter(function ($count) {
                    return $count > 1;
                });

            if ($duplicates->isNotEmpty()) {
                 return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => 'Service types found duplicated'
                ], 422); // 422 Unprocessable Entity
                return back()->withErrors([
                    'service' => 'Service types 1 and 5 cannot be duplicated.',
                ])->withInput();
            }

            // If validation fails, return JSON response
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422); // 422 Unprocessable Entity
            }

            if ($request->repaired_replacement == 'Replacement') {
                $replacement_tag = 'Yes';
                $replacement_tag_number = $request->replacement_tag_number;
            } else {
                $replacement_tag = 'No';
                $replacement_tag_number = NULL;
            }

            $lastServiceBillId = ServiceBill::max('bill_no');
            $newserviceBillNo = $lastServiceBillId ? $lastServiceBillId + 1 : 1;
            $serviceBillNo = str_pad($newserviceBillNo, 3, '0', STR_PAD_LEFT);

            $new_service_bill = ServiceBill::updateOrCreate(['complaint_id' => $complaint->id, 'complaint_no' => $complaint->complaint_number], [
                'bill_no' => $serviceBillNo ?? null,
                'complaint_id' => $complaint->id ?? null,
                'complaint_no' => json_decode($complaint)->complaint_number ?? null,
                'division' => $complaint->product_details->categories->id ?? null,
                'category' => $request->category_of_complaint ?? null,
                'complaint_type' => $request->complaint_type ?? null,
                'complaint_reason' => $request->complaint_reason ?? null,
                'condition_of_service' => $request->condition_of_service ?? null,
                'received_product' => $request->received_product ?? null,
                'nature_of_fault' => $request->nature_of_fault ?? null,
                'service_location' => $request->service_location ?? null,
                'repaired_replacement' => $request->repaired_replacement ?? null,
                'replacement_tag' => $replacement_tag ?? null,
                'replacement_tag_number' => $replacement_tag_number ?? null,
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
                ServiceBillProductDetails::where('service_bill_id', $new_service_bill->id)->delete();
                if (isset($complaint->service_center) && $request->service && count($request->service) > 0) {
                    foreach ($request->service as $service) {
                        ServiceBillProductDetails::create([
                            'service_bill_id' => $new_service_bill->id,
                            'service_type' => $service['service_type'],
                            'product_id' => $service['product_id'],
                            'quantity' => $service['quantity'] ?? '',
                            'distance' => $service['distance'] ?? '',
                            'appreciation' => $service['appreciation'] ?? '',
                            'price' => $service['price'],
                            'subtotal' => $service['subtotal']
                        ]);
                    }
                }

                ComplaintTimeline::create([
                    'complaint_id' => $complaint->id,
                    'created_by' => $request->user()->id,
                    'status' => '102',
                    'remark' => 'Service Bill Created',
                ]);

                return response()->json(['status' => 'success', 'message' => 'Service bill created successfully', 'data' => $new_service_bill], $this->created);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $service_bill = ServiceBill::with('service_bill_products.service_type_details', 'service_bill_products.product')->find($id);
            if (!$service_bill) {
                return response()->json(['status' => 'error', 'message' => 'Service bill Not Exsits'], $this->badrequest);
            }
            $complaint = Complaint::with([
                'service_center_details:id,customer_code,name',
                'complaint_type_details',
                'product_details.categories',
                'product_details.subcategories',
                'createdbyname:id,name',
                'warranty_details'
            ])->where(['service_center' => $request->user()->id])
                ->first();


            if (!$complaint) {
                 return response()->json(['status' => 'error', 'message' => 'Service center don\'t have the access of this service bill'], $this->notFound);
            }

            $data = collect($complaint->only([
                'complaint_number',
                'complaint_date',
                'product_serail_number',
                'customer_bill_date',
                'under_warranty'
            ]))->mapWithKeys(function ($value, $key) {
                return [
                    match ($key) {
                        'customer_bill_date' => 'warranty_start_date',
                        default => $key,
                    } => ($value === "" ? null : $value)
                ];
            })->toArray();

            $data += [
                "service_center_name" => optional($complaint->service_center_details)?->name 
                    ? ($complaint->service_center_details->customer_code 
                        ? "[{$complaint->service_center_details->customer_code}] " 
                        : "") . $complaint->service_center_details->name
                    : null,
                "created_by" => optional($complaint->createdbyname)?->name,
            ];

            $data['service_bill'] = collect($service_bill->only([
                'complaint_type',
                'complaint_reason',
                'condition_of_service',
                'received_product',
                'nature_of_fault',
                'service_location',
                'repaired_replacement',
                'replacement_tag',
                'replacement_tag_number',
                'line_voltage',
                'load_voltage',
                'current',
                'water_source',
                'panel_rating_running',
                'panel_rating_starting',
                'status'
            ]))->mapWithKeys(function ($value, $key) {
                return [
                    match ($key) {
                        default => $key,
                    } => ($value === "" ? null : $value)
                ];
            })->toArray();

            if (isset($service_bill)) {
                $data['service_bill']['service_bill_status_name'] =
                    $service_bill->status == 0 ? "Draft" : ($service_bill->status == 1 ? "Claimed" : ($service_bill->status == 2 ? "Customer Payable" : ($service_bill->status == 3 ? "Approved" : "Cancel")));
            }

            $data['service_bill_products'] = $service_bill?->service_bill_products ?
                $service_bill->service_bill_products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'service_bill_id' => $product->service_bill_id,
                        'service_type' => $product->service_type,
                        'service_type_name' => $product->service_type_details?->charge_type, // Accessing related model
                        'product_id' => $product->product_id,
                        'product_name' => $product->product?->product_name, // Accessing related model
                        'quantity' => $product->quantity ?? null,
                        'distance' => $product->distance ?? null,
                        'appreciation' => $product->appreciation ?? null,
                        'price' => $product->price,
                        'subtotal' => $product->subtotal
                    ];
                })->toArray() : null;



            $lastServiceBillId = ServiceBill::max('bill_no');
            $newserviceBillNo = $lastServiceBillId ? $lastServiceBillId + 1 : 1;
            $serviceBillNo = str_pad($newserviceBillNo, 3, '0', STR_PAD_LEFT);
            $data["division"] = $complaint->product_details?->categories?->category_name ?? null;
            $data["group_name"] = $complaint->product_details?->subcategories?->subcategory_name ?? null;
            $data['serviceBillNo'] = $serviceBillNo ?? null;
            $data["recived_from"] = optional($complaint->createdbyname)->name ?? null;
            $data["item"] = optional($complaint->product_details)->product_name ?? null;
            $data["comments"] = $complaint ? $complaint->description : null;
            $result = app(AjaxController::class)->getProductTimeInterval(new Request([
                'product_id' => $complaint->product_id,
                'sale_bill_date' => $complaint->customer_bill_date
            ]));
            $response = $result->getData(true);
            $data['warranty_upto'] = $response['warrenty_expire_date'] ?? null;
            $imageArray = [];
            $collectionNames = [
                'product_sr_no',
                'scr_job_card',
                'photo_3',
                'photo_4',
                'photo_5',
                'voltage_image',
                'current_image',
            ];

            $imageArray = [];

            foreach ($collectionNames as $collection) {
                $medias = $service_bill->getMedia($collection);

                $mediaData = $medias->isEmpty()
                    ? [[
                        'name' => 'no-image.png',
                        'url' => asset(config('constants.NO_IMAGE_URL')),
                    ]]
                    : $medias->map(function ($media) {
                        return [
                            'name' => $media->file_name,
                            'url' => $media->getFullUrl(),
                        ];
                    })->toArray();

                // Assign directly to key
                $imageArray[$collection] = $mediaData;
            }
            $data['images'] = $imageArray;

            if ($complaint) {
                return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
            } else {
                return response()->json(['status' => 'success', 'data' => "Service Bill Not Found"], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'nullable|in:0,1,2,3,4,5'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422); // 422 Unprocessable Entity
            }
            $service_bill = ServiceBill::with('service_bill_products.service_type_details', 'service_bill_products.product')->find($id);
            if (!$service_bill) {
                return response()->json(['status' => 'error', 'message' => 'Service bill Not Exsits'], $this->badrequest);
            }
            if (isset($request->status)) {
                $newStatus = $request->status ?? 0;
                // if ($newStatus > $service_bill->status) {
                $service_bill->status = $request->status;
                $service_bill->save();
                return response()->json(['status' => 'success', 'message' => 'Service Bill Status updated successfully!']);
                // } else {
                //     return response()->json(['status' => 'error', 'message' => 'You can\'t revert the status.']);
                // }
            }
            return response()->json(['status' => 'success', 'message' => 'No changes detected !']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
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
        try {
            $service_bill = ServiceBill::with('service_bill_products.service_type_details', 'service_bill_products.product')->find($id);
            if (!$service_bill) {
                return response()->json(['status' => 'error', 'message' => 'Service bill Not Exsits'], $this->badrequest);
            }
            $complaint = Complaint::with([
                'service_center_details:id,customer_code,name',
                'complaint_type_details',
                'product_details.categories',
                'product_details.subcategories',
                'createdbyname:id,name',
                'warranty_details'
            ])->where(['id' => $id , 'service_center' => $request->user()->id])
                ->first();

            if (!$complaint) {
                return response()->json(['status' => 'error', 'message' => 'Loggedin uesr don\'t have access of this service bill'], $this->notFound);
            }

            $validator = Validator::make($request->all(), [
                'serviceBillNo' => 'required',
                'category_of_complaint' => 'nullable|string',
                'complaint_type' => 'nullable|exists:service_bill_complaint_types,service_bill_complaint_type_name',
                'complaint_reason' => 'nullable|exists:service_complaint_reasons,service_complaint_reasons',
                'condition_of_service' => 'nullable|string',
                'received_product'     => 'nullable|string',
                'nature_of_fault' => 'nullable|string',
                'service_location'     => 'nullable|string',
                'repaired_replacement' => 'nullable|in:Replacement,Repaired',
                'line_voltage' => 'nullable|numeric|digits:3',
                'load_voltage' => 'nullable|numeric|digits:3',
                'current' => 'nullable|numeric|digits:2',
                'water_source' => 'nullable|string',
                'panel_rating_running' => 'nullable|numeric|digits:3',
                'panel_rating_starting' => 'nullable|regex:/^\d+\/\d+$/',
                'product_sr_no'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'scr_job_card'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'photo_3'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'photo_4'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'photo_5'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'voltage_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'current_image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
                'service.*.service_type' => 'required|integer',
                'service.*.product_id' => 'nullable|integer',
                'service.*.quantity' => 'required|integer|min:1',
                'service.*.distance' => 'nullable|numeric|min:0',
                'service.*.appreciation' => 'nullable',
                'service.*.price' => 'required|numeric|min:0',
                'service.*.subtotal' => 'required|numeric|min:0',
            ]);

           $serviceTypes = collect($request->input('service'))->pluck('service_type');
            $duplicates = $serviceTypes
                ->filter(function ($type) {
                    return in_array($type, [1, 5]);
                })
                ->countBy()
                ->filter(function ($count) {
                    return $count > 1;
                });

            if ($duplicates->isNotEmpty()) {
                 return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => 'Service types found duplicated'
                ], 422); // 422 Unprocessable Entity
                return back()->withErrors([
                    'service' => 'Service types 1 and 5 cannot be duplicated.',
                ])->withInput();
            }
            
            // If validation fails, return JSON response
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422); // 422 Unprocessable Entity
            }
            if ($request->repaired_replacement == 'Replacement') {
                $replacement_tag = 'Yes';
                $replacement_tag_number = $request->replacement_tag_number;
            } else {
                $replacement_tag = 'No';
                $replacement_tag_number = NULL;
            }
            $serviceBillNo = $service_bill->bill_no;

            $new_service_bill = ServiceBill::updateOrCreate(['complaint_id' => $complaint->id, 'complaint_no' => $complaint->complaint_number], [
                'bill_no' => $serviceBillNo ?? null,
                'complaint_id' => $complaint->id ?? null,
                'complaint_no' => json_decode($complaint)->complaint_number ?? null,
                'division' => $complaint->product_details->categories->id ?? null,
                'category' => $request->category_of_complaint ?? null,
                'complaint_type' => $request->complaint_type ?? null,
                'complaint_reason' => $request->complaint_reason ?? null,
                'condition_of_service' => $request->condition_of_service ?? null,
                'received_product' => $request->received_product ?? null,
                'nature_of_fault' => $request->nature_of_fault ?? null,
                'service_location' => $request->service_location ?? null,
                'repaired_replacement' => $request->repaired_replacement ?? null,
                'replacement_tag' => $replacement_tag ?? null,
                'replacement_tag_number' => $replacement_tag_number ?? null,
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
                ServiceBillProductDetails::where('service_bill_id', $new_service_bill->id)->delete();
                if (isset($complaint->service_center) && $request->service && count($request->service) > 0) {
                    foreach ($request->service as $service) {
                        ServiceBillProductDetails::create([
                            'service_bill_id' => $new_service_bill->id,
                            'service_type' => $service['service_type'],
                            'product_id' => $service['product_id'],
                            'quantity' => $service['quantity'] ?? '',
                            'distance' => $service['distance'] ?? '',
                            'appreciation' => $service['appreciation'] ?? '',
                            'price' => $service['price'],
                            'subtotal' => $service['subtotal']
                        ]);
                    }
                }
                return response()->json(['status' => 'success', 'message' => 'Service bill created successfully', 'data' => $new_service_bill], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
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
        //
    }

    public function serviceBillComplaintReasons(Request $request)
    {
        try {
            $complaintId = $request->complaintId ?? '';
            $selected = $request->selected ?? '';
            $reasons = ServiceComplaintReason::where('service_bill_complaint_id', $complaintId)->get();
            $data = [
                'complaint_reasons' => $reasons->map(fn($reason) => [
                    'key' => $reason->service_complaint_reasons,
                    'value' =>  $reason->service_complaint_reasons
                ])->toArray()
            ];
            if ($data) {
                return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
            } else {
                return response()->json(['status' => 'success', 'data' => "No Reason"], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    // get service charge product 
    public function getServiceChargeProduct(Request $request, $id)
    {
        try {
            $data = [];
            $complaint = Complaint::where(['id'=> $id , 'service_center' => $request->user()->id])
                ->first();

            if (!$complaint) {
                return response()->json(['status' => 'error', 'message' => 'Service center don\'t have access of this service bill'], $this->notFound);
            }

            if($request->charge_type_id == 4){
                return response()->json(['status' => 'success', 'data' => "custom"], $this->successStatus);
            }

            $pro_sub_cat = $complaint->product_details->subcategories->service_category_id;
            $pro_cat =  $complaint ? $complaint->product_details->category_id : '';
            $categoryIds = explode(',', $pro_sub_cat);
            $service_charge_products = ServiceChargeProducts::where('charge_type_id', $request->charge_type_id)->whereIn('category_id', $categoryIds)->get();
            if (isset($service_charge_products)) {
                $data = collect($service_charge_products)->toArray();
            }
            if ($data) {
                return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
            } else {
                return response()->json(['status' => 'success', 'data' => "No Product"], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getServiceProductDetails(Request $request, $id)
    {
        try {
            $data = [];

            $service_charge_products = ServiceChargeProducts::where('id', $request->id)->first();
            if (isset($service_charge_products)) {
                $data = collect($service_charge_products)->toArray();
            }
            if ($data) {
                return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
            } else {
                return response()->json(['status' => 'success', 'data' => "No Product"], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function select_option(Request $request, $id)
    {
        try {
            $service_bill = ServiceBill::where('complaint_id', $id)->exists();

            $complaint = Complaint::with([
                'service_center_details:id,customer_code,name',
                'complaint_type_details',
                'product_details.categories',
                'product_details.subcategories',
                'createdbyname:id,name',
                'warranty_details'
            ])->where(['id'=> $id , 'service_center' => $request->user()->id])
                ->first();

            if (!$complaint) {
                return response()->json(['status' => 'error', 'message' => 'Service center don\'t have access of this service bill'], $this->notFound);
            }
            $data = $this->getComplaintDropdowns($complaint);
            if ($complaint) {
                return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
            } else {
                return response()->json(['status' => 'success', 'data' => "No Complaints"], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }


    public function change_status(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                   'status' => 'required|in:0,1,2,4'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422); // 422 Unprocessable Entity
            }

            $userId = $request->user()->id;
            $service_bill = ServiceBill::where('id', $id)
                ->whereHas('complaint', function ($q) use ($userId) {
                    $q->where('service_center', $userId);
                })
                ->select('id', 'complaint_no', 'bill_no', 'status')
                ->first();

            if(!$service_bill){
                return response()->json(['status' => 'error', 'message' => 'Loggedin uesr don\'t have a access to change the status of this service bill'], $this->notFound);
            }
            if(isset($request->status)){
                 $message = "Draft";
                 switch ($request->status) {
                    case '1':
                         $message = "Claimed to Company";
                         break;
                    case '2':
                         $message = "Customer Payable";
                         break;
                    case '3':
                         $message = "Approved";
                         break;
                    case '4':
                         $message = "Canceled";
                         break;
                    default:
                         $message = "Draft";
                         break;
                 }

                 $service_bill->status = $request->status;
                 $service_bill->save();
                 return response()->json(['status' => true , 'message' => 'Service bill status is now ' .$message , 'data' => $service_bill],$this->successStatus);
            }
            return response()->json(['status' => true , 'message' => 'No changes detected', 'data' => $service_bill],$this->successStatus);            
        }catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
