<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketIntelligencesField extends Model
{
    use HasFactory;

    protected $fillable = [ 'active', 'field_name', 'field_type', 'label_name', 'placeholder', 'key', 'is_required', 'is_multiple', 'ranking', 'module','created_by', 'created_at', 'updated_at','division_id','input_type'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function fieldsData()
    {
        return $this->hasMany('App\Models\MarketIntelligencesFielddata', 'field_id', 'id')->select('id','value','field_id');
    }
    public function customertypes()
    {
        return $this->belongsTo('App\Models\CustomerType', 'module', 'id');
    }
}
