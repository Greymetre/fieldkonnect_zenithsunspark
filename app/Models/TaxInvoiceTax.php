<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxInvoiceTax extends Model
{
    use HasFactory;

    protected $table = 'tax_invoice_tax';

    protected $fillable = [
        'tax_name',
        'tax_percentage',
    ];
}
