<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrimarySchemeDetail extends Model
{
    use HasFactory;
    protected $table = 'primary_schemes_details';

    protected $fillable = [ 'active', 'primary_scheme_id', 'product_id', 'category_id', 'subcategory_id', 'groups','min','max', 'points','created_at', 'updated_at'];
    public $timestamps = true; 


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

    public function primaryscheme()
    {
        return $this->belongsTo('App\Models\PrimaryScheme', 'primary_scheme_id', 'id');
    }


}
