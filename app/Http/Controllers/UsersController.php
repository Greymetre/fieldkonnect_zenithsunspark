<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\UserRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\DataTables\UsersDataTable;
use App\DataTables\UserCityDataTable;
use App\Exports\ASMRatingDetailReportExport;
use App\Exports\ASMRatingReportExport;
use App\Exports\CHRatingDetailReportExport;
use App\Exports\CHRatingReportExport;
use App\Exports\FOSRatingReportExport;
use App\Imports\UserImport;
use App\Exports\UserExport;
use App\Exports\UserTemplate;
use App\Models\UserDetails;
use App\Models\City;
use App\Models\UserCityAssign;
use App\Imports\UserCityImport;
use App\Exports\UserCityMapedExport;
use App\Exports\UserSalesReportExport;
use App\Models\Branch;
use App\Models\Customers;
use App\Models\CustomerType;
use App\Models\Designation;
use App\Models\Division;
use App\Models\Department;
use App\Models\District;
use App\Models\Order;
use App\Models\TransactionHistory;
use App\Models\UserEducation;
use App\Models\UserPmsRemark;
use App\Models\WareHouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->user = new User();
        $this->path = 'users';
        $this->aadhar_card_path = 'aadhar_card';
        $this->pan_card_path = 'pan_card';
    }

    public function index(UsersDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $divisions = Division::where('active', 'Y')->get();
        $branches = Branch::where('active', 'Y')->get();
        $departments = Department::where('active', 'Y')->get();
        return $dataTable->render('users.index', compact('divisions', 'branches', 'departments'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles = Role::where('name', '!=', 'super-admin')->pluck('name', 'id');
        $cities = City::where('active', '=', 'Y')->pluck('city_name', 'id');
        $reportings = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->where('active', '=', 'Y')->select('id', 'name')->get();
        $customertype = CustomerType::select('id', 'customertype_name')->orderBy('id', 'desc')->get();

        $branches = Branch::where('active', '=', 'Y')->get();
        $designations = Designation::where('active', '=', 'Y')->get();
        $divisions = Division::where('active', '=', 'Y')->get();
        $departments = Department::where('active', '=', 'Y')->get();
        $pay_rolls = Config('constants.pay_roll');
        $warehouses = WareHouse::all();


        return view('users.create', compact('roles', 'cities', 'reportings', 'branches', 'designations', 'divisions', 'customertype', 'departments', 'pay_rolls', 'warehouses'))->with('user', $this->user);
    }

    public function store(UserRequest $request)
    {

        $user = User::create([
            'active'   =>  isset($request['active']) ? $request['active'] : 'Y',
            'name'   =>  isset($request['name']) ? $request['name'] : $request['first_name'] . ' ' . $request['last_name'],
            'first_name'   =>  isset($request['first_name']) ? $request['first_name'] : '',
            'last_name'   =>  isset($request['last_name']) ? $request['last_name'] : '',
            'mobile'   =>  isset($request['mobile']) ? $request['mobile'] : null,
            'email'   =>  isset($request['email']) ? $request['email'] : '',
            'leave_balance'   =>  isset($request['leave_balance']) ? $request['leave_balance'] : '0.00',
            'grade'   =>  isset($request['grade']) ? $request['grade'] : NULL,
            'blood_group'   =>  isset($request['blood_group']) ? $request['blood_group'] : NULL,
            'personal_number'   =>  isset($request['personal_number']) ? $request['personal_number'] : NULL,
            'password'   =>  isset($request['password']) ? Hash::make($request['password']) : '',
            'notification_id'   =>  isset($request['notification_id']) ? $request['notification_id'] : '',
            'device_type'   =>  isset($request['device_type']) ? $request['device_type'] : '',
            'gender'   =>  isset($request['gender']) ? $request['gender'] : '',
            'profile_image'   =>  isset($request['profile_image']) ? $request['profile_image'] : '',
            'latitude'   =>  isset($request['latitude']) ? $request['latitude'] : '',
            'longitude' => isset($request['longitude']) ? $request['longitude'] : '',
            'location' => !empty($request['location']) ? $request['location'] : '',
            'branch_id' => (isset($request['branch_id']) && count($request['branch_id']) > 0) ? implode(',', $request['branch_id']) : '',
            'primary_branch_id' => isset($request['primary_branch_id']) ? $request['primary_branch_id'] : '',
            'branch_show' => isset($request['branch_show']) ? implode(',', $request['branch_show']) : NULL,
            'department_id' => isset($request['department_id']) ? $request['department_id'] : '',
            'employee_codes' => isset($request['employee_codes']) ? $request['employee_codes'] : '',
            'designation_id' => isset($request['designation_id']) ? $request['designation_id'] : '',
            'division_id' => isset($request['division_id']) ? $request['division_id'] : '',
            'reportingid' => isset($request['reportingid']) ? $request['reportingid'] : '',
            'show_attandance_report' => isset($request['show_attandance_report']) ? $request['show_attandance_report'] : 1,
            'payroll' => isset($request['payroll']) ? $request['payroll'] : '',
            'warehouse_id' => isset($request['warehouse_id']) ? $request['warehouse_id'] : NULL,
            'customerid' => isset($request['customerid']) ? $request['customerid'] : NULL,
        ]);
        $user->roles()->sync($request->input('roles', []));
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $user->givePermissionTo($permissions);

        if ($request->file('image')) {
            $user->addMedia($request->file('image'))->toMediaCollection('profile_image');
        }
        if (!empty($request['cities'])) {
            foreach ($request['cities'] as $key => $city) {
                UserCityAssign::updateOrCreate(
                    ['userid' => $user['id'], 'city_id' => $city],
                    ['userid' => $user['id'], 'city_id' => $city,  'reportingid' => $request['reportingid']]
                );
            }
        }
        UserDetails::insert([
            'user_id' => $user['id'],
            'marital_status'   =>  isset($request['marital_status']) ? $request['marital_status'] : null,
            'date_of_birth'   =>  isset($request['date_of_birth']) ? $request['date_of_birth'] : null,
            'pan_number'   =>  isset($request['pan_number']) ? $request['pan_number'] : null,
            'aadhar_number'   =>  isset($request['aadhar_number']) ? $request['aadhar_number'] : null,
            'emergency_number'   =>  isset($request['emergency_number']) ? $request['emergency_number'] : null,
            'current_address'   =>  isset($request['current_address']) ? $request['current_address'] : null,
            'permanent_address'   =>  isset($request['permanent_address']) ? $request['permanent_address'] : null,
            'father_name'   =>  isset($request['father_name']) ? $request['father_name'] : null,
            'father_date_of_birth'   =>  isset($request['father_date_of_birth']) ? $request['father_date_of_birth'] : null,
            'mother_name'   =>  isset($request['mother_name']) ? $request['mother_name'] : null,
            'mother_date_of_birth'   =>  isset($request['mother_date_of_birth']) ? $request['mother_date_of_birth'] : null,
            'marriage_anniversary'   =>  isset($request['marriage_anniversary']) ? $request['marriage_anniversary'] : null,
            'spouse_name'  =>  isset($request['spouse_name']) ? $request['spouse_name'] : null,
            'spouse_date_of_birth'  =>  isset($request['spouse_date_of_birth']) ? $request['spouse_date_of_birth'] : null,
            'children_one'  =>  isset($request['children_one']) ? $request['children_one'] : null,
            'children_one_date_of_birth'  =>  isset($request['children_one_date_of_birth']) ? $request['children_one_date_of_birth'] : null,
            'children_two'  =>  isset($request['children_two']) ? $request['children_two'] : null,
            'children_two_date_of_birth'  =>  isset($request['children_two_date_of_birth']) ? $request['children_two_date_of_birth'] : null,
            'children_three'  =>  isset($request['children_three']) ? $request['children_three'] : null,
            'children_three_date_of_birth'  =>  isset($request['children_three_date_of_birth']) ? $request['children_three_date_of_birth'] : null,
            'children_four'  =>  isset($request['children_four']) ? $request['children_four'] : null,
            'children_four_date_of_birth'  =>  isset($request['children_four_date_of_birth']) ? $request['children_four_date_of_birth'] : null,
            'children_five'  =>  isset($request['children_five']) ? $request['children_five'] : null,
            'children_five_date_of_birth'  =>  isset($request['children_five_date_of_birth']) ? $request['children_five_date_of_birth'] : null,
            'account_number'   =>  isset($request['account_number']) ? $request['account_number'] : null,
            'bank_name'   =>  isset($request['bank_name']) ? $request['bank_name'] : null,
            'ifsc_code'   =>  isset($request['ifsc_code']) ? $request['ifsc_code'] : null,
            //'salary'   =>  isset($request['salary']) ? $request['salary'] :0.00,
            'salary'   =>  $request['salary'] ?? 0.00,
            //'ctc_annual'   =>  isset($request['ctc_annual']) ? $request['ctc_annual'] :0.00,
            'ctc_annual'   => $request['ctc_annual'] ?? 0.00,
            //'gross_salary_monthly'   =>  isset($request['gross_salary_monthly']) ? $request['gross_salary_monthly'] :0.00,
            'gross_salary_monthly' => $request['gross_salary_monthly'] ?? 0.00,
            //'last_year_increments'   =>  isset($request['last_year_increments']) ? $request['last_year_increments'] :0.00,
            'last_year_increments' => $request['last_year_increments'] ?? 0.00,

            'last_year_increment_percent'   =>  isset($request['last_year_increment_percent']) ? $request['last_year_increment_percent'] : null,
            // 'last_year_increment_value'   =>  isset($request['last_year_increment_value']) ? $request['last_year_increment_value'] :0.00,

            'last_year_increment_value' => $request['last_year_increment_value'] ?? 0.00,

            'last_promotion' =>  isset($request['last_promotion']) ? $request['last_promotion'] : null,
            'pf_number'   =>  isset($request['pf_number']) ? $request['pf_number'] : null,
            'un_number'   =>  isset($request['un_number']) ? $request['un_number'] : null,
            'esi_number'   =>  isset($request['esi_number']) ? $request['esi_number'] : null,
            'probation_period'   =>  isset($request['probation_period']) ? $request['probation_period'] : null,
            'date_of_confirmation'   =>  isset($request['date_of_confirmation']) ? $request['date_of_confirmation'] : null,
            'notice_period'   =>  isset($request['notice_period']) ? $request['notice_period'] : null,
            'date_of_leaving'   =>  isset($request['date_of_leaving']) ? $request['date_of_leaving'] : null,
            'date_of_joining'   =>  isset($request['date_of_joining']) ? $request['date_of_joining'] : null,
            'biometric_code'   =>  isset($request['biometric_code']) ? $request['biometric_code'] : null,
            'order_mails'   =>  isset($request['order_mails']) ? $request['order_mails'] : '',
            'order_mails_type'   =>  isset($request['order_mails_type']) ? implode(',', $request['order_mails_type']) : '',
            'other_education'  => isset($request['other_education']) ? $request['other_education'] : null,
            'previous_exp'   =>  isset($request['previous_exp']) ? $request['previous_exp'] : null,
            'current_company_tenture'   =>  isset($request['current_company_tenture']) ? $request['current_company_tenture'] : null,
            'total_exp'   =>  isset($request['total_exp']) ? $request['total_exp'] : null,

        ]);
        if ($request->education_detail && count($request->education_detail) > 0) {
            foreach ($request->education_detail as $education_detail) {
                if ($education_detail['degree_name'] != null && $education_detail['degree_name'] != '') {
                    $new_education_detail = new UserEducation();
                    $new_education_detail->user_id = $user['id'];
                    $new_education_detail->degree_name = $education_detail['degree_name'];
                    $new_education_detail->board_name = $education_detail['board_name'];
                    $new_education_detail->percentage = $education_detail['percentage'];
                    $new_education_detail->grade = $education_detail['grade'];
                    $new_education_detail->save();
                    if ($education_detail['image'] && $education_detail['image'] != null && $education_detail['image'] != '') {
                        $new_education_detail->addMedia($education_detail['image'])->toMediaCollection('education_image');
                    }
                }
            }
        }

        if ($request->file('aadhar_card_image')) {
            $user->addMedia($request->file('aadhar_card_image'))->toMediaCollection('aadhar_image');
        }
        if ($request->file('pan_card_image')) {
            $user->addMedia($request->file('pan_card_image'))->toMediaCollection('pan_image');
        }
        return redirect()->route('users.index');
    }

    public function edit($id)
    {
        //abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $user = User::with('userinfo')->where('id', $id)->first();
        // dd($user->userinfo);
        $roles = Role::where('name', '!=', 'super-admin')->pluck('name', 'id');
        $user->load('roles');
        $user['cities'] = UserCityAssign::where('userid', '=', $id)->pluck('city_id');
        //$user['reportingid'] = UserCityAssign::where('userid','=',$id)->pluck('reportingid')->first();
        $customertype = CustomerType::select('id', 'customertype_name')->orderBy('id', 'desc')->get();
        $cities = City::where('active', '=', 'Y')->pluck('city_name', 'id');
        $reportings = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->where('active', '=', 'Y')->select('id', 'name')->get();

        $branches = Branch::where('active', '=', 'Y')->get();
        $designations = Designation::where('active', '=', 'Y')->get();
        $divisions = Division::where('active', '=', 'Y')->get();
        $departments = Department::where('active', '=', 'Y')->get();
        $pay_rolls = Config('constants.pay_roll');
        $warehouses = WareHouse::all();
        // dd($user);
        return view('users.create', compact('roles', 'user', 'cities', 'reportings', 'branches', 'designations', 'divisions', 'customertype', 'departments', 'pay_rolls', 'warehouses'));
    }

    //public function update(Request $request, User $user)
    public function update(Request $request, $id)
    {
        $details_updated = UserDetails::updateOrCreate(['user_id' => $id], [
            'marital_status'   =>  isset($request['marital_status']) ? $request['marital_status'] : null,
            'date_of_birth'   =>  isset($request['date_of_birth']) ? $request['date_of_birth'] : null,
            'pan_number'   =>  isset($request['pan_number']) ? $request['pan_number'] : null,
            'aadhar_number'   =>  isset($request['aadhar_number']) ? $request['aadhar_number'] : null,
            'emergency_number'   =>  isset($request['emergency_number']) ? $request['emergency_number'] : null,
            'current_address'   =>  isset($request['current_address']) ? $request['current_address'] : null,
            'permanent_address'   =>  isset($request['permanent_address']) ? $request['permanent_address'] : null,
            'father_name'   =>  isset($request['father_name']) ? $request['father_name'] : null,
            'father_date_of_birth'   =>  isset($request['father_date_of_birth']) ? $request['father_date_of_birth'] : null,
            'mother_name'   =>  isset($request['mother_name']) ? $request['mother_name'] : null,
            'mother_date_of_birth'   =>  isset($request['mother_date_of_birth']) ? $request['mother_date_of_birth'] : null,
            'marriage_anniversary'   =>  isset($request['marriage_anniversary']) ? $request['marriage_anniversary'] : null,
            'spouse_name'  =>  isset($request['spouse_name']) ? $request['spouse_name'] : null,
            'spouse_date_of_birth'  =>  isset($request['spouse_date_of_birth']) ? $request['spouse_date_of_birth'] : null,
            'children_one'  =>  isset($request['children_one']) ? $request['children_one'] : null,
            'children_one_date_of_birth'  =>  isset($request['children_one_date_of_birth']) ? $request['children_one_date_of_birth'] : null,
            'children_two'  =>  isset($request['children_two']) ? $request['children_two'] : null,
            'children_two_date_of_birth'  =>  isset($request['children_two_date_of_birth']) ? $request['children_two_date_of_birth'] : null,
            'children_three'  =>  isset($request['children_three']) ? $request['children_three'] : null,
            'children_three_date_of_birth'  =>  isset($request['children_three_date_of_birth']) ? $request['children_three_date_of_birth'] : null,
            'children_four'  =>  isset($request['children_four']) ? $request['children_four'] : null,
            'children_four_date_of_birth'  =>  isset($request['children_four_date_of_birth']) ? $request['children_four_date_of_birth'] : null,
            'children_five'  =>  isset($request['children_five']) ? $request['children_five'] : null,
            'children_five_date_of_birth'  =>  isset($request['children_five_date_of_birth']) ? $request['children_five_date_of_birth'] : null,
            'account_number'   =>  isset($request['account_number']) ? $request['account_number'] : null,
            'bank_name'   =>  isset($request['bank_name']) ? $request['bank_name'] : null,
            'ifsc_code'   =>  isset($request['ifsc_code']) ? $request['ifsc_code'] : null,
            //'salary'   =>  isset($request['salary']) ? $request['salary'] :0.00,
            'salary'   => $request['salary'] ?? 0.00,
            //'ctc_annual'   =>  isset($request['ctc_annual']) ? $request['ctc_annual'] :0.00,
            'ctc_annual'  => $request['ctc_annual'] ?? 0.00,
            //'gross_salary_monthly'   =>  isset($request['gross_salary_monthly']) ? $request['gross_salary_monthly'] :0.00,
            'gross_salary_monthly'   => $request['gross_salary_monthly'] ?? 0.00,
            //'last_year_increments'   =>  isset($request['last_year_increments']) ? $request['last_year_increments'] :0.00,
            'last_year_increments' => $request['last_year_increments'] ?? 0.00,
            'last_year_increment_percent'   =>  isset($request['last_year_increment_percent']) ? $request['last_year_increment_percent'] : null,
            // 'last_year_increment_value'   =>  isset($request['last_year_increment_value']) ? $request['last_year_increment_value'] :0.00,
            'last_year_increment_value' => $request['last_year_increment_value'] ?? 0.00,
            'last_promotion' =>  isset($request['last_promotion']) ? $request['last_promotion'] : null,
            'pf_number'   =>  isset($request['pf_number']) ? $request['pf_number'] : null,
            'un_number'   =>  isset($request['un_number']) ? $request['un_number'] : null,
            'esi_number'   =>  isset($request['esi_number']) ? $request['esi_number'] : null,
            'probation_period'   =>  isset($request['probation_period']) ? $request['probation_period'] : null,
            'date_of_confirmation'   =>  isset($request['date_of_confirmation']) ? $request['date_of_confirmation'] : null,
            'notice_period'   =>  isset($request['notice_period']) ? $request['notice_period'] : null,
            'date_of_leaving'   =>  isset($request['date_of_leaving']) ? $request['date_of_leaving'] : null,
            'date_of_joining'   =>  isset($request['date_of_joining']) ? $request['date_of_joining'] : null,
            'biometric_code'   =>  isset($request['biometric_code']) ? $request['biometric_code'] : null,
            'order_mails'   =>  isset($request['order_mails']) ? $request['order_mails'] : '',
            'order_mails_type'   =>  isset($request['order_mails_type']) ? implode(',', $request['order_mails_type']) : '',
            'other_education'  => isset($request['other_education']) ? $request['other_education'] : null,
            'previous_exp'   =>  isset($request['previous_exp']) ? (int)$request['previous_exp'] : 0,
            'current_company_tenture'   =>  isset($request['current_company_tenture']) ? (int)$request['current_company_tenture'] : 0,
            'total_exp'   =>  isset($request['total_exp']) ? (int)$request['total_exp'] : 0,
        ]);
        if ($request->education_detail && count($request->education_detail) > 0) {
            foreach ($request->education_detail as $education_detail) {
                if ($education_detail['degree_name'] != null && $education_detail['degree_name'] != '') {
                    $new_education_detail = UserEducation::updateOrCreate(
                        [
                            'user_id' => $id,
                            'education_type_id' => $education_detail['education_type_id']
                        ],
                        [
                            'user_id' => $id,
                            'education_type_id' => $education_detail['education_type_id'],
                            'degree_name' => $education_detail['degree_name'],
                            'board_name' => $education_detail['board_name'],
                            'percentage' => $education_detail['percentage'],
                            'grade' => $education_detail['grade'],
                        ]
                    );
                    if (isset($education_detail['image']) && $education_detail['image'] != null && $education_detail['image'] != '') {
                        $new_education_detail->addMedia($education_detail['image'])->toMediaCollection('education_image');
                    }
                }
            }
        }
        $user = User::where('id', $id)->first();
        if ($request->file('image')) {
            $user->addMedia($request->file('image'))->toMediaCollection('profile_image');
        }
        if ($request->file('aadhar_card_image')) {
            $user->addMedia($request->file('aadhar_card_image'))->toMediaCollection('aadhar_image');
        }
        if ($request->file('pan_card_image')) {
            $user->addMedia($request->file('pan_card_image'))->toMediaCollection('pan_image');
        }
        $user->name = isset($request['name']) ? $request['first_name'] . ' ' . $request['last_name'] : '';
        $user->first_name = isset($request['first_name']) ? $request['first_name'] : '';
        $user->last_name = isset($request['last_name']) ? $request['last_name'] : '';
        $user->mobile = isset($request['mobile']) ? $request['mobile'] : '';
        $user->email = isset($request['email']) ? $request['email'] : '';
        $user->leave_balance = isset($request['leave_balance']) ? $request['leave_balance'] : '0.00';
        $user->grade = isset($request['grade']) ? $request['grade'] : NULL;
        $user->blood_group = isset($request['blood_group']) ? $request['blood_group'] : NULL;
        $user->personal_number = isset($request['personal_number']) ? $request['personal_number'] : NULL;
        $user->show_attandance_report = isset($request['show_attandance_report']) ? $request['show_attandance_report'] : '';
        if ($request['password'] && !empty($request['password'])) {
            $user->password = isset($request['password']) ? Hash::make($request['password']) : '';
            $user->password_string = $request['password'];
        }
        if ($request['profile_image']) {
            $user->profile_image = isset($request['profile_image']) ? $request['profile_image'] : '';
        }
        $user->location = !empty($request['location']) ? $request['location'] : '';
        $user->gender = isset($request['gender']) ? $request['gender'] : '';
        $user->reportingid = isset($request['reportingid']) ? $request['reportingid'] : null;
        $user->region_id = isset($request['region_id']) ? $request['region_id'] : null;

        $user->branch_id = (isset($request['branch_id']) && count($request['branch_id']) > 0) ? implode(',', $request['branch_id']) : NULL;
        $user->primary_branch_id = isset($request['primary_branch_id']) ? $request['primary_branch_id'] : null;
        $user->department_id = isset($request['department_id']) ? $request['department_id'] : null;
        $user->employee_codes = isset($request['employee_codes']) ? $request['employee_codes'] : null;
        $user->designation_id = isset($request['designation_id']) ? $request['designation_id'] : null;
        $user->division_id = isset($request['division_id']) ? $request['division_id'] : null;
        $user->payroll = isset($request['payroll']) ? $request['payroll'] : null;
        $user->warehouse_id = isset($request['warehouse_id']) ? $request['warehouse_id'] : null;
        $user->branch_show = isset($request['branch_show']) ? implode(',', $request['branch_show']) : null;
        $user->sales_type = isset($request['sales_type']) ? $request['sales_type'] : '';
        if ($user->save()) {
            $user->roles()->sync($request->input('roles', []));
            $permissions = $user->getPermissionsViaRoles()->pluck('name');
            $user->syncPermissions($permissions);
            if (!empty($request['cities'])) {
                foreach ($request['cities'] as $key => $city) {
                    UserCityAssign::updateOrCreate(
                        ['userid' => $id, 'city_id' => $city],
                        ['userid' => $id, 'city_id' => $city,  'reportingid' => $request['reportingid']]
                    );
                }
                UserCityAssign::whereNotIn('city_id', $request['cities'])->where('userid', $id)->delete();
            }
        }
        if ($request['password'] && !empty($request['password']) && !$user->roles()->where('id', '29')->exists()) {
            $user->tokens()->delete();
            $sessionFiles = File::files(storage_path('framework/sessions'));
            foreach ($sessionFiles as $file) {
                $sessionContent = File::get($file);
                if (str_contains($sessionContent, 'user_idsss";i:' . $user->id . ';')) {
                    echo $file->getFilename();
                    echo "<br>";
                    File::delete($file);
                }
            }
            Auth::logout();
            return redirect()->route('login')->with('status', 'Password updated successfully. Please log in with your new password.');
        } else {
            return redirect()->route('users.index');
        }
    }

    public function show($id)
    {
        //abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $user = User::find($id);
        $user->load('roles');
        return view('users.show', compact('user'));
    }

    public function destroy(User $user)
    {

        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        UserCityAssign::where('userid', $user->id)->delete();

        $user->delete();

        return response()->json(['status' => 'success', 'message' => 'User delete successfully.']);
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('user_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new UserImport, request()->file('import_file'));
        return back();
    }
    public function download(Request $request)
    {
        abort_if(Gate::denies('user_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UserExport($request), 'users.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('user_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UserTemplate, 'users.xlsx');
    }

    public function active(Request $request)
    {
        if (User::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y'])) {
            $user = User::find($request['id']);
            $user->tokens()->delete();
            $sessionFiles = File::files(storage_path('framework/sessions'));
            foreach ($sessionFiles as $file) {
                $sessionContent = File::get($file);
                if (str_contains($sessionContent, 'user_idsss";i:' . $user->id . ';')) {
                    echo $file->getFilename();
                    echo "<br>";
                    File::delete($file);
                }
            }
            $message = ($request['active'] == 'Y') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'User ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }

    public function userCity(UserCityDataTable $dataTable)
    {
        return $dataTable->render('users.usercity');
    }

    public function userCityUpload(Request $request)
    {
        abort_if(Gate::denies('user_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new UserCityImport, request()->file('import_file'));
        return back();
    }
    public function userCitydownload(Request $request)
    {
        abort_if(Gate::denies('user_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UserCityMapedExport($request), 'users.xlsx');
    }

    public function reports_sale(Request $request)
    {
        $user_ids = getUsersReportingToAuth();
        $users = User::where('active', 'Y')->whereIn('id', $user_ids)->get();
        $designations = Designation::where('active', 'Y')->get();
        $divisions = Division::where('active', 'Y')->get();
        $branchs = Branch::where('active', 'Y')->get();


        if ($request->ajax()) {
            $data = User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 29);
            })->with('reportinginfo', 'getbranch', 'getdivision', 'getdesignation', 'all_attendance_details', 'visits', 'customers');
            if ($request->user_id && $request->user_id != '' && $request->user_id != NULL) {
                $data->where('id', $request->user_id);
            } else {
                $data->whereIn('id', $user_ids);
            }
            if ($request->designation_id && $request->designation_id != '' && $request->designation_id != NULL) {
                $data->where('designation_id', $request->designation_id);
            }
            if ($request->division_id && $request->division_id != '' && $request->division_id != NULL) {
                $data->where('division_id', $request->division_id);
            }
            if ($request->branch_id && $request->branch_id != '' && $request->branch_id != NULL) {
                $data->where('branch_id', $request->branch_id);
            }
            $data = $data->latest();

            $start_date = Carbon::parse($request->start_date)->startOfDay();
            $end_date = Carbon::parse($request->end_date)->endOfDay();


            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('attendance_count', function ($query) use ($request) {
                    return count($query->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->whereBetween('punchin_date', [$request->start_date, $request->end_date]));
                })
                ->addColumn('other_attendance_count', function ($query) use ($request) {
                    return count($query->all_attendance_details->whereIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->whereBetween('punchin_date', [$request->start_date, $request->end_date]));
                })
                ->addColumn('total_attendance_count', function ($query) use ($request) {
                    return count($query->all_attendance_details->whereBetween('punchin_date', [$request->start_date, $request->end_date]));
                })

                ->addColumn('dis_visit_total', function ($query) use ($request) {
                    return count($query->visits->where('customers.customertype', '1')->whereBetween('checkin_date', [$request->start_date, $request->end_date]));
                })
                ->addColumn('dis_visit_unique', function ($query) use ($request) {
                    return count($query->visits->where('customers.customertype', '1')->whereBetween('checkin_date', [$request->start_date, $request->end_date])->groupBy('customers.id'));
                })

                ->addColumn('dil_visit_total', function ($query) use ($request) {
                    return count($query->visits->where('customers.customertype', '3')->whereBetween('checkin_date', [$request->start_date, $request->end_date]));
                })
                ->addColumn('dil_visit_unique', function ($query) use ($request) {
                    return count($query->visits->where('customers.customertype', '3')->whereBetween('checkin_date', [$request->start_date, $request->end_date])->groupBy('customers.id'));
                })

                ->addColumn('ret_visit_total', function ($query) use ($request) {
                    return count($query->visits->where('customers.customertype', '2')->whereBetween('checkin_date', [$request->start_date, $request->end_date]));
                })
                ->addColumn('ret_visit_unique', function ($query) use ($request) {
                    return count($query->visits->where('customers.customertype', '2')->whereBetween('checkin_date', [$request->start_date, $request->end_date])->groupBy('customers.id'));
                })

                ->addColumn('serv_visit_total', function ($query) use ($request) {
                    return count($query->visits->where('customers.customertype', '4')->whereBetween('checkin_date', [$request->start_date, $request->end_date]));
                })
                ->addColumn('serv_visit_unique', function ($query) use ($request) {
                    return count($query->visits->where('customers.customertype', '4')->whereBetween('checkin_date', [$request->start_date, $request->end_date])->groupBy('customers.id'));
                })

                ->addColumn('inf_visit_total', function ($query) use ($request) {
                    return count($query->visits->where('customers.customertype', '5')->whereBetween('checkin_date', [$request->start_date, $request->end_date]));
                })
                ->addColumn('inf_visit_unique', function ($query) use ($request) {
                    return count($query->visits->where('customers.customertype', '5')->whereBetween('checkin_date', [$request->start_date, $request->end_date])->groupBy('customers.id'));
                })

                ->addColumn('tot_visit_total', function ($query) use ($request) {
                    return count($query->visits->whereBetween('checkin_date', [$request->start_date, $request->end_date]));
                })
                ->addColumn('tot_visit_unique', function ($query) use ($request) {
                    return count($query->visits->whereBetween('checkin_date', [$request->start_date, $request->end_date])->groupBy('customers.id'));
                })

                ->addColumn('dis_registration', function ($query) use ($start_date, $end_date) {
                    return count($query->customers->where('customertype', '1')->whereBetween('created_at', [$start_date, $end_date]));
                })
                ->addColumn('del_registration', function ($query) use ($start_date, $end_date) {
                    return count($query->customers->where('customertype', '3')->whereBetween('created_at', [$start_date, $end_date]));
                })
                ->addColumn('ret_registration', function ($query) use ($start_date, $end_date) {
                    return count($query->customers->where('customertype', '2')->whereBetween('created_at', [$start_date, $end_date]));
                })
                ->addColumn('serv_registration', function ($query) use ($start_date, $end_date) {
                    return count($query->customers->where('customertype', '4')->whereBetween('created_at', [$start_date, $end_date]));
                })
                ->addColumn('inf_registration', function ($query) use ($start_date, $end_date) {
                    return count($query->customers->where('customertype', '5')->whereBetween('created_at', [$start_date, $end_date]));
                })
                ->addColumn('tot_registration', function ($query) use ($start_date, $end_date) {
                    return count($query->customers->whereBetween('created_at', [$start_date, $end_date]));
                })
                ->rawColumns(['attendance_count', 'other_attendance_count', 'total_attendance_count', 'dis_visit_total', 'dis_visit_unique'])
                ->make(true);
        }

        return view('reports.reports_sale', compact('users', 'designations', 'divisions', 'branchs'));
    }

    public function user_sales_report_download(Request $request)
    {
        abort_if(Gate::denies('user_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new UserSalesReportExport($request), 'users_sales_report.xlsx');
    }

    public function fos_rating(Request $request)
    {
        $user_ids = getUsersReportingToAuth();
        $users = User::where('active', 'Y')->where('sales_type', 'Secondary')->whereIn('id', $user_ids)->get();
        $designations = Designation::where('active', 'Y')->get();
        $divisions = Division::where('active', 'Y')->get();
        $branchs = Branch::where('active', 'Y')->get();

        $currentMonth = Carbon::now()->month; // Get the current month number
        $months = [];

        for ($i = 1; $i < $currentMonth; $i++) {
            $monthNumber = str_pad($i, 2, '0', STR_PAD_LEFT); // Format month number with leading zero
            $monthName = Carbon::create()->month($i)->format('M'); // Get the abbreviated month name
            $months[$monthNumber] = $monthName;
        }

        if ($request->month && !empty($request->month)) {
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;
            $month = intval($request->month);
            $firstDate = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth()->toDateString();
            $lastDate = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth()->toDateString();
            $yesterday = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth()->subDay()->toDateString();
        } else {
            $firstDate = Carbon::now()->startOfMonth()->toDateString();
            $lastDate = Carbon::now()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();
        }

        if ($request->ajax()) {
            $query = User::with(['all_attendance_details', 'visits', 'customers', 'userinfo', 'cities'])
                ->where('sales_type', 'Secondary')
                ->where('active', 'Y');

            if ($request->user_id) {
                $query->where('id', $request->user_id);
            } else {
                $query->whereIn('id', $user_ids);
            }

            if ($request->designation_id) {
                $query->where('designation_id', $request->designation_id);
            }

            if ($request->division_id) {
                $query->where('division_id', $request->division_id);
            }

            if ($request->branch_id) {
                $query->where('branch_id', $request->branch_id);
            }

            if ($request->start_date && !empty($request->start_date) && $request->end_date && !empty($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $query->whereHas('userinfo', function ($query) use ($end_date, $start_date) {
                    $query->where('date_of_joining', '>=', $start_date)
                        ->where('date_of_joining', '<=', $end_date);
                });
            }


            $data = $query->get()->map(function ($user) use ($lastDate) {
                $working_days = $user->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
                $order_value = Order::where('created_by', $user->id)->where('order_date', '<=', $lastDate)->sum('sub_total');
                $sale_index = $working_days ? (($order_value / 100000) / $working_days) * 100 : 0;

                $registered_retailers = Customers::where(['customertype' => '2', 'created_by' => $user->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->count();
                $registration_index = $working_days ? (($registered_retailers / $working_days) / 5) * 100 : 0;

                $total_visit = $user->visits->where('checkin_date', '<=', $lastDate)->count();
                $visit_index = $working_days ? (($total_visit / $working_days) / 10) * 100 : 0;

                $sharthi_customer = TransactionHistory::where('created_at', '<=', $lastDate . ' 23:59:59')->groupBy('customer_id')->pluck('customer_id')->toArray();
                $activation_retailers = Customers::where(['customertype' => '2', 'created_by' => $user->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->whereIn('id', $sharthi_customer)->count();
                $activation_index = $working_days ? (($activation_retailers / $working_days) / 5) * 100 : 0;

                $user->performance_rating = ($sale_index * 0.5) + ($registration_index * 0.1) + ($visit_index * 0.1) + ($activation_index * 0.3);
                return $user;
            })->sortByDesc('performance_rating');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('cities', function ($user) {
                    $district_ids = City::whereIn('id', $user->cities->pluck('city_id'))->pluck('district_id');
                    return implode(", ", District::whereIn('id', $district_ids)->pluck('district_name')->toArray());
                })
                ->addColumn('userinfo.date_of_joining', function ($user) {
                    return date('d M y', strtotime($user->userinfo->date_of_joining));
                })
                ->addColumn('yesterday_productivity_visit', function ($user) use ($yesterday) {
                    $retailers = Customers::where('customertype', '2')->pluck('id');
                    $order_counts = Order::where('order_date', $yesterday)->where('created_by', $user->id)->whereIn('buyer_id', $retailers)->count();
                    $yesterday_visit = $user->visits->where('checkin_date', $yesterday)->count();
                    return $yesterday_visit ? number_format(($order_counts / $yesterday_visit) * 100, 2) . "%" : "0.00%";
                })
                ->addColumn('order_value_current_month', function ($user) use ($lastDate, $firstDate) {
                    $order_value = Order::where('order_date', '>=', $firstDate)->where('order_date', '<=', $lastDate)->where('created_by', $user->id)->sum('sub_total');
                    return $order_value ? number_format(($order_value - ($order_value * 0.35)) / 100000, 2) : "0.00";
                })
                ->addColumn('total_order_value', function ($user) use ($lastDate) {
                    $order_value = Order::where('order_date', '<=', $lastDate)->where('created_by', $user->id)->sum('sub_total');
                    return $order_value ? number_format(($order_value - ($order_value * 0.35)) / 100000, 2) : "0.00";
                })
                ->addColumn('sale_index', function ($user) use ($lastDate) {
                    $working_days = $user->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
                    $order_value = Order::where('order_date', '<=', $lastDate)->where('created_by', $user->id)->sum('sub_total');
                    $sale_index = $working_days ? (($order_value / 100000) / $working_days) * 100 : 0;
                    return number_format($sale_index, 2) . "%";
                })
                ->addColumn('registration_index', function ($user) use ($lastDate) {
                    $working_days = $user->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
                    $registered_retailers = Customers::where(['customertype' => '2', 'created_by' => $user->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->count();
                    $registration_index = $working_days ? (($registered_retailers / $working_days) / 5) * 100 : 0;
                    return number_format($registration_index, 2) . "%";
                })
                ->addColumn('visit_index', function ($user) use ($lastDate) {
                    $working_days = $user->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
                    $total_visit = $user->visits->where('checkin_date', '<=', $lastDate)->count();
                    $visit_index = $working_days ? (($total_visit / $working_days) / 10) * 100 : 0;
                    return number_format($visit_index, 2) . "%";
                })
                ->addColumn('activation_index', function ($user) use ($lastDate) {
                    $working_days = $user->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
                    $sharthi_customer = TransactionHistory::where('created_at', '<=', $lastDate . ' 23:59:59')->groupBy('customer_id')->pluck('customer_id')->toArray();
                    $activation_retailers = Customers::where(['customertype' => '2', 'created_by' => $user->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->whereIn('id', $sharthi_customer)->count();
                    $activation_index = $working_days ? (($activation_retailers / $working_days) / 5) * 100 : 0;
                    return number_format($activation_index, 2) . "%";
                })
                ->addColumn('performance_rating', function ($user) {
                    $performance_rating = number_format($user->performance_rating, 2);
                    $badge_class = $performance_rating < 25 ? 'danger' : ($performance_rating < 30 ? 'warning' : 'success');
                    return "<span class='badge badge-{$badge_class} p-2' style='font-size: 14px;font-weight: 900;text-shadow: 1px 2px 3px #000;'>{$performance_rating}%</span>";
                })
                ->rawColumns(['cities', 'userinfo.date_of_joining', 'yesterday_productivity_visit', 'order_value_current_month', 'total_order_value', 'sale_index', 'registration_index', 'visit_index', 'activation_index', 'performance_rating'])
                ->make(true);
        }

        return view('reports.fos_rating', compact('months', 'users', 'designations', 'divisions', 'branchs'));
    }
    public function asm_rating(Request $request)
    {
        $user_ids = getUsersReportingToAuth();
        $users = User::where('active', 'Y')->where('sales_type', 'Primary')->whereIn('id', $user_ids)->get();
        $designations = Designation::where('active', 'Y')->get();
        $divisions = Division::where('active', 'Y')->get();
        $branchs = Branch::where('active', 'Y')->get();

        $currentMonth = Carbon::now()->month; // Get the current month number

        $FinancialYears = getFinancialYears();

        if ($request->ajax()) {
            $query = User::where('sales_type', 'Primary')
                ->where('active', 'Y');

            if ($request->user_id) {
                $query->where('id', $request->user_id);
            } else {
                $query->whereIn('id', $user_ids);
            }

            if ($request->designation_id) {
                $query->where('designation_id', $request->designation_id);
            }

            if ($request->division_id) {
                $query->where('division_id', $request->division_id);
            }

            if ($request->branch_id) {
                $query->where('branch_id', $request->branch_id);
            }

            if ($request->start_date && !empty($request->start_date) && $request->end_date && !empty($request->end_date)) {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $query->whereHas('userinfo', function ($query) use ($end_date, $start_date) {
                    $query->where('date_of_joining', '>=', $start_date)
                        ->where('date_of_joining', '<=', $end_date);
                });
            }

            $year = date('Y');
            $month = date('m');

            if ($month >= 4) {
                $financial_year = $year . '-' . ($year + 1);
            } else {
                $financial_year = ($year - 1) . '-' . $year;
            }

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('increment', function ($query) use ($financial_year) {
                    $pmsData = UserPmsRemark::where(['user_id' => $query['id'], 'fyear' => $financial_year])->first();
                    return $pmsData ? $pmsData->recommended_increment : '';
                })
                ->addColumn('remark', function ($query) use ($financial_year) {
                    $pmsData = UserPmsRemark::where(['user_id' => $query['id'], 'fyear' => $financial_year])->first();
                    return $pmsData ? $pmsData->remark : '';
                })

                ->rawColumns(['increment', 'remark'])
                ->make(true);
        }

        $roles = Role::whereNotIn('name', ['superadmin', 'Admin', 'Sub_Admin', 'HR_Admin', 'HO_Account', 'Sub_Support', 'Accounts Order', 'Service Admin', 'All Customers', 'Sub billing', 'Sales Admin', 'Marketing_Admin', 'MIS_ADMIN', 'Marketing Team', 'Data_Crm'])->select('name', 'id')->get();

        return view('reports.asm_rating', compact('users', 'designations', 'divisions', 'branchs', 'FinancialYears', 'roles'));
    }
    public function ch_rating(Request $request)
    {
        // $user_ids = getUsersReportingToAuth();
        $roles = ['PUMPBM', 'PUMPCH', 'FAN&A/BM/MM', 'FAN/CH/GM/SH'];
        $users = User::where('active', 'Y')->whereIn('designation_id', ['5', '6', '7'])
            ->get();
        // $designations = Designation::where('active', 'Y')->get();
        // $divisions = Division::where('active', 'Y')->get();
        $branchs = Branch::where('active', 'Y')->get();

        $currentMonth = Carbon::now()->month; // Get the current month number

        $FinancialYears = getFinancialYears();

        // if ($request->ajax()) {
        //     $query = User::with(['all_attendance_details', 'visits', 'customers', 'userinfo', 'cities'])
        //         ->where('sales_type', 'Secondary')
        //         ->where('active', 'Y');

        //     if ($request->user_id) {
        //         $query->where('id', $request->user_id);
        //     } else {
        //         $query->whereIn('id', $user_ids);
        //     }

        //     if ($request->designation_id) {
        //         $query->where('designation_id', $request->designation_id);
        //     }

        //     if ($request->division_id) {
        //         $query->where('division_id', $request->division_id);
        //     }

        //     if ($request->branch_id) {
        //         $query->where('branch_id', $request->branch_id);
        //     }

        //     if ($request->start_date && !empty($request->start_date) && $request->end_date && !empty($request->end_date)) {
        //         $start_date = $request->start_date;
        //         $end_date = $request->end_date;
        //         $query->whereHas('userinfo', function ($query) use ($end_date, $start_date) {
        //             $query->where('date_of_joining', '>=', $start_date)
        //                 ->where('date_of_joining', '<=', $end_date);
        //         });
        //     }


        //     $data = $query->get()->map(function ($user) use ($lastDate) {
        //         $working_days = $user->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
        //         $order_value = Order::where('created_by', $user->id)->where('order_date', '<=', $lastDate)->sum('sub_total');
        //         $sale_index = $working_days ? (($order_value / 100000) / $working_days) * 100 : 0;

        //         $registered_retailers = Customers::where(['customertype' => '2', 'created_by' => $user->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->count();
        //         $registration_index = $working_days ? (($registered_retailers / $working_days) / 5) * 100 : 0;

        //         $total_visit = $user->visits->where('checkin_date', '<=', $lastDate)->count();
        //         $visit_index = $working_days ? (($total_visit / $working_days) / 10) * 100 : 0;

        //         $sharthi_customer = TransactionHistory::where('created_at', '<=', $lastDate . ' 23:59:59')->groupBy('customer_id')->pluck('customer_id')->toArray();
        //         $activation_retailers = Customers::where(['customertype' => '2', 'created_by' => $user->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->whereIn('id', $sharthi_customer)->count();
        //         $activation_index = $working_days ? (($activation_retailers / $working_days) / 5) * 100 : 0;

        //         $user->performance_rating = ($sale_index * 0.5) + ($registration_index * 0.1) + ($visit_index * 0.1) + ($activation_index * 0.3);
        //         return $user;
        //     })->sortByDesc('performance_rating');

        //     return Datatables::of($data)
        //         ->addIndexColumn()
        //         ->addColumn('cities', function ($user) {
        //             $district_ids = City::whereIn('id', $user->cities->pluck('city_id'))->pluck('district_id');
        //             return implode(", ", District::whereIn('id', $district_ids)->pluck('district_name')->toArray());
        //         })
        //         ->addColumn('userinfo.date_of_joining', function ($user) {
        //             return date('d M y', strtotime($user->userinfo->date_of_joining));
        //         })
        //         ->addColumn('yesterday_productivity_visit', function ($user) use ($yesterday) {
        //             $retailers = Customers::where('customertype', '2')->pluck('id');
        //             $order_counts = Order::where('order_date', $yesterday)->where('created_by', $user->id)->whereIn('buyer_id', $retailers)->count();
        //             $yesterday_visit = $user->visits->where('checkin_date', $yesterday)->count();
        //             return $yesterday_visit ? number_format(($order_counts / $yesterday_visit) * 100, 2) . "%" : "0.00%";
        //         })
        //         ->addColumn('order_value_current_month', function ($user) use ($lastDate, $firstDate) {
        //             $order_value = Order::where('order_date', '>=', $firstDate)->where('order_date', '<=', $lastDate)->where('created_by', $user->id)->sum('sub_total');
        //             return $order_value ? number_format(($order_value - ($order_value * 0.35)) / 100000, 2) : "0.00";
        //         })
        //         ->addColumn('total_order_value', function ($user) use ($lastDate) {
        //             $order_value = Order::where('order_date', '<=', $lastDate)->where('created_by', $user->id)->sum('sub_total');
        //             return $order_value ? number_format(($order_value - ($order_value * 0.35)) / 100000, 2) : "0.00";
        //         })
        //         ->addColumn('sale_index', function ($user) use ($lastDate) {
        //             $working_days = $user->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
        //             $order_value = Order::where('order_date', '<=', $lastDate)->where('created_by', $user->id)->sum('sub_total');
        //             $sale_index = $working_days ? (($order_value / 100000) / $working_days) * 100 : 0;
        //             return number_format($sale_index, 2) . "%";
        //         })
        //         ->addColumn('registration_index', function ($user) use ($lastDate) {
        //             $working_days = $user->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
        //             $registered_retailers = Customers::where(['customertype' => '2', 'created_by' => $user->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->count();
        //             $registration_index = $working_days ? (($registered_retailers / $working_days) / 5) * 100 : 0;
        //             return number_format($registration_index, 2) . "%";
        //         })
        //         ->addColumn('visit_index', function ($user) use ($lastDate) {
        //             $working_days = $user->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
        //             $total_visit = $user->visits->where('checkin_date', '<=', $lastDate)->count();
        //             $visit_index = $working_days ? (($total_visit / $working_days) / 10) * 100 : 0;
        //             return number_format($visit_index, 2) . "%";
        //         })
        //         ->addColumn('activation_index', function ($user) use ($lastDate) {
        //             $working_days = $user->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
        //             $sharthi_customer = TransactionHistory::where('created_at', '<=', $lastDate . ' 23:59:59')->groupBy('customer_id')->pluck('customer_id')->toArray();
        //             $activation_retailers = Customers::where(['customertype' => '2', 'created_by' => $user->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->whereIn('id', $sharthi_customer)->count();
        //             $activation_index = $working_days ? (($activation_retailers / $working_days) / 5) * 100 : 0;
        //             return number_format($activation_index, 2) . "%";
        //         })
        //         ->addColumn('performance_rating', function ($user) {
        //             $performance_rating = number_format($user->performance_rating, 2);
        //             $badge_class = $performance_rating < 25 ? 'danger' : ($performance_rating < 30 ? 'warning' : 'success');
        //             return "<span class='badge badge-{$badge_class} p-2' style='font-size: 14px;font-weight: 900;text-shadow: 1px 2px 3px #000;'>{$performance_rating}%</span>";
        //         })
        //         ->rawColumns(['cities', 'userinfo.date_of_joining', 'yesterday_productivity_visit', 'order_value_current_month', 'total_order_value', 'sale_index', 'registration_index', 'visit_index', 'activation_index', 'performance_rating'])
        //         ->make(true);
        // }

        return view('reports.ch_rating', compact('users', 'branchs', 'FinancialYears'));
    }



    public function fos_rating_report_download(Request $request)
    {
        abort_if(Gate::denies('user_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new FOSRatingReportExport($request), 'FOS_Rating_Report.xlsx');
    }

    public function asm_rating_report_download(Request $request)
    {

        abort_if(Gate::denies('user_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if (empty(array_diff($request->role_id, [2, 32]))) {
            if ($request->download == 'simple') {
                return Excel::download(new ASMRatingReportExport($request), 'Rating Report.xlsx');
            } else {
                return Excel::download(new ASMRatingDetailReportExport($request), 'Detail Rating Report(PMS).xlsx');
            }
        } else if (empty(array_diff($request->role_id, [3, 6, 13]))) {
            if ($request->download == 'simple') {
                return Excel::download(new CHRatingReportExport($request), 'Rating Report.xlsx');
            } else {
                return Excel::download(new CHRatingDetailReportExport($request), 'Detail Rating Report(PMS).xlsx');
            }
        } else {
            return redirect()->back()->with('info', 'Working on this role type user rating report !!');
        }
    }

    public function ch_rating_report_download(Request $request)
    {
        abort_if(Gate::denies('user_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CHRatingReportExport($request), 'CH_BM_Rating_Report.xlsx');
    }

    public function CustomerUserView(Request $request)
    {
        if (isset($request->id) && !empty($request->id)) {
            $id = decrypt($request->id);
            $this->user = User::with('userinfo')->where('id', $id)->first();
        }
        $roles = Role::where('name', '!=', 'super-admin')->pluck('name', 'id');
        // $customers = Customers::where('active', 'Y')->get();
        return view('users.customer_user_create', compact('roles'))->with('user', $this->user);
    }


    public function pms_form(Request $request)
    {
        $update = UserPmsRemark::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'fyear' => $request->fyear,
            ],
            [
                'recommended_increment' => $request->recommended_increment,
                'recommended_designation' => $request->designation_id,
                'remark' => $request->remarks,
            ]
        );

        return redirect('reports/asm_rating')->with('info', 'Successfully Updated');
    }
}
