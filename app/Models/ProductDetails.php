<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetails extends Model
{
    use HasFactory;

    protected $table = 'product_details';

    protected $fillable = [ 'active', 'detail_title', 'detail_description', 'product_id', 'detail_image', 'mrp', 'price', 'discount', 'max_discount', 'selling_price', 'gst', 'rmc', 'isprimary', 'hsn_code', 'ean_code','stock_qty','production_qty','deleted_at', 'created_at', 'updated_at' ,'budget_for_month' , 'top_sku'];

    public function products()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id')->select('id','product_name','display_name','product_image');
    }
}
