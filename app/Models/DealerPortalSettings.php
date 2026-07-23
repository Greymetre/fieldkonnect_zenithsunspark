<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DealerPortalSettings extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = ['slider', 'slider_heading','created_at','updated_at'];

    public $timestamps = true;

    public function registerMediaCollections(): void {
        $this->addMediaCollection('dealer_portal_slider_image')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')));
    }
}
