<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;
    protected $table = 'designations';

    protected $fillable = [ 'active', 'designation_name','created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];
}
