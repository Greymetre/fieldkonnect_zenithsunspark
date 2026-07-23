<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'message',
        'created_by',
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
