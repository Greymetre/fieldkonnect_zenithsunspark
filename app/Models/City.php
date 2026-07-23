<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'cities';

    protected $fillable = [ 'active', 'city_name', 'district_id', 'created_by', 'updated_by', 'state_id', 'deleted_at', 'grade', 'created_at', 'updated_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
    public function districtname()
    {
        return $this->belongsTo('App\Models\District', 'district_id', 'id')->select('id','district_name','state_id');
    }
    public function assignusers()
    {
        return $this->belongsTo('App\Models\UserCityAssign', 'id', 'city_id')->select('city_id','userid','reportingid');
    }
    public function statename()
    {
        return $this->belongsTo('App\Models\State', 'state_id', 'id')->select('id','state_name','country_id');
    }
}
