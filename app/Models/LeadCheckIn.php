<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCheckIn extends Model
{
    use HasFactory;

    protected $table = 'lead_check_in';

    protected $fillable = [ 'active', 'lead_id', 'user_id', 'checkin_date', 'checkin_time', 'checkin_latitude', 'checkin_longitude', 'checkin_address', 'checkout_date', 'checkout_time', 'time_interval', 'checkout_latitude', 'checkout_longitude', 'checkout_address', 'checkout_note', 'deleted_at', 'created_at', 'updated_at', 'distance' ];

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo('App\Models\Lead', 'lead_id', 'id');
    }

    public function visitreports()
    {
        return $this->belongsTo('App\Models\VisitReport', 'id', 'checkin_id')->select('id','checkin_id', 'description','report_title','visit_image','visit_type_id');
    }
}
