<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gamification extends Model
{
    use HasFactory;

    protected $table = 'gamifications';

    protected $fillable = [ 'user_id', 'customer_id', 'type', 'points','created_at', 'updated_at'];

    
    public function customerinfo()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id')->select('id','name');
    }

    public function userinfo()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name','profile_image');
    }

}
