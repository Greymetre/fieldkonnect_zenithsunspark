<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceChargeDivision extends Model
{
    use HasFactory;

    protected $fillable = [ 'active', 'division_name','created_by', 'created_at', 'updated_at'];

    public function getuser()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
