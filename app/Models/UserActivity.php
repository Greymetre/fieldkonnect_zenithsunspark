<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $table = 'user_activities';

    protected $fillable = ['active', 'userid', 'customerid', 'latitude', 'longitude', 'time', 'address', 'description', 'type', 'deleted_at', 'created_at', 'updated_at'];


    public function customers()
    {
        return $this->belongsTo('App\Models\Customers', 'customerid', 'id')->select('id','name', 'first_name', 'last_name','mobile','created_at');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'userid', 'id')->select('id','name','mobile','profile_image');
    }
}
