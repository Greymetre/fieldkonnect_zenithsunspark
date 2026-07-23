<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [ 'active', 'buyer_id', 'seller_id', 'order_id', 'total_qty', 'shipped_qty', 'orderno', 'fiscal_year', 'sales_no', 'invoice_no', 'invoice_date','transport_name','lr_no','dispatch_date', 'transport_details', 'total_gst', 'sub_total', 'grand_total', 'paid_amount','payment_status','description', 'status_id', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

    public function message()
    {
        return [
            'buyer_id.required' => 'Enter Buyer',
            'seller_id.required' => 'Enter Seller',
            'order_id.required' => 'Enter Order ID',
            'invoice_no.required' => 'Enter Invoice No',
            'invoice_date.required' => 'Enter Invoice Date',
            'grand_total.required' => 'Enter Invoice Amount',
            'fiscal_year.required' => 'Enter Financial Year',
            'sales_no.required' => 'Enter Sales No',
            'dispatch_date.required' => 'Enter Dispatched Date',
            'lr_no.required'         => 'Enter LR Number',
        ];
    }

    public function insertrules()
    {
        return [
            'buyer_id' => 'required|exists:customers,id',
            'seller_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'invoice_no' => 'required',
            'invoice_date' => 'required',
            'grand_total' => 'required',
            'dispatch_date' => 'required',
            'lr_no'         => 'required',

            //'fiscal_year' => 'required',
            //'sales_no' => 'required|unique:secondary_sales,sales_no',
        ];
    }
    public function updaterules($id ='')
    {
        return [
            'buyer_id' => 'required|exists:customers,id',
            'seller_id' => 'required|exists:customers,id',
            'order_id' => 'nullable|exists:orders,id',
            'invoice_no' => 'required',
            'invoice_date' => 'required',
            'grand_total' => 'required',
            'fiscal_year' => 'required',
            'sales_no' => 'required|unique:secondary_sales,sales_no',
        ];
    }

    public function save_data($request)
    {
        try
        {
            $created_at = getcurentDateTime();

            if( $sales_id = Sales::insertGetId([
                'active' => 'Y',
                'buyer_id' => isset($request['buyer_id'])? $request['buyer_id']:null,
                'seller_id' => isset($request['seller_id'])? $request['seller_id']:null,
                'order_id' => isset($request['order_id'])? $request['order_id']:null,
                'total_qty' => isset($request['total_qty'])? array_sum($request['total_qty']):0,
                'shipped_qty' => isset($request['shipped_qty'])? array_sum($request['shipped_qty']):0,
                'orderno' => isset($request['orderno'])? $request['orderno'] :'',
                'invoice_no' => isset($request['invoice_no'])? $request['invoice_no']:'',
                'invoice_date' => isset($request['invoice_date'])? $request['invoice_date']:getcurentDate(),
                'total_gst' => isset($request['total_gst'])? $request['total_gst']:0.00,
                'sub_total' => isset($request['sub_total'])? $request['sub_total']:0.00,
                'grand_total' => isset($request['grand_total'])?  $request['grand_total']:0.00,
                'lr_no' =>  isset($request['lr_no'])? $request['lr_no'] :null,
                'dispatch_date' =>  isset($request['dispatch_date'])? $request['dispatch_date'] :null,
                'description' =>  isset($request['description'])? $request['description'] :null,
                'status_id' =>  isset($request['status_id'])? $request['status_id'] :null,
                'created_by' =>  isset($request['created_by'])? $request['created_by'] :null,
                'created_at' => $created_at ,
                'updated_at' => $created_at
            ]) )
            {
                return $response = array('status' => 'success', 'message' => 'Sales Insert Successfully','sales_id' => $sales_id);
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
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function sellers()
    {
        return $this->belongsTo('App\Models\Customers', 'seller_id', 'id')->select('id','name', 'first_name', 'last_name');
    }
    public function buyers()
    {
        return $this->belongsTo('App\Models\Customers', 'buyer_id', 'id');
    }

    public function customeraddress()
    {
        return $this->belongsTo('App\Models\Address', 'buyer_id', 'customer_id')->select('id','address1', 'address2', 'landmark', 'locality', 'customer_id', 'user_id', 'country_id', 'state_id','district_id' ,'city_id', 'pincode_id','zipcode');
    }

    public function invoiceimages()
    {
        return $this->hasMany('App\Models\Attachment','sales_id','id')->select('sales_id','file_path');
    }

    public function salespoints()
    {
        return $this->hasMany('App\Models\Attachment','sales_id','id')->select('sales_id','points');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name');
    }

    public function saledetails()
    {
        return $this->hasMany('App\Models\SalesDetails','sales_id','id')->select('sales_id','product_id', 'quantity', 'shipped_qty', 'price', 'tax_amount', 'line_total', 'status_id');
    }

    public function orders()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }
}
