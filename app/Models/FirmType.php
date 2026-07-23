<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmType extends Model
{
    use HasFactory;

    protected $table = 'firm_types';

    protected $fillable = [ 'active', 'firmtype_name', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
