<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeftRedemptionDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'redemption_id',
        'utr_number',
        'tds',
        'remark'
    ];

    public $timestamps = true;

    public function redemption(){
        return $this->belongsTo(Redemption::class, 'redemption_id', 'id');
    }
}
