<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitType extends Model
{
    use HasFactory;

    protected $table = 'visit_types';

    protected $fillable = ['active', 'type_name','created_by','deleted_at', 'created_at', 'updated_at', 'next_visit'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
