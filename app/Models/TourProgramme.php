<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourProgramme extends Model
{
    use HasFactory;

    protected $table = 'tour_programmes';

    protected $fillable = [ 'date', 'userid', 'town', 'objectives', 'type', 'status', 'deleted_at', 'created_at', 'updated_at'];

    public function userinfo()
    {
        // return $this->belongsTo('App\Models\User', 'userid', 'id')->select('id','name');
        return $this->belongsTo('App\Models\User', 'userid', 'id');
    }

    public function tourdetails()
    {
        return $this->hasMany('App\Models\TourDetail', 'tourid', 'id');
    }
}
