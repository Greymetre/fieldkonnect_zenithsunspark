<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Branch;
use App\Models\CustomerType;
use App\Models\Division;
use App\Models\Designation;
use App\Models\Department;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Count;

class UserExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    public function __construct(Request $request)
    {
        $this->user_type = $request->user_type;
        $this->active = $request->active;
        $this->division_id = $request->division_id;
        $this->branch_id = $request->branch_id;
        $this->department_id = $request->department_id;
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        $data = User::with('createdbyname', 'getbranch', 'getdesignation', 'reportinginfo', 'userinfo', 'getdivision', 'resignation')
            ->where(function ($query) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Sub_Admin')) {
                    $query->whereIn('id', $this->userids);
                }
                if ($this->active && !empty($this->active)) {
                    $query->where('active', $this->active);
                }
                if ($this->division_id && !empty($this->division_id)) {
                    $query->where('division_id', $this->division_id);
                }
                if ($this->branch_id && !empty($this->branch_id)) {
                    $query->where('branch_id', $this->branch_id);
                }
                if ($this->department_id && !empty($this->department_id)) {
                    $query->where('department_id', $this->department_id);
                }
            })
            ->whereHas('roles', function ($query) {
                if ($this->user_type == 'customer') {
                    $query->whereIn('id', config('constants.customer_roles'));
                } else {
                    $query->whereNotIn('id', config('constants.customer_roles'));
                }
            })
            ->latest()
            ->get();

        return $data;
    }

    public function headings(): array
    {
        if (Auth::user()->id == 1) {
            return ['ID', 'Employees Code', 'User Name', 'Designation', 'Role', 'Branch Name', 'Location', 'Department', 'Division', 'Reporting To', 'Mobile', 'Email', 'Gender', 'Date Of Joining', 'Date Of Birth', 'Age', 'CTC Annual', 'Gross Salary Monthly', 'CTC Per Month', 'Last Year Increments', 'Last Year Increment Percent', 'Last Year Increments Value', 'Last Promotion', 'Marital Status', 'Father Name', 'Father Date Of Birth', 'Mother Name', 'Mother  DOB', 'Marriage Anniversary', 'Spouse name', 'Spouse Date Of Birth', 'Children-1', 'Children-1 DOB', 'Children-2', 'Children-2 DOB', 'Children-3', 'Children-3 DOB', 'Children-4', 'Children-4 DOB', 'Children-5', 'Children-5 DOB', 'PAN Number', 'Adhar Number', 'Emergency Number', 'Current Address', 'Permanent Address', 'Biometric Code', 'Account Number', 'Bank Name', 'IFSC Code', 'PF Number', 'UN Number', 'ESI Number', 'Probation Period', 'Date of Confirmation', 'Notice Period', 'Date of leaving', 'High School', 'Higher Secondary', 'Graducation', 'Post Graducation', 'Other', 'Current Company TENURE', 'Previous Exp', 'Total Exp', 'Sales Type', 'Status', 'profile_image', 'designation_id', 'branch_id','primary_branch_id', 'division_id', 'department_id', 'Reporting ID', 'Role Ids', 'payroll', 'warehouse_id', 'Attandance Summary Report', 'Order Mails', 'Order Mail Type', 'Order Mail Type ID', 'Leave Balance', 'Grade', 'Blood Group', 'Personal Number'];
        } else {
            return ['ID', 'Employees Code', 'User Name', 'Designation', 'Role', 'Branch Name', 'Location', 'Department', 'Division', 'Reporting To', 'Mobile', 'Email', 'Gender', 'Date Of Joining', 'Date Of Birth', 'Age', 'Last Promotion', 'Marital Status', 'Father Name', 'Father Date Of Birth', 'Mother Name', 'Mother  DOB', 'Marriage Anniversary', 'Spouse name', 'Spouse Date Of Birth', 'Children-1', 'Children-1 DOB', 'Children-2', 'Children-2 DOB', 'Children-3', 'Children-3 DOB', 'Children-4', 'Children-4 DOB', 'Children-5', 'Children-5 DOB', 'PAN Number', 'Adhar Number', 'Emergency Number', 'Current Address', 'Permanent Address', 'Biometric Code', 'Account Number', 'Bank Name', 'IFSC Code', 'PF Number', 'UN Number', 'ESI Number', 'Probation Period', 'Date of Confirmation', 'Notice Period', 'Date of leaving', 'High School', 'Higher Secondary', 'Graducation', 'Post Graducation', 'Other', 'Current Company TENURE', 'Previous Exp', 'Total Exp', 'Sales Type', 'Status', 'profile_image', 'designation_id', 'branch_id','primary_branch_id', 'division_id', 'department_id', 'Reporting ID', 'Role Ids', 'payroll' ,'warehouse_id', 'Attandance Summary Report', 'Order Mails', 'Order Mail Type', 'Order Mail Type ID', 'Leave Balance', 'Grade', 'Blood Group', 'Personal Number'];
        }
    }

    public function map($data): array
    {

        if ($data['active'] == 'Y') {
            $status = 'ACTIVE';
        } else {
            $status = 'INACTIVE';
        }
        $roles = '';
        if (count($data['roles']) > 0) {
            foreach ($data['roles'] as $k => $role) {
                if ($k == (count($data['roles']) - 1)) {
                    $roles .= $role->id;
                } else {
                    $roles .= $role->id . ', ';
                }
            }
        }
        if ($data['userinfo'] && !empty($data['userinfo']['order_mails_type'])) {
            $mail_types = explode(',', $data['userinfo']['order_mails_type']);
            $mail_types_name = CustomerType::whereIn('id', $mail_types)->pluck('customertype_name')->toArray();
        } else {
            $mail_types_name = array();
        }

        $branchIds = array_filter(explode(',', $data->branch_id));
        if (!empty($branchIds)) {
            $branche_names = Branch::whereIn('id', $branchIds)->pluck('branch_name')->implode(', ');
        } else {
            $branche_names = "-";
        }

        if (Auth::user()->id == 1) {
            return [
                $data['id'],
                $data['employee_codes'],
                $data['name'],
                isset($data['getdesignation']['designation_name']) ? $data['getdesignation']['designation_name'] : '',
                str_replace(str_split('[/]/"'), '', $data->getRoleNames()),
                $branche_names,
                $data['location'],
                isset($data['getdepartment']['name']) ? $data['getdepartment']['name'] : '',
                isset($data['getdivision']['division_name']) ? $data['getdivision']['division_name'] : '',
                isset($data['reportinginfo']['name']) ? $data['reportinginfo']['name'] : '',
                $data['mobile'],
                $data['email'],
                $data['gender'],
                isset($data['userinfo']['date_of_joining']) ? $data['userinfo']['date_of_joining'] : '',
                isset($data['userinfo']['date_of_birth']) ? $data['userinfo']['date_of_birth'] : '',
                isset($data['userinfo']['date_of_birth']) ? Carbon::parse($data['userinfo']['date_of_birth'])->age : '',
                isset($data['userinfo']['ctc_annual']) ? $data['userinfo']['ctc_annual'] : '',
                isset($data['userinfo']['gross_salary_monthly']) ? $data['userinfo']['gross_salary_monthly'] : '',
                isset($data['userinfo']['salary']) ? $data['userinfo']['salary'] : '',
                isset($data['userinfo']['last_year_increments']) ? $data['userinfo']['last_year_increments'] : '',
                isset($data['userinfo']['last_year_increment_percent']) ? $data['userinfo']['last_year_increment_percent'] : '',
                isset($data['userinfo']['last_year_increment_value']) ? $data['userinfo']['last_year_increment_value'] : '',
                isset($data['userinfo']['last_promotion']) ? $data['userinfo']['last_promotion'] : '',
                isset($data['userinfo']['marital_status']) ? $data['userinfo']['marital_status'] : '',
                isset($data['userinfo']['father_name']) ? $data['userinfo']['father_name'] : '',
                isset($data['userinfo']['father_date_of_birth']) ? $data['userinfo']['father_date_of_birth'] : '',
                isset($data['userinfo']['mother_name']) ? $data['userinfo']['mother_name'] : '',
                isset($data['userinfo']['mother_date_of_birth']) ? $data['userinfo']['mother_date_of_birth'] : '',
                isset($data['userinfo']['spouse_name']) ? $data['userinfo']['spouse_name'] : '',
                isset($data['userinfo']['spouse_date_of_birth']) ? $data['userinfo']['spouse_date_of_birth'] : '',
                isset($data['userinfo']['marriage_anniversary']) ? $data['userinfo']['marriage_anniversary'] : '',
                isset($data['userinfo']['children_one']) ? $data['userinfo']['children_one'] : '',
                isset($data['userinfo']['children_one_date_of_birth']) ? $data['userinfo']['children_one_date_of_birth'] : '',
                isset($data['userinfo']['children_two']) ? $data['userinfo']['children_two'] : '',
                isset($data['userinfo']['children_two_date_of_birth']) ? $data['userinfo']['children_two_date_of_birth'] : '',
                isset($data['userinfo']['children_three']) ? $data['userinfo']['children_three'] : '',
                isset($data['userinfo']['children_three_date_of_birth']) ? $data['userinfo']['children_three_date_of_birth'] : '',
                isset($data['userinfo']['children_four']) ? $data['userinfo']['children_four'] : '',
                isset($data['userinfo']['children_four_date_of_birth']) ? $data['userinfo']['children_four_date_of_birth'] : '',
                isset($data['userinfo']['children_five']) ? $data['userinfo']['children_five'] : '',
                isset($data['userinfo']['children_five_date_of_birth']) ? $data['userinfo']['children_five_date_of_birth'] : '',
                isset($data['userinfo']['pan_number']) ? $data['userinfo']['pan_number'] : '',
                isset($data['userinfo']['aadhar_number']) ? $data['userinfo']['aadhar_number'] : '',
                isset($data['userinfo']['emergency_number']) ? $data['userinfo']['emergency_number'] : '',
                isset($data['userinfo']['current_address']) ? $data['userinfo']['current_address'] : '',
                isset($data['userinfo']['permanent_address']) ? $data['userinfo']['permanent_address'] : '',
                isset($data['userinfo']['biometric_code']) ? $data['userinfo']['biometric_code'] : '',
                isset($data['userinfo']['account_number']) ? $data['userinfo']['account_number'] : '',
                isset($data['userinfo']['bank_name']) ? $data['userinfo']['bank_name'] : '',
                isset($data['userinfo']['ifsc_code']) ? $data['userinfo']['ifsc_code'] : '',
                isset($data['userinfo']['pf_number']) ? $data['userinfo']['pf_number'] : '',
                isset($data['userinfo']['un_number']) ? $data['userinfo']['un_number'] : '',
                isset($data['userinfo']['esi_number']) ? $data['userinfo']['esi_number'] : '',
                isset($data['userinfo']['probation_period']) ? $data['userinfo']['probation_period'] : '',
                isset($data['userinfo']['date_of_confirmation']) ? $data['userinfo']['date_of_confirmation'] : '',
                isset($data['resignation']['notice']) ? ($data['resignation']['notice'] > 10 ? $data['resignation']['notice'].' Days' : $data['resignation']['notice'].' Month') : '',
                isset($data['resignation']['last_working_date']) ? date('d M Y', strtotime($data['resignation']['last_working_date'])) : '',
                isset($data['geteducation']) ? $data['geteducation']->where('education_type_id', '0')->value('degree_name') : '',
                isset($data['geteducation']) ? $data['geteducation']->where('education_type_id', '1')->value('degree_name') : '',
                isset($data['geteducation']) ? $data['geteducation']->where('education_type_id', '2')->value('degree_name') : '',
                isset($data['geteducation']) ? $data['geteducation']->where('education_type_id', '3')->value('degree_name') : '',
                isset($data['geteducation']) ? $data['geteducation']->where('education_type_id', '4')->value('degree_name') : '',
                isset($data['userinfo']['current_company_tenture']) ? $data['userinfo']['current_company_tenture'] : '',
                isset($data['userinfo']['previous_exp']) ? $data['userinfo']['previous_exp'] : '',
                isset($data['userinfo']['total_exp']) ? $data['userinfo']['total_exp'] : '',
                $data['sales_type'],
                $status,
                $data['profile_image'],
                $data['designation_id'] ?? '-',
                $data['branch_id'] ?? '-',
                $data['primary_branch_id'] ?? '',
                $data['division_id'] ?? '-',
                $data['department_id'] ?? '-',
                $data['reportingid'] ?? '-',
                $roles,
                $data['payroll'] ?? '-',
                $data['warehouse_id'] ?? '-',
                (string)$data['show_attandance_report'] ?? '0',
                $data['userinfo'] ? $data['userinfo']['order_mails'] : '',
                implode(',', $mail_types_name),
                $data['userinfo'] ? $data['userinfo']['order_mails_type'] : '',
                $data['leave_balance'] ?? '-',
                $data['grade'] ?? '-',
                $data['blood_group'] ?? '-',
                $data['personal_number'] ?? '-',
            ];
        } else {
            return [
                $data['id'],
                $data['employee_codes'],
                $data['name'],
                isset($data['getdesignation']['designation_name']) ? $data['getdesignation']['designation_name'] : '',
                str_replace(str_split('[/]/"'), '', $data->getRoleNames()),
                $branche_names,
                $data['location'],
                isset($data['getdepartment']['name']) ? $data['getdepartment']['name'] : '',
                isset($data['getdivision']['division_name']) ? $data['getdivision']['division_name'] : '',
                isset($data['reportinginfo']['name']) ? $data['reportinginfo']['name'] : '',
                $data['mobile'],
                $data['email'],
                $data['gender'],
                isset($data['userinfo']['date_of_joining']) ? $data['userinfo']['date_of_joining'] : '',
                isset($data['userinfo']['date_of_birth']) ? $data['userinfo']['date_of_birth'] : '',
                isset($data['userinfo']['date_of_birth']) ? Carbon::parse($data['userinfo']['date_of_birth'])->age : '',
                isset($data['userinfo']['last_promotion']) ? $data['userinfo']['last_promotion'] : '',
                isset($data['userinfo']['marital_status']) ? $data['userinfo']['marital_status'] : '',
                isset($data['userinfo']['father_name']) ? $data['userinfo']['father_name'] : '',
                isset($data['userinfo']['father_date_of_birth']) ? $data['userinfo']['father_date_of_birth'] : '',
                isset($data['userinfo']['mother_name']) ? $data['userinfo']['mother_name'] : '',
                isset($data['userinfo']['mother_date_of_birth']) ? $data['userinfo']['mother_date_of_birth'] : '',
                isset($data['userinfo']['spouse_name']) ? $data['userinfo']['spouse_name'] : '',
                isset($data['userinfo']['spouse_date_of_birth']) ? $data['userinfo']['spouse_date_of_birth'] : '',
                isset($data['userinfo']['marriage_anniversary']) ? $data['userinfo']['marriage_anniversary'] : '',
                isset($data['userinfo']['children_one']) ? $data['userinfo']['children_one'] : '',
                isset($data['userinfo']['children_one_date_of_birth']) ? $data['userinfo']['children_one_date_of_birth'] : '',
                isset($data['userinfo']['children_two']) ? $data['userinfo']['children_two'] : '',
                isset($data['userinfo']['children_two_date_of_birth']) ? $data['userinfo']['children_two_date_of_birth'] : '',
                isset($data['userinfo']['children_three']) ? $data['userinfo']['children_three'] : '',
                isset($data['userinfo']['children_three_date_of_birth']) ? $data['userinfo']['children_three_date_of_birth'] : '',
                isset($data['userinfo']['children_four']) ? $data['userinfo']['children_four'] : '',
                isset($data['userinfo']['children_four_date_of_birth']) ? $data['userinfo']['children_four_date_of_birth'] : '',
                isset($data['userinfo']['children_five']) ? $data['userinfo']['children_five'] : '',
                isset($data['userinfo']['children_five_date_of_birth']) ? $data['userinfo']['children_five_date_of_birth'] : '',
                isset($data['userinfo']['pan_number']) ? $data['userinfo']['pan_number'] : '',
                isset($data['userinfo']['aadhar_number']) ? $data['userinfo']['aadhar_number'] : '',
                isset($data['userinfo']['emergency_number']) ? $data['userinfo']['emergency_number'] : '',
                isset($data['userinfo']['current_address']) ? $data['userinfo']['current_address'] : '',
                isset($data['userinfo']['permanent_address']) ? $data['userinfo']['permanent_address'] : '',
                isset($data['userinfo']['biometric_code']) ? $data['userinfo']['biometric_code'] : '',
                isset($data['userinfo']['account_number']) ? $data['userinfo']['account_number'] : '',
                isset($data['userinfo']['bank_name']) ? $data['userinfo']['bank_name'] : '',
                isset($data['userinfo']['ifsc_code']) ? $data['userinfo']['ifsc_code'] : '',
                isset($data['userinfo']['pf_number']) ? $data['userinfo']['pf_number'] : '',
                isset($data['userinfo']['un_number']) ? $data['userinfo']['un_number'] : '',
                isset($data['userinfo']['esi_number']) ? $data['userinfo']['esi_number'] : '',
                isset($data['userinfo']['probation_period']) ? $data['userinfo']['probation_period'] : '',
                isset($data['userinfo']['date_of_confirmation']) ? $data['userinfo']['date_of_confirmation'] : '',
                isset($data['resignation']['notice']) ? ($data['resignation']['notice'] > 10 ? $data['resignation']['notice'].' Days' : $data['resignation']['notice'].' Month') : '',
                isset($data['resignation']['last_working_date']) ? date('d M Y', strtotime($data['resignation']['last_working_date'])) : '',
                isset($data['geteducation']) ? $data['geteducation']->where('education_type_id', '0')->value('degree_name') : '',
                isset($data['geteducation']) ? $data['geteducation']->where('education_type_id', '1')->value('degree_name') : '',
                isset($data['geteducation']) ? $data['geteducation']->where('education_type_id', '2')->value('degree_name') : '',
                isset($data['geteducation']) ? $data['geteducation']->where('education_type_id', '3')->value('degree_name') : '',
                isset($data['geteducation']) ? $data['geteducation']->where('education_type_id', '4')->value('degree_name') : '',
                isset($data['userinfo']['current_company_tenture']) ? $data['userinfo']['current_company_tenture'] : '',
                isset($data['userinfo']['previous_exp']) ? $data['userinfo']['previous_exp'] : '',
                isset($data['userinfo']['total_exp']) ? $data['userinfo']['total_exp'] : '',
                $data['sales_type'],
                $status,
                $data['profile_image'],
                $data['designation_id'] ?? '-',
                $data['branch_id'] ?? '-',
                $data['primary_branch_id'] ?? '',
                $data['division_id'] ?? '-',
                $data['department_id'] ?? '-',
                $data['reportingid'] ?? '-',
                $roles,
                $data['payroll'] ?? '-',
                $data['warehouse_id'] ?? '-',
                (string)$data['show_attandance_report'] ?? '0',
                $data['userinfo'] ? $data['userinfo']['order_mails'] : '',
                implode(',', $mail_types_name),
                $data['userinfo'] ? $data['userinfo']['order_mails_type'] : '',
                $data['leave_balance'] ?? '-',
                $data['grade'] ?? '-',
                $data['blood_group'] ?? '-',
                $data['personal_number'] ?? '-',
            ];
        }
    }
}
