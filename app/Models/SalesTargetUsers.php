<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTargetUsers extends Model
{
    use HasFactory;

    protected $table = 'salestargetusers';

    protected $fillable = [ 'user_id', 'branch_id', 'type', 'month' ,'year', 'target', 'achievement','achievement_percent','created_at', 'updated_at' ];

    public $timestamps = true;

    public function user()  {
        return $this->belongsTo(User::class);
    }

    public function branch()  {
        return $this->belongsTo(Branch::class);
    }

}
