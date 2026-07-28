<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDispatchItem extends Model
{
    protected $fillable = [
        'sales_order_dispatch_id', 'sales_order_item_id', 'product_id', 'warehouse_id', 'quantity',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function salesOrderItem() { return $this->belongsTo(SalesOrderItem::class); }
}
