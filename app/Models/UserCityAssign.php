<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCityAssign extends Model
{
    use HasFactory;

    protected $table = 'user_city_assigns';

    protected $fillable = ['userid', 'city_id', 'reportingid', 'created_at', 'updated_at'];

    public function userinfo()
    {
        return $this->belongsTo('App\Models\User', 'userid', 'id');
    }

    public function reportinginfo()
    {
        return $this->belongsTo('App\Models\User', 'reportingid', 'id');
    }

    public function cityname()
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'id')->select('id','city_name','district_id','grade');
    }
}
