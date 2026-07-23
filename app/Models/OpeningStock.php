<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningStock extends Model
{
    use HasFactory;

    protected $fillable = [
         'item_code', 'item_description','item_group','ware_house_name' , 'branch_id' , 'opening_stocks' , 'open_order_qty'
    ];

    // public function branch()
    // {
    //     return $this->belongsTo(Branch::class, 'branch_id');
    // }

    public function product()
    {
        return Product::where(function($query) {
            $query->where('product_code', $this->item_code)
                  ->orWhere('sap_code', $this->item_code);
        })->whereHas('subcategories', function($query) {
            $query->where('subcategory_name', $this->item_group);
        })->first();
    }

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'ware_houses_id');
    }
}
