<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseLog extends Model
{
    use HasFactory;

    protected $fillable = ['log_date','expense_id', 'created_by', 'status_type','created_at'];

    public function logusers(){
        return $this->belongsTo(User::class,'created_by','id');
    }
}
