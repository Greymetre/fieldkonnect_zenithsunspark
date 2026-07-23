<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeatUser extends Model
{
    use HasFactory;

    protected $table = 'beat_users';

    protected $fillable = [ 'active', 'beat_id', 'user_id','created_at', 'updated_at'];

    public function beats()
    {
        return $this->belongsTo('App\Models\Beat', 'beat_id', 'id')->select('id','beat_name','description','created_by','city_id','state_id','district_id');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','active','name','mobile','profile_image');
    }
}
