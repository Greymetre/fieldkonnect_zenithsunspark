<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class salesWeightage extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'weightage','division_id','department_id','designation_id','category_name','indicator','annum_target','display_name','financial_year'];


    public function devisions()
    {
        return $this->belongsTo('App\Models\Division', 'division_id', 'id');
    }

}
