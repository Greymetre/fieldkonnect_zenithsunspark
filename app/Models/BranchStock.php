<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchStock extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'warehouse_id', 'branch_name', 'division_id', 'amount', 'days', 'year', 'quarter', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    public function division()
    {
        return $this->belongsTo('App\Models\Division', 'division_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\WareHouse', 'warehouse_id', 'id');
    }
}
