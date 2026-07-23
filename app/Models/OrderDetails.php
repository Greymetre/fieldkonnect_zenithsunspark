<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = ['active','order_id','product_id','product_detail_id','quantity','shipped_qty','price','discount','gst','gst_amount','discount_amount','tax_amount','line_total','status_id','scheme_name','scheme_discount','scheme_amount','cluster_discount','cluster_amount','deal_discount','deal_amount','distributor_discount','distributor_amount','frieght_discount','frieght_amount','cash_dis','cash_amounts','agri_standard_dis','agri_standard_dis_amounts','scheme_type','scheme_value_type','minimum','maximum','ebd_dis','special_dis','special_amounts','ebd_amount','start_date','end_date','created_at','updated_at'];

    public function products()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function orders()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function productdetails()
    {
        return $this->belongsTo('App\Models\ProductDetails', 'product_detail_id', 'id')->select('id','detail_title','gst');
    }

    public function statusname()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name');
    }
}
