<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrimarySales extends Model
{
    use HasFactory;

    protected $table = 'primary_sales';

    protected $fillable = [
        'active',
        'invoiceno',
        'invoice_date',
        'month',
        'division',
        'bp_code',
        'dealer',
        'customer_id',
        'branch',
        'city',
        'state',
        'final_branch',
        'branch_id',
        'sales_person',
        'emp_code',
        'model_name',
        'product_name',
        'group_code',
        'itm_group_name',
        'quantity',
        'lp',
        'rate',
        'net_amount',
        'tax_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'tax_code',
        'sinv_gst_amt',
        'total_amount',
        'new_group',
        'store_name',
        'group_name',
        'new_group_name',
        'product_id',
        'sap_code',
        'remarks',
        'new_product',
        'new_dealer',
        'serial_no',
        'item_no',
        'group_1',
        'group_2',
        'group_3',
        'group_4',
        'sell_from',
        'created_at',
        'updated_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'emp_code', 'employee_codes');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
