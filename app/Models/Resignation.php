<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
    use HasFactory;

    protected $fillable = ['submit_date', 'division_id', 'branch_id', 'user_id', 'employee_code', 'notice', 'date_of_joining', 'last_working_date', 'cug_sim_no', 'reason', 'persoanla_email', 'persoanla_mobile', 'address', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function check_list()
    {
        return $this->hasOne(ResignationCheckList::class, 'resignation_id', 'id');
    }
}
