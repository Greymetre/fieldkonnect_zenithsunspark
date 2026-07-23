<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    use HasFactory;

    protected $table = 'check_in';

    protected $fillable = [ 'active', 'customer_id', 'user_id', 'checkin_date', 'checkin_time', 'checkin_latitude', 'checkin_longitude', 'checkin_address', 'checkout_date', 'checkout_time', 'time_interval', 'checkout_latitude', 'checkout_longitude', 'checkout_address', 'deleted_at', 'created_at', 'updated_at', 'distance', 'beatscheduleid' ];

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function customers()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id')->select('id','name', 'first_name', 'last_name','mobile','created_at','customertype');
    }

    public function beatschedules()
    {
        return $this->belongsTo('App\Models\BeatSchedule', 'beatscheduleid', 'id')->select('id','beat_id','beat_date');
    }
    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'beatscheduleid', 'beatscheduleid')->select('id','beatscheduleid', 'total_qty', 'grand_total');
    }
    public function visitreports()
    {
        return $this->belongsTo('App\Models\VisitReport', 'id', 'checkin_id')->select('id','checkin_id', 'description','report_title','visit_image','visit_type_id');
    }

    public function orders_sum()
    {
        return $this->hasMany(Order::class, 'created_by', 'user_id');
    }

}
