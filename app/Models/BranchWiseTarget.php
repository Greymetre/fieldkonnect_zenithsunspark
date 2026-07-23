<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchWiseTarget extends Model
{
    use HasFactory;

    protected $table = 'branchwise_targets';

    protected $fillable = ['user_id','user_name','branch_id','branch_name','div_id','division_name','type','month','year','target','achievement','created_at', 'updated_at' ];

    public $timestamps = true;

    public function user()  {
        return $this->belongsTo(User::class);
    }

    public function branch()  {
        return $this->belongsTo(Branch::class);
    }

    public function division()  {
        return $this->belongsTo(Division::class);
    }

}
