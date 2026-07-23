<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    protected $fillable = ['warehouse_id', 'product_id', 'quantity'];

    protected $casts = ['quantity' => 'decimal:3'];
}
