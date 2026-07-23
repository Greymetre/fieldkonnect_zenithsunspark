<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MspActivity extends Model
{
    use HasFactory;

    protected $table = 'msp_activities';

    protected $fillable = [
        'emp_code',
        'fyear',
        'activity_date',
        'month',
        'msp_count',
        'created_at',
        'updated_at',
        'activity_type'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'emp_code', 'employee_codes');
    }

    public function activityType()
    {
        return $this->belongsTo(MarketingActivity::class, 'activity_type', 'id');
    }

    public function cities()
    {
        return $this->hasMany(MspActivityCity::class, 'msp_activity_id', 'id');
    }

    public function customer()
    {
        return $this->hasMany(MspActivityCustomer::class, 'msp_activity_id', 'id');
    }
}
