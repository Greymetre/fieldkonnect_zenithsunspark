<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FieldKonnectAppSetting extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = ['app_version', 'order_discount_limit'];

    public $timestamps = true;

    public function registerMediaCollections(): void {

        $this->addMediaCollection('product_catalogue')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')));

    }

}
