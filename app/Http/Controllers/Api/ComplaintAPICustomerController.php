<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\ComplaintWorkDone;
use App\Models\ServiceBill;
use App\Models\ComplaintTimeline;
use App\Models\ComplaintType;
use App\Models\Customers;
use App\Models\Category;
use App\Models\User;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\Branch;
use App\Models\EmployeeDetail;

class ComplaintAPICustomerController extends Controller
{

    public function __construct()
    {
        $this->complaint = new Complaint();


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
    public function index(Request $request)
    {
        try {
            
            if (isset($request->user()->customertype) && $request->user()->customertype ==  4){
                $filters = $request->all();
                    // $user_ids = getUsersReportingToAuth($request->user()->id);
                $user_id = $request->user()->id; 
                $query = Complaint::with(['service_bill' => function ($query) {
                            $query->select('id', 'complaint_id' , 'status'); // Add columns you need
                        }])->where('service_center', $user_id)
                        ->select('id', 'complaint_number', 'complaint_status', 'complaint_date' , 'service_type')->orderBy('id', 'desc');

                if (isset($request->from_date) && isset($request->to_date)) {
                    $complaint_from_date = Carbon::parse($request->from_date)->startOfDay()->format('Y-m-d');
                    $complaint_to_date = Carbon::parse($request->to_date)->startOfDay()->format('Y-m-d');
                    $query->whereBetween('complaint_date', [$complaint_from_date, $complaint_to_date]);
                }

                foreach ($filters as $key => $value) {
                    if (isset($value)) {
                        switch ($key) {
                            case 'complaint_status':
                            case 'complaint_type':
                            case 'under_warranty':
                            case 'purchased_branch':
                            case 'service_type':
                            case 'warranty_bill':
                            case 'register_by':
                            case 'category':
                            case 'complaint_recieve_via';
                                $query->where($key, $value);
                                break;
                            case 'complaint_number':
                                $query->where($key, 'like', "%{$value}%");
                                break;
                            case 'service_bill':
                                $query->whereHas('service_bill', function ($subquery) use ($value) {
                                    $subquery->where('status', $value);
                                });
                                break;
                            // Add more cases if needed
                        }
                    }
                }

                $complaints = [];
                $query->chunk(100, function ($complaintChunk) use (&$complaints) {
                    foreach ($complaintChunk as $complaint) {
                        $complaints[] = $complaint;
                    }
                });

                if (!empty($complaints)) {
                    return response()->json(['status' => 'success', 'data' => $complaints], $this->successStatus);
                } else {
                    return response()->json(['status' => 'success', 'data' => "No Complaints"], $this->notFound);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Complaint can access only Service Center'], $this->notFound);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        try {
             if (isset($request->user()->customertype) && $request->user()->customertype ==  4){

                $complaint = Complaint::with([
                    'customer:id,customer_name,customer_number,customer_email,customer_address,customer_place,customer_state,customer_district,customer_city,customer_pindcode',
                    'customer.pincodeDetails:id,pincode',
                    'service_center_details:id,customer_code,name',
                    'complaint_type_details',
                    'product_details.categories',
                    'product_details.subcategories',
                    'createdbyname:id,name',
                    'party',
                    'purchased_branch_details',
                    'warranty_details'
                ])->where(['id'=> $id , 'service_center' => $request->user()->id])
                ->first();

                if (!$complaint) {
                     return response()->json(['status' => 'error', 'message' => 'This complaint is not assign to this service center'], $this->notFound);
                }

                $work_done = ComplaintWorkDone::where('complaint_id', $complaint->id)->latest()->first();
                $service_bill = ServiceBill::with('service_bill_products')->where('complaint_id', $complaint->id)->latest()->first();
                $complete_complaint = ComplaintTimeline::where('complaint_id', $complaint->id)->where('status', '3')->latest()->first();
                $close_complaint = ComplaintTimeline::where('complaint_id', $complaint->id)->where('status', '4')->latest()->first();

                // Prepare the data dynamically
                $data = collect($complaint->only([
                    'complaint_number',
                    'complaint_date',
                    'complaint_status',
                    'complaint_type',
                    'product_serail_number',
                    'seller',
                    'company_sale_bill_no',
                    'customer_bill_no',
                    'under_warranty',
                    'warranty_bill',
                    'service_type',
                    'register_by',
                    'product_laying',
                    'description'
                ]))->mapWithKeys(function ($value, $key) {
                    return [
                        match ($key) {
                            'seller' => 'bill_by_company_party_name',
                            'under_warranty' => 'warranty_status',
                            default => $key,
                        } => ($value === "" ? null : $value)
                    ];
                })->toArray();


                // Append related fields
               $data += [
                    "service_center_name" => optional($complaint->service_center_details)?->name 
                        ? ($complaint->service_center_details->customer_code 
                            ? "[{$complaint->service_center_details->customer_code}] " 
                            : "") . $complaint->service_center_details->name
                        : null,
                    "created_by" => optional($complaint->createdbyname)?->name,
                ];

                // Customer Details
                $data += collect($complaint->customer?->only([
                    'customer_name',
                    'customer_number',
                    'customer_email',
                    'customer_address',
                    'customer_place',
                    'customer_state',
                    'customer_district',
                    'customer_city'
                ]))->map(fn($value) => $value === "" ? null : $value)->toArray();
                $data["customer_pincode"] = optional($complaint->customer?->pincodeDetails)->pincode ?? null;

                // Product Details
                $data += collect($complaint->product_details?->only([
                    'sap_code',
                    'product_name',
                    'specification',
                    'product_no',
                    'phase',
                    'product_code',
                    'expiry_interval_preiod'
                ]))->mapWithKeys(function ($value, $key) {
                    return [
                        match ($key) {
                            'sap_code' => 'product_sap_code',
                            'specification' => 'hp',
                            'product_no' => 'statge',
                            'product_code' => 'model_code',
                            default => $key,
                        } => ($value === "" ? null : $value)
                    ];
                })->toArray();
                $data["complaint_type_name"] = $complaint->complaint_type_details?->name ?? null;
                $data["category_name"] = $complaint->product_details?->categories?->category_name ?? null;
                $data["group_name"] = $complaint->product_details?->subcategories?->subcategory_name ?? null;
                $data['company_sale_bill_date'] = getDateInIndFomate($complaint->company_sale_bill_date) ?? null;
                $data['bill_to_customer_party_name'] = $complaint->party?->customer_name ?? null;
                $data['warranty_customer_bill_date'] = getDateInIndFomate($complaint->customer_bill_date) ?? null;
                $result = app(AjaxController::class)->getProductTimeInterval(new Request([
                    'product_id' => $complaint->product_id,
                    'sale_bill_date' => $complaint->customer_bill_date
                ]));
                $response = $result->getData(true);
                $data['warranty_upto'] = $response['warrenty_expire_date'] ?? null;
                $data['service_branch'] = isset($complaint->purchased_branch_details)
                    ? ($complaint->purchased_branch_details->branch_code . ' ' . $complaint->purchased_branch_details->branch_name)
                    : null;

                //Work Done Details 
                $data += collect($work_done?->only([
                    'done_by',
                    'remark',
                ]))->mapWithKeys(function ($value, $key) {
                    return [
                        match ($key) {
                            'done_by' => 'action_done_by_asc',
                            'remark'  => 'service_center_remark',
                            default => $key,
                        } => ($value === "" ? null : $value)
                    ];
                })->toArray();
                $data['work_done_date'] = isset($work_done->created_at) ? getDateInIndFomate($work_done->created_at) : null;

                $data['service_bill'] = $service_bill ? collect($service_bill->only([
                    // Complete & Close Details
                    'id',
                    'replacement_tag',
                    'replacement_tag_number',
                    'category',
                    'complaint_type',
                    'complaint_reason',
                    'condition_of_service',
                    'received_product',
                    'nature_of_fault',
                    'service_location',
                    'status'
                ]))->mapWithKeys(function ($value, $key) {
                    return [
                        match ($key) {
                            'id'              => 'service_bill_id',
                            'category'        => 'service_bill_category',
                            'complaint_type'  => 'service_bill_complaint_type',
                            'complaint_reason' => 'service_bill_complaint_reason',
                            'status' => 'service_bill_status',
                            default           => $key,
                        } => ($value === "" ? null : $value)
                    ];
                })->toArray() : null;

                $data["complete_remark"] = $complete_complaint ? $complete_complaint->remark : null;
                $data["close_remark"] = $close_complaint ? $close_complaint->remark : null;

                // Service Bill Details

                $data["serice_bill_total"] = $service_bill ? $service_bill->service_bill_products?->sum('subtotal') : null;
                $data['service_bill_date'] = $service_bill ? getDateInIndFomate($service_bill->created_at) : null;
                if (isset($service_bill)) {
                    $data['service_bill']['service_bill_status_name'] =
                        $service_bill->status == 0 ? "Draft" : ($service_bill->status == 1 ? "Claimed" : ($service_bill->status == 2 ? "Customer Payable" : ($service_bill->status == 3 ? "Approved" : "Cancel")));
                }

                // attchments
                $warranty_activation_attach = $complaint->warranty_details?->getMedia('warranty_activation_attach');
                $data['warranty_activation_attachments'] = $warranty_activation_attach?->map(function ($media) {
                    return [
                        'url' => $media->getFullUrl(),
                        'type' => $media->mime_type,
                        'thumbnail' => $media->mime_type == 'application/pdf'
                            ? asset('assets/img/pdf-icon.jpg')
                            : $media->getFullUrl(),
                    ];
                })->toArray();

                $complaint_work_done_attach = $work_done?->getMedia('complaint_work_done_attach');
                $data['complaint_work_done_attach'] = $complaint_work_done_attach?->map(function ($media) {
                    return [
                        'url' => $media->getFullUrl(),
                        'type' => $media->mime_type,
                        'thumbnail' => $media->mime_type == 'application/pdf'
                            ? asset('assets/img/pdf-icon.jpg')
                            : $media->getFullUrl(),
                    ];
                })->toArray();

                $complaint_attach = $complaint?->getMedia('complaint_attach');
                $data['complaint_attach'] = $complaint_attach?->map(function ($media) {
                    return [
                        'url' => $media->getFullUrl(),
                        'type' => $media->mime_type,
                        'thumbnail' => $media->mime_type == 'application/pdf'
                            ? asset('assets/img/pdf-icon.jpg')
                            : $media->getFullUrl(),
                    ];
                })->toArray();

                if ($complaint) {
                    return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
                } else {
                    return response()->json(['status' => 'success', 'data' => "No Complaints"], $this->successStatus);
                }
            } else {
                  return response()->json(['status' => 'error', 'message' => 'Complaint can access only Service Center'], $this->notFound);
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
    public function edit($id)
    {
        //
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
        $validator = Validator::make($request->all(), [
            'complaint_status' => 'nullable|in:3,4',
            'assign_user'      => 'nullable|integer',
            'service_center'   => 'nullable|integer'
        ]);

        // If validation fails, return JSON response
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }
        try {
            if (!isset($request->complaint_status) && empty($request->assign_user) && empty($request->service_center)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Either complaint_status , service_center or assign_user must be provided.'
                ], $this->badrequest); // 400 Bad Request
            }
            $data = [];
            $complaint = Complaint::where(['id'=> $id , 'service_center' => $request->user()->id])
                ->first();
            if (!$complaint) {
                return response()->json(['status' => 'error', 'message' => 'This complaint is not assign to this service center'], $this->notFound);
            }
            if (isset($request->complaint_status)) {
                if ($request->complaint_status != $complaint->complaint_status) {
                    $data['remark'] = '';
                    if ($request->complaint_status == 3) {
                        if ($complaint->complaint_status != 2) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'The complaint status is must be work done.'
                            ], $this->badrequest);
                        }
                        $result = app(ComplaintController::class)->checkCompleteComplaint(new Request([
                            'id' => $complaint->id,
                        ]));
                        // dd($result);
                        $response = $result->getData(true);
                        if ($response['status'] != 'success') {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Service bill missing!.'
                            ], $this->badrequest);
                        }
                        if (!isset($request->remark)) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Add Complete remark.'
                            ], $this->badrequest);
                        }
                        $data['remark'] = $request->remark;
                    }
                    if ($request->complaint_status == 4) {
                        if ($complaint->complaint_status != 3) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Need to Complete the this complaint first.'
                            ], $this->badrequest);
                        }
                        if (!isset($request->remark)) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Add Close remark.'
                            ], $this->badrequest);
                        }
                        $data['remark'] = $request->remark;
                    }
                    $data['complaint_status'] = $request->complaint_status;
                    ComplaintTimeline::create([
                        'complaint_id' => $complaint->id,
                        'created_by' => $request->user()->id,
                        'remark'     =>  $data['remark'] ?? null,
                        'status' => $request->complaint_status,
                    ]);
                }
            }
            if (isset($request->assign_user)) {
                $roleName = 'Service Eng';
                $exists = User::where('id', $request->assign_user)
                    ->whereHas('roles', function ($query) use ($roleName) {
                        $query->where('name', $roleName);
                    })
                    ->exists(); // Check if a matching user exists

                if (!$exists) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The selected assign_user is not a valid Service Engineer.'
                    ], 422);
                }

                $data['assign_user'] = $request->assign_user;
                ComplaintTimeline::create([
                    'complaint_id' => $complaint->id,
                    'created_by' => $request->user()->id,
                    'remark' => $request->assign_user,
                    'status' => '100',
                ]);
            }
            if (isset($request->service_center)) {
                $exists = Customers::where([
                    'customertype' => '4',
                    'id' => $request->service_center
                ])->exists();
                if (!$exists) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The selected service center is not a valid Service Center.'
                    ], 422);
                }
                $data['service_center'] = $request->service_center;
                ComplaintTimeline::create([
                    'complaint_id' => $complaint->id,
                    'created_by' => $request->user()->id,
                    'remark' => $request->service_center,
                    'status' => '101',
                ]);
            }
            $complaint->update($data);
            return response()->json(['status' => 'success', 'data' => $complaint], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function work_done_submit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'done_by' => 'required|in:Repairing,Replacement,Telephonic Complaint Resolve,Complaint Cancelltion',
            'remark'  => 'required',
            'work_done_attach' => 'nullable|array', // Must be an array
            'work_done_attach.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5048' // Each file must be an image
        ]);

        // If validation fails, return JSON response
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }
        try {
            $complaint = Complaint::where(['id'=> $id , 'service_center' => $request->user()->id])
                ->first();
            if (!$complaint) {
                return response()->json(['status' => 'error', 'message' => 'This complaint is not assign to this service center'], $this->notFound);
            }
            if ($complaint->complaint_status != 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The complaint status is must be Open.'
                ], $this->badrequest);
            }
            $word_done = ComplaintWorkDone::create([
                'complaint_id' => $complaint->id,
                'done_by' => $request->done_by,
                'remark' => $request->remark,
            ]);
            if ($request->work_done_attach && count($request->work_done_attach) > 0) {
                foreach ($request->work_done_attach as $file) {
                    $customname = time() . '.' . $file->getClientOriginalExtension();
                    $word_done->addMedia($file)
                        ->usingFileName($customname)
                        ->toMediaCollection('complaint_work_done_attach');
                }
            }
            $complaint->complaint_status = '2';
            $complaint->save();
            ComplaintTimeline::create([
                'complaint_id' => $complaint->id,
                'created_by' => $request->user()->id,
                'remark' => $request->remark,
                'status' => '2',
            ]);
            return response()->json(['status' => 'success', 'data' => $complaint], $this->successStatus);
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

    //  select-option
    public function select_option(Request $request)
    {
        try {
            $data = [];
            $service_centers = [];
            /*
            commeted this code for now
            if ($request->user()->hasRole('Service Admin') || $request->user()->hasRole('superadmin')){
                $service_centers = Customers::where('customertype', '4')->select('name' , 'first_name' , 'last_name' , 'id' , 'mobile' , 'customer_code')->get();
            }else{
                $service_centers = Customers::where('customertype', '4')->whereIn('id', EmployeeDetail::where('user_id', $request->user()->id)->pluck('customer_id'))
                    ->select('name', 'first_name', 'last_name', 'id', 'mobile' , 'customer_code')
                    ->get();
            }
            */
            $service_centers = Customers::where('customertype', '4')->select('name' , 'first_name' , 'last_name' , 'id' , 'mobile' , 'customer_code')->get();
            $work_done_option = ["Repairing", "Replacement", "Telephonic Complaint Resolve", "Complaint Cancelltion"];
            $status_option    = ["Open", "Pending", "Work Done", "Complete", "Closed", "Cancelled"];
            $complaint_types = ComplaintType::where('active', 'Y')->select('id', 'name')->get();
            $user_ids = [$request->user()->id];
            $roleNames = ["Service Eng", "Service Admin"];
            $assign_users = User::whereIn('id', $user_ids)
                ->select('id', 'name', 'employee_codes')
                ->get();
            $data = [
                'work_done_option' => collect($work_done_option)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray()
            ];
            $data += [
                'status_option' => collect($status_option)->map(fn($item, $key) => ['key' => $key, 'value' => $item])->values()->toArray()
            ];
            $data += [
                'service_centers' => $service_centers->map(fn($center) => [
                    'key' => $center->id,
                    'value' => ($center->customer_code ? "[{$center->customer_code}] " : "") . $center->name
                ])->toArray()
            ];
            $data += [
                'complaint_types' => $complaint_types->map(fn($complaint_type) => [
                    'key' => $complaint_type->id,
                    'value' => $complaint_type->name
                ])->toArray()
            ];
            $data += [
                'assign_users' => $assign_users->map(fn($user) => [
                    'key' => $user->id,
                    'value' => "[{$user->employee_codes}] {$user->name}"
                ])->toArray()
            ];
            return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function filter_option(Request $request)
    {
        try {
            $data = [];
            $users          = User::whereIn('id', [$request->user()->id])->select('id', 'name', 'employee_codes', 'branch_id')->get();
            $branchIds = $users->pluck('branch_id')->unique()->filter();
            // Get only those branches
            $branches = Branch::whereIn('id', $branchIds)
                ->select('id', 'branch_name')
                ->get();
            $category = Category::where('active', 'Y')->select('id', 'category_name')->get();
            $status_option = [
                1   => "Pending",
                0      => "Open",
                2 => "Work Done",
                3  => "Complete",
                4    => "Closed",
                5 => "Cancelled",
            ];
            $under_warranty   = ["Yes", "No"];
            $service_types   = ["Paid", "Free"];
            $complaint_register_by = ["Dealer", " Distributor", "Retailer", "Marketing Team", "ASC", "Service Enginer"];
            $service_bills = ["Draft", "Claimed To Company", "Customer Payable", "Approved", "Cancelled"];

            $complaint_types = ComplaintType::where('active', 'Y')->select('id', 'name')->get();
            $complaint_recieved_via = ["WhatsApp", "Toll-Free Call", "E-Mail"];

            $data += [
                'complaint_types' => $complaint_types->map(fn($complaint_type) => [
                    'key' => $complaint_type->id,
                    'value' => $complaint_type->name
                ])->toArray()
            ];
            $data += [
                'service_bill' => collect($service_bills)->map(fn($service_bill , $key) => [
                    'key' => $key,
                    'value' => $service_bill
                ])->toArray()
            ];
            $data += [
                'status_option' => collect($status_option)->map(fn($item, $key) => ['key' => $key, 'value' => $item])->values()->toArray()
            ];
            $data += [
                'under_warranty' => collect($under_warranty)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray()
            ];
            $data += [
                'branches' => $branches->map(fn($branch) => [
                    'key' => $branch->id,
                    'value' => $branch->branch_name
                ])->toArray()
            ];
            $data += [
                'service_types' => collect($service_types)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray()
            ];
            $data += [
                'warranty_bill' => collect($under_warranty)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray()
            ];
            $data += [
                'register_by' => collect($complaint_register_by)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray()
            ];
            $data += [
                'division' => $category->map(fn($item) => [
                    'key' => $item->category_name,
                    'value' => $item->category_name
                ])->toArray()
            ];
            $data += [
                'complaint_recieve_via' => collect($complaint_recieved_via)->map(fn($item) => ['key' => $item, 'value' => $item])->toArray()
            ];

            return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    // all complaints 
     public function complaint_type_count(Request $request)
    {
        try {
             if (isset($request->user()->customertype) && $request->user()->customertype ==  4){
                $filters = $request->all();

                // $user_ids = getUsersReportingToAuth($request->user()->id);
                $user_ids = [$request->user()->id]; 
                $query = Complaint::whereIn('service_center', $user_ids)
                    ->select('id', 'complaint_number', 'complaint_status', 'complaint_date')->orderBy('id', 'desc');


                if (isset($request->from_date) && isset($request->to_date)) {
                    $complaint_from_date = Carbon::parse($request->from_date)->startOfDay()->format('Y-m-d');
                    $complaint_to_date = Carbon::parse($request->to_date)->startOfDay()->format('Y-m-d');
                    $query->whereBetween('complaint_date', [$complaint_from_date, $complaint_to_date]);
                }

                
                $data = [
                    'all_complaints'    => (clone $query)->count(),
                    'complaints_pending'    => (clone $query)->where('complaint_status', '1')->count(),
                    'complaints_work_done'  => (clone $query)->where('complaint_status', '2')->count(),
                    'complaints_cancelled'  => (clone $query)->where('complaint_status', '5')->count(),
                    'complaints_in_process' => (clone $query)->where('complaint_status', '0')->count(),
                    'complaints_complete'   => (clone $query)->where('complaint_status', '3')->count(),
                    'complaints_closed'     => (clone $query)->where('complaint_status', '4')->count(),
                ];
                if (!empty($data)) {
                    return response()->json(['status' => 'success', 'data' => $data], $this->successStatus);
                } else {
                    return response()->json(['status' => 'success', 'data' => "No Complaints"], $this->notFound);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'This complaint is not assign to this service center'], $this->notFound);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getNotes(Request $request , $id){
          try {
           $complaint = Complaint::where(['id'=> $id , 'service_center' => $request->user()->id])
                ->first();
            if (!$complaint) {
                return response()->json(['status' => 'error', 'message' => 'This complaint is not assign to this service center'], $this->notFound);
            }

           $complaint_timeline = ComplaintTimeline::with('created_by_details')
                    ->where(['complaint_id' => $complaint->id, 'status' => 'Note'])
                    ->get();
        
            $data = $complaint_timeline->map(function ($item) use ($complaint) {
                return [
                    'Notes'       => $item->remark === "" ? null : $item->remark,
                    'created_at' => Carbon::parse($item->created_at)->format('d-m-Y H:i:s'),
                    'complaint_number' => $complaint->complaint_number,
                    'created_by'  => optional($item->created_by_details)->name ?? null, // Safely get the name
                    'id'          => optional($item->created_by_details)->id ?? null,   // Safely get the ID
                ];
            })->toArray();


            if($data){
                return response()->json(['status' => 'success', 'notes' => $data], $this->successStatus);
            }else{
                return response()->json(['status' => 'success', 'notes' => "Not Found"], $this->notFound);
            }

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
