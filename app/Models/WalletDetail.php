<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletDetail extends Model
{
    use HasFactory;

    protected $table = 'wallet_details';

    protected $fillable = [ 'active', 'wallet_id', 'points', 'product_id', 'category_id', 'subcategory_id', 'quantity', 'deleted_at', 'created_at', 'updated_at'];

    public function wallets()
    {
        return $this->belongsTo('App\Models\Wallet', 'wallet_id', 'id')->select('id','active','customer_id','scheme_id','schemedetail_id','invoice_no','invoice_amount','invoice_date','transaction_at');
    }
}
