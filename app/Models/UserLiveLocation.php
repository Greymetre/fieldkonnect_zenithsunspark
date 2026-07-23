<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLiveLocation extends Model
{
    use HasFactory;

    protected $table = 'user_live_locations';

    protected $fillable = [ 'active', 'userid', 'latitude', 'longitude', 'time', 'address', 'deleted_at', 'created_at', 'updated_at'];
}
