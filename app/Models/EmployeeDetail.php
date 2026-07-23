<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDetail extends Model
{
    use HasFactory;
    protected $table = 'employee_details';

    protected $fillable = [ 'active', 'customer_id','user_id','created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];



    public function employee_detail()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
