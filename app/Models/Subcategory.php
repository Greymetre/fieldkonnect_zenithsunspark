<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $table = 'subcategories';

    protected $fillable = [ 'active', 'ranking' ,'subcategory_name', 'subcategory_image', 'sap_code','category_id', 'service_category_id', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at' ];


    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
    public function categories()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id')->select('id','category_name','category_image');
    }
}
