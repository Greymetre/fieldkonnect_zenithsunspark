<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MspActivityCustomer extends Model
{
    use HasFactory;

     protected $fillable = ['msp_activity_id' , 'customer_id'];

     public function customer()
     {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
     }
}
