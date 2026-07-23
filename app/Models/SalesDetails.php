<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDetails extends Model
{
    use HasFactory;

    protected $table = 'sales_details';

    protected $fillable = [ 'active', 'sales_id', 'product_id','product_detail_id', 'quantity', 'shipped_qty', 'price', 'tax_amount', 'line_total', 'status_id', 'created_at', 'updated_at'];

    public function products()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id')->select('id','product_name','display_name','product_image', 'brand_id', 'model_no');
    }

    public function sales()
    {
        return $this->belongsTo('App\Models\Sales', 'sales_id', 'id');
    }

    public function productdetails()
    {
        return $this->belongsTo('App\Models\ProductDetails', 'product_detail_id', 'id')->select('id','detail_title');
    }
}
