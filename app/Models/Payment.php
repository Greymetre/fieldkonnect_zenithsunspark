<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [ 'active', 'user_id', 'customer_id', 'customer_name', 'payment_date', 'payment_mode', 'payment_type', 'bank_name', 'reference_no', 'amount', 'response', 'description', 'file_path', 'status_id', 'deleted_at', 'created_at', 'updated_at'];

    public function paymentdetails()
    {
        return $this->hasMany('App\Models\PaymentDetail', 'payment_id', 'id');
    }

    public function customers()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id')->select('id','name', 'first_name', 'last_name','mobile','email');
    }
}
