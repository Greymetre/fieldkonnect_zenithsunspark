<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketing extends Model
{
    use HasFactory;

    protected $fillable = ['event_date','division','event_center','place_of_participant','event_district','state','event_under_type','event_under_name','branch','responsible_for_event','branding_team_member','name_of_participant','category_of_participant','mob_no_of_participant','google_drivelink','count_of_participant','created_by', 'created_at', 'updated_at'];


    public function createdByName()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
