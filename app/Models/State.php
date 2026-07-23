<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = 'states';

    protected $fillable = [ 'active', 'state_name', 'country_id', 'gst_code', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
    public function countryname()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id')->select('id','country_name');
    }
    public function statecities()
    {
        return $this->hasMany('App\Models\City', 'state_id', 'id')->select('id','city_name', 'state_id', 'grade');
    }
}
