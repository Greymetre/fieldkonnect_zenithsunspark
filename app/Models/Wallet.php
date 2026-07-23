<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'wallets';

    protected $fillable = [ 'active', 'customer_id', 'scheme_id', 'schemedetail_id', 'points', 'point_type', 'invoice_amount', 'invoice_no', 'coupon_code','invoice_date', 'transaction_at', 'transaction_type', 'sales_id', 'deleted_at', 'created_at', 'updated_at', 'checkinid','quantity','userid'];

    public function customers()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id')->select('id','name', 'first_name', 'last_name','customertype','mobile');
    }

    public function schemes()
    {
        return $this->belongsTo('App\Models\SchemeHeader', 'scheme_id', 'id')->select('id','scheme_name');
    }

    public function walletdetails()
    {
        return $this->hasMany('App\Models\WalletDetail','wallet_id','id')->where('active','Y')->select('id','wallet_id','points','coupon_code','product_id','category_id','subcategory_id','quantity');
    }
    public function usersinfo()
    {
        return $this->belongsTo('App\Models\User', 'userid', 'id')->select('id','name','mobile');
    }
}
