<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeatCustomer extends Model
{
    use HasFactory;

    protected $table = 'beat_customers';

    protected $fillable = [ 'active', 'beat_id', 'customer_id','created_at', 'updated_at'];

    public function beats()
    {
        return $this->belongsTo('App\Models\Beat', 'beat_id', 'id')->select('id', 'active','beat_name','created_by');
    }

    public function customers()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id')->select('id','active','name','mobile','profile_image','latitude','longitude','email','first_name','last_name');
    }

    public function beatschedules()
    {
        return $this->belongsTo('App\Models\BeatSchedule', 'beat_id', 'beat_id')->select('id','beat_id','beat_date','user_id');
    }
}
