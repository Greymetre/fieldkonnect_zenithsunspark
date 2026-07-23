<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'redeem_mode',
        'account_holder',
        'account_number',
        'bank_name',
        'ifsc_code',
        'redeem_amount',
        'gift_id',
        'product_send',
        'dispatch_number',
        'status',
        'created_by',
        'approve_date',
        'deatils',
        'invoice_number'
    ];

    public function customer(){
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name','profile_image');
    }

    public function product(){
        return $this->belongsTo(Gifts::class, 'gift_id', 'id');
    }

    public function neft_details(){
        return $this->hasOne(NeftRedemptionDetails::class);
    }

    public function gift_details(){
        return $this->hasOne(GiftRedemptionDetail::class);
    }
    
}
