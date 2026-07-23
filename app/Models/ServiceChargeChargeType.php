<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceChargeChargeType extends Model
{
    use HasFactory;

    protected $fillable = [ 'active', 'charge_type','created_by', 'created_at', 'updated_at'];

    public function getuser()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
