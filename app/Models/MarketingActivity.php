<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug' , 'type', 'activity_division'
    ];

    public function setSlugAttribute($value)
    {
        // Convert the slug to snake_case format
        $this->attributes['slug'] = strtolower(str_replace(' ', '_', $value));
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'activity_division', 'id');
    }
}
