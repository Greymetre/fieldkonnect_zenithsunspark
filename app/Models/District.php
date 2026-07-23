<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $fillable = [ 'active', 'district_name', 'state_id', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
    public function statename()
    {
        return $this->belongsTo('App\Models\State', 'state_id', 'id')->select('id','state_name','country_id');
    }
}
