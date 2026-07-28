<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'order_type', 'customer_id', 'warehouse_id', 'order_date',
        'status', 'subtotal', 'total_gst', 'grand_total', 'notes', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'subtotal' => 'decimal:2',
        'total_gst' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function customer() { return $this->belongsTo(Customers::class, 'customer_id'); }
    public function warehouse() { return $this->belongsTo(WareHouse::class, 'warehouse_id'); }
    public function items() { return $this->hasMany(SalesOrderItem::class); }
    public function payments() { return $this->hasMany(SalesOrderPayment::class); }
    public function latestPayment() { return $this->hasOne(SalesOrderPayment::class)->latestOfMany(); }
    public function dispatches() { return $this->hasMany(SalesOrderDispatch::class); }
    public function returns()
    {
        return $this->hasMany(SalesOrderDispatch::class)
            ->where('dispatch_number', 'like', 'RTR-%');
    }
    public function latestReturn()
    {
        return $this->hasOne(SalesOrderDispatch::class)
            ->where('dispatch_number', 'like', 'RTR-%')
            ->latestOfMany();
    }
}
