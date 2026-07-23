<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentTaxInvoiceNo extends Model
{
    use HasFactory;

    protected $table = 'current_tax_invoices_no';

    protected $fillable = [
        'prefix',
        'next_number',
    ];
}
