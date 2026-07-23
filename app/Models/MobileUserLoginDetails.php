<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileUserLoginDetails extends Model
{
    use HasFactory;

    protected $table = 'mobile_user_login_details';

    protected $fillable = [
        'active', 
        'customer_id', 
        'user_id', 
        'app_version', 
        'device_type', 
        'device_name', 
        'unique_id',
        'multi_login',
        'first_login_date', 
        'last_login_date',
        'login_status',
        'app',
        'created_at', 
        'updated_at'
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
