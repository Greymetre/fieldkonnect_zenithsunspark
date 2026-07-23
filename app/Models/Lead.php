<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Lead extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = [
        'company_name',
        'company_url',
        'address_id',
        'status',
        'assign_to',
        'lead_source',
        'lead_generation_date',
        'conversion_date',
        'customer_id',
        'others',
        'created_by',
    ];
    
    public function contacts(){
        return $this->hasMany(LeadContact::class);
    }

    public function notes(){
        return $this->hasMany(LeadNote::class);
    }

        public function registerMediaCollections(): void {
        $this->addMediaCollection('lead_file')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')));
            //  ->singleFile();
    }

    public function assign_user(){
        return $this->belongsTo(User::class,'assign_to');
    }

    public function status_is(){
        return $this->belongsTo(Status::class,'status')->where('module', 'LeadStatus');
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'model');
    }

    public function createdby(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function tasks(){
        return $this->hasMany(LeadTask::class);
    }

    public function opportunities(){
        return $this->hasMany(LeadOpportunity::class);
    }
}
