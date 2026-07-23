<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
    use HasFactory;

    // protected $fillable = [ 'user_id', 'weightage_id', 'year', 'target', 'achivment', 'acual', 'rating', 'rating_by', 'appraisal_type', 'appraisal_session', 'remark','grade'];

    // public function users()
    // {
    //     return $this->belongsTo('App\Models\User', 'user_id', 'id');
    // }

    // public function rating_by_user()
    // {
    //     return $this->belongsTo('App\Models\User', 'rating_by', 'id');
    // }

    // public function sales_weightage()
    // {
    //     return $this->belongsTo('App\Models\salesWeightage', 'weightage_id', 'id');
    // }

    protected $fillable = [ 'user_id', 'weightage_id', 'year', 'target', 'achivment', 'acual', 'rating', 'rating_by', 'appraisal_type', 'appraisal_session', 'remark','grade','kra'];

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function rating_by_user()
    {
        return $this->belongsTo('App\Models\User', 'rating_by', 'id');
    }

    public function sales_weightage()
    {
        return $this->belongsTo('App\Models\salesWeightage', 'weightage_id', 'id');
    }


}
