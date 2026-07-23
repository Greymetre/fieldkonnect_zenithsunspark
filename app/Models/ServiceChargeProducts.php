<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceChargeProducts extends Model
{
    use HasFactory;

    protected $fillable = ['active', 'charge_type_id', 'product_name','division_id', 'category_id', 'price', 'other_charge', 'created_by', 'created_at', 'updated_at'];

    public function charge_type()
    {
        return $this->belongsTo('App\Models\ServiceChargeChargeType', 'charge_type_id', 'id');
    }
    public function division()
    {
        return $this->belongsTo('App\Models\ServiceChargeDivision', 'division_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo('App\Models\ServiceChargeCategories', 'category_id', 'id');
    }
}
