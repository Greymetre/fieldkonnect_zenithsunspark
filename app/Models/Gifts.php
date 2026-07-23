<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gifts extends Model
{
    use HasFactory;

    protected $table = 'gifts';

    protected $fillable = [ 'active', 'product_name', 'display_name', 'description', 'product_image', 'mrp', 'price', 'points', 'subcategory_id', 'category_id', 'brand_id', 'customer_type_id', 'unit_id', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

   public function categories()
    {
        return $this->belongsTo('App\Models\GiftCategory', 'category_id', 'id')->select('id','category_name','category_image');
    }

    public function subcategories()
    {
        return $this->belongsTo('App\Models\GiftSubcategory', 'subcategory_id', 'id')->select('id','subcategory_name','subcategory_image');
    }

    public function brands()
    {
        return $this->belongsTo('App\Models\GiftBrand', 'brand_id', 'id')->select('id','brand_name','brand_image');
    }

    public function models()
    {
        return $this->belongsTo('App\Models\GiftModel', 'unit_id', 'id')->select('id','model_name');
    }
    public function customer_types()
    {
        return $this->belongsTo('App\Models\CustomerType', 'customer_type_id', 'id')->select('id','customertype_name');
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
