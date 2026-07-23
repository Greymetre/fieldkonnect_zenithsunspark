<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompOffLeave extends Model
{
    use HasFactory;

    protected $table = 'comp_off_leaves';

    protected $fillable = [
        'user_id',
        'leave_id',
        'comp_off_date',
        'expiry_date',
        'is_used',
        'balance',
        'created_at',
        'updated_at',
    ];
}
