<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'assigned_to',
        'created_by',
        'description',
        'date',
        'time',
        'priority',
        'status',
        'open_date',
        'due_date',
        'close_date',
        'remark'
    ];

    
    public function lead(){
        return $this->belongsTo(Lead::class);
    }

    public function assignUser(){
        return $this->belongsTo(User::class,'assigned_to');
    }


    public function createdby(){
        return $this->belongsTo(User::class,'created_by');
    }
}
