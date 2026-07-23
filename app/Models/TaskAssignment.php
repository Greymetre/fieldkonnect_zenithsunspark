<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    use HasFactory;

    
    protected $fillable = [ 'task_id','user_id' ];

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name');
    }

   
}
