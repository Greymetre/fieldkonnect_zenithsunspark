<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndUser extends Model
{
    use HasFactory;

    protected $fillable = ['customer_name','customer_number', 'customer_email', 'customer_address', 'customer_place', 'state_id', 'district_id', 'city_id', 'customer_pindcode', 'customer_country','customer_state','customer_district','customer_city','status','created_at', 'updated_at'];

    public $timestamps = true;

    public function pincodeDetails()
    {
        return $this->belongsTo(Pincode::class, 'customer_pindcode', 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
