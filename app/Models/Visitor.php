<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $table = 'visitors';

    protected $fillable = [ 'ip_address', 'country', 'state', 'city', 'system_name', 'device', 'browser', 'is_mobile', 'created_at', 'updated_at'];
}
