<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $fillable = [
        'sales_order_id', 'product_id', 'quantity', 'dispatched_quantity', 'rate', 'gst_percent',
        'taxable_amount', 'gst_amount', 'total_amount',
    ];

    public function salesOrder() { return $this->belongsTo(SalesOrder::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
