<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';

    protected $fillable = [ 'active', 'status_name', 'display_name', 'status_message', 'module', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
