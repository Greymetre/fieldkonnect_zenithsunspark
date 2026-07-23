<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTarget extends Model
{
    use HasFactory;

    protected $table = 'sales_targets';
    protected $fillable = [ 'active', 'userid', 'startdate', 'enddate', 'amount', 'achievement', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'userid', 'id')->select('id','name');
    }

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
