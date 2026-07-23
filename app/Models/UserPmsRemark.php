<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPmsRemark extends Model
{
    use HasFactory;

    protected $table = 'user_pms_remarks';

    protected $fillable = [
        'user_id',
        'fyear',
        'recommended_increment',
        'recommended_designation',
        'remark',
        'created_at',
        'updated_at',
    ];
}
