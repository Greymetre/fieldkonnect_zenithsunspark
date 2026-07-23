<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketIntelligenceServeyData extends Model
{
    use HasFactory;

    protected $table = 'market_intelligence_servey_data';

    protected $fillable = [
        'servey_id',
        'key',
        'value',
    ];

    public function servey()
    {
        return $this->hasOne(MarketIntelligenceServey::class, 'servey_id', 'id');
    }
}
