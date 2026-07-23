<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use App\DataTables\ComplaintDataTable;
use App\Exports\ComplaintExport;
use App\Models\Branch;
use App\Models\City;
use App\Models\Complaint;
use App\Models\ComplaintTimeline;
use App\Models\ComplaintType;
use App\Models\ComplaintWorkDone;
use App\Models\Customers;
use App\Models\District;
use App\Models\Division;
use App\Models\Category;
use App\Models\EndUser;
use App\Models\Media;
use App\Models\Pincode;
use App\Models\Product;
use App\Models\ServiceBill;
use App\Models\State;
use App\Models\User;
use App\Models\WarrantyActivation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use DB;
use Excel;
use Validator;
use DataTables;
use Carbon\Carbon;
use Auth;
use App\Http\Controllers\AjaxController;

class ComplaintController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->complaint = new Complaint();
        $this->path = 'complaints';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ComplaintDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('complaint_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $assign_users_ids = Complaint::whereNotNull('assign_user')->pluck('assign_user')->toArray();
        $roleNames = ["Service Eng", "Service Admin"];

        $assign_users = User::whereIn('id', $assign_users_ids)
            ->whereHas('roles', function ($query) use ($roleNames) {
                $query->whereIn('name', $roleNames);
            })
            ->with(['roles.permissions']) // Eager load roles and permissions
            ->select('id', 'name', 'employee_codes')
            ->get();
        $service_centers_id = Complaint::pluck('service_center');
        $service_centers  = Customers::whereIn('id' , $service_centers_id)->get();
        $complaint_types =  ComplaintType::where('active' , 'Y')->select('id' , 'name')->get();
        $categories = Category::where('active' , 'Y')->select('id' , 'category_name')->get();
        return view('complaint.index' , compact('service_centers' , 'assign_users' , 'complaint_types' , 'categories'));
        // return $dataTable->render('complaint.index');
    }

    public function getComplaints(ComplaintDataTable $dataTable, Request $request){

         $query = Complaint::with([
            'party',
            'service_center_details',
            'customer.pincodeDetails',
            'complaint_type_details',
            'product_details.categories',
            'division_details',
            'complaint_time_line', // Ensure this is included
            'service_bill',
            'purchased_branch_details',
            'product_details.categories',
            'createdbyname',
            'complaint_work_dones',
            'warranty_details',
            'assign_users'
        ])->latest()->newQuery();
        if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Sub_Admin') && !Auth::user()->hasRole('Service Admin') &&  !Auth::user()->hasRole('CRM_Support')){
           $query->where('assign_user' , Auth::user()->id);
        }

        if (isset($request->complaint_date)) {
            $date = explode(' - ' , $request->complaint_date);
            if(isset($date)){
                try{
                    $complaintDate_start = Carbon::parse($date[0])->startOfDay()->format('Y-m-d H:i:s');
                    $complaintDate_end = Carbon::parse($date[1])->endOfDay()->format('Y-m-d H:i:s');
                    $query->whereBetween('complaint_date', [$complaintDate_start, $complaintDate_end]);
                }catch(\Exception $e){
                    
                }
            }

        }
        if (isset($request->complaint_number)) {
            $query->where('complaint_number', 'like', '%' . $request->complaint_number . '%');
        }
        if (!empty($request->service_center_name) && collect($request->service_center_name)->filter()->isNotEmpty()) {
            $query->whereIn('service_center', (array) $request->service_center_name);
        }
        if (!empty($request->assign_user) && collect($request->assign_user)->filter()->isNotEmpty()) {
            $query->whereIn('assign_user', (array) $request->assign_user);
        }
        if (isset($request->service_center_code)) {
            $query->whereHas('service_center_details', function($q) use ($request) {
                $q->where('customer_code', 'like', '%' . $request->service_center_code . '%');
            });
        }
        if (isset($request->seller)) {
            $query->where('seller', 'like', '%' . $request->seller . '%');
        }
        if (isset($request->customer_name)) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->customer_name . '%');
            });
        }
        if (isset($request->customer_email)) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('customer_email', 'like', '%' . $request->customer_email . '%');
            });
        }
        if (isset($request->customer_number)) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('customer_number', 'like', '%' . $request->customer_number . '%');
            });
        }
        if (isset($request->customer_address)) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('customer_address', 'like', '%' . $request->customer_address . '%');
            });
        }
        if (isset($request->customer_place)) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('customer_place', 'like', '%' . $request->customer_place . '%');
            });
        }
        if (isset($request->customer_country)) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('customer_country', 'like', '%' . $request->customer_country . '%');
            });
        }
        if (isset($request->customer_state)) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('customer_state', 'like', '%' . $request->customer_state . '%');
            });
        }
        if (isset($request->customer_city)) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('customer_city', 'like', '%' . $request->customer_city . '%');
            });
        }
        if (isset($request->pincode) && $request->pincode != '') {
            $query->whereHas('customer.pincodeDetails', function($query) use ($request) {
                $query->where('pincode', 'like', '%' . $request->pincode . '%'); // Filter pincode
            });
        }
        if (isset($request->customer_complaint_type) && $request->customer_complaint_type != '') {
            $query->whereHas('complaint_type_details', function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->customer_complaint_type . '%'); // Filter pincode
            });
        }
        if (isset($request->category_name) && $request->category_name != '') {
            $query->whereHas('product_details.categories', function($query) use ($request) {
                $query->where('category_name', 'like', '%' . $request->category_name . '%'); // Filter pincode
            });
        }
        if (isset($request->product_name) && $request->product_name != '') {
            $query->whereHas('product_details', function($query) use ($request) {
                $query->where('product_name', 'like', '%' . $request->product_name . '%'); // Filter pincode
            });
        }
        if (isset($request->product_code) && $request->product_code != '') {
            $query->whereHas('product_details', function($query) use ($request) {
                $query->where('product_code', 'like', '%' . $request->product_code . '%'); // Filter pincode
            });
        }
        if (isset($request->product_serail_number)) {
            $query->where('product_serail_number', 'like', '%' . $request->product_serail_number . '%');
        }
        if (isset($request->specification) && $request->specification != '') {
            $query->whereHas('product_details', function($query) use ($request) {
                $query->where('specification', 'like', '%' . $request->specification . '%'); // Filter pincode
            });
        }
        if (isset($request->product_no) && $request->product_no != '') {
            $query->whereHas('product_details', function($query) use ($request) {
                $query->where('product_no', 'like', '%' . $request->product_no . '%'); // Filter pincode
            });
        }
         if (isset($request->phase) && $request->phase != '') {
            $query->whereHas('product_details', function($query) use ($request) {
                $query->where('phase', 'like', '%' . $request->phase . '%'); // Filter pincode
            });
        }
        if (isset($request->category_name_1) && $request->category_name_1 != '') {
            $query->whereHas('product_details.categories', function($query) use ($request) {
                $query->where('category_name', 'like', '%' . $request->category_name_1 . '%'); // Filter pincode
            });
        }
        if (isset($request->customer_bill_date)) {
            try{
                 $complaintDate = Carbon::parse($request->customer_bill_date)->format('Y-m-d');
                $query->whereDate('customer_bill_date', '=', $complaintDate);
            }catch(\Exception $e){
                
            }
        }
        if (isset($request->customer_bill_date_1)) {
             try{
                 $complaintDate = Carbon::parse($request->customer_bill_date_1)->format('Y-m-d');
                $query->whereDate('customer_bill_date', '=', $complaintDate);
            }catch(\Exception $e){
                
            }
        }
        if (isset($request->service_type)) {
            $query->where('service_type', 'like', '%' . $request->service_type . '%');
        }
        if (isset($request->last_update_date)) {
            try{
                 $complaintDate = Carbon::parse($request->last_update_date)->format('Y-m-d');
                 $query->whereDate('updated_at', '=', $complaintDate);
            }catch(\Exception $e){
                
            }
        }
        if (isset($request->service_status) && $request->service_status != '') {
            $query->whereHas('service_bill', function($query) use ($request) {
                $query->where('status', $request->service_status); // Filter pincode
            });
        }
        if (isset($request->description)) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        if (isset($request->service_branch) && $request->service_branch != '') {
            $query->whereHas('purchased_branch_details', function ($subQuery) use ($request) {
                $subQuery->whereRaw("CONCAT(branch_code, ' ', branch_name) LIKE ?", ['%' . $request->service_branch . '%']);
            });
        }
        if (isset($request->purchased_party_name) && $request->purchased_party_name != '') {
            $query->whereHas('customer', function ($subQuery) use ($request) {
                $subQuery->whereRaw("CONCAT(customer_name, ' ', customer_number) LIKE ?", ['%' . $request->purchased_party_name . '%']);
            });
        }
        if (isset($request->warranty_bill)) {
            $query->where('warranty_bill', 'like', '%' . $request->warranty_bill . '%');
        }
        if($request->customer_bill_no){
            $query->where('customer_bill_no', 'like', '%' . $request->customer_bill_no . '%');
        }
        if($request->under_warranty){
            $query->where('under_warranty', 'like', '%' . $request->under_warranty . '%');
        }
        if (isset($request->service_type_1)) {
            $query->where('service_type', 'like', '%' . $request->service_type_1 . '%');
        }
        if (isset($request->company_sale_bill_no)) {
            $query->where('company_sale_bill_no', 'like', '%' . $request->company_sale_bill_no . '%');
        }
        if (isset($request->company_sale_bill_date)) {
            try{
                 $complaintDate = Carbon::parse($request->company_sale_bill_date)->format('Y-m-d');
                 $query->whereDate('company_sale_bill_date', '=', $complaintDate);
            }catch(\Exception $e){
                
            }
        }
        if (isset($request->register_by)) {
            $query->where('register_by', 'like', '%' . $request->register_by . '%');
        }
        if (isset($request->createdbyname_name) && $request->createdbyname_name != '') {
            $query->whereHas('createdbyname', function($query) use ($request) {
                $query->where('name', $request->createdbyname_name); // Filter pincode
            });
        }
        if (isset($request->created_at)) {
            try{
                 $complaintDate = Carbon::parse($request->created_at)->format('Y-m-d');
                 $query->whereDate('created_at', '=', $complaintDate);
            }catch(\Exception $e){

            }

        }
        if (isset($request->status) && $request->status != '') {
            $query->where('complaint_status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addIndexColumn()
            ->addColumn('status', function ($query) {
                if($query->complaint_status == '0'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-secondary">Open</span></a>';
                }elseif($query->complaint_status == '1'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-warning">Pending</span></a>';
                }elseif($query->complaint_status == '2'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-info">Work Done</span></a>';
                }elseif($query->complaint_status == '3'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-success">Completed</span></a>';
                }elseif($query->complaint_status == '4'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-primary">Closed</span></a>';
                }elseif($query->complaint_status == '5'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-danger">Canceled</span></a>';
                }
            })
            ->editColumn('created_at' , function($query){
                try {
                    return $query->created_at ? Carbon::parse($query->created_at)->format('d-m-Y h:i:s') : '';
                } catch (\Exception $e) {
                    return ''; // Return null if parsing fails
                }
            })
            ->addColumn('last_status', function ($query) {
                if($query->complaint_status == '0'){
                    return '<span class="badge badge-secondary">Open</span>';
                }elseif($query->complaint_status == '1'){
                    return '<span class="badge badge-warning">Pending</span>';
                }elseif($query->complaint_status == '2'){
                    return '<span class="badge badge-info">Work Done</span>';
                }elseif($query->complaint_status == '3'){
                    return '<span class="badge badge-success">Completed</span>';
                }elseif($query->complaint_status == '4'){
                    return '<span class="badge badge-primary">Closed</span>';
                }elseif($query->complaint_status == '5'){
                    return '<span class="badge badge-danger">Canceled</span>';
                }
            })
            ->addColumn('work_done_time', function ($query) {
                $date = $query->complaint_time_line->where('status',2)->sortByDesc('id')->first();
                if(isset($date)){
                   return Carbon::parse($date->created_at)->format('d-m-Y h:i a');
                } 
                return "NOT DONE";
            })
            ->addColumn('complaint_work_dones', function ($query) {
                $data = $query->complaint_work_dones->sortByDesc('id')->first();
                if(isset($data)){
                    return $data->done_by ?? '';
                } 
                return "NOT DONE";
            })
            ->addColumn('customer_pindcode', function ($query) {
                 return getPincode($query->customer->customer_pindcode)??'';    
            })
            ->addColumn('complaint_work_remark', function ($query) {
                $data = $query->complaint_work_dones->sortByDesc('id')->first();
                if(isset($data)){
                    return $data->remark ?? '';
                } 
                return "NOT DONE";
            })
            ->addColumn('pending_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '1')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "00:00:00";
            })
            ->addColumn('open_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '0')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "Action Not Performed";
            })
            ->addColumn('canceled_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '5')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "Action Not Performed";
            })
             ->addColumn('work_done_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '2')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "Action Not Performed";
            })
            ->addColumn('compleated_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '3')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "Action Not Performed";
            })
            ->addColumn('close_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '4')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "Action Not Performed";
            })
            ->addColumn('service_bill_status', function ($query) {
                if (!$query->service_bill) {
                    return "No Action"; // Handle the case where service_bill is null
                }

                switch ($query->service_bill->status) {
                    case '0':
                        return '<a href="' . route('service_bills.show', $query->service_bill->id) . '" title="Show Service Bill">
                                    <span class="badge badge-secondary">Draft</span>
                                </a>';
                    case '1':
                        return '<span class="badge badge-warning">Claimed</span>';
                    case '2':
                        return '<span class="badge badge-info">Customer Payable</span>';
                    case '3':
                        return '<span class="badge badge-success">Approved</span>';
                    case '4':
                        return '<span class="badge badge-danger">Cancelled</span>';
                    default:
                        return "No Action";
                }
            })
            ->addColumn('service_bill_date', function ($query) {
                if (!$query->service_bill) {
                    return "Not Done Yet"; // Handle the case where service_bill is null
                }
                if($query->service_bill->status == '3'){
                     return $query->service_bill->updated_at ?? '';
                }else{
                    return "Not Done Yet"; // Handle the case where service_bill is null
                }  
            })
             ->addColumn('service_center_remark', function ($query) {
                if (!isset($query->complaint_work_dones)) {
                    return "No remark"; // Handle the case where service_bill is null
                }

                $service_bill = $query->complaint_work_dones->sortByDesc('id')->first();
                if($service_bill){
                     return  $service_bill->remark;
                }else{
                    return "No remark"; // Handle the case where service_bill is null
                }
            })
           ->addColumn('service_branch', function ($query) {
                return ($query->purchased_branch_details->branch_code ?? '-') . ' ' . ($query->purchased_branch_details->branch_name ?? '-');
            })
           ->addColumn('work_complated_duration', function ($query) {
                $complaint_status = $query->complaint_time_line->where('status' , 3)->sortByDesc('id')->first();
                if(isset($complaint_status->created_at) && isset($query->created_at)){
                   return calculatedTAT($complaint_status->created_at,$query->created_at);
                }
                return "Not Completed Yet";
            })
           ->addColumn('open_duration', function ($query) {
                $complaint_status = $query->complaint_time_line->where('status' , 0)->sortByDesc('id')->first();
                if(isset($complaint_status->created_at) && isset($query->created_at)){
                   return calculatedTAT($complaint_status->created_at,$query->created_at);
                }
                return "Not Open Yet";
            })
            ->addColumn('closed_date', function ($query) {
                $complaint_status = $query->complaint_time_line->where('status' , 4)->sortByDesc('id')->first();
                if(isset($complaint_status->created_at)){
                    return getDateInIndFomate($complaint_status->created_at) ?? '';
                }
                return "Not Closed Yet";
            })
            ->addColumn('complaint_date', function ($query) {
                return date('d-m-Y', strtotime($query->complaint_date));
            })
            ->addColumn('complaint_number', function ($query) {
                if (auth()->user()->can(['complaint_view'])) {
                    $btn = ' <a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint">
                                '.$query->complaint_number.'
                                </a>';
                }else{
                    $btn = $query->complaint_number;
                }
                // $btn = '';
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
            })
            ->editColumn('company_sale_bill_date' , function($query){
                 return getDateInIndFomate($query->company_sale_bill_date) ?? '';
            })
            ->rawColumns(['action', 'status','complaint_number' , 'service_bill_status' , 'service_bill_date'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $serial_number = $request->serial_number ?? '';
        $newComplaintNumber = $this->getComplaintNumber();

        $roleNames = ["Service Eng", "Service Admin"]; // Add more roles as needed

        $assign_users = User::whereHas('roles', function ($query) use ($roleNames) {
            $query->whereIn('name', $roleNames); // Check for multiple roles
        })
        ->with(['roles' => function ($query) {
            $query->with('permissions');
        }])
        ->select('id', 'name', 'employee_codes')
        ->get();


        $service_centers = Customers::where('customertype', '4')->select('id', 'name', 'customer_code')->get();
        $branchs = Branch::where('active', 'Y')->select('id', 'branch_name', 'branch_code')->get();
        $pincodes = Pincode::where('active', 'Y')->select('id', 'pincode')->get();
        $divisions = Division::where('active', 'Y')->select('id', 'division_name')->get();
        $complaint_types = ComplaintType::where('active', 'Y')->select('id', 'name')->get();
        $products = Product::where('active', 'Y')->select('product_name', 'id')->get();
        if (isset($request->serial_no) && !empty($request->serial_no)) {
            $this->complaint['serail_number'] = $request->serial_no;
        }
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        return view('complaint.create', compact('serial_number','assign_users', 'service_centers', 'branchs', 'pincodes', 'divisions', 'complaint_types', 'newComplaintNumber', 'products', 'states'))->with('complaints', $this->complaint);
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
            'customer_number'    => 'required',
            'customer_state'          => 'required',
            'customer_district'    => 'required',
            'customer_city'            => 'required',
        ];


        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $Cstate = State::where('id', $request->customer_state)->first();
            $Cdistrict = District::where('id', $request->customer_district)->first();
            $Ccity = City::where('id', $request->customer_city)->first();
            
            $end_user = EndUser::updateOrCreate(['customer_number' => $request->customer_number ?? ''], [
                'customer_name' => $request->customer_name ?? '',
                'customer_number' => $request->customer_number ?? '',
                'customer_email' => $request->customer_email ?? '',
                'customer_address' => $request->customer_address ?? '',
                'customer_place' => $request->customer_place ?? '',
                'customer_pindcode' => $request->customer_pindcode ?? '',
                'customer_country' => $request->customer_country ?? '',
                'customer_state' => $Cstate->state_name ?? '',
                'customer_district' => $Cdistrict->district_name ?? '',
                'customer_city' => $Ccity->city_name ?? '',
                'state_id' => $request->customer_state ?? '',
                'district_id' => $request->customer_district ?? '',
                'city_id' => $request->customer_city ?? '',
            ]);
            $request->end_user_id = $end_user->id;

            $check_warranty = WarrantyActivation::with('customer', 'media')->where('product_serail_number', $request->product_serail_number)->first();

            $newComplaintNumber = $this->getComplaintNumber();
            $complaint = Complaint::create([
                'complaint_number' => $newComplaintNumber,
                'complaint_date' => $request->complaint_date ? cretaDate($request->complaint_date)  : NULL,
                'claim_amount' => $request->claim_amount ?? NULL,
                'seller' => $request->seller ?? NULL,
                'end_user_id' => $request->end_user_id ?? NULL,
                'party_name' => $request->party_name ?? NULL,
                'product_laying' => $request->product_laying ?? NULL,
                'service_center' => $request->service_center ?? NULL,
                'assign_user' => $request->assign_user ?? NULL,
                'product_id' => $request->product_id ?? NULL,
                'product_serail_number' => $request->product_serail_number ?? NULL,
                'product_code' => $request->product_code ?? NULL,
                'product_name' => $request->product_name ?? NULL,
                'category' => $request->category ?? NULL,
                'specification' => $request->specification ?? NULL,
                'product_no' => $request->product_no ?? NULL,
                'phase' => $request->phase ?? NULL,
                'seller_branch' => $request->seller_branch ?? NULL,
                'purchased_branch' => $request->purchased_branch ?? NULL,
                'product_group' => $request->product_group ?? NULL,
                'company_sale_bill_no' => $request->company_sale_bill_no ?? NULL,
                'company_sale_bill_date' => $request->company_sale_bill_date ? cretaDate($request->company_sale_bill_date)  : NULL,
                'customer_bill_date' => $request->customer_bill_date ? cretaDate($request->customer_bill_date) : NULL,
                'customer_bill_no' => $request->customer_bill_no ?? NULL,
                'company_bill_date_month' => $request->company_bill_date_month ?? NULL,
                'under_warranty' => $request->under_warranty ?? NULL,
                'service_type' => $request->service_type ?? NULL,
                'customer_bill_date_month' => $request->customer_bill_date_month ?? NULL,
                'warranty_bill' => $request->warranty_bill ?? NULL,
                'fault_type' => $request->fault_type ?? NULL,
                'service_centre_remark' => $request->service_centre_remark ?? NULL,
                'complaint_status' => $request->complaint_status ?? 1,
                'remark' => $request->remark ?? NULL,
                'division' => $request->division ?? NULL,
                'register_by' => $request->register_by ?? NULL,
                'complaint_type' => $request->complaint_type ?? NULL,
                'description' => $request->description ?? NULL,
                'created_by_device' => 'user',
                'created_by' => auth()->user()->id,
                'complaint_recieve_via' => $request->complaint_recieve_via ?? Null,
            ]);
            if ($request->file('files') && count($request->file('files')) > 0) {
                foreach ($request->file('files') as $file) {
                    $customname = time() . '.' . $file->getClientOriginalExtension();
                    $complaint->addMedia($file)
                        ->usingFileName($customname)
                        ->toMediaCollection('complaint_attach');
                }
            
            }
            $this->sendMsgToCustomer($complaint , $type=1);
            if(isset($complaint->service_center)){
                $this->sendMsgToServiceCenter($complaint);
            }

            return Redirect::to('complaints')->with('message_success', 'Complaint Store Successfully and the complaint number is <span title="Copy" id="copyText">' . $newComplaintNumber . '</span>');
        }else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function show(Complaint $complaint)
    {
        $timelines = ComplaintTimeline::with('created_by_details')->where('complaint_id', $complaint->id)->orderBy('created_at', 'desc')->get();
        $roleName = 'Service Eng';
        $assign_users = User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })
            ->with(['roles' => function ($query) {
                $query->with('permissions');
            }])->select('id', 'name', 'employee_codes')
            ->get();
        $work_done = ComplaintWorkDone::where('complaint_id', $complaint->id)->latest()->first();
        $complete_complaint = ComplaintTimeline::where('complaint_id', $complaint->id)->where('status', '3')->latest()->first();
        $close_complaint = ComplaintTimeline::where('complaint_id', $complaint->id)->where('status', '4')->latest()->first();
        $service_bill = ServiceBill::with('service_bill_products')->where('complaint_id', $complaint->id)->latest()->first();
        $service_centers = Customers::where('customertype', '4')->select('id', 'name', 'customer_code')->get();
        $result = app(AjaxController::class)->getProductTimeInterval(new Request([
            'product_id' => $complaint->product_id,
            'sale_bill_date' => $complaint->customer_bill_date
        ]));
        $response = $result->getData(true);
        return view('complaint.show', compact('complaint', 'timelines', 'assign_users', 'service_centers', 'work_done', 'service_bill', 'complete_complaint', 'close_complaint' , 'response'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function edit(Complaint $complaint)
    {
        $this->complaint = $complaint;
        $roleName = "Service Eng";

        $assign_users = User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })
            ->with(['roles' => function ($query) {
                $query->with('permissions');
            }])->select('id', 'name')
            ->get();
        $service_centers = Customers::where('customertype', '4')->select('id', 'name')->get();
        $branchs = Branch::where('active', 'Y')->select('id', 'branch_name', 'branch_code')->get();
        $pincodes = Pincode::where('active', 'Y')->select('id', 'pincode')->get();
        $divisions = Division::where('active', 'Y')->select('id', 'division_name')->get();
        $complaint_types = ComplaintType::where('active', 'Y')->select('id', 'name')->get();
        $products = Product::where('active', 'Y')->select('product_name', 'id')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        return view('complaint.create', compact('assign_users', 'service_centers', 'branchs', 'pincodes', 'divisions', 'complaint_types', 'products', 'states'))->with('complaints', $this->complaint);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Complaint $complaint)
    {
        if ($request->file('files') && count($request->file('files')) > 0) {
            foreach ($request->file('files') as $file) {
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $complaint->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('complaint_attach');
            }
        }
        $request['complaint_date'] = $request->complaint_date ? cretaDate($request->complaint_date) : NULL;
        $request['company_sale_bill_date'] = $request->company_sale_bill_date ? cretaDate($request->company_sale_bill_date) : NULL;
        $request['customer_bill_date'] =  $request->customer_bill_date ? cretaDate($request->customer_bill_date) : NULL;
        $complaint->update($request->all());
        $newComplaintNumber = $complaint->complaint_number;

        return Redirect::to('complaints')->with('message_success', 'Complaint Update Successfully and the complaint number is <span title="Copy" id="copyText">' . $newComplaintNumber . '</span>');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Complaint $complaint)
    {
        //
    }

    public function getComplaintNumber()
    {
        $currentYear = date('y');
        $nextYear = $currentYear + 1;
        $financialYear = "$currentYear-$nextYear";
        $latestComplaint = Complaint::where('complaint_number', 'like', "SEC/HO/$financialYear/%")
            ->orderBy('complaint_number', 'desc')
            ->first();

        if ($latestComplaint) {
            $lastComplaintNumber = explode('/', $latestComplaint->complaint_number);
            $nextComplaintNumber = intval(end($lastComplaintNumber)) + 1;
        } else {
            $nextComplaintNumber = 1;
        }

        $nextComplaintNumberPadded = str_pad($nextComplaintNumber, 3, '0', STR_PAD_LEFT);

        return "SEC/HO/$financialYear/$nextComplaintNumberPadded";
    }

    public function deleteAttachment(Request $request)
    {
        Media::where('id', $request->id)->delete();
        return response()->json(['status' => true]);
    }

    public function cancelComplaint(Request $request)
    {
        try {
            $compalint = Complaint::find($request->id);
            $compalint->complaint_status = '5';
            $compalint->save();

            ComplaintTimeline::create([
                'complaint_id' => $request->id,
                'created_by' => auth()->user()->id,
                'status' => '5',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Complaint Status update successfully !']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function pendingComplaint(Request $request)
    {
        try {
            $compalint = Complaint::find($request->id);
            $compalint->complaint_status = '1';
            $compalint->save();

            ComplaintTimeline::create([
                'complaint_id' => $request->id,
                'created_by' => auth()->user()->id,
                'status' => '1',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Complaint Status update successfully !']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function openComplaint(Request $request)
    {
        try {
            $compalint = Complaint::find($request->id);
            $compalint->complaint_status = '0';
            $compalint->save();

            ComplaintTimeline::create([
                'complaint_id' => $request->id,
                'created_by' => auth()->user()->id,
                'status' => '0',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Complaint Status update successfully !']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    public function complaint_download(Request $request)
    {
        abort_if(Gate::denies('complaint_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ComplaintExport($request), 'Complaint.xlsx');
    }

    public function work_done(Complaint $complaint)
    {
        return view('complaint.work_done', compact('complaint'));
    }

    public function work_done_submit(Request $request)
    {
        $word_done = ComplaintWorkDone::create([
            'complaint_id' => $request->complaint_id,
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

        $compalint = Complaint::find($request->complaint_id);
        $compalint->complaint_status = '2';
        $compalint->save();

        ComplaintTimeline::create([
            'complaint_id' => $request->complaint_id,
            'created_by' => auth()->user()->id,
            'remark' => $request->remark,
            'status' => '2',
        ]);

        return redirect()->route('complaints.show', $compalint->id);
    }

    public function assign_user(Request $request)
    {

        $compalint = Complaint::find($request->complaint_id);
        $compalint->assign_user = $request->user_id;
        $compalint->save();

        ComplaintTimeline::create([
            'complaint_id' => $request->complaint_id,
            'created_by' => auth()->user()->id,
            'remark' => $request->user_id,
            'status' => '100',
        ]);

        return response()->json(['status' => 'success', 'message' => 'User assign successfully.']);
    }

    public function assign_service_center(Request $request)
    {

        $compalint = Complaint::find($request->complaint_id);
        $compalint->service_center = $request->service_center_id;
        $compalint->save();

        ComplaintTimeline::create([
            'complaint_id' => $request->complaint_id,
            'created_by' => auth()->user()->id,
            'remark' => $request->service_center_id,
            'status' => '101',
        ]);

        $this->sendMsgToServiceCenter($compalint);
        return response()->json(['status' => 'success', 'message' => 'Service Center assign successfully.']);
    }

    public function checkCompleteComplaint(Request $request)
    {
        $complaint = Complaint::find($request->id);
        if ($complaint) {
            $warranty = WarrantyActivation::where('product_serail_number', $complaint->product_serail_number)->where('status', '1')->first();
            $work_done = ComplaintWorkDone::where('complaint_id', $complaint->id)->latest()->first();
            $service_bill = ServiceBill::where('complaint_id', $request->id)->first();
            // if ($complaint->product_serail_number && !$complaint->product_serail_number) {
            //     return response()->json(['status' => 'error', 'message' => 'To complete this complaint, You need to add the product serial number first. <a href="' . route('complaints.edit', $request->id) . '" style="color:blue;">Click here</a> to add product serial number.']);
            // } else 
            if ($complaint->product_serail_number && !$warranty) {
                return response()->json(['status' => 'error', 'message' => 'To complete this complaint, You need to Activate Warranty. <a href="' . route('warranty_activation.create') . '?serial_no=' . $complaint->product_serail_number . '" style="color:blue;">Click here</a> to Activate Warranty.']);
            } else if (!$service_bill) {
                return response()->json(['status' => 'error', 'message' => 'To complete this complaint, You need to add service bill. <a href="' . route('service_bills.create') . '?complaint_id=' . $request->id . '" style="color:blue;">Click here</a> to add.']);
            } else {
                return response()->json(['status' => 'success', 'message' => 'Take remark and complete complaint.']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Complaint Not Found.']);
        }
    }

    public function completeComplaint(Request $request)
    {
        $complaint = Complaint::find($request->id);
        if ($complaint) {
            $complaint->complaint_status = '3';
            $complaint->save();

            ComplaintTimeline::create([
                'complaint_id' => $request->id,
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
                'status' => '3',
            ]);
            return response()->json(['status' => 'success', 'message' => 'Complaint Complete Successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Complaint Not Found.']);
        }
    }
    public function closeComplaint(Request $request)
    {
        $complaint = Complaint::find($request->id);
        if ($complaint) {
            $complaint->complaint_status = '4';
            $complaint->save();

            ComplaintTimeline::create([
                'complaint_id' => $request->id,
                'remark' => $request->remark,
                'created_by' => auth()->user()->id,
                'status' => '4',
            ]);
            return response()->json(['status' => 'success', 'message' => 'Complaint Complete Successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Complaint Not Found.']);
        }
    }

    // add complaint notes 
    public function complaint_add_notes(Request $request){
        abort_if(Gate::denies('add_complaint_notes'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'complaint_id' => 'required',
            'complaint_notes' => 'required',
        ]);
        try{
             $compalint_time_line = ComplaintTimeline::create([
                  'complaint_id' => $request->complaint_id ?? '',
                  'created_by'   => Auth::user()->id,
                  'status'       => "Note" ,
                  'remark'       => $request->complaint_notes ?? NULL
            ]);  
            return Redirect::to('complaints/' . $request->complaint_id)->with('message_success', 'Complaint Notes Added Successfully');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    // send message
    private function sendMsgToServiceCenter($compalint){
        $complaint_number = $compalint->complaint_number ?? '-';
        $service          = $compalint->service_type ?? '-';
        $model_no         = $compalint->product_details->model_no ?? '-';
        $serial_number    = $compalint->product_serail_number ?? '-';
        $customer_name    = $compalint->customer->customer_name  ?? '';
        $customer_city    = $compalint->customer->customer_city ?? '';

        $customer_location = 'Dist. ' . 
            ($compalint->customer->customer_district ?? '-') . ' ' . 
            ($compalint->customer->customer_state ?? '-');

        $customer_contact = ($compalint->customer->customer_number ?? '-');
        $SenderId         = "SILCCD";
        $mobile_no        = $compalint->service_center_details->mobile ?? '';
        $service_center_mobile   = isset($compalint->service_center_details->mobile) ? $compalint->service_center_details->mobile  : '-';

        $template = 'CN:'.$complaint_number.' SN:'.$serial_number.' Model:'.$model_no.' Assigned,Ser:'.$service.',Cus:'.$customer_name .'['. $customer_contact . '],' . $customer_city .'.Silver Consumer Electricals Ltd';

        $encoded_template = urlencode($template);
        $message_res = sendMessageByInfisms($service_center_mobile , $encoded_template , $SenderId);
        $this->sendMsgToCustomer($compalint , $type=2);
    }

    private function sendMsgToCustomer($compalint , $type=1){
        $complaint_number = $compalint->complaint_number ?? '-';
        $service          = $compalint->service_type ?? '-';
        $serial_number    = $compalint->product_serail_number ?? '-';
        $product_code     = $compalint->product_code ?? '-';
        $model_no         = $compalint->product_details->model_no ?? '-';
        $divisions_name   = $compalint->product_details->categories->category_name ?? '';
        $service_center   = isset($compalint->service_center_details->name) ? $compalint->service_center_details->name  : '-';
        $service_center_code   = isset($compalint->service_center_details->customer_code) ? $compalint->service_center_details->customer_code  : '-';
        $service_center_mobile   = isset($compalint->service_center_details->mobile) ? $compalint->service_center_details->mobile  : '-';
        $SenderId         = "SILCCD";
        $mobile_no        = $compalint->customer->customer_number ?? '';
        $service_center_city = $compalint->service_center_details->customeraddress->cityname->city_name ?? '-';

        if($type == 1){
            $template = 'CN:'.$complaint_number.' Registered,SN:'.$serial_number.',Model:'.$model_no.',Ser:'.$service.',Div:'.$divisions_name.'.Team call You soon.Silver Consumer Electricals Ltd';
        }else if($type == 2){
            $template = 'CN:'.$complaint_number.' assigned to ASC:'.$service_center.','.$service_center_city.', Mob:'.$service_center_mobile.'.Silver Consumer Electricals Ltd';
        }
        $encoded_template = urlencode($template);
        $message_res = sendMessageByInfisms($mobile_no , $encoded_template , $SenderId);

        if(isset($message_res) && $message_res["status"] = "success"){
             try{
                 $compalint_time_line = ComplaintTimeline::create([
                      'complaint_id' => $compalint->id ?? '',
                      'created_by'   => Auth::user()->name,
                      'status'       => "587" ,
                      'remark'       => $template ??'',
                ]);  
            }catch(\Exception $e){
               
            }
        }
    }
}
