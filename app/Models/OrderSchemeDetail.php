<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSchemeDetail extends Model
{
    use HasFactory;
    protected $table = 'order_scheme_details';

    protected $fillable = [ 'active', 'order_scheme_id', 'product_id', 'category_id', 'subcategory_id', 'points','created_at', 'updated_at'];


    public function products()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }
    public function categories()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id')->select('id','category_name','category_image');
    }
    public function subcategories()
    {
        return $this->belongsTo('App\Models\Subcategory', 'subcategory_id', 'id')->select('id','subcategory_name','subcategory_image');
    }

    public function orderscheme()
    {
        return $this->belongsTo('App\Models\OrderScheme', 'order_scheme_id', 'id');
    }


}
