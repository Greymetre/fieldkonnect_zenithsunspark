<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadOpportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'assigned_to',
        'lead_contact_id',
        'created_by',
        'amount',
        'type',
        'estimated_close_date',
        'confidence',
        'note',
        'status'
    ];

    
    public function lead(){
        return $this->belongsTo(Lead::class);
    }

    public function leadContact(){
        return $this->belongsTo(LeadContact::class, 'lead_contact_id');
    }

    public function createdby(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function status_is(){
        return $this->belongsTo(OpportunitieStatus::class,'status');
    }

    public function assignUser(){
        return $this->belongsTo(User::class,'assigned_to');
    }
}
