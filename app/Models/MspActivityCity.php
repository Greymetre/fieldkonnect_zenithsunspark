<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MspActivityCity extends Model
{
    use HasFactory;

    protected $fillable = ['msp_activity_id' , 'city_id'];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
