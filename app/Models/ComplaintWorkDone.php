<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ComplaintWorkDone extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = ['complaint_id','done_by', 'remark','created_at','updated_at'];
    public $timestamps = true;

    public function registerMediaCollections(): void {
        $this->addMediaCollection('complaint_work_done_attach')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
    }
}
