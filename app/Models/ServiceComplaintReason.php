<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceComplaintReason extends Model
{
    use HasFactory;

    protected $fillable = [
           'service_bill_complaint_id' , 'service_complaint_reasons'
    ];

    public function service_bill_complaint_type(){
        return $this->belongsTo(ServiceBillComplaintType::class, 'service_bill_complaint_id' , 'id');
    }
}
