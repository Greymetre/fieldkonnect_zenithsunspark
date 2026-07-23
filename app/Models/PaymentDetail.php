<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;

    protected $table = 'payment_details';

    protected $fillable = [ 'active', 'payment_id', 'sales_id', 'invoice_no' ,'amount', 'created_at', 'updated_at'];

    public function payments()
    {
        return $this->belongsTo('App\Models\Payment', 'payment_id', 'id');
    }

    public function sales()
    {
        return $this->belongsTo('App\Models\Sales', 'sales_id', 'id');
    }
}
