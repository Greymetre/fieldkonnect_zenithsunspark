<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class UserTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return User::select('id','name', 'first_name', 'last_name', 'mobile', 'email', 'password','gender', 'profile_image')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['id','User Name','Email','Gender','User Code','Location','Employee Codes','Branch id','Designation id','Division id','Department id','Payroll','Reportingid','role_ids','Sales Type','Date Of Joining','Date Of Birth','Last Year Increments','Last Year Increment Percent','Last promotion','CTC Annual','Gross Salary Monthly','Salary','last_year_increment_value','marital_status','father_name','father_date_of_birth','mother_name','mother_date_of_birth','marriage_anniversary','spouse_name','spouse_date_of_birth','children_one','children_one_date_of_birth','children_two','children_two_date_of_birth','children_three','children_three_date_of_birth','children_four','children_four_date_of_birth','children_five','children_five_date_of_birth','pan_number','aadhar_number','emergency_number','current_address','permanent_address','biometric_code','account_number','bank_name','ifsc_code','pf_number','un_number','esi_number','probation_period','date_of_confirmation','notice_period','date_of_leaving','current_company_tenture','previous_exp','total_exp',];
    }

}
