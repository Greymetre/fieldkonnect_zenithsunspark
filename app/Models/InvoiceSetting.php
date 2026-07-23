<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceSetting extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = [
        'invoice_logo',
        'invoice_esign',
        'company_name',
        'gst_number',
        'pan_number',
    ];

    /**
     * A setting can have many labels.
     */
    public function labels()
    {
        return $this->hasMany(InvoiceLabel::class);
    }

    /**
     * Register media collections for logo & e-sign.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('invoice_logo')->singleFile();
        $this->addMediaCollection('invoice_esign')->singleFile();
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'model');
    }
}
