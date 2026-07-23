<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerAppointmentKyc extends Model
{
    use HasFactory;

    protected $fillable = ['appointment_id','channel_partner','place','concerned_branch','dealer_code','division','proprietary_concern','partnership_firm','ltd_pvt','distribution_channel','created_at', 'updated_at'];

    public function appointment_detail()
    {
        return $this->hasOne(DealerAppointment::class, 'appointment_id', 'id');
    }
}
