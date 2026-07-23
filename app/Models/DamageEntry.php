<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DamageEntry extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = [ 'customer_id', 'coupon_code', 'point', 'scheme_id', 'status', 'remark', 'created_by', 'created_at', 'updated_at'];

    public function customer(){
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    public function scheme(){
        return $this->belongsTo(Services::class, 'coupon_code', 'serial_no');
    }

    public function scheme_details(){
        return $this->belongsTo(SchemeHeader::class, 'scheme_id', 'id');
    }

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name','profile_image');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('damageattach1')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('damageattach2')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('damageattach3')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
    }
}
