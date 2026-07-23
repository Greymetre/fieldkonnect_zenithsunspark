<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_center_id',
        'month',
        'year',
        'claim_number',
        'claim_amount',
        'courier_details',
        'courier_date',
        'asc_bill_no',
        'asc_bill_date',
        'asc_bill_amount',
        'claim_sattlement_details',
        'submitted_by_se',
        'claim_approved',
        'claim_done',
        'claim_date',
    ];

    public function service_center_details()
    {
        return $this->belongsTo(Customers::class, 'service_center_id', 'id');
    }

    public function claim_generation_details()
    {
        return $this->hasMany(ClaimGenerationDetail::class, 'claim_generation_id', 'id');
    }


    public function getCourierDateAttribute($value)
    {
        try {
            return cretaDateForFront($value);
        } catch (\Exception $e) {
            return ''; // Return null if parsing fails
        }
    }

    public function getAscBillDateAttribute($value)
    {
        try {
            return cretaDateForFront($value);
        } catch (\Exception $e) {
            return ''; // Return null if parsing fails
        }
    }

}
