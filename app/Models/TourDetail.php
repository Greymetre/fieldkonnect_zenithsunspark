<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourDetail extends Model
{
    use HasFactory;

    protected $table = 'tour_details';

    protected $fillable = [ 'tourid', 'city_id', 'visited_date', 'visited_cityid', 'last_visited', 'created_at', 'updated_at'];

    public function tourinfo()
    {
        return $this->belongsTo('App\Models\TourProgramme', 'tourid', 'id')->select('id','date', 'userid', 'town', 'objectives');
    }

    public function cityname()
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'id')->select('id','city_name','district_id','grade');
    }

    public function visitedcities()
    {
        return $this->belongsTo('App\Models\City', 'visited_cityid', 'id')->select('id','city_name','district_id','grade');
    }
}
