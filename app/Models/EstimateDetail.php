<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'product_id',
        'product_dec',
        'hsn_sac',
        'hsn_sac_type',
        'quantity',
        'mrp',
        'tax',
        'tax_amount',
        'amount',
    ];

    /**
     * Get the estimate that owns the detail.
     */
    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    /**
     * Get the product for this estimate detail.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tax_details()
    {
        return $this->belongsTo(TaxInvoiceTax::class, 'tax', 'id');
    }
}
