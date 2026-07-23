<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [ 'active','title','key_name', 'value', 'module', 'created_at', 'updated_at' ];
}
