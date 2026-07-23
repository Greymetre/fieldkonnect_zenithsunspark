<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyTimeline extends Model
{
    use HasFactory;

    protected $fillable = ['warranty_id','created_by','status','remark','created_at','updated_at'];

    public $timestamps = true;

    public function createdByName()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
