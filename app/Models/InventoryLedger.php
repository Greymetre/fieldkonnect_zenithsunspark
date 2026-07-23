<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLedger extends Model
{
    protected $fillable = [
        'warehouse_id', 'product_id', 'transaction_type', 'reference_type',
        'reference_id', 'quantity_in', 'quantity_out', 'balance_quantity',
        'remark', 'created_by',
    ];
}
