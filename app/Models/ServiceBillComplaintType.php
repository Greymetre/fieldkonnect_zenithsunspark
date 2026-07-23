<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBillComplaintType extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_bill_complaint_type_name'
    ];

    public function service_complaint_reasons()
    {
        return $this->hasMany(ServiceComplaintReason::class, 'service_bill_complaint_id');
    }

    public function service_group_complaints()
    {
        return $this->hasOne(ServiceGroupComplaint::class, 'service_bill_complaint_id');
    }
}
