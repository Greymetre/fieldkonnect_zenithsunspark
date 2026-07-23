<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxInvoiceTds extends Model
{
    use HasFactory;

    protected $table = 'tax_invoice_tds';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tax_name',
        'rate',
        'section',
    ];
}
