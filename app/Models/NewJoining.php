<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class NewJoining extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = ['email','first_name','middle_name','last_name','gender','dob','mobile_number','contact_number','father_name','father_occupation','mother_name','mother_occupation','marital_status','spouse_name','spouse_dob','spouse_education','spouse_occupation','anniversary','present_address','present_city','present_state','present_pincode','permanent_address','permanent_city','permanent_state','permanent_pincode','pan','aadhar','driving_licence','blood_group','language','other_language','qualification','experience','skill','occupy','branch','department','date_of_joining','designation', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function registerMediaCollections(): void {
        $this->addMediaCollection('adhar_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')));

        $this->addMediaCollection('pan_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('passport_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('ssc_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('hsc_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
             
        $this->addMediaCollection('graduation_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('birth_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('relieving_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('last_salray_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('bank_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('offer_images')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
             
    }

    public function branch_details()
    {
     return $this->belongsTo(Branch::class, 'branch', 'id');
    }

    public function department_details()
    {
     return $this->belongsTo(Department::class, 'department', 'id');
    }

    public function designation_details()
    {
     return $this->belongsTo(Designation::class, 'designation', 'id');
    }
}
