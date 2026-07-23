<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;

    protected $fillable = ['product_code', 'invoice_no','invoice_date' ,'branch_code','party_name', 'customer_id', 'bp_code','product_name', 'product_description', 'product_store','qty','group','new_group', 'serial_no', 'narration', 'created_by', 'created_at', 'updated_at'];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_code', 'product_code');
    }

    public function warrantyDetails()
    {
        return $this->belongsTo('App\Models\WarrantyActivation', 'serial_no', 'product_serail_number');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_code', 'branch_code');
    }

}
