<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    use HasFactory;

    protected $fillable = [ 'customer_id', 'coupon_code', 'active_point', 'provision_point', 'point', 'scheme_id', 'status', 'remark', 'created_by', 'created_at', 'updated_at'];

    public function customer(){
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    public function scheme(){
        return $this->belongsTo(Services::class, 'coupon_code', 'serial_no');
    }

    public function scheme_details(){
        return $this->belongsTo(SchemeHeader::class, 'scheme_id', 'id');
    }

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name','profile_image');
    }
}
