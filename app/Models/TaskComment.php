<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;

    
    protected $fillable = [ 'task_id','user_id','comment' ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name');
    }
   
}
