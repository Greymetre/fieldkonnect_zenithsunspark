<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketIntelligencesFielddata extends Model
{
    use HasFactory;

    protected $fillable = [ 'field_id', 'value', 'created_at', 'updated_at'];
}
