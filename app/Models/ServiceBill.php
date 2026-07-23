<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Carbon\Carbon;

class ServiceBill extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = ['bill_no','complaint_id','complaint_no','division','category','complaint_type','complaint_reason','condition_of_service','received_product','nature_of_fault','service_location','repaired_replacement','replacement_tag','replacement_tag_number', 'line_voltage','load_voltage','current','water_source','panel_rating_running','panel_rating_starting','created_at', 'updated_at'];

    public $timestamps = true;


    public function service_bill_products()
    {
        return $this->hasMany(ServiceBillProductDetails::class, 'service_bill_id', 'id');
    }

    public function complaint()
    {
        return $this->hasOne(Complaint::class, 'id', 'complaint_id');
    }

    public function division_details()
    {
        return $this->hasOne(Category::class, 'id', 'division');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('product_sr_no')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('scr_job_card')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('photo_3')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('photo_4')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('photo_5')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('voltage_image')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
        $this->addMediaCollection('current_image')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
    }

      public function getUpdatedAtAttribute($value)
    {
        try {
            return $value ? Carbon::parse($value)->format('d-m-Y h:i:s') : '';
        } catch (\Exception $e) {
            return ''; // Return null if parsing fails
        }
    }
}
