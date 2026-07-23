<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponProfile extends Model
{
    use HasFactory;

    protected $table = 'coupon_profiles';

    protected $fillable = ['active', 'profile_name', 'coupon_length', 'excluding_character', 'coupon_count', 'created_by', 'deleted_at', 'created_at', 'updated_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
