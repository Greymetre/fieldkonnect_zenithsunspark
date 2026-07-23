<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    use HasFactory;

    protected $table = 'pincodes';

    protected $fillable = [ 'active', 'pincode', 'city_id', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
    public function cityname()
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'id')->select('id','city_name','district_id');
    }
    public function assigncitiesusers()
    {
        return $this->belongsTo('App\Models\UserCityAssign', 'city_id', 'city_id')->select('city_id','userid','reportingid');
    }
}
