<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $table = 'leaves';

    protected $fillable = [ 'active', 'user_id', 'from_date', 'to_date', 'type', 'bal_type', 'reason', 'created_by', 'created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
    

}
