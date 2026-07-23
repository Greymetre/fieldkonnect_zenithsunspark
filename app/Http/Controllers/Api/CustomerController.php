<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Validator;
use Gate;
use App\Models\Customers;
use App\Models\CustomerType;
use App\Models\Beat;
use App\Models\BeatSchedule;
use App\Models\BeatCustomer;
use App\Models\Attachment;
use App\Models\Address;
use App\Models\State;
use App\Models\CustomerDetails;
use App\Models\Pincode;
use App\Models\SurveyData;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Sales;
use App\Models\CheckIn;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\Tasks;
use App\Models\Wallet;
use App\Models\DealIn;
use App\Models\OrderDetails;
use App\Models\ParentDetail;
use App\Models\EmployeeDetail;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->customers = new Customers();
        $this->address = new Address();
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
        $this->path = 'customers';
    }

    public function storeCustomer(Request $request)
    {
        try {
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'address' => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                'mobile'  => 'required|numeric|unique:customers,mobile',
                // 'email'  => 'email|unique:customers,email',
                'customertype'       => 'nullable|exists:customer_types,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->messages()->all()], $this->badrequest);
            }
            //$request['fordelete'] = implode(',', $request->getContent());
            $request['mobile'] = preg_replace("/[^0-9]/", "", $request['mobile']);
            if (strlen(preg_replace('/\s+/', '', $request['mobile'])) == 10) {
                $request['mobile'] = '91' . preg_replace('/\s+/', '', $request['mobile']);
            }

            $customerdetails = Customers::where('mobile', $request['mobile'])->first();
            if (!empty($customerdetails)) {
                return response(['status' => 'error', 'message' => 'Mobile Number Already Exist'], 400);
            } else {

                $customergst = CustomerDetails::where('gstin_no', '=', $request['gstin_no'])->whereNotNull('gstin_no')->first();

                if (!empty($customergst)) {
                    return response(['status' => 'error', 'message' => 'GST Number Already Exist'], 400);
                } else {

                    $name = explode(" ", $request['full_name']);
                    $request['last_name'] = isset($request['last_name']) ? $request['last_name'] : array_pop($name);
                    $request['first_name'] = isset($request['first_name']) ? $request['first_name'] : implode(" ", $name);
                    $request['created_by'] = $user->id;

                    $customertype = CustomerType::where('type_name', '=', 'retailer')->pluck('id')->first();
                    $request['customertype'] = isset($request['customertype']) ? $request['customertype'] : $customertype;
                    //$response =  $this->customers->save_data($request);

                    if ($request->file('image')) {
                        $image = $request->file('image');
                        // $filename = 'punchin_'.autoIncrementId('Attendance', 'id');
                        $filename = 'customer';
                        $request['profile_image'] = fileupload($image, $this->path, $filename);
                    }

                    if ($customer = Customers::updateOrCreate(['mobile' => $request['mobile']], [
                        'active' => 'Y',
                        'name' => !empty($request['name']) ? ucfirst($request['name']) : '',
                        'first_name' => !empty($request['first_name']) ? ucfirst($request['first_name']) : '',
                        'last_name' => !empty($request['last_name']) ? ucfirst($request['last_name']) : '',
                        'mobile' => $request['mobile'],
                        'email' => !empty($request['email']) ? $request['email'] : null,
                        'password' => !empty($request['password']) ? Hash::make($request['password']) : '',
                        'notification_id' => !empty($request['notification_id']) ? $request['notification_id'] : '',
                        'latitude' => !empty($request['latitude']) ? $request['latitude'] : null,
                        'longitude' => !empty($request['longitude']) ? $request['longitude'] : null,
                        'device_type' => !empty($request['device_type']) ? ucfirst($request['device_type']) : '',
                        'gender' => !empty($request['gender']) ? ucfirst($request['gender']) : '',
                        'customer_code' => !empty($request['customer_code']) ? $request['customer_code'] : '',
                        'profile_image' =>  !empty($request['profile_image']) ? $request['profile_image'] : '',
                        'status_id' =>  !empty($request['status_id']) ? $request['status_id'] : 2,
                        'customertype' =>  !empty($request['customertype']) ? $request['customertype'] : 1,
                        'firmtype' =>  !empty($request['firmtype']) ? $request['firmtype'] : null,
                        //'executive_id' =>  !empty($request['executive_id'])? $request['executive_id'] : $request['created_by'],
                        'created_by' =>  !empty($request['created_by']) ? $request['created_by'] : null,
                        'manager_name' => !empty($request['manager_name']) ? $request['manager_name'] : '',
                        'manager_phone' => !empty($request['manager_phone']) ? $request['manager_phone'] : '',
                        'contact_number' => !empty($request['contact_number']) ? $request['contact_number'] : '',
                        //'parent_id' => !empty($request['parent_id'])? $request['parent_id'] :null,
                        'created_at' => getcurentDateTime(),
                        'updated_at' => getcurentDateTime()
                    ])) {

                        //parent start

                        // if(!empty($request['parent_id']))
                        // {
                        //     foreach($request['parent_id'] as $key => $rows) {
                        // $parentDetail = ParentDetail::create(
                        //   [ 
                        //     'customer_id' => $customer->id,
                        //     'parent_id' => $rows,
                        //     'created_by' => Auth::user()->id,
                        //   ]
                        //  );
                        //  }
                        // }

                        if (!empty($request['parent_id'])) {

                            $parent_data = explode(",", $request['parent_id']);
                            foreach ($parent_data as $key => $row_parent) {
                                $parentDetail = ParentDetail::create(
                                    [
                                        'customer_id' => $customer->id,
                                        'parent_id' => $row_parent,
                                        'created_by' => Auth::user()->id,
                                    ]
                                );
                            }
                        }

                        // parent end

                        //employee start

                        $employeeDetail = EmployeeDetail::create(
                            [
                                'customer_id' => $customer->id,
                                'user_id' => Auth::user()->id,
                                'created_by' => Auth::user()->id,
                            ]
                        );

                        // employee end  

                        // $useractivity = array(
                        //     'userid' => $user->id, 
                        //     'customer_id' => $customer->id,
                        //     'latitude' => $request['latitude'], 
                        //     'longitude' => $request['longitude'], 
                        //     'type' => 'Counter Created',
                        //     'description' => $user->name.' Created to '.$request['name'],
                        // );
                        // submitUserActivity($useractivity);

                        $useractivity = array(
                            'userid' => $user->id,
                            'customer_id' => $customer->id,
                            'latitude' => $request['latitude'] ?? NULL,
                            'longitude' => $request['longitude'] ?? NULL,
                            //'type' => 'Counter Created',
                            //'description' => $user->name.' Created to '.$request['name'],
                        );
                        submitUserActivity($useractivity);


                        $request['customer_id'] = $customer->id;
                        $pincodes = Pincode::with('cityname', 'cityname.districtname')->where('pincode', '=', $request['zipcode'])->first();
                        $request['state_id'] = !empty($pincodes['cityname']['districtname']['state_id']) ? $pincodes['cityname']['districtname']['state_id'] : $request['state_id'];
                        $request['district_id'] = !empty($pincodes['cityname']['district_id']) ? $pincodes['cityname']['district_id'] : $request['district_id'];
                        $request['city_id'] = !empty($pincodes['city_id']) ? $pincodes['city_id'] : $request['city_id'];
                        $request['zipcode'] = !empty($request['pincode_id']) ? $request['pincode_id'] : $request['zipcode'];
                        $request['pincode_id'] = !empty($pincodes['id']) ? $pincodes['id'] : $request['pincode_id'];

                        $request['country_id'] = !empty($request['country_id']) ? $request['country_id'] : State::where('id', $request['state_id'])->pluck('country_id')->first();
                        $request['landmark'] = !empty($request['landmark']) ? $request['landmark'] : '';
                        Address::updateOrCreate(['customer_id' => $request['customer_id']], [
                            'active'    => 'Y',
                            'customer_id'   =>  $request['customer_id'],
                            'address1' => !empty($request['address1']) ? $request['address1'] : '',
                            'address2' => !empty($request['address2']) ? $request['address2'] : '',
                            'landmark' => !empty($request['landmark']) ? $request['landmark'] : '',
                            'locality' => !empty($request['locality']) ? $request['locality'] : $request['landmark'],
                            'country_id' => !empty($request['country_id']) ? $request['country_id'] : null,
                            'state_id' => !empty($request['state_id']) ? $request['state_id'] : null,
                            'district_id' => !empty($request['district_id']) ? $request['district_id'] : null,
                            'city_id' => !empty($request['city_id']) ? $request['city_id'] : null,
                            'pincode_id' => !empty($request['pincode_id']) ? $request['pincode_id'] : null,
                            'zipcode' => !empty($request['zipcode']) ? $request['zipcode'] : '',
                            'created_by' => !empty($request['created_by']) ? $request['created_by'] : Auth::user()->id,
                            'created_at' => getcurentDateTime(),
                            'updated_at' => getcurentDateTime()
                        ]);

                        if ($request->file('shopimage')) {
                            $image = $request->file('shopimage');
                            $filename = 'customer';
                            $request['shop_image'] = fileupload($image, $this->path, $filename);
                        }

                        // if($request->file('image')){
                        //    $image = $request->file('image');
                        //    $filename = 'customer';
                        //    $request['shop_image'] = fileupload($image, $this->path.'/shopimage', $filename);
                        // }


                        //  if($request->file('image')){
                        // $image = $request->file('image');
                        // // $filename = 'punchin_'.autoIncrementId('Attendance', 'id');
                        // $filename = 'customer';
                        // $request['profile_image'] = fileupload($image, $this->path, $filename);
                        // }







                        if ($request->file('visiting_card')) {
                            $image = $request->file('visiting_card');
                            $filename = 'customer';
                            $request['visiting_image'] = fileupload($image, $this->path, $filename);
                        }

                        if ($request->file('gstin_image')) {
                            $image = $request->file('gstin_image');
                            $filename = 'customer';
                            $gstinimagepath = fileupload($image, $this->path, $filename);
                            Attachment::updateOrCreate([
                                'customer_id'   =>  $request['customer_id'],
                                'document_name' =>  'gstin'
                            ], [
                                'active'        => 'Y',
                                'file_path'     => $gstinimagepath,
                                'document_name' =>  'gstin',
                                'customer_id' => $request['customer_id'],
                                'created_at' => getcurentDateTime(),
                                'updated_at' => getcurentDateTime()
                            ]);
                        }

                        if ($request->file('pan_image')) {
                            $image = $request->file('pan_image');
                            $filename = 'customer';
                            $panimagepath = fileupload($image, $this->path, $filename);
                            Attachment::updateOrCreate([
                                'customer_id'   =>  $request['customer_id'],
                                'document_name' =>  'pan'
                            ], [
                                'active'        => 'Y',
                                'file_path'     => $panimagepath,
                                'document_name' =>  'pan',
                                'customer_id' => $request['customer_id'],
                                'created_at' => getcurentDateTime(),
                                'updated_at' => getcurentDateTime()
                            ]);
                        }

                        if ($request->file('aadhar_image')) {
                            $image = $request->file('aadhar_image');
                            $filename = 'customer';
                            $aadharimagepath = fileupload($image, $this->path, $filename);
                            Attachment::updateOrCreate([
                                'customer_id'   =>  $request['customer_id'],
                                'document_name' =>  'aadhar'
                            ], [
                                'active'        => 'Y',
                                'file_path'     => $aadharimagepath,
                                'document_name' =>  'aadhar',
                                'customer_id' => $request['customer_id'],
                                'created_at' => getcurentDateTime(),
                                'updated_at' => getcurentDateTime()
                            ]);
                        }

                        if ($request->file('other_image')) {
                            $image = $request->file('other_image');
                            $filename = 'customer';
                            $otherimagepath = fileupload($image, $this->path, $filename);
                            Attachment::updateOrCreate([
                                'customer_id'   =>  $request['customer_id'],
                                'document_name' =>  'other'
                            ], [
                                'active'        => 'Y',
                                'file_path'     => $otherimagepath,
                                'document_name' =>  'other',
                                'customer_id' => $request['customer_id'],
                                'created_at' => getcurentDateTime(),
                                'updated_at' => getcurentDateTime()
                            ]);
                        }

                        CustomerDetails::updateOrCreate(['customer_id' => $request['customer_id']], [
                            'active'        => 'Y',
                            'customer_id'   => isset($request['customer_id']) ? $request['customer_id'] : null,
                            'gstin_no'      => isset($request['gstin_no']) ? ucfirst($request['gstin_no']) : '',
                            'pan_no'        => isset($request['pan_no']) ? ucfirst($request['pan_no']) : '',
                            'aadhar_no'     => isset($request['aadhar_no']) ? ucfirst($request['aadhar_no']) : '',
                            'otherid_no'    => isset($request['otherid_no']) ? ucfirst($request['otherid_no']) : '',
                            'enrollment_date' => isset($request['enrollment_date']) ? $request['enrollment_date'] : null,
                            'approval_date'  => isset($request['approval_date']) ? $request['approval_date'] : null,
                            'shop_image' => isset($request['shop_image']) ? $request['shop_image'] : '',
                            //'shop_image' => isset($request['image'])? $request['image']:'',
                            'visiting_card' => isset($request['visiting_image']) ? $request['visiting_image'] : '',
                            'grade'     => isset($request['grade']) ? $request['grade'] : '',
                            'visit_status'     => isset($request['status_type']) ? $request['status_type'] : '',
                            'created_at'    => getcurentDateTime(),
                        ]);


                        if ($request['beat_id']) {
                            $beats = BeatCustomer::updateOrCreate(['customer_id' => $request['customer_id']], [
                                'active' => 'Y',
                                'beat_id' => $request['beat_id'],
                                'customer_id' => $request['customer_id'],
                                'created_at' => getcurentDateTime(),
                            ]);
                        }
                        if ($request['survey']) {
                            $surveydetail = collect([]);
                            $surveyqus = json_decode($request['survey'], true);
                            foreach ($surveyqus as $key => $rows) {
                                SurveyData::updateOrCreate([
                                    'customer_id' => $request['customer_id'],
                                    'field_id' => $rows['field_id']
                                ], [
                                    'customer_id'   => isset($request['customer_id']) ? $request['customer_id'] : null,
                                    'field_id' => isset($rows['field_id']) ? $rows['field_id'] : null,
                                    'value' => isset($rows['value']) ? $rows['value'] : '',
                                    'created_by' => isset($request['created_by']) ? $request['created_by'] : Auth::user()->id,
                                    'created_at' => getcurentDateTime(),
                                ]);
                            }

                            // if($surveydetail->isNotEmpty())
                            // {
                            //     SurveyData::insert($surveydetail->toArray());
                            // }
                        }
                        if ($request['dealing']) {
                            $dealings = json_decode($request['dealing'], true);
                            foreach ($dealings as $key => $deal) {
                                DealIn::updateOrCreate([
                                    'customer_id' => $request['customer_id'],
                                    'types' => $deal['types']
                                ], [
                                    'customer_id'   => !empty($request['customer_id']) ? $request['customer_id'] : null,
                                    'types' => !empty($deal['types']) ? $deal['types'] : '',
                                    'hcv' => isset($deal['hcv']) ? $deal['hcv'] : false,
                                    'mav' => isset($deal['mav']) ? $deal['mav'] : false,
                                    'lmv' => isset($deal['lmv']) ? $deal['lmv'] : false,
                                    'lcv' => isset($deal['lcv']) ? $deal['lcv'] : false,
                                    'other' => isset($deal['other']) ? $deal['other'] : false,
                                    'tractor' => isset($deal['tractor']) ? $deal['tractor'] : false,
                                ]);
                            }
                        }
                        if ($request['customertype'] == '1' || $request['customertype'] == '3') {
                            $passis = generatePassword();
                            if (strlen($request['mobile']) > 10 && substr($request['mobile'], 0, 2) === '91') {
                                $request['mobile'] = substr($request['mobile'], 2);
                            }
                            $user = User::create([
                                'active'   =>  isset($request['active']) ? $request['active'] : 'Y',
                                'name'   =>  isset($request['name']) ? $request['name'] : $request['first_name'] . ' ' . $request['last_name'],
                                'first_name'   =>  isset($request['first_name']) ? $request['first_name'] : '',
                                'last_name'   =>  isset($request['last_name']) ? $request['last_name'] : '',
                                'mobile'   =>  isset($request['mobile']) ? $request['mobile'] : null,
                                'email'   =>  isset($request['email']) ? $request['email'] : 'customer'.$customer->id.'@gmail.com',
                                'password'   =>  Hash::make($passis),
                                'reportingid' => !empty($request['created_by']) ? $request['created_by'] : null,
                                'password_string'   =>  $passis,
                                'customerid' => $customer->id,
                            ]);
                            $user->roles()->sync(['29']);
                            $permissions = $user->getPermissionsViaRoles()->pluck('name');
                            $user->givePermissionTo($permissions);
                        }
                        if ($request['customertype'] == '4') {
                            $passis = generatePassword();
                            if (strlen($request['mobile']) > 10 && substr($request['mobile'], 0, 2) === '91') {
                                $request['mobile'] = substr($request['mobile'], 2);
                            }
                            $user = User::create([
                                'active'   =>  isset($request['active']) ? $request['active'] : 'Y',
                                'name'   =>  isset($request['name']) ? $request['name'] : $request['first_name'] . ' ' . $request['last_name'],
                                'first_name'   =>  isset($request['first_name']) ? $request['first_name'] : '',
                                'last_name'   =>  isset($request['last_name']) ? $request['last_name'] : '',
                                'mobile'   =>  isset($request['mobile']) ? $request['mobile'] : null,
                                'email'   =>  isset($request['email']) ? $request['email'] : 'customer'.$customer->id.'@gmail.com',
                                'password'   =>  Hash::make($passis),
                                'reportingid' => !empty($request['created_by']) ? $request['created_by'] : null,
                                'password_string'   =>  $passis,
                                'customerid' => $customer->id,
                            ]);
                            $user->roles()->sync(['40']);
                            $permissions = $user->getPermissionsViaRoles()->pluck('name');
                            $user->givePermissionTo($permissions);
                        }
                        $asmnotify = collect([
                            'title' => 'Successfully added',
                            'body' =>  'You have successfully added ' . $request['name']
                        ]);
                        sendNotification($user->id, $asmnotify);
                        return response()->json(['status' => 'success', 'message' => 'Data inserted successfully.'], $this->successStatus);
                    }
                    return response(['status' => 'error', 'message' => 'No Record inserted.'], 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function updateCustomerLocation(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;

            if (Customers::where('id', $request['customer_id'])->update([
                'latitude' => isset($request['latitude']) ? $request['latitude'] : null,
                'longitude' => isset($request['longitude']) ? $request['longitude'] : null,
            ])) {
                return response()->json(['status' => 'success', 'message' => 'Data updated successfully.'], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Updated.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getRetailers(Request $request)
    {

        $cityid = $request->city_id;
        $customertype = $request->customertype;
        $customer_id = array();

        if (!empty($cityid) && $cityid[0] != null) {
            $customer_id = Address::whereIn('city_id', $cityid)->pluck('customer_id')->toArray();
        }

        $customerTypes = CustomerType::select('id', 'customertype_name')->get();
        $branch_id = $request->branch_id;
        $branch_user_id = array();

        if (!empty($branch_id) && $branch_id[0] != null) {
            $branch_user_id = User::whereIn('branch_id', $branch_id)->pluck('id')->toArray();
        }

        try {
            $user = $request->user();
            $userids = getUsersReportingToAuth($user->id); // Get users reporting to the authenticated user
            $customer_ids_assign = EmployeeDetail::whereIn('user_id', $userids)->distinct('customer_id')->pluck('customer_id')->toArray();

            $pageSize = $request->input('pageSize', 10000); // Default to 10000 if pageSize is not provided
            $search = $request->input('search');
            // $chunkSize = 10000;
            // $customerIdChunks = array_chunk($customer_ids_assign, $chunkSize);

            // foreach ($customerIdChunks as $chunk) {
                $query = $this->customers->with('customeraddress', 'customerdetails', 'customertypes')
                    ->where('active', 'Y')
                    // ->whereNotNull('sap_code')
                    ->where(function ($query) use ($search, $customer_id, $branch_user_id, $customertype) {
                        if (!empty($search)) {
                            $query->where(function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%")
                                    ->orWhere('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('mobile', 'like', "%{$search}%");
                            });
                        }
                        if (!empty($customer_id)) {
                            $query->whereIn('id', $customer_id);
                        }
                        if (!empty($customertype)) {
                            $query->where('customertype', $customertype);
                        }

                        // Filter by branch_user_id
                        if (!empty($branch_user_id)) {
                            $query->whereHas('getemployeedetail', function ($query) use ($branch_user_id) {
                                $query->whereIn('user_id', $branch_user_id);
                            });
                        }
                    })
                    ->whereHas('getemployeedetail', function ($querys) use ($userids, $user) {
                        if (!$user->hasRole('superadmin') && !$user->hasRole('Admin') && !$user->hasRole('Sub_Admin') && !$user->hasRole('HR_Admin') && !$user->hasRole('HO_Account')) {
                            $querys->whereIn('user_id', $userids);
                        }
                    })
                    ->whereIn('id', $customer_ids_assign)
                    ->select('id', 'name', 'first_name', 'last_name', 'mobile', 'email', 'profile_image', 'customer_code', 'latitude', 'longitude', 'customertype','sap_code')
                    // dd($query->toSql());
                    ->orderBy('name', 'asc')
                    ->paginate($pageSize);
            // }


            $db_data = $query;

            // dd($db_data);
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'customer_id' => isset($value['id']) ? $value['id'] : 0,
                        'name' => isset($value['name']) ? $value['name'].' ('.$value['sap_code'].')': '',
                        //'first_name' => isset($value['first_name']) ? $value['first_name'] : '',
                        //'last_name' => isset($value['last_name']) ? $value['last_name'] : '',
                        'mobile' => isset($value['mobile']) ? $value['mobile'] : '',
                        'email' => isset($value['email']) ? $value['email'] : '',
                        'profile_image' => isset($value['profile_image']) ? $value['profile_image'] : '',
                        //'customer_code' => isset($value['customer_code']) ? $value['customer_code'] : '',
                        //'totalamount' => isset($value['totalamount']) ? $value['totalamount'] : '',
                        //'totalpaid' => isset($value['totalpaid']) ? $value['totalpaid'] : '',
                        //'outstanding' => $value['totalamount']-$value['totalpaid'],
                        'address1' => isset($value['customeraddress']['address1']) ? $value['customeraddress']['address1'] : '',
                        'address2' => isset($value['customeraddress']['address2']) ? $value['customeraddress']['address2'] : '',
                        'latitude' => isset($value['latitude']) ? $value['latitude'] : '',
                        'longitude' => isset($value['longitude']) ? $value['longitude'] : '',
                        //'shop_image' => isset($value['customerdetails']['shop_image']) ? $value['customerdetails']['shop_image'] : '',
                        //'visiting_card' => isset($value['customerdetails']['visiting_card']) ? $value['customerdetails']['visiting_card'] : '',
                        'grade' => isset($value['customerdetails']['grade']) ? $value['customerdetails']['grade'] : '',
                        'visit_status' => isset($value['customerdetails']['visit_status']) ? $value['customerdetails']['visit_status'] : '',
                        'customer_type' => isset($value['customertypes']['customertype_name']) ? $value['customertypes']['customertype_name'] : '',
                        'distance' => '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'customerTypes' => $customerTypes, 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'customerTypes' => $customerTypes, 'data' => $data], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getDistributors(Request $request)
    {
        try {
            $user = $request->user();
            $userids = getUsersReportingToAuth($user->id);
            $customer_ids_assign = EmployeeDetail::whereIn('user_id', $userids)->pluck('customer_id')->toArray();
            $pageSize = $request->input('pageSize');
            $search = $request->input('search');
            $query = $this->customers
                // ->whereNotNull('sap_code')
                ->with('customertypes', 'firmtypes')
                ->whereHas('customertypes', function ($query) {
                    $query->where('type_name', '=', 'distributor')->orWhere('type_name', '=', 'Dealer');
                })
                ->whereNotNull('sap_code')
                ->whereIn('customertype', ['1', '3']);
                if(!$user->hasRole('superadmin') && !$user->hasRole('Admin') && !$user->hasRole('Sub_Admin')){
                    $query = $query->whereIn('id', $customer_ids_assign);
                }
                if (!empty($search)) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    });
                }
                $query = $query->select('id', 'name', 'first_name', 'last_name', 'mobile', 'email', 'profile_image', 'customer_code', 'sap_code')->orderBy('name', 'asc');
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'customer_id' => isset($value['id']) ? $value['id'] : 0,
                        'name' => isset($value['name']) ? $value['name'].' ('.$value['sap_code'].')': '',
                        'first_name' => isset($value['first_name']) ? $value['first_name'] : '',
                        'last_name' => isset($value['last_name']) ? $value['last_name'] : '',
                        'mobile' => isset($value['mobile']) ? $value['mobile'] : '',
                        'email' => isset($value['email']) ? $value['email'] : '',
                        'profile_image' => isset($value['profile_image']) ? $value['profile_image'] : '',
                        'customer_code' => isset($value['customer_code']) ? $value['customer_code'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getCustomerList(Request $request)
    {
        try {
            $user = $request->user();
            $userids = getUsersReportingToAuth($user->id);
            $pageSize = $request->input('pageSize');
            $query = $this->customers->with('customeraddress:customer_id,address1,address2', 'customerdetails:customer_id,grade,visit_status', 'customertypes')->select('id', 'name', 'first_name', 'last_name', 'mobile', 'email', 'profile_image', 'customer_code', 'latitude', 'longitude')->whereIn('executive_id', $userids)->latest();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'customer_id' => isset($value['id']) ? $value['id'] : 0,
                        'name' => isset($value['name']) ? $value['name'] : '',
                        'mobile' => isset($value['mobile']) ? $value['mobile'] : '',
                        //'first_name' => isset($value['first_name']) ? $value['first_name'] : '',
                        //'last_name' => isset($value['last_name']) ? $value['last_name'] : '',
                        'email' => isset($value['email']) ? $value['email'] : '',
                        'profile_image' => isset($value['profile_image']) ? $value['profile_image'] : '',
                        'customer_code' => isset($value['customer_code']) ? $value['customer_code'] : '',
                        //'totalamount' => isset($value['totalamount']) ? $value['totalamount'] : '',
                        //'totalpaid' => isset($value['totalpaid']) ? $value['totalpaid'] : '',
                        //'outstanding' => $value['totalamount']-$value['totalpaid'],
                        'address1' => isset($value['customeraddress']['address1']) ? $value['customeraddress']['address1'] : '',
                        'address2' => isset($value['customeraddress']['address2']) ? $value['customeraddress']['address2'] : '',
                        'latitude' => isset($value['latitude']) ? $value['latitude'] : '',
                        'longitude' => isset($value['longitude']) ? $value['longitude'] : '',
                        //'shop_image' => isset($value['customerdetails']['shop_image']) ? $value['customerdetails']['shop_image'] : '',
                        //'visiting_card' => isset($value['customerdetails']['visiting_card']) ? $value['customerdetails']['visiting_card'] : '',
                        'grade' => isset($value['customerdetails']['grade']) ? $value['customerdetails']['grade'] : '',
                        'visit_status' => isset($value['customerdetails']['visit_status']) ? $value['customerdetails']['visit_status'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    // public function getCustomerInfo(Request $request)
    // {

    //      try
    //     { 
    //         $validator = Validator::make($request->all(), [
    //              'customer_id' => 'nullable|exists:customers,id',
    //         ]); 
    //         if ($validator->fails()) {
    //             return response()->json(['status' => 'error','message' => $validator->messages()->all()],$this->badrequest);
    //         }
    //         $user = $request->user();
    //         $user_id = $user->id;
    //         $fromdate = isset($request->fromDate) ? $request->fromDate : null;
    //         $todate = isset($request->toDate) ? $request->toDate :null;

    //         $customer_id = $request->input('customer_id');

    //         $orders = Order::where(function ($query) use($customer_id, $fromdate, $todate){

    //             $query->where('buyer_id', '=', $customer_id);

    //             if(!empty($fromdate) && !empty($todate))
    //             {
    //                 $query->whereBetween('order_date', [$fromdate, $todate]);
    //             }

    //         })
    //         ->select('grand_total','id','total_qty')->get();

    //         //nnn

    //             $sum_quantity = 0;
    //            foreach($orders as $order){
    //              $sum_quantity = OrderDetails::where('order_id',$order->id)->sum('quantity')??0;
    //              }

    //             $sum_quantity = (int)$sum_quantity;


    //        ///nnn

    //         $sales = Sales::where(function ($query) use($customer_id, $fromdate, $todate){
    //             $query->where('buyer_id', '=', $customer_id);
    //             if(!empty($fromdate) && !empty($todate))
    //             {
    //                 $query->whereBetween('invoice_date', [$fromdate, $todate]);
    //             }
    //         })->select('grand_total')->get();

    //         $checkins = CheckIn::with('visitreports')->where('customer_id', '=', $customer_id)->select('checkin_date','checkin_time')->latest()->limit(10)->get();
    //         $last_order_date = Order::where('buyer_id', '=', $customer_id)->latest()->pluck('order_date')->first();
    //         $data = $this->customers->with('customerdetails','getparentdetail','parentdetail','customeraddress','customerdocuments','surveys','surveys.fields','customeraddress.cityname','customeraddress.districtname','customeraddress.statename','customeraddress.pincodename','customertypes','customerdeals')->where('id', $customer_id)->select('id','name','first_name','last_name','mobile','email','profile_image','customer_code','customertype','contact_number', 'latitude' , 'longitude',
    //                DB::raw('(SELECT SUM(grand_total) FROM sales WHERE sales.buyer_id = customers.id) as totalamount'), 
    //                DB::raw('(SELECT SUM(paid_amount) FROM sales WHERE sales.buyer_id = id) as totalpaid'))->first();

    //         $total_value = $orders->sum('grand_total');
    //         $total_qty = $orders->sum('total_qty');
    //         $beatinfo = Beat::whereHas('beatcustomers', function ($query) use($customer_id){
    //                             $query->where('customer_id','=',$customer_id);
    //                         })
    //                         ->select('beat_name','id')->first();


    //          //$data['parent_name'] = $data->parentdetail->name??'';

    //          $parent = array();
    //          $parent_id = array();
    //          if(!empty($data['getparentdetail']))
    //         {  

    //             foreach($data['getparentdetail'] as $key => $parent_data) {
    //                 $parent[] = isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name: '';
    //                 $parent_id[] = isset($parent_data->parent_id) ? $parent_data->parent_id: '';
    //             }

    //         }

    //         $data['parent_id'] = implode(',', $parent_id);
    //         $data['parent_name'] = implode(',', $parent);                
    //         $data['beat_name'] = isset($beatinfo['beat_name']) ? $beatinfo['beat_name'] : '';
    //         $data['beat_id'] = isset($beatinfo['id']) ? $beatinfo['id'] : null;
    //         $data['outstanding'] = $data['totalamount']-$data['totalpaid'];
    //         $data['total_order_value'] = $total_value;
    //         // $data['total_order_quantity'] = $total_qty; 
    //         $data['total_order_quantity'] = $sum_quantity; 

    //         $data['avg_order_value'] = ($total_value >= 1) ? number_format((float)$total_value/$orders->count(), 1, '.', '').' %'  : '';
    //         $data['avg_order_quantity'] = ($total_qty >= 1) ? number_format((float)$total_qty/$orders->count(), 1, '.', '').' %'  : '';
    //         $data['total_sales_value'] = $sales->sum('grand_total');
    //         $data['last_visited'] = (string)$checkins->pluck('checkin_date')->first();
    //         $data['last_order_date'] = isset($last_order_date) ? $last_order_date : '';
    //         $data['visited'] = $checkins;
    //         $data['email'] = isset($data['email']) ? $data['email'] : '';
    //         $data['customer_code'] = isset($data['customer_code']) ? $data['customer_code'] : '';
    //         $data['activities']= UserActivity::with('users')->where('customerid','=',$customer_id)->select('userid','time','description','type')->latest()->limit(5)->get();
    //         $data['tasks']= Tasks::with('users')->where('completed','=',0)->where('customer_id','=',$customer_id)->select('user_id','title','descriptions','datetime')->orderBy('datetime','asc')->limit(5)->get();
    //         unset($data['totalamount'] , $data['totalpaid']);
    //         $data['total_points'] = Wallet::where('customer_id','=',$customer_id)->where('transaction_type','=','Cr')->sum('points');
    //         $data['total_coupon_scan'] = Wallet::where('customer_id','=',$customer_id)->where('transaction_type','=','Cr')->sum('quantity');
    //         return response()->json(['status' => 'success','message' => 'Data retrieved successfully.','data' => $data ], $this->successStatus);
    //     }
    //     catch(\Exception $e)
    //     {
    //         return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
    //     }   
    // }

    public function getCustomerInfo(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'nullable|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->messages()->all()], $this->badrequest);
            }
            $user = $request->user();
            $user_id = $user->id;
            $fromdate = isset($request->fromDate) ? $request->fromDate : null;
            $todate = isset($request->toDate) ? $request->toDate : null;

            $customer_id = $request->input('customer_id');

            $orders = Order::where(function ($query) use ($customer_id, $fromdate, $todate) {

                $query->where('buyer_id', '=', $customer_id);

                if (!empty($fromdate) && !empty($todate)) {
                    $query->whereBetween('order_date', [$fromdate, $todate]);
                } else {
                    $cdate = Carbon::now();
                    $firstDateOfCurrentMonth = $cdate->startOfMonth()->toDateString();
                    $query->where('order_date', '>=', $firstDateOfCurrentMonth);
                }
            })
                ->select('sub_total', 'id', 'total_qty')->get();

            //nnn

            $sum_quantity = 0;
            foreach ($orders as $order) {
                $sum_quantity = OrderDetails::where('order_id', $order->id)->sum('quantity') ?? 0;
            }

            $sum_quantity = (int)$sum_quantity;


            ///nnn

            $sales = Sales::where(function ($query) use ($customer_id, $fromdate, $todate) {
                $query->where('buyer_id', '=', $customer_id);
                if (!empty($fromdate) && !empty($todate)) {
                    $query->whereBetween('invoice_date', [$fromdate, $todate]);
                }
            })->select('grand_total')->get();

            $checkins = CheckIn::with('visitreports', 'visitreports.visittypename')->where('customer_id', '=', $customer_id)->select('checkin_date', 'checkin_time')->latest()->limit(10)->get();
            $last_order_date = Order::where('buyer_id', '=', $customer_id)->latest()->pluck('order_date')->first();
            $data = $this->customers->with('customerdetails', 'visitsinfo', 'getparentdetail', 'parentdetail', 'customeraddress', 'customerdocuments', 'surveys', 'surveys.fields', 'customeraddress.cityname', 'customeraddress.districtname', 'customeraddress.statename', 'customeraddress.pincodename', 'customertypes', 'customerdeals')->where('id', $customer_id)->select(
                'id',
                'name',
                'first_name',
                'last_name',
                'mobile',
                'email',
                'profile_image',
                'customer_code',
                'customertype',
                'contact_number',
                'latitude',
                'longitude',
                'sap_code',
                DB::raw('(SELECT SUM(grand_total) FROM sales WHERE sales.buyer_id = customers.id) as totalamount'),
                DB::raw('(SELECT SUM(paid_amount) FROM sales WHERE sales.buyer_id = id) as totalpaid')
            )->first();

            $total_value = $orders->sum('sub_total');
            $total_qty = $orders->sum('total_qty');
            $beatinfo = Beat::whereHas('beatcustomers', function ($query) use ($customer_id) {
                $query->where('customer_id', '=', $customer_id);
            })
                ->select('beat_name', 'id')->first();


            //$data['parent_name'] = $data->parentdetail->name??'';

            $parent = array();
            $parent_id = array();
            $activity = [];
            if (!empty($data['visitsinfo'])) {
                foreach ($data['visitsinfo'] as $key => $visit) {
                    $activity[] = [
                        "id" => $visit->id,
                        "customer_id" => $visit->customer_id,
                        "description" => $visit->description,
                        "report_title" => $visit->visittypename ? $visit->visittypename->type_name : '-',
                        "visit_image" => $visit->visit_image,
                        "user_id" => $visit->user_id,
                        "user_name" => $visit['users']->name ?? '',
                        "created_at" => Carbon::parse($visit->created_at)->format('d-m-Y'),
                    ];
                }

                // If you need to store this result back into $data, you can do so

            }

            if (!empty($data['getparentdetail'])) {

                foreach ($data['getparentdetail'] as $key => $parent_data) {
                    $parent[] = isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name : '';
                    $parent_id[] = isset($parent_data->parent_id) ? $parent_data->parent_id : '';
                }
            }
            $data['activity'] = $activity;
            $data['parent_id'] = implode(',', $parent_id);
            $data['parent_name'] = implode(',', $parent);
            $data['beat_name'] = isset($beatinfo['beat_name']) ? $beatinfo['beat_name'] : '';
            $data['beat_id'] = isset($beatinfo['id']) ? $beatinfo['id'] : null;
            $data['outstanding'] = $data['totalamount'] - $data['totalpaid'];
            $data['total_order_value'] = $total_value;
            // $data['total_order_quantity'] = $total_qty; 
            $data['total_order_quantity'] = $sum_quantity;

            $data['avg_order_value'] = ($total_value >= 1) ? number_format((float)$total_value / $orders->count(), 1, '.', '') . ' %'  : '';
            $data['avg_order_quantity'] = ($total_qty >= 1) ? number_format((float)$total_qty / $orders->count(), 1, '.', '') . ' %'  : '';
            $data['total_sales_value'] = $sales->sum('grand_total');
            if (!empty($last_order_date) && isset($last_order_date)) {
                $dateTime1 = Carbon::parse($last_order_date);
                $data['last_order_date'] = $dateTime1->format('d-m-Y');
            } else {
                $data['last_order_date'] = "";
            }
            if (!empty($data['visitsinfo']) && isset($data['visitsinfo'][0]->created_at)) {
                $dateTime = Carbon::parse($data['visitsinfo'][0]->created_at);
                $data['last_visited'] = $dateTime->format('d-m-Y');
            } else {
                $data['last_visited'] = "";
            }

            // $data['last_visited'] = isset($data['visitsinfo']) ? $data['visitsinfo'][0]->created_at : '';
            // $data['last_order_date'] = isset($last_order_date) ? $last_order_date : '';
            $data['visited'] = $checkins;
            $data['email'] = isset($data['email']) ? $data['email'] : '';
            $data['customer_code'] = isset($data['customer_code']) ? $data['customer_code'] : '';
            $data['activities'] = UserActivity::with('users')->where('customerid', '=', $customer_id)->select('userid', 'time', 'description', 'type')->latest()->limit(5)->get();
            $data['tasks'] = Tasks::with('users')->where('completed', '=', 0)->where('customer_id', '=', $customer_id)->select('user_id', 'title', 'descriptions', 'datetime')->orderBy('datetime', 'asc')->limit(5)->get();
            unset($data['totalamount'], $data['totalpaid']);
            $data['total_points'] = Wallet::where('customer_id', '=', $customer_id)->where('transaction_type', '=', 'Cr')->sum('points');
            $data['total_coupon_scan'] = Wallet::where('customer_id', '=', $customer_id)->where('transaction_type', '=', 'Cr')->sum('quantity');
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'customers' => $data['visitsinfo']], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function updateCustomerProfile(Request $request)
    {
        try {
            $name = explode(" ", $request['full_name']);
            $request['last_name'] = isset($request['last_name']) ? $request['last_name'] : array_pop($name);
            $request['first_name'] = isset($request['first_name']) ? $request['first_name'] : implode(" ", $name);

            $validator = Validator::make($request->all(), [
                'name'      => 'required',
                // 'email'     => 'required|email|unique:customers,email,'.$request->customer_id,
                // 'mobile'    => 'required|unique:customers,mobile,'.$request->customer_id,
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 201, 'msg' =>  implode(', ', $validator->messages()->all())], 200);
            }

            if ($customer = Customers::where('id', '=', $request->customer_id)->update([
                'name'      => isset($request->name) ? $request->name : '',
                'first_name' => isset($request->first_name) ? $request->first_name : '',
                'last_name' => isset($request->last_name) ? $request->last_name : '',
                'email'     => isset($request->email) ? $request->email : null,
                'mobile'    => isset($request->mobile) ? $request->mobile : null,
                'latitude'  => isset($request->latitude) ? $request->latitude : null,
                'longitude' => isset($request->longitude) ? $request->longitude : null,
                'gender'    => isset($request->gender) ? $request->gender : '',
                'firmtype'  => isset($request->firmtype) ? $request->firmtype : null,
                //'parent_id'  => isset($request->parent_id) ? $request->parent_id : null,
                'contact_number'  => isset($request->contact_number) ? $request->contact_number : null,
            ])) {


                //parent start
                // if(!empty($request['parent_id']))
                // {
                //     ParentDetail::where('customer_id',$request->customer_id)->delete(); 
                //     foreach($request['parent_id'] as $key => $rows) {
                // $parentDetail = ParentDetail::updateOrCreate(
                //   [ 
                //     'customer_id' => $request->customer_id,
                //     'parent_id' => $rows,
                //     'created_by' => Auth::user()->id,
                //   ]
                //  );
                // }
                // }

                if (!empty($request['parent_id'])) {
                    ParentDetail::where('customer_id', $request->customer_id)->delete();
                    $parent_data = explode(",", $request['parent_id']);
                    foreach ($parent_data as $key => $row_parent) {
                        $parentDetail = ParentDetail::create(
                            [
                                'customer_id' => $request->customer_id,
                                'parent_id' => $row_parent,
                                'created_by' => Auth::user()->id,
                            ]
                        );
                    }
                }

                //parent end

                Address::updateOrCreate(['id'   =>  $request['address_id'], 'customer_id'   =>  $request->customer_id], [
                    'active'    => 'Y',
                    'customer_id'   =>  $request['customer_id'],
                    'address1' => isset($request['address1']) ? $request['address1'] : '',
                    'address2' => isset($request['address2']) ? $request['address2'] : '',
                    'landmark' => isset($request['landmark']) ? $request['landmark'] : '',
                    'locality' => isset($request['locality']) ? $request['locality'] : '',
                    'country_id' => isset($request['country_id']) ? $request['country_id'] : null,
                    'state_id' => isset($request['state_id']) ? $request['state_id'] : null,
                    'district_id' => isset($request['district_id']) ? $request['district_id'] : null,
                    'city_id' => isset($request['city_id']) ? $request['city_id'] : null,
                    'pincode_id' => isset($request['pincode_id']) ? $request['pincode_id'] : null,
                    'zipcode' => isset($request['zipcode']) ? $request['zipcode'] : '',
                    'created_by' => $request->user()->id,
                    'updated_at' => getcurentDateTime()
                ]);

                // if($request->file('shopimage')){
                //     $image = $request->file('shopimage');
                //     $filename = 'customer';
                //     $request['shop_image'] = fileupload($image, $this->path, $filename);
                // }

                if ($request->file('gstin_image')) {
                    $image = $request->file('gstin_image');
                    $filename = 'customer';
                    $gstinimagepath = fileupload($image, $this->path, $filename);
                    Attachment::updateOrCreate(['document_name'   =>  'gstin', 'customer_id'   =>  $request->customer_id], [
                        'active'        => 'Y',
                        'file_path'     => $gstinimagepath,
                        'document_name' =>  'gstin',
                        'customer_id' => $request['customer_id'],
                        'updated_at' => getcurentDateTime()
                    ]);
                }

                if ($request->file('pan_image')) {
                    $image = $request->file('pan_image');
                    $filename = 'customer';
                    $panimagepath = fileupload($image, $this->path, $filename);
                    Attachment::updateOrCreate(['document_name'   =>  'pan', 'customer_id'   =>  $request->customer_id], [
                        'active'        => 'Y',
                        'file_path'     => $panimagepath,
                        'document_name' =>  'pan',
                        'customer_id' => $request['customer_id'],
                        'updated_at' => getcurentDateTime()
                    ]);
                }

                if ($request->file('aadhar_image')) {
                    $image = $request->file('aadhar_image');
                    $filename = 'customer';
                    $aadharimagepath = fileupload($image, $this->path, $filename);
                    Attachment::updateOrCreate(['document_name'   =>  'aadhar', 'customer_id'   =>  $request->customer_id], [
                        'active'        => 'Y',
                        'file_path'     => $aadharimagepath,
                        'document_name' =>  'aadhar',
                        'customer_id' => $request['customer_id'],
                        'updated_at' => getcurentDateTime()
                    ]);
                }

                if ($request->file('other_image')) {
                    $image = $request->file('other_image');
                    $filename = 'customer';
                    $otherimagepath = fileupload($image, $this->path, $filename);
                    Attachment::updateOrCreate(['document_name'   =>  'aadhar', 'customer_id'   =>  $request->customer_id], [
                        'active'        => 'Y',
                        'file_path'     => $otherimagepath,
                        'document_name' =>  'other',
                        'customer_id' => $request['customer_id'],
                        'updated_at' => getcurentDateTime()
                    ]);
                }

                if ($request->file('visiting_card')) {
                    $image = $request->file('visiting_card');
                    $filename = 'customer';
                    $request['visiting_image'] = fileupload($image, $this->path, $filename);
                    CustomerDetails::updateOrCreate(['customer_id'   =>  $request->customer_id], [
                        'visiting_card'  =>  isset($request['visiting_image']) ? $request['visiting_image'] : '',
                    ]);
                }

                if ($request->file('shop_image')) {
                    $image = $request->file('shop_image');
                    $filename = 'customer';
                    $request['shop_image'] = fileupload($image, $this->path, $filename);
                    CustomerDetails::updateOrCreate(['customer_id'   =>  $request->customer_id], [
                        'shop_image'  =>  isset($request['shop_image']) ? $request['shop_image'] : '',
                    ]);
                }

                CustomerDetails::updateOrCreate(['customer_id'   =>  $request->customer_id], [
                    'active'        => 'Y',
                    'customer_id'   => isset($request['customer_id']) ? $request['customer_id'] : null,
                    'gstin_no'      => isset($request['gstin_no']) ? ucfirst($request['gstin_no']) : '',
                    'pan_no'        => isset($request['pan_no']) ? ucfirst($request['pan_no']) : '',
                    'aadhar_no'     => isset($request['aadhar_no']) ? ucfirst($request['aadhar_no']) : '',
                    'otherid_no'    => isset($request['otherid_no']) ? ucfirst($request['otherid_no']) : '',
                    'enrollment_date' => isset($request['enrollment_date']) ? $request['enrollment_date'] : null,
                    'approval_date'  => isset($request['approval_date']) ? $request['approval_date'] : null,
                    'grade' => isset($request['grade']) ? $request['grade'] : '',
                    'visit_status' => isset($request['status_type']) ? $request['status_type'] : '',
                    'updated_at'    => getcurentDateTime(),
                ]);
                if ($request['beat_id']) {
                    $beats = BeatCustomer::updateOrCreate(['beat_id'   =>  $request['beat_id'], 'customer_id'   =>  $request->customer_id], [
                        'active' => 'Y',
                        'beat_id' => $request['beat_id'],
                        'customer_id' => $request['customer_id'],
                        'updated_at' => getcurentDateTime(),
                    ]);
                }
                if ($request['survey']) {
                    $surveyqus = json_decode($request['survey'], true);
                    foreach ($surveyqus as $key => $rows) {

                        SurveyData::updateOrCreate(['field_id'   =>  $rows['field_id'], 'customer_id'   =>  $request->customer_id], [
                            'customer_id'   => isset($request['customer_id']) ? $request['customer_id'] : null,
                            'field_id' => !empty($rows['field_id']) ? $rows['field_id'] : null,
                            'value' => !empty($rows['value']) ? $rows['value'] : '',
                            'created_by' => !empty($request['created_by']) ? $request['created_by'] : $request->user()->id,
                            'updated_at' => getcurentDateTime(),
                        ]);
                    }
                }
                if ($request['dealing']) {
                    $dealings = json_decode($request['dealing'], true);
                    foreach ($dealings as $key => $deal) {
                        DealIn::updateOrCreate([
                            'customer_id' => $request['customer_id'],
                            'types' => $deal['types']
                        ], [
                            'customer_id'   => !empty($request['customer_id']) ? $request['customer_id'] : null,
                            'types' => !empty($deal['types']) ? $deal['types'] : '',
                            'hcv' => !empty($deal['hcv']) ? $deal['hcv'] : false,
                            'mav' => !empty($deal['mav']) ? $deal['mav'] : false,
                            'lmv' => !empty($deal['lmv']) ? $deal['lmv'] : false,
                            'lcv' => !empty($deal['lcv']) ? $deal['lcv'] : false,
                            'other' => !empty($deal['other']) ? $deal['other'] : false,
                            'tractor' => !empty($deal['tractor']) ? $deal['tractor'] : false,
                        ]);
                    }
                }
                if ($request->file('image')) {
                    $image = $request->file('image');
                    // $filename = 'punchin_'.autoIncrementId('Attendance', 'id');
                    $filename = 'customer';
                    $request['profile_image'] = fileupload($image, $this->path, $filename);
                    Customers::where('id', '=', $request->customer_id)->update([
                        'profile_image'      => $request['profile_image']
                    ]);
                }
                return response()->json(['status' => 200, 'msg' => 'Customer Update Successfully', 'data' => $customer], 200);
            }

            return response()->json(['status' => 201, 'msg' => 'Error in user registertion'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 201, 'msg' => $e->getMessage()], 500);
        }
    }

    public function active(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'  => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->messages()->all()], $this->badrequest);
        }
        if (Customers::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'Y' : 'N'])) {
            $message = ($request['active'] == 'N') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'Customer ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }
}
