<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';

    protected $fillable = ['active', 'branch_name', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at', 'branch_code','warehouse_id'];


    public function getuser()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id', 'name');
    }

    public function getBranchUsers()
    {
        return $this->hasMany('App\Models\User', 'branch_id', 'id');
    }

    public function getTotalGrossSalary()
    {
        return $this->getBranchUsers()->with('userinfo')->get()->sum(function ($user) {
            return $user->userinfo->gross_salary_monthly ?? 0;
        });
    }

    public function getTotalGrossSalarySales()
{
    return $this->getBranchUsers()
        ->with('userinfo')
        ->get()
        ->sum(function ($user) {
            if (in_array($user->designation_id, [59, 1, 5, 11])) {
                return $user->userinfo->gross_salary_monthly ?? 0;
            }
            return 0;
        });
}

}
