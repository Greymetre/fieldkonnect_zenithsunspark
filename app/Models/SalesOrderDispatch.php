<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDispatch extends Model
{
    protected $fillable = ['dispatch_number', 'sales_order_id', 'dispatch_date', 'remark', 'dispatched_by'];
    protected $casts = ['dispatch_date' => 'date'];
    public function items() { return $this->hasMany(SalesOrderDispatchItem::class); }
}
