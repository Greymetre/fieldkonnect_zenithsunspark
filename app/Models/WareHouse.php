<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WareHouse extends Model
{
    use HasFactory;
    
    protected $fillable = [ 'warehouse_code', 'warehouse_name', 'created_at', 'updated_at'];
}
