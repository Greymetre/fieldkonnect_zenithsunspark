<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'countries';

    protected $fillable = [ 'active', 'country_name', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function countrystates()
    {
        return $this->hasMany('App\Models\State', 'country_id', 'id')->select('id','state_name', 'country_id');
    }
}
