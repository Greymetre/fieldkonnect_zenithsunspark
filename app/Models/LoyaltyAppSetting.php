<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LoyaltyAppSetting extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = ['customer_types','product_catalogue', 'scheme_catalogue', 'terms_condition', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function registerMediaCollections(): void {

        $this->addMediaCollection('loyalty_side_menu_image')
              ->singleFile() // Restricts to only one image
              ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
              ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')));

        $this->addMediaCollection('slider_image')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')));

        $this->addMediaCollection('gift_slider_image')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')));

        $this->addMediaCollection('product_catalogue')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('scheme_catalogue')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();

        $this->addMediaCollection('terms_condition')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
    }
}
