<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPdfValue extends Model
{
    use HasFactory;

    protected $table = 'custom_pdf_values';

    protected $fillable = [
        'estimate_id',
        'label_id',
        'value',
    ];

    /**
     * Relation with Estimate
     */
    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    /**
     * Relation with Label (or InvoiceLabel if that’s the actual table)
     */
    public function label()
    {
        return $this->belongsTo(InvoiceLabel::class, 'label_id', 'id');
    }
}
