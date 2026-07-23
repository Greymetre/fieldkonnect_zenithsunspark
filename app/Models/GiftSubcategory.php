<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftSubcategory extends Model
{
    use HasFactory;

    protected $table = 'giftsubcategories';

    protected $fillable = [ 'active', 'ranking' ,'subcategory_name', 'subcategory_image','category_id', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at' ];


    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
    public function categories()
    {
        return $this->belongsTo('App\Models\GiftCategory', 'category_id', 'id')->select('id','category_name','category_image');
    }
}
