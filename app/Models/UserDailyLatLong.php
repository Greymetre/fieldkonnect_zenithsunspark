<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDailyLatLong extends Model
{
    use HasFactory;

    protected $table = 'user_daily_lat_longs';

    protected $fillable = [
        'user_id',
        'date',
        'latitude',
        'longitude',
    ];
}
