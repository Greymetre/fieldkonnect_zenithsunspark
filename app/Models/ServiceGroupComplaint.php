<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceGroupComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'subcategory_id', 
        'service_bill_complaint_id'
    ];

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id');
    }

    public function service_bill_complaint_type()
    {
        return $this->belongsTo(ServiceBillComplaintType::class, 'service_bill_complaint_id');
    }
}
