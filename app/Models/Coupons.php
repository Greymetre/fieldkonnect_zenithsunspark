<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupons extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [ 'active', 'coupon', 'points', 'expiry_date', 'product_id', 'coupon_profile_id', 'deleted_at', 'created_at', 'updated_at' ];

    public function couponprofiles()
    {
        return $this->belongsTo('App\Models\CouponProfile', 'coupon_profile_id', 'id')->select('id','profile_name','created_by');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name');
    }

    public function products()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id')->select('id','product_name','display_name','product_image');
    }
}
