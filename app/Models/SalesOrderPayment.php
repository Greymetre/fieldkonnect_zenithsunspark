<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderPayment extends Model
{
    protected $fillable = [
        'receipt_number', 'sales_order_id', 'payment_date', 'amount_received',
        'payment_mode', 'reference_number', 'received_by',
    ];

    protected $casts = ['payment_date' => 'date', 'amount_received' => 'decimal:2'];

    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
}
