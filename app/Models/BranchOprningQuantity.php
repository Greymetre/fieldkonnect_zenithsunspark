<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchOprningQuantity extends Model
{
    use HasFactory;

    protected $table = 'branch_oprning_quantities';

    protected $fillable = [
        'item_code',
        'item_description',
        'item_group',
        'branch_id',
        'qty_month',
        'open_order_qty',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
