<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UserEducation extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id','education_type_id', 'degree_name', 'board_name', 'percentage', 'grade', 'created_at', 'updated_at'
  ];

    public function registerMediaCollections(): void {
        $this->addMediaCollection('education_image')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
    }

}
