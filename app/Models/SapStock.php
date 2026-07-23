<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SapStock extends Model
{
    use HasFactory;

    protected $fillable = ['product_sap_code', 'product_description', 'product_category_sap_code', 'product_category_name', 'warehouse_code', 'warehouse_name', 'instock_qty', 'value', 'itm_remarks', 'created_at', 'updated_at'];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_sap_code', 'sap_code');
    }

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'warehouse_code', 'warehouse_code');
    }

    public function product_category()
    {
        return $this->belongsTo(Category::class, 'product_category_sap_code', 'sap_code');
    }
}
