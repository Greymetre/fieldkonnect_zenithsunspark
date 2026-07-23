<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderScheme extends Model
{
    use HasFactory;

    protected $table = 'order_schemes';

    protected $fillable = [ 'active', 'scheme_name', 'scheme_description', 'start_date', 'end_date', 'customer_type', 'scheme_type', 'scheme_basedon', 'assign_to', 'branch', 'state', 'customer','minimum', 'maximum','created_at', 'updated_at','repetition','week_days','year_months'];


    public function orderscheme_details()
    {
        return $this->hasMany('App\Models\OrderSchemeDetail','order_scheme_id','id')->where('active','Y')->select('id','order_scheme_id','product_id','category_id','subcategory_id','points');
    }


}
