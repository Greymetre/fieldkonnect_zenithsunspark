<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBillProductDetails extends Model
{
    use HasFactory;

    protected $fillable = ['service_bill_id','service_type','product_id','quantity','distance','appreciation','price','subtotal', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function service_bill()
    {
        return $this->belongsTo(ServiceBill::class, 'service_bill_id', 'id');
    }

    public function service_type_details()
    {
        return $this->belongsTo(ServiceChargeChargeType::class, 'service_type', 'id');
    }

    public function product()
    {
        return $this->belongsTo(ServiceChargeProducts::class, 'product_id', 'id');
    }

}
