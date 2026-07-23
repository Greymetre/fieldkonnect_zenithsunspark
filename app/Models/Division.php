<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $table = 'divisions';

    protected $fillable = [ 'active', 'division_name','created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

    public function getuser()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

}
