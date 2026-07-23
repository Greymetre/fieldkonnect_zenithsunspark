<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [ 'active','buyer_id','seller_id','executive_id','total_qty','shipped_qty','orderno','order_date','completed_date','estimated_date','total_gst','total_discount','extra_discount','extra_discount_amount','sub_total','grand_total','order_taking','status_id','address_id','suc_del','gst_amount','schme_amount','schme_val','ebd_amount','ebd_discount',	'special_discount','special_amount','cluster_discount','cluster_amount','deal_discount','deal_amount','distributor_discount','distributor_amount','frieght_discount','frieght_amount', 'agri_standard_discount', 'agri_standard_discount_amount','gst5_amt','gst12_amt','gst18_amt','gst28_amt','order_remark','discount_status','created_by', 'updated_by','deleted_at','created_at','updated_at','beatscheduleid',];

    public function message()
    {
        return [
            'buyer_id.required' => 'Enter Buyer',
            'seller_id.required' => 'Enter Seller',
            'orderno.required' => 'Enter Invoice No',
            'order_date.required' => 'Enter Invoice Date',
            'grand_total.required' => 'Enter Invoice Amount',
        ];
    }

    public function insertrules()
    {
        return [
            'buyer_id' => 'required|exists:customers,id',
            'seller_id' => 'required|exists:customers,id',
        ];
    }
    public function updaterules($id ='')
    {
        return [
            'buyer_id' => 'required|exists:customers,id',
            'seller_id' => 'required|exists:customers,id',
        ];
    }

    public function save_data($request)
    {
        try
        {
            
            $created_at = getcurentDateTime();
            $request['orderno'] = !empty($request['orderno']) ? $request['orderno'] : date('Y').'_'.$request['seller_id'].'_'.autoIncrementId('Order','id');

            if( $order_id = Order::insertGetId([
                'active' => 'Y',

                'buyer_id' => isset($request['buyer_id'])? $request['buyer_id']:null,
                'seller_id' => isset($request['seller_id'])? $request['seller_id']:null,

                // 'buyer_id' => isset($request['seller_id'])? $request['seller_id']:null,
                // 'seller_id' => $buyer,
                 'total_qty' => 0,
                 //'total_qty' => isset($request['quantity'])? $request['quantity']:0,
                'executive_id' => isset($request['executive_id'])? $request['executive_id']:null,
                'shipped_qty' => 0,
                'orderno' => isset($request['orderno'])? $request['orderno'] :'',
                'order_date' => isset($request['order_date'])? $request['order_date']:getcurentDate(),
                'total_gst' => isset($request['total_gst'])? $request['total_gst']:0.00,
                'sub_total' => isset($request['sub_total'])? $request['sub_total']:0.00,
                'grand_total' => isset($request['grand_total'])?  $request['grand_total']:0.00,
                'order_taking' => isset($request['order_taking'])?  $request['order_taking']:'MobileApp',
                'suc_del' => isset($request['suc_del'])?  $request['suc_del']:'',  
                'beatscheduleid' => isset($request['beatscheduleid']) ? $request['beatscheduleid'] :null,   
                'gst_amount' => isset($request['gst_amount']) ? $request['gst_amount'] :null,   
                'schme_val' => isset($request['schme_val']) ? $request['schme_val'] :null,   
                'schme_amount' => isset($request['schme_amount']) ? $request['schme_amount'] :null,   
                'ebd_discount' => isset($request['ebd_discount']) ? $request['ebd_discount'] :null,   
                'ebd_amount' => isset($request['ebd_amount']) ? $request['ebd_amount'] :null,   
                'special_discount' => isset($request['special_discount']) ? $request['special_discount'] :null,   
                'special_amount' => isset($request['special_amount']) ? $request['special_amount'] :null,   
                'cluster_discount' => isset($request['cluster_discount']) ? $request['cluster_discount'] :null,   
                'cluster_amount' => isset($request['cluster_amount']) ? $request['cluster_amount'] :null,   
                'deal_discount' => isset($request['deal_discount']) ? $request['deal_discount'] :null,   
                'deal_amount' => isset($request['deal_amount']) ? $request['deal_amount'] :null,   
                'distributor_discount' => isset($request['distributor_discount']) ? $request['distributor_discount'] :null,   
                'distributor_amount' => isset($request['distributor_amount']) ? $request['distributor_amount'] :null,   
                'frieght_discount' => isset($request['frieght_discount']) ? $request['frieght_discount'] :null,   
                'frieght_amount' => isset($request['frieght_amount']) ? $request['frieght_amount'] :null,   
                'cash_discount' => isset($request['cash_discount']) ? $request['cash_discount'] : 0.00,   
                'cash_amount' => (isset($request['cash_amount']) && $request['cash_amount'] != NULL) ? $request['cash_amount'] : 0.00,   
                'total_discount' => (isset($request['total_discount']) && $request['total_discount'] != NULL) ? $request['total_discount'] : 0.00,   
                'total_amount' => (isset($request['total_amount']) && $request['total_amount'] != NULL) ? $request['total_amount'] : 0.00,   
                'product_cat_id' => isset($request['product_cat_id']) ? $request['product_cat_id'] :null,   
                'dod_discount' => isset($request['dod_discount']) ? $request['dod_discount'] :null,   
                'special_distribution_discount' => isset($request['special_distribution_discount']) ? $request['special_distribution_discount'] :null,   
                'distribution_margin_discount' => isset($request['distribution_margin_discount']) ? $request['distribution_margin_discount'] :null,   
                'total_fan_discount' => isset($request['total_fan_discount']) ? $request['total_fan_discount'] :null,   
                'total_fan_discount_amount' => isset($request['total_fan_discount_amount']) ? $request['total_fan_discount_amount'] :null,
                'dod_discount_amount' => (isset($request['dod_discount_amount']) && $request['dod_discount_amount'] != NULL) ? $request['dod_discount_amount'] : 0.00,
                'special_distribution_discount_amount' => (isset($request['special_distribution_discount_amount']) && $request['special_distribution_discount_amount'] != NULL) ? $request['special_distribution_discount_amount'] :0.00,
                'distribution_margin_discount_amount' => (isset($request['distribution_margin_discount_amount']) && $request['distribution_margin_discount_amount'] != NULL) ? $request['distribution_margin_discount_amount'] :0.00,
                'fan_extra_discount' => isset($request['fan_extra_discount']) ? $request['fan_extra_discount'] :0,
                'fan_extra_discount_amount' => (isset($request['fan_extra_discount_amount']) && $request['fan_extra_discount_amount'] != NULL) ? $request['fan_extra_discount_amount'] :0.00,
                'agri_standard_discount' => isset($request['agri_standard_discount']) ? $request['agri_standard_discount'] :"0.00",
                'agri_standard_discount_amount' => isset($request['agri_standard_discount_amount']) ? $request['agri_standard_discount_amount'] : "0.00",
                'advance' => isset($request['advance']) ? $request['advance'] : "0.00",
                'gst5_amt' => isset($request['gst5_amt']) ? $request['gst5_amt'] :0.00,   
                'gst12_amt' => isset($request['gst12_amt']) ? $request['gst12_amt'] :0.00,   
                'gst18_amt' => isset($request['gst18_amt']) ? $request['gst18_amt'] :0.00,   
                'gst28_amt' => isset($request['gst28_amt']) ? $request['gst28_amt'] :0.00,   
                'created_by' => isset($request['created_by']) ? $request['created_by'] :null,     
                'order_remark' => isset($request['order_remark']) ? $request['order_remark'] :null,     
                'created_at' => $created_at ,
                'updated_at' => $created_at
            ]) )
            {
                return $response = array('status' => 'success', 'message' => 'Sales Insert Successfully','order_id' => $order_id);
            }
            return $response = array('status' => 'error', 'message' => 'Error in Sales Store');
        }
        catch(\Exception $e)
        {
            return $response = array('status' => 'error', 'message' => $e->getMessage());
        }
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name','profile_image','branch_id');
    }
    public function updatedbyname()
    {
        return $this->belongsTo('App\Models\User', 'updated_by', 'id')->select('id','name','profile_image');
    }

    public function sellers()
    {
        return $this->belongsTo('App\Models\Customers', 'seller_id', 'id')->select('id','name', 'first_name', 'last_name','mobile','email','sap_code');
    }
    public function buyers()
    {
        return $this->belongsTo('App\Models\Customers', 'buyer_id', 'id')->select('id','name', 'first_name', 'last_name','mobile','email','customertype','executive_id');
    }

    public function customeraddress()
    {
        return $this->belongsTo('App\Models\Address', 'buyer_id', 'customer_id')->select('id','address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id','district_id' ,'city_id', 'pincode_id','zipcode');
    }

    public function orderdetails()
    {
        // return $this->hasMany('App\Models\OrderDetails', 'order_id', 'id')->select('id','order_id', 'product_id', 'product_detail_id','quantity', 'shipped_qty', 'price', 'tax_amount', 'line_total', 'status_id');

     return $this->hasMany('App\Models\OrderDetails', 'order_id', 'id');


    }

    public function address()
    {
       return $this->belongsTo('App\Models\Address', 'address_id', 'id')->select('id','address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id','district_id' ,'city_id', 'pincode_id');
    }

    public function statename()
    {
        return $this->belongsTo('App\Models\State', 'state_id', 'id')->select('id', 'state_name');
    }

    public function statusname()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name','display_name');
    }


    public function getuserdetails()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function getsalesdetail()
    {
        return $this->hasOne('App\Models\Sales', 'order_id', 'id')->select('invoice_no','invoice_date');
    }
}
