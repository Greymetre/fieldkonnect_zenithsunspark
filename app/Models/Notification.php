<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [ 'active', 'type', 'data','customer_id','user_id', 'deleted_at','created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name');
    }
}
