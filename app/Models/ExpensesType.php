<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensesType extends Model
{
    use HasFactory;
    protected $table = 'expenses_types';

    protected $fillable = [ 'name', 'rate', 'is_active', 'allowance_type_id', 'created_at', 'updated_at','payroll_id'];
}
