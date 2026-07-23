<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MarketIntelligenceServey extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $table = 'market_intelligence_serveys';

    protected $fillable = [
        'title',
        'division_id',
        'created_by',
        'crated_at',
        'updated_at'
    ];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name' ,'employee_codes', 'division_id');
    }

    public function division()
    {
        return $this->belongsTo('App\Models\Division', 'division_id', 'id')->select('id','division_name');
    }

    public function data()
    {
        return $this->hasMany(MarketIntelligenceServeyData::class, 'servey_id', 'id');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('servey_image')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
    }


}
