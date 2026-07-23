<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemeHeader extends Model
{
    use HasFactory;

    protected $table = 'scheme_headers';

    protected $fillable = [ 'active', 'scheme_name', 'scheme_description', 'start_date', 'end_date', 'scheme_image', 'scheme_type', 'point_value', 'created_at', 'updated_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function schemedetails()
    {
        return $this->hasMany('App\Models\SchemeDetails','scheme_id','id')->where('active','Y');
    }
}
