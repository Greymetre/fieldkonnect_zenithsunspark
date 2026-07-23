<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlannedSOP extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'branch_id',
        'planning_month',
        'product_id',
        'plan_next_month',
        'opening_stock',
        'open_order_qty',
        'production_qty',
        'budget_for_month',
        'last_month_sale',
        'last_three_month_avg',
        'last_year_month_sale',
        'sku_unit_price',
        's_op_val',
        'top_sku',
        'dispatch_against_plan',
        'status',
        'created_by',
        'verify_by',
        'view_only',
        'plan_next_month_value',
        'division_id'
    ];

    public function getProduct(){
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function getBranch(){
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    public function primarySale(){
          return $this->hasOne('App\Models\PlannedSopSaleData', 'planned_sop_id');
    }
}
