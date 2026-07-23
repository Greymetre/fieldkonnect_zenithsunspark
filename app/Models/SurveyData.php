<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyData extends Model
{
    use HasFactory;

    protected $table = 'survey_data';

    protected $fillable = [ 'field_id', 'customer_id', 'value', 'created_by', 'created_at', 'updated_at' ];

    public function customers()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id')->select('id','name', 'first_name', 'last_name','mobile','email');
    }

    public function fields()
    {
        return $this->belongsTo('App\Models\Field', 'field_id', 'id')->select('id','field_name','label_name');
    }

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
