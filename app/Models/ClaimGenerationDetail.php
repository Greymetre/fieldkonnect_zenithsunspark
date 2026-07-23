<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimGenerationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_generation_id' , 'complaint_id'
    ];

    public function claim(){
        return $this->belongsTo('App\Models\ClaimGeneration' , 'claim_generation_id' , 'id');
    }

    public function complaints(){
        return $this->belongsTo('App\Models\Complaint' , 'complaint_id' , 'id');
    }
}
