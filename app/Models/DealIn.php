<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealIn extends Model
{
    use HasFactory;

    protected $table = 'deal_ins';

    protected $fillable = ['customer_id', 'types', 'hcv', 'mav', 'lmv', 'lcv', 'other', 'tractor', 'created_at', 'updated_at'];

    public function customers()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id')->select('id','name', 'first_name', 'last_name','mobile','email','executive_id');
    }
    public function customeraddress()
    {
        return $this->belongsTo('App\Models\Address', 'customer_id', 'customer_id')->select('id','state_id','district_id' ,'city_id');
    }
}
