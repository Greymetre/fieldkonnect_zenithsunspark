<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftModel extends Model
{
    use HasFactory;

    protected $fillable = [ 'active', 'ranking' ,'model_name', 'model_image','sub_category_id', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at' ];


    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
    public function subCategories()
    {
        return $this->belongsTo('App\Models\GiftSubcategory', 'sub_category_id', 'id')->select('id','subcategory_name','subcategory_image');
    }
}
