<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTargetCustomers extends Model
{
    use HasFactory;

    protected $table = 'salestargetcustomers';

    protected $fillable = [ 'customer_id', 'branch_id', 'div_id','type','month' ,'year', 'target', 'achievement',
            'achievement_percent','created_at', 'updated_at' ];

    public $timestamps = true;

    public function customer()  {
        return $this->belongsTo(Customers::class);
    }

}
