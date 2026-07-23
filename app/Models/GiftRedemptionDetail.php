<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftRedemptionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'redemption_id',
        'redemption_no',
        'purchase_rate',
        'gst',
        'total_purchase',
        'purchase_invoice_no',
        'purchase_return_no',
        'client_invoice_no'
    ];

    public $timestamps = true;

    public function redemption(){
        return $this->belongsTo(Redemption::class, 'redemption_id', 'id');
    }
}
