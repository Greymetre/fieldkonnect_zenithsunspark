<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SendNotifications;
use App\Models\Address;
use App\Models\BeatSchedule;
use App\Models\BeatUser;
use App\Models\CustomerDetails;
use App\Models\Customers;
use App\Models\CustomerType;
use App\Models\FieldKonnectAppSetting;
use App\Models\MobileUserLoginDetails;
use App\Models\ParentDetail;
use App\Models\Pincode;
use App\Models\State;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;
use App\Services\InfismsApiClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

use Validator;
use Gate;
use Illuminate\Support\Facades\File;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->users = new User();
        $this->customer = new Customers();
        $this->usersLogin = new UserLogin();
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 402;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
        $this->path = 'users';
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->noContent);
            }
            $username = $request->input('username');
            if (!$user = $this->users->with('roles')->where('mobile', $username)->orWhere('email', $username)->first()) {
                return response()->json(['status' => 'error', 'message' => 'User not found'], $this->notFound);
            }
            $checkLastLogin = MobileUserLoginDetails::where('user_id', $user['id'])->first();
            if ($checkLastLogin) {
                if (!$user->hasRole('superadmin')) {
                    if ($checkLastLogin->unique_id != NULL && $checkLastLogin->unique_id != $request['unique_id'] && $checkLastLogin->multi_login == '0') {
                        return response()->json(['status' => 'error', 'message' =>  'Multiple device login is not allowed. For support, please contact FieldKonnect at 9713113280.'], $this->noContent);
                    };
                }

                MobileUserLoginDetails::updateOrCreate(['user_id' => $user['id']], [
                    'app_version'   =>  $request['app_version'],
                    'device_name'   =>  $request['device_name'],
                    'device_type'   =>  $request['device_type'],
                    'unique_id'   =>  $request['unique_id'],
                    'last_login_date'   =>  Carbon::now(),
                    'login_status'   =>  '1',
                    'app'   =>  '2',
                ]);
            } else {
                MobileUserLoginDetails::updateOrCreate(['user_id' => $user['id']], [
                    'app_version'   =>  $request['app_version'],
                    'device_name'   =>  $request['device_name'],
                    'device_type'   =>  $request['device_type'],
                    'unique_id'   =>  $request['unique_id'],
                    'first_login_date'   =>  Carbon::now(),
                    'last_login_date'   =>  Carbon::now(),
                    'login_status'   =>  '1',
                    'app'   =>  '2',
                ]);
            }
            if (!$user->hasRole('superadmin')) {
                $user->tokens()->delete();
            }

            if ($user->active != 'Y') {
                return response()->json(['status' => 'error', 'message' => 'Your account is deactivated don\'t hesitate to get in touch with admin.'], $this->notFound);
            }
            $password = $request->input('password');
            if (Hash::check($password, $user['password'])) {
                $token = $user->createToken('gSQ01LKOg1JV0O9eMsDiAN0TqkQlOpulK7vWemPF')->accessToken;
                $user->update([
                    'notification_id' => !empty($request['device_token']) ? $request['device_token'] : '',
                    'device_type' => isset($request['device_type']) ? $request['device_type'] : ''
                ]);
                $todayDate = Carbon::today()->toDateString();
                $todayBeatSchedule = BeatSchedule::where('user_id', $user['id'])->where('beat_date', $todayDate)->get();
                $beatUser = BeatUser::where('user_id', $user['id'])->get();
                $nestedData['id'] = isset($user['id']) ? $user['id'] : 0;
                $nestedData['name'] = isset($user['name']) ? $user['name'] : '';
                $nestedData['dividion_id'] = isset($user['division_id']) ? $user['division_id'] : '';
                $nestedData['first_name'] = isset($user['first_name']) ? $user['first_name'] : '';
                $nestedData['last_name'] = isset($user['last_name']) ? $user['last_name'] : '';
                $nestedData['email'] = isset($user['email']) ? $user['email'] : '';
                $nestedData['mobile'] = isset($user['mobile']) ? $user['mobile'] : '';
                $nestedData['profile_image'] = isset($user['profile_image']) ? $user['profile_image'] : '';
                $nestedData['gender'] = isset($user['gender']) ? $user['gender'] : '';
                $nestedData['payroll_id'] = isset($user['payroll']) ? $user['payroll'] : '';
                $nestedData['todayBeatSchedule'] = count($todayBeatSchedule) > 0 ? true : false;
                $nestedData['beatUser'] = count($beatUser) > 0 ? true : false;
                $nestedData['access_token'] = $token;
                $nestedData['roles'] = $user->roles->pluck('id')->toArray();
                $nestedData['user_type'] = $user->roles->pluck('name')->toArray();
                $nestedData['leave_balance'] = $user->leave_balance;
                if ($user->hasRole('Customer Dealer')) {
                    $user['provider'] = 'retailers';
                } else {
                    $user['provider'] = 'users';
                }
                $user['entry_from'] = 'App';
                $this->usersLogin->save_data($user);

                return response()->json(['status' => 'success', 'userinfo' => $nestedData], $this->successStatus);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Password not match'], $this->unauthorized);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $user = $this->users->where('id', $user_id)->first();
            $nestedData['company_name'] = isset($user['companies']['company_name']) ? $user['companies']['company_name'] : '';
            $nestedData['name'] = isset($user['name']) ? $user['name'] : '';
            $nestedData['first_name'] = isset($user['first_name']) ? $user['first_name'] : '';
            $nestedData['last_name'] = isset($user['last_name']) ? $user['last_name'] : '';
            $nestedData['email'] = isset($user['email']) ? $user['email'] : '';
            $nestedData['mobile'] = isset($user['mobile']) ? $user['mobile'] : '';
            $nestedData['profile_image'] = isset($user['profile_image']) ? $user['profile_image'] : '';
            $nestedData['gender'] = isset($user['gender']) ? $user['gender'] : '';
            $nestedData['region_id'] = isset($user['region_id']) ? $user['region_id'] : '';
            return response()->json(['status' => 'success', 'userinfo' => $nestedData], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            $request['user_id'] = $user->id;
            if ($request->file('image')) {
                $image = $request->file('image');
                $filename = 'user_' . $request['user_id'];
                $request['profile_image'] = fileupload($image, $this->path, $filename);
            }
            $users =  $this->users->where('id', $request['user_id'])->first();
            if ($request['profile_image']) {
                $users->profile_image = $request['profile_image'];
            }
            if ($users->save()) {
                $response['profile_image'] = $this->users->where('id', $request['user_id'])->pluck('profile_image')->first();
                return response()->json($response, $this->successStatus);
            }
            return response()->json($response, $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if ($request->user()->token()->revoke()) {
                MobileUserLoginDetails::updateOrCreate(['user_id' => $user->id], [
                    'login_status'   =>  '0',
                ]);
                $this->users->where('id', $user->id)->update([
                    'notification_id' => ""
                ]);
                if ($user->hasRole('Customer Dealer')) {
                    $user['provider'] = 'retailers';
                } else {
                    $user['provider'] = 'users';
                }
                $this->usersLogin->logout($user);
                return response()->json(['status' => 'success', 'message' => 'Logout Successfully'], $this->successStatus);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in Logout'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function customerLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile_number' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->noContent);
            }
            if (strlen(preg_replace('/\s+/', '', $request['mobile_number'])) == 10) {
                $request['mobile_number'] = '91' . preg_replace('/\s+/', '', $request['mobile_number']);
            }
            $username = $request['mobile_number'];

            if (!$user = $this->customer->with('customerdetails')->where('mobile', $username)->first()) {
                return response()->json(['status' => 'error', 'message' => 'User not found'], $this->notFound);
            } else {
                if ($user->active != 'Y') {
                    return response()->json(['status' => 'error', 'message' => 'Your account is deactivated don\'t hesitate to get in touch with admin.'], $this->notFound);
                }
                // this logic added recently beacause client want service center can loggedin by password
               
                CustomerDetails::updateOrCreate(['customer_id' => $user->id], [
                    // 'active'    => 'Y',
                    'customer_id'   =>  $user->id,
                    'fcm_token'   =>  $request['fcm_token'],
                ]);
                $checkLastLogin = MobileUserLoginDetails::where('customer_id', $user->id)->first();
                if ($checkLastLogin) {
                    MobileUserLoginDetails::updateOrCreate(['customer_id' => $user->id], [
                        'customer_id'   =>  $user->id,
                        'app_version'   =>  $request['app_version'],
                        'device_type'   =>  $request['device_type'],
                        'device_name'   =>  $request['device_name'],
                        'last_login_date'   =>  Carbon::now(),
                        'login_status'   =>  '1',
                        'app'   =>  '1',
                    ]);
                } else {
                    MobileUserLoginDetails::updateOrCreate(['customer_id' => $user->id], [
                        'customer_id'   =>  $user->id,
                        'app_version'   =>  $request['app_version'],
                        'device_type'   =>  $request['device_type'],
                        'device_name'   =>  $request['device_name'],
                        'first_login_date'   =>  Carbon::now(),
                        'last_login_date'   =>  Carbon::now(),
                        'login_status'   =>  '1',
                        'app'   =>  '1',
                    ]);
                }
                if((isset($user->customertype) && $user->customertype == 4) || isset($request->password)){
                    return $this->serviceCenterLogin($request , $user ,$validator);
                }
                if ($username == '917788996655') {
                    $otp = 1234;
                } else {
                    $otp = rand(1000, 9999);
                }


                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'http://sms.infisms.co.in/API/SendSMS.aspx?UserID=SILCLN&UserPassword=sil%24clnco&PhoneNumber=' . $username . '&Text=%22' . $otp . '%22is%20your%20OTP%20to%20login%20into%20the%20SILVER%20FAMILY%20App.%20Let%27s%20grow%20together%20and%20achieve%20more.%20From%20SILVER%20CONSUMER%20ELECTRICALS%20PRIVATE%20LIMITED&SenderId=SILCCD&AccountType=2&MessageType=0',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Cookie: ASP.NET_SessionId=ti1fkgsldce1g3rn4l5ee4e1'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $user->otp = $otp;
                $user->save();
                $nestedData['id'] = $user->id;
                $nestedData['otp'] = $user->otp;

                return response()->json(['status' => 'success', 'info' => $nestedData], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    private function serviceCenterLogin($request , $user , $validator){
       if(empty($request->password)) {
            $validator->after(function ($validator) {
                $validator->errors()->add('password', 'The password field is required for service center users.');
            });
        }
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], $this->noContent);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect password'
            ], 401);
        }

        $token = $user->createToken('gSQ01LKOg1JV0O9eMsDiAN0TqkQlOpulK7vWemPF')->accessToken;
        $profile_image = $user->shop_image;
        $user->shop_image = $user->profile_image;
        $user->profile_image = $profile_image;
        $user->token = $token;
        $user->total_point = $user->customer_transacation->sum('point');
        $user->active_point = $user->customer_transacation->where('status', '1')->sum('point');
        $user->provision_point = $user->customer_transacation->where('status', '0')->sum('point');
        return response()->json(['status' => 'success', 'userinfo' => $user], $this->successStatus);
    }

    public function verifyotp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'otp' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->noContent);
            }
            $id = $request->input('id');
            $otp = $request->input('otp');

            if (!$user = $this->customer->with('getparentdetail', 'customerdetails', 'customeraddress', 'customeraddress.statename', 'customeraddress.districtname', 'customeraddress.cityname')->where('id', $id)->where('otp', $otp)->first()) {
                return response()->json(['status' => 'error', 'message' => 'Wroung OTP !!'], $this->notFound);
            } else {
                $token = $user->createToken('gSQ01LKOg1JV0O9eMsDiAN0TqkQlOpulK7vWemPF')->accessToken;
                $profile_image = $user->shop_image;
                $user->shop_image = $user->profile_image;
                $user->profile_image = $profile_image;
                $user->token = $token;
                $user->total_point = $user->customer_transacation->sum('point');
                $user->active_point = $user->customer_transacation->where('status', '1')->sum('point');
                $user->provision_point = $user->customer_transacation->where('status', '0')->sum('point');
                return response()->json(['status' => 'success', 'userinfo' => $user], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function customerSignup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'shop_name' => 'required',
                'address' => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                'mobile'  => 'required|numeric|unique:customers,mobile',
                'customertype'       => 'nullable|exists:customer_types,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->noContent);
            }
            $request['mobile'] = preg_replace("/[^0-9]/", "", $request['mobile']);
            if (strlen(preg_replace('/\s+/', '', $request['mobile'])) == 10) {
                $request['mobile'] = '91' . preg_replace('/\s+/', '', $request['mobile']);
            }


            $customerdetails = Customers::where('mobile', $request['mobile'])->first();
            if (!empty($customerdetails)) {
                return response()->json(['status' => 'error', 'message' => 'Mobile Number Already Exist'], 400);
            } else {
                $name = explode(" ", $request['name']);
                $request['last_name'] = isset($request['last_name']) ? $request['last_name'] : array_pop($name);
                $request['first_name'] = isset($request['first_name']) ? $request['first_name'] : implode(" ", $name);
                $request['created_by'] = 0;
                $customertype = CustomerType::where('type_name', '=', 'retailer')->pluck('id')->first();
                $request['customertype'] = isset($request['customertype']) ? $request['customertype'] : $customertype;

                if ($customer = Customers::updateOrCreate(['mobile' => $request['mobile']], [
                    'active' => 'Y',
                    'name' => !empty($request['shop_name']) ? ucfirst($request['shop_name']) : '',
                    'first_name' => !empty($request['first_name']) ? ucfirst($request['first_name']) : '',
                    'last_name' => !empty($request['last_name']) ? ucfirst($request['last_name']) : '',
                    'mobile' => $request['mobile'],
                    'customertype' =>  !empty($request['customertype']) ? $request['customertype'] : 2,
                    'created_by' =>  !empty($request['created_by']) ? $request['created_by'] : null,
                    'created_at' => getcurentDateTime(),
                    'updated_at' => getcurentDateTime()
                ])) {
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
                            'email'   =>  isset($request['email']) ? $request['email'] : 'customer' . $customer->id . '@gmail.com',
                            'password'   =>  Hash::make($passis),
                            'reportingid' => !empty($request['created_by']) ? $request['created_by'] : null,
                            'password_string'   =>  $passis,
                            'customerid' => $customer->id,
                        ]);
                        $user->roles()->sync(['29']);
                        $permissions = $user->getPermissionsViaRoles()->pluck('name');
                        $user->givePermissionTo($permissions);
                    }

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
                        'created_by' => !empty($request['created_by']) ? $request['created_by'] : 0,
                        'created_at' => getcurentDateTime(),
                        'updated_at' => getcurentDateTime()
                    ]);
                    CustomerDetails::updateOrCreate(['customer_id' => $request['customer_id']], [
                        'active'    => 'Y',
                        'customer_id'   =>  $request['customer_id'],
                        'fcm_token'   =>  $request['fcm_token'],
                    ]);
                    if (!empty($request['parent_id'])) {
                        foreach ($request['parent_id'] as $key => $rows) {
                            $parentDetail = ParentDetail::create(
                                [
                                    'customer_id' => $request['customer_id'],
                                    'parent_id' => $rows,
                                ]
                            );
                        }
                    }
                    $otp = rand(1000, 9999);

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'http://sms.infisms.co.in/API/SendSMS.aspx?UserID=SILCLN&UserPassword=sil%24clnco&PhoneNumber=' . $request['mobile'] . '&Text=%22' . $otp . '%22is%20your%20OTP%20to%20login%20into%20the%20SILVER%20FAMILY%20App.%20Let%27s%20grow%20together%20and%20achieve%20more.%20From%20SILVER%20CONSUMER%20ELECTRICALS%20PRIVATE%20LIMITED&SenderId=SILCCD&AccountType=2&MessageType=0',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Cookie: ASP.NET_SessionId=ti1fkgsldce1g3rn4l5ee4e1'
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    $customer->otp = $otp;
                    if($request['customertype'] == '4'){
                        $password = Hash::make($request['mobile']);
                        $customer->password = $password;
                    }
                    $customer->save();
                    $noti_data = [
                        'fcm_token' => $customer->customerdetails->fcm_token,
                        'title' => 'Sign up Successful 💯',
                        'msg' => $customer->name . ' your sign up is successful in Silver Saarthi.',
                    ];
                    $send_notification = SendNotifications::send($noti_data);
                    return response()->json(['status' => 'success', 'userinfo' => $customer, 'push_notification' => $send_notification], $this->successStatus);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function customerlogout(Request $request)
    {
        try {
            $user = $request->user();
            if ($request->user()->token()->revoke()) {
                MobileUserLoginDetails::updateOrCreate(['customer_id' => $user->id], [
                    'customer_id'   =>  $user->id,
                    'login_status'   =>  '0',
                ]);
                return response()->json(['status' => 'success', 'message' => 'Logout Successfully'], $this->successStatus);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in Logout'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getOrderDiscountLimit(Request $request)
    {
        try {
            $order_discount_limit = FieldKonnectAppSetting::where('id', '1')->first();
            return response()->json(['status' => 'success', 'order_discount_limit' => $order_discount_limit->order_discount_limit], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
