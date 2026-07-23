<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    protected $table = 'user_details';
    
   protected $fillable = ['active', 'user_id', 'date_of_birth', 'date_of_joining', 'marital_status', 'deleted_at', 'created_at', 'updated_at','salary','last_year_increments','last_promotion', 'order_mails', 'order_mails_type','aadhar_number','pan_number','emergency_number','current_address','permanent_address','father_name','father_date_of_birth','mother_name','mother_date_of_birth','marriage_anniversary','spouse_name','spouse_date_of_birth','children_one','children_one_date_of_birth','children_two','children_two_date_of_birth','children_three','children_three_date_of_birth','children_four','children_four_date_of_birth','children_five','children_five_date_of_birth','account_number','bank_name','ifsc_code','ctc_annual','gross_salary_monthly','last_year_increment_percent','last_year_increment_value','pf_number','un_number','esi_number','probation_period','date_of_confirmation','notice_period','date_of_leaving','biometric_code','other_education','previous_exp','current_company_tenture','total_exp']; 
}
