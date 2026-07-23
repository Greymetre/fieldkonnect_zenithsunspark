<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReporting extends Model
{
    use HasFactory;

    protected $table = 'user_reportings';
    protected $fillable = [ 'active', 'userid', 'users', 'created_by', 'deleted_at', 'created_at', 'updated_at'];

    public function userslists()
    {
        return $this->hasManyJson('App\Models\User', 'users', 'id')->select('id','name');
    }

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function reportinginfo()
    {
        return $this->belongsTo('App\Models\User', 'userid', 'id')->select('id','name');
    }
}
