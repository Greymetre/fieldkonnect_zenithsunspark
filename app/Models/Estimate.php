<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Estimate extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'customer_id',
        'place_of_supply',
        'estimate_no',
        'order_no',
        'estimate_date',
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
        'status',
        'invoice_id',
    ];

    public function term()
    {
        return $this->belongsTo(PaymentTerm::class, 'payment_term', 'id');
    }

    public function tds_details()
    {
        return $this->belongsTo(TaxInvoiceTds::class, 'tds', 'id');
    }

    /**
     * Get the customer that owns the estimate.
     */
    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }

    /**
     * Get the user who created the estimate.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the details for the estimate.
     */
    public function details()
    {
        return $this->hasMany(EstimateDetail::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('estimate_files');
    }

    public function state()
    {
        return $this->belongsTo(\App\Models\State::class, 'place_of_supply');
    }

    public function custom_pdf_values()
    {
        return $this->hasMany(CustomPdfValue::class);
    }
}
