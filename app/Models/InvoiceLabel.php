<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;;

class InvoiceLabel extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = [
        'invoice_setting_id',
        'name',
        'page_heading',
        'page',
    ];

    /**
     * Each label belongs to an invoice setting.
     */
    public function invoiceSetting()
    {
        return $this->belongsTo(InvoiceSetting::class);
    }

    /**
     * Register media collection for label icon.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('label_icon')->singleFile();
    }
}
