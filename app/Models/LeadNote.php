<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'note',
        'created_by',
    ];

    
    public function lead(){
        return $this->belongsTo(Lead::class);
    }

    public function createdby(){
        return $this->belongsTo(User::class, 'created_by');
    }
}
