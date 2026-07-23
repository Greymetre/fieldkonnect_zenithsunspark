<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\DB;
use Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\UserDetails;
use App\Models\Address;
use App\Models\Pincode;
use App\Models\UserEducation;
use Spatie\Permission\Models\Role;

class UserImport implements ToCollection, WithValidation, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new User([
            //
        ]);
    }

    public function collection(Collection $rows)
    {
        $userdetails = collect([]);
        $addressdetails = collect([]);
        foreach ($rows as $k=>$row) {
            if(isset($row['last_year_increment_percent']) && !is_numeric($row['last_year_increment_percent'])){
                $row['last_year_increment_percent'] = (int) str_replace('%', '', $row['last_year_increment_percent']);
            }
   
            if (!empty($row['id'])) {
                if (is_numeric($row['spouse_date_of_birth'])) {
                    $excelDate = $row['spouse_date_of_birth'] - 25569; // Adjust for Excel's epoch
                    $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                    $row['spouse_date_of_birth'] = !empty($row['spouse_date_of_birth']) ? Carbon::createFromTimestamp($unixTimestamp) : '';
                }
                if (is_numeric($row['children_1_dob'])) {
                    $excelDate = $row['children_1_dob'] - 25569; // Adjust for Excel's epoch
                    $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                    $row['children_1_dob'] = !empty($row['children_1_dob']) ? Carbon::createFromTimestamp($unixTimestamp) : '';
                }
                if (is_numeric($row['children_2_dob'])) {
                    $excelDate = $row['children_2_dob'] - 25569; // Adjust for Excel's epoch
                    $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                    $row['children_2_dob'] = !empty($row['children_2_dob']) ? Carbon::createFromTimestamp($unixTimestamp) : '';
                }
                if (is_numeric($row['children_3_dob'])) {
                    $excelDate = $row['children_3_dob'] - 25569; // Adjust for Excel's epoch
                    $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                    $row['children_3_dob'] = !empty($row['children_3_dob']) ? Carbon::createFromTimestamp($unixTimestamp) : '';
                }
                if (is_numeric($row['children_4_dob'])) {
                    $excelDate = $row['children_4_dob'] - 25569; // Adjust for Excel's epoch
                    $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                    $row['children_4_dob'] = !empty($row['children_4_dob']) ? Carbon::createFromTimestamp($unixTimestamp) : '';
                }
                if (is_numeric($row['children_5_dob'])) {
                    $excelDate = $row['children_5_dob'] - 25569; // Adjust for Excel's epoch
                    $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                    $row['children_5_dob'] = !empty($row['children_5_dob']) ? Carbon::createFromTimestamp($unixTimestamp) : '';
                }
                if (is_numeric($row['date_of_joining'])) {
                    $excelDate = $row['date_of_joining'] - 25569; // Adjust for Excel's epoch
                    $unixTimestamp = strtotime('+' . $excelDate . ' days', strtotime('1970-01-01'));
                    $row['date_of_joining'] = !empty($row['date_of_joining']) ? Carbon::createFromTimestamp($unixTimestamp) : '';
                }
                $name = trim($row['user_name']);
                $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
                $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));

                User::where('id', '=', $row['id'])->update([
                    'name' => !empty($name) ? ucfirst(strtolower($name)) : '',
                    'first_name' => !empty($first_name) ? ucfirst(strtolower($first_name)) : '',
                    'last_name' => !empty($last_name) ? ucfirst(strtolower($last_name)) : '',
                    //'mobile' => $row['mobile'],
                    // 'mobile' => !empty($row['mobile']) ? $row['mobile'] : null,
                    'email' => !empty($row['email']) ? $row['email'] : null,
                    'leave_balance' => !empty($row['leave_balance']) ? $row['leave_balance'] : 0.00,
                    'grade' => !empty($row['grade']) ? $row['grade'] : NULL,
                    'blood_group' => !empty($row['blood_group']) ? $row['blood_group'] : NULL,
                    'personal_number' => !empty($row['personal_number']) ? $row['personal_number'] : NULL,
                    //'password' => !empty($row['password'])? Hash::make($row['password']) :'',
                    'gender' => !empty($row['gender']) ? $row['gender'] : '',
                    //'profile_image' => !empty($row['profile_image'])? $row['profile_image']:'',
                    'user_code' => !empty($row['user_code']) ? $row['user_code'] : '',
                    'location' => !empty($row['location']) ? $row['location'] : '',
                    'employee_codes' => !empty($row['employees_code']) ? $row['employees_code'] : '',
                    'branch_id' => !empty($row['branch_id']) ? $row['branch_id'] : '',
                    'primary_branch_id' => !empty($row['primary_branch_id']) ? $row['primary_branch_id'] : '',
                    'designation_id' => !empty($row['designation_id']) ? $row['designation_id'] : '',
                    'division_id' => !empty($row['division_id']) ? $row['division_id'] : '',
                    'department_id' => !empty($row['department_id']) ? $row['department_id'] : '',
                    'payroll' => !empty($row['payroll']) ? $row['payroll'] : '',
                    'warehouse_id' => !empty($row['warehouse_id']) ? $row['warehouse_id'] : NULL,
                    'reportingid' => !empty($row['reporting_id']) ? $row['reporting_id'] : '',
                    'sales_type' => !empty($row['sales_type']) ? $row['sales_type'] : '',
                    'show_attandance_report' => !empty($row['attandance_summary_report']) ? (int)$row['attandance_summary_report'] : '',
                    //'created_at' => getcurentDateTime(),
                    //'updated_at' => getcurentDateTime()
                ]);
                $user = User::find($row['id']);
                $user->roles()->sync(explode(',', $row['role_ids']));


                UserDetails::where('user_id', '=', $row['id'])->update([
                    'date_of_joining' => (!empty($row['date_of_joining']) && $row['date_of_joining'] != null) ? date('Y-m-d', strtotime($row['date_of_joining'])) : null,
                    'date_of_birth' => (!empty($row['date_of_birth']) && $row['date_of_birth'] != null) ? date('Y-m-d', strtotime($row['date_of_birth'])) : null,
                    'last_year_increments' => !empty($row['last_year_increments']) ? $row['last_year_increments'] : null,
                    'last_year_increment_percent' => !empty($row['last_year_increment_percent']) ? $row['last_year_increment_percent'] . '%' : null,
                    'last_promotion' => !empty($row['last_promotion']) ? $row['last_promotion'] : null,
                    'ctc_annual' => !empty($row['ctc_annual']) ? $row['ctc_annual'] : 0.00,
                    'gross_salary_monthly' => !empty($row['gross_salary_monthly']) ? $row['gross_salary_monthly'] : 0.00,
                    'salary' => !empty($row['ctc_per_month']) ? $row['ctc_per_month'] : 0.00,
                    'last_year_increment_value' => !empty($row['last_year_increments_value']) ? (int)$row['last_year_increments_value'] : 0.00,
                    'marital_status' => !empty($row['marital_status']) ? $row['marital_status'] : 0.00,
                    'father_name' => !empty($row['father_name']) ? $row['father_name'] : null,
                    'father_date_of_birth' => !empty($row['father_date_of_birth']) ? date('Y-m-d', strtotime($row['father_date_of_birth'])) : null,
                    'mother_name' => !empty($row['mother_name']) ? $row['mother_name'] : null,
                    'mother_date_of_birth' => !empty($row['mother_dob']) ? date('Y-m-d', strtotime($row['mother_dob'])) : null,
                    'marriage_anniversary' => !empty($row['marriage_anniversary']) ? date('Y-m-d', strtotime($row['marriage_anniversary'])) : null,
                    'spouse_name' => !empty($row['spouse_name']) ? $row['spouse_name'] : null,
                    'spouse_date_of_birth' => !empty($row['spouse_date_of_birth']) ? date('Y-m-d', strtotime($row['spouse_date_of_birth'])) : null,
                    'children_one' => !empty($row['children_1']) ? $row['children_1'] : null,
                    'children_one_date_of_birth' => !empty($row['children_1_dob']) ? date('Y-m-d', strtotime($row['children_1_dob'])) : null,
                    'children_two' => !empty($row['children_2']) ? $row['children_2'] : null,
                    'children_two_date_of_birth' => !empty($row['children_2_dob']) ? date('Y-m-d', strtotime($row['children_2_dob'])) : null,
                    'children_three' => !empty($row['children_3']) ? $row['children_3'] : null,
                    'children_three_date_of_birth' => !empty($row['children_3_dob']) ? date('Y-m-d', strtotime($row['children_3_dob'])) : null,
                    'children_four' => !empty($row['children_4']) ? $row['children_4'] : null,
                    'children_four_date_of_birth' => !empty($row['children_4_dob']) ? date('Y-m-d', strtotime($row['children_4_dob'])) : null,
                    'children_five' => !empty($row['children_5']) ? $row['children_5'] : null,
                    'children_five_date_of_birth' => !empty($row['children_5_dob']) ? date('Y-m-d', strtotime($row['children_5_dob'])) : null,
                    'pan_number' => !empty($row['pan_number']) ? $row['pan_number'] : null,
                    'aadhar_number' => !empty($row['adhar_number']) ? $row['adhar_number'] : null,
                    'emergency_number' => !empty($row['emergency_number']) ? $row['emergency_number'] : null,
                    'current_address' => !empty($row['current_address']) ? $row['current_address'] : null,
                    'permanent_address' => !empty($row['permanent_address']) ? $row['permanent_address'] : null,
                    'biometric_code' => !empty($row['biometric_code']) ? $row['biometric_code'] : null,
                    'account_number' => !empty($row['account_number']) ? $row['account_number'] : null,
                    'bank_name' => !empty($row['bank_name']) ? $row['bank_name'] : null,
                    'ifsc_code' => !empty($row['ifsc_code']) ? $row['ifsc_code'] : null,
                    'pf_number' => !empty($row['pf_number']) ? $row['pf_number'] : null,
                    'un_number' => !empty($row['un_number']) ? $row['un_number'] : null,
                    'esi_number' => !empty($row['esi_number']) ? $row['esi_number'] : null,
                    'probation_period' => !empty($row['probation_period']) ? date('Y-m-d', strtotime($row['probation_period'])) : null,
                    'date_of_confirmation' => !empty($row['date_of_confirmation']) ? date('Y-m-d', strtotime($row['date_of_confirmation'])) : null,
                    'notice_period' => !empty($row['notice_period']) ? $row['notice_period'] : null,
                    'date_of_leaving' => !empty($row['date_of_leaving']) ? date('Y-m-d', strtotime($row['date_of_leaving'])) : null,
                    'current_company_tenture' => !empty($row['current_company_tenure']) ? $row['current_company_tenure'] : 0,
                    'previous_exp' => !empty($row['previous_exp']) ? $row['previous_exp'] : null,
                    'total_exp' => !empty($row['total_exp']) ? $row['total_exp'] : 0,
                    'order_mails'   =>  isset($row['order_mails']) ? $row['order_mails'] : '',
                    'order_mails_type'   =>  isset($row['order_mail_type_id']) ? $row['order_mail_type_id'] : '',
                    'created_at' => getcurentDateTime(),
                    'updated_at' => getcurentDateTime()
                ]);
                if (!empty($row['higher_secondary']) && $row['higher_secondary'] != null && $row['higher_secondary'] != '') {
                    UserEducation::updateOrCreate(
                        [
                            'user_id' => $row['id'], 'education_type_id' => '1'
                        ],
                        [
                            'user_id' => $row['id'],
                            'education_type_id' => '1',
                            'degree_name' => $row['higher_secondary'],
                        ]
                    );
                }
                if (!empty($row['high_school']) && $row['high_school'] != null && $row['high_school'] != '') {
                    UserEducation::updateOrCreate(
                        [
                            'user_id' => $row['id'], 'education_type_id' => '0'
                        ],
                        [
                            'user_id' => $row['id'],
                            'education_type_id' => '0',
                            'degree_name' => $row['high_school'],
                        ]
                    );
                }
                if (!empty($row['graducation']) && $row['graducation'] != null && $row['graducation'] != '') {
                    UserEducation::updateOrCreate(
                        [
                            'user_id' => $row['id'], 'education_type_id' => '2'
                        ],
                        [
                            'user_id' => $row['id'],
                            'education_type_id' => '2',
                            'degree_name' => $row['graducation'],
                        ]
                    );
                }
                if (!empty($row['post_graducation']) && $row['post_graducation'] != null && $row['post_graducation'] != '') {
                    UserEducation::updateOrCreate(
                        [
                            'user_id' => $row['id'], 'education_type_id' => '3'
                        ],
                        [
                            'user_id' => $row['id'],
                            'education_type_id' => '3',
                            'degree_name' => $row['post_graducation'],
                        ]
                    );
                }
                if (!empty($row['other']) && $row['other'] != null && $row['other'] != '') {
                    UserEducation::updateOrCreate(
                        [
                            'user_id' => $row['id'], 'education_type_id' => '4'
                        ],
                        [
                            'user_id' => $row['id'],
                            'education_type_id' => '4',
                            'degree_name' => $row['other'],
                        ]
                    );
                }
            } else {

                $name = trim($row['user_name']);
                $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
                $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));
                if ($user = User::create([
                    'active' => 'Y',
                    'name' => !empty($name) ? ucfirst(strtolower($name)) : '',
                    'first_name' => !empty($first_name) ? ucfirst(strtolower($first_name)) : '',
                    'last_name' => !empty($last_name) ? ucfirst(strtolower($last_name)) : '',
                    'mobile' => $row['mobile'],
                    'email' => !empty($row['email']) ? $row['email'] : null,
                    'leave_balance' => !empty($row['leave_balance']) ? $row['leave_balance'] : 0.00,
                    'grade' => !empty($row['grade']) ? $row['grade'] : NULL,
                    'blood_group' => !empty($row['blood_group']) ? $row['blood_group'] : NULL,
                    'personal_number' => !empty($row['personal_number']) ? $row['personal_number'] : NULL,
                    'password' => !empty($row['password']) ? Hash::make($row['password']) : '',
                    'gender' => !empty($row['gender']) ? $row['gender'] : '',
                    'profile_image' => !empty($row['profile_image']) ? $row['profile_image'] : '',
                    'user_code' => !empty($row['user_code']) ? $row['user_code'] : '',
                    'location' => !empty($row['location']) ? $row['location'] : '',
                    'employee_codes' => !empty($row['employees_code']) ? $row['employees_code'] : '',
                    'branch_id' => !empty($row['branch_id']) ? $row['branch_id'] : '',
                    'primary_branch_id' => !empty($row['primary_branch_id']) ? $row['primary_branch_id'] : '',
                    'designation_id' => !empty($row['designation_id']) ? $row['designation_id'] : '',
                    'division_id' => !empty($row['division_id']) ? $row['division_id'] : '',
                    'warehouse_id' => !empty($row['warehouse_id']) ? $row['warehouse_id'] : NULL,
                    'department_id' => !empty($row['department_id']) ? $row['department_id'] : '',
                    'created_at' => getcurentDateTime(),
                    'updated_at' => getcurentDateTime()
                ])) {
                    $roles = Role::where('name', '=', $row['role'])->pluck('id')->toArray();
                    $user->roles()->sync($roles);
                    $permissions = $user->getPermissionsViaRoles()->pluck('name');
                    $user->givePermissionTo($permissions);
                    $userdetails->push([
                        'user_id' => $user['id'],
                        'date_of_joining' => (!empty($row['date_of_joining']) && $row['date_of_joining'] != null) ? date('Y-m-d', strtotime($row['date_of_joining'])) : null,
                        'date_of_birth' => (!empty($row['date_of_birth']) && $row['date_of_birth'] != null) ? date('Y-m-d', strtotime($row['date_of_birth'])) : null,
                        'last_year_increments' => !empty($row['last_year_increments']) ? $row['last_year_increments'] : null,
                        'last_year_increment_percent' => !empty($row['last_year_increment_percent']) ? $row['last_year_increment_percent'] . '%' : null,
                        'last_promotion' => !empty($row['last_promotion']) ? $row['last_promotion'] : null,
                        'ctc_annual' => !empty($row['ctc_annual']) ? $row['ctc_annual'] : 0.00,
                        'gross_salary_monthly' => !empty($row['gross_salary_monthly']) ? $row['gross_salary_monthly'] : 0.00,
                        'salary' => !empty($row['ctc_per_month']) ? $row['ctc_per_month'] : 0.00,
                        'last_year_increment_value' => !empty($row['last_year_increments_value']) ? $row['last_year_increments_value'] : 0.00,
                        'marital_status' => !empty($row['marital_status']) ? $row['marital_status'] : 0.00,
                        'father_name' => !empty($row['father_name']) ? $row['father_name'] : null,
                        'father_date_of_birth' => !empty($row['father_date_of_birth']) ? date('Y-m-d', strtotime($row['father_date_of_birth'])) : null,
                        'mother_name' => !empty($row['mother_name']) ? $row['mother_name'] : null,
                        'mother_date_of_birth' => !empty($row['mother_dob']) ? date('Y-m-d', strtotime($row['mother_dob'])) : null,
                        'marriage_anniversary' => !empty($row['marriage_anniversary']) ? date('Y-m-d', strtotime($row['marriage_anniversary'])) : null,
                        'spouse_name' => !empty($row['spouse_name']) ? $row['spouse_name'] : null,
                        'spouse_date_of_birth' => !empty($row['spouse_date_of_birth']) ? date('Y-m-d', strtotime($row['spouse_date_of_birth'])) : null,
                        'children_one' => !empty($row['children_1']) ? $row['children_1'] : null,
                        'children_one_date_of_birth' => !empty($row['children_1_dob']) ? date('Y-m-d', strtotime($row['children_1_dob'])) : null,
                        'children_two' => !empty($row['children_2']) ? $row['children_2'] : null,
                        'children_two_date_of_birth' => !empty($row['children_2_dob']) ? date('Y-m-d', strtotime($row['children_2_dob'])) : null,
                        'children_three' => !empty($row['children_3']) ? $row['children_3'] : null,
                        'children_three_date_of_birth' => !empty($row['children_3_dob']) ? date('Y-m-d', strtotime($row['children_3_dob'])) : null,
                        'children_four' => !empty($row['children_4']) ? $row['children_4'] : null,
                        'children_four_date_of_birth' => !empty($row['children_4_dob']) ? date('Y-m-d', strtotime($row['children_4_dob'])) : null,
                        'children_five' => !empty($row['children_5']) ? $row['children_5'] : null,
                        'children_five_date_of_birth' => !empty($row['children_5_dob']) ? date('Y-m-d', strtotime($row['children_5_dob'])) : null,
                        'pan_number' => !empty($row['pan_number']) ? $row['pan_number'] : null,
                        'aadhar_number' => !empty($row['adhar_number']) ? $row['adhar_number'] : null,
                        'emergency_number' => !empty($row['emergency_number']) ? $row['emergency_number'] : null,
                        'current_address' => !empty($row['current_address']) ? $row['current_address'] : null,
                        'permanent_address' => !empty($row['permanent_address']) ? $row['permanent_address'] : null,
                        'biometric_code' => !empty($row['biometric_code']) ? $row['biometric_code'] : null,
                        'account_number' => !empty($row['account_number']) ? $row['account_number'] : null,
                        'bank_name' => !empty($row['bank_name']) ? $row['bank_name'] : null,
                        'ifsc_code' => !empty($row['ifsc_code']) ? $row['ifsc_code'] : null,
                        'pf_number' => !empty($row['pf_number']) ? $row['pf_number'] : null,
                        'un_number' => !empty($row['un_number']) ? $row['un_number'] : null,
                        'esi_number' => !empty($row['esi_number']) ? $row['esi_number'] : null,
                        'probation_period' => !empty($row['probation_period']) ? date('Y-m-d', strtotime($row['probation_period'])) : null,
                        'date_of_confirmation' => !empty($row['date_of_confirmation']) ? date('Y-m-d', strtotime($row['date_of_confirmation'])) : null,
                        'notice_period' => !empty($row['notice_period']) ? $row['notice_period'] : null,
                        'date_of_leaving' => !empty($row['date_of_leaving']) ? date('Y-m-d', strtotime($row['date_of_leaving'])) : null,
                        'current_company_tenture' => !empty($row['current_company_tenure']) ? $row['current_company_tenure'] : 0,
                        'previous_exp' => !empty($row['previous_exp']) ? $row['previous_exp'] : null,
                        'total_exp' => !empty($row['total_exp']) ? $row['total_exp'] : 0,
                        'created_at' => getcurentDateTime(),
                        'updated_at' => getcurentDateTime()
                    ]);

                    if (!empty($row['higher_secondary']) && $row['higher_secondary'] != null && $row['higher_secondary'] != '') {
                        UserEducation::create(
                            [
                                'user_id' => $user->id,
                                'education_type_id' => '1',
                                'degree_name' => $row['higher_secondary'],
                            ]
                        );
                    }
                    if (!empty($row['high_school']) && $row['high_school'] != null && $row['high_school'] != '') {
                        UserEducation::create(
                            [
                                'user_id' => $user->id,
                                'education_type_id' => '0',
                                'degree_name' => $row['high_school'],
                            ]
                        );
                    }
                    if (!empty($row['graducation']) && $row['graducation'] != null && $row['graducation'] != '') {
                        UserEducation::create(
                            [
                                'user_id' => $user->id,
                                'education_type_id' => '2',
                                'degree_name' => $row['graducation'],
                            ]
                        );
                    }
                    if (!empty($row['post_graducation']) && $row['post_graducation'] != null && $row['post_graducation'] != '') {
                        UserEducation::create(
                            [
                                'user_id' => $user->id,
                                'education_type_id' => '3',
                                'degree_name' => $row['post_graducation'],
                            ]
                        );
                    }
                    if (!empty($row['other']) && $row['other'] != null && $row['other'] != '') {
                        UserEducation::create(
                            [
                                'user_id' => $user->id,
                                'education_type_id' => '4',
                                'degree_name' => $row['other'],
                            ]
                        );
                    }

                    //$pincode = Pincode::where('pincode','=',$row['pincode_id'])->select('id','city_id')->first();
                    $addressdetails->push([
                        'active' => 'Y',
                        'user_id' => $user['id'],
                        'address1' => !empty($row['address1']) ? $row['address1'] : '',
                        'address2' => !empty($row['address2']) ? $row['address2'] : '',
                        'landmark' => !empty($row['landmark']) ? $row['landmark'] : '',
                        'locality' => !empty($row['locality']) ? $row['locality'] : '',
                        'country_id' => !empty($row['country_id']) ? $row['country_id'] : null,
                        'state_id' => !empty($row['state_id']) ? $row['state_id'] : null,
                        // 'district_id' => !empty($city['district_id'])? $city['district_id']:null,
                        // 'city_id' => !empty($pincode['city_id'])? $pincode['city_id']:null,
                        // 'pincode_id' => !empty($pincode['id'])? $pincode['id']:null,
                        'created_by' => Auth::user()->id,
                        'created_at' => getcurentDateTime(),
                        'updated_at' => getcurentDateTime()
                    ]);
                }
            }
        }


        if ($userdetails->isNotEmpty()) {
            UserDetails::insert($userdetails->toArray());
        }
        if ($addressdetails->isNotEmpty()) {
            Address::insert($addressdetails->toArray());
        }
    }

    public function rules(): array
    {
        return [
            'user_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
            'primary_branch_id' => 'nullable|exists:branches,id',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function onFailure(Failure ...$failures)
    {
        dd($failures);
        Log::stack(['import-failure-logs'])->info(json_encode($failures));
    }
}
