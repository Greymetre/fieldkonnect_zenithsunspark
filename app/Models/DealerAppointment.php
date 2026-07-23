<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DealerAppointment extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = ['branch','district','city','place','appointment_date','customertype','division','asc_divi','old_user','old_division','old_firm_name','old_gst','security_deposit','SDservicecenterd','SDPUMPMOTORS','SDF&A','gst_type','gst_no','parent_id','firm_type','firm_name','cin_no','related_firm_name','line_business','office_address','office_pincode','office_mobile','office_email','godown_address','godown_pincode','godown_mobile','godown_email','status','ppd_name_1','ppd_adhar_1','ppd_pan_1','ppd_name_2','ppd_adhar_2','ppd_pan_2','ppd_name_3','ppd_adhar_3','ppd_pan_3','ppd_name_4','ppd_adhar_4','ppd_pan_4','contact_person_name','mobile_email','bank_name','bank_address','account_type','account_number','ifsc_code','payment_term','credit_period','cheque_no_1','cheque_account_number_1','cheque_bank_1','cheque_no_2','cheque_account_number_2','cheque_bank_2','manufacture_company_1','manufacture_product_1','manufacture_business_1','manufacture_turn_over_1','manufacture_company_2','manufacture_product_2','manufacture_business_2','manufacture_turn_over_2','present_annual_turnover','motor_anticipated_business_1','motor_next_year_business_1','pump_anticipated_business_1','pump_next_year_business_1','F&A_anticipated_business_1','F&A_next_year_business_1','lighting_anticipated_business_1','lighting_next_year_business_1','agri_anticipated_business_1','agri_next_year_business_1','solar_anticipated_business_1','solar_next_year_business_1','anticipated_business_total','approval_status','sales_approve','ho_approve','bm_remark','bm_remark_user','created_by','created_at','updated_at'];

    public $timestamps = true;


    public function sales_approve_user()
    {
     return $this->belongsTo(User::class, 'sales_approve', 'id');
    }

    public function ho_approve_user()
    {
     return $this->belongsTo(User::class, 'ho_approve', 'id');
    }

    public function parent()
    {
     return $this->belongsTo(Customers::class, 'parent_id', 'id');
    }

    public function bm_remark_user_details()
    {
     return $this->belongsTo(User::class, 'bm_remark_user', 'id');
    }

    public function branch_details()
    {
     return $this->belongsTo(Branch::class, 'branch', 'id');
    }

    public function district_details()
    {
     return $this->belongsTo(District::class, 'district', 'id');
    }

    public function city_details()
    {
     return $this->belongsTo(City::class, 'city', 'id');
    }
    public function appointment_kyc_detail()
    {
     return $this->belongsTo(DealerAppointmentKyc::class, 'id', 'appointment_id');
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name','employee_codes');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('service_policy')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('dealer_policy')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('mou_sheet')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('mcl_cheque_1')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('mcl_cheque_2')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('gst_certificate')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('adhar_card')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('pan_card')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('bank_statement')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('shop_image')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('application_form')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('cancel_cheque')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('profile_picture')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('certificate')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('dealer_board')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('welcome_kit')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
    }
}
