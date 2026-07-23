<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
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
     * Relationships
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function tax_details()
    {
        return $this->belongsTo(TaxInvoiceTax::class, 'tax', 'id');
    }
}
