<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldData extends Model
{
    use HasFactory;

    protected $table = 'fieldsdata';

    protected $fillable = [ 'field_id', 'value', 'created_at', 'updated_at'];
}
