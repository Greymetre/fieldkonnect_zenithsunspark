<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeatSchedule extends Model
{
    use HasFactory;

    protected $table = 'beat_schedules';

    protected $fillable = [ 'active', 'beat_id', 'beat_date', 'user_id', 'created_at', 'updated_at','tourid'];

    public function beatcheckininfo()
    {
        return $this->hasMany('App\Models\CheckIn', 'beatscheduleid', 'id');
    }
    public function beatschedulecustomer()
    {
        return $this->hasMany('App\Models\Customers', 'beatscheduleid', 'id');
    }
    public function beatscheduleorders()
    {
        return $this->hasMany('App\Models\Order', 'beatscheduleid', 'id');
    }
    public function beats()
    {
        return $this->belongsTo('App\Models\Beat', 'beat_id', 'id')->select('id','active','beat_name','description','created_by','city_id','state_id','district_id');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
    public function beatcustomers()
    {
        return $this->hasMany('App\Models\BeatCustomer', 'beat_id', 'beat_id');
    }
    public function beatCounters()
    {
        return $this->hasMany('App\Models\CheckIn', 'user_id', 'user_id')->select('id','customer_id', 'user_id','checkin_date');
    }
}
