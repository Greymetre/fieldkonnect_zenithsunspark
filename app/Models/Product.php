<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = ['active', 'ranking' ,'product_name','product_code','new_group','sub_group','expiry_interval','expiry_interval_preiod', 'display_name', 'description', 'subcategory_id', 'category_id', 'brand_id', 'product_image', 'unit_id', 'hsn_sac', 'hsn_sac_no', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at', 'specification', 'part_no', 'product_no', 'model_no','phase','suc_del','sap_code' , 'branch_id'];
    public function categories()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id')->select('id','category_name','category_image');
    }

    public function opening_stock()
    {
        return $this->hasOne('App\Models\OpeningStock', 'product_id', 'id')->select('id', 'product_id', 'opening_stocks');
    }


    public function subcategories()
    {
        return $this->belongsTo('App\Models\Subcategory', 'subcategory_id', 'id')->select('id','subcategory_name','subcategory_image' , 'service_category_id');
    }

    public function brands()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id', 'id')->select('id','brand_name','brand_image');
    }

    public function unitmeasures()
    {
        return $this->belongsTo('App\Models\UnitMeasure', 'unit_id', 'id')->select('id','unit_name','unit_code');
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function productdetails()
    {
        return $this->hasMany('App\Models\ProductDetails', 'product_id', 'id')->select('id','product_id','detail_title','detail_description','mrp','price','selling_price','gst','isprimary','hsn_code','ean_code','discount','max_discount','budget_for_month','top_sku');
    }

    public function productpriceinfo()
    {
        return $this->belongsTo('App\Models\ProductDetails', 'id', 'product_id');
    }

    public function serial_numbers()
    {
        return $this->hasMany('App\Models\Services', 'product_code', 'product_code')->where('branch_code', 'HO0000');
    }

    public function getSchemeDetail(){
      return $this->hasOne('App\Models\OrderSchemeDetail', 'product_id', 'id');
    }

}
