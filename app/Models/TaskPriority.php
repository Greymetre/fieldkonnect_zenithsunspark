<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskPriority extends Model
{
    use HasFactory;

    
    protected $fillable = [ 'active','name','created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at' ];


    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

   
}
