<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'customer_id',
        'place_of_supply',
        'invoice_no',
        'order_no',
        'invoice_date',
        'payment_term',
        'due_date',
        'user_id',
        'sub_total',
        'discount_type',
        'discount',
        'discount_amount',
        'tds',
        'tds_amount',
        'adjustment',
        'grand_total',
        'customer_notes',
        't_c',
        'paid_amount',
        'status_id',
    ];

    /**
     * Relationships
     */
    public function term()
    {
        return $this->belongsTo(PaymentTerm::class, 'payment_term', 'id');
    }
    public function tds_details()
    {
        return $this->belongsTo(TaxInvoiceTds::class, 'tds', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id', 'id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('invoice_files');
    }

    public function state()
    {
        return $this->belongsTo(\App\Models\State::class, 'place_of_supply');
    }
}
