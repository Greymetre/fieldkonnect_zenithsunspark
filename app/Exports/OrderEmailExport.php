<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class OrderEmailExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{    
    public function __construct($request)
    {  
        $this->pending_status = $request->input('pending_status');
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->order_id = $request->input('order_id');
        $this->dividion_id = $request->input('dividion_id');

        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        // return OrderDetails::with('orders','orders.createdbyname')->whereHas('orders',function ($query)  {
        //                         if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

        //                         {
        //                             $query->whereIn('created_by', $this->userids);
        //                         }
        //                     })->select('id','order_id', 'product_id', 'product_detail_id', 'quantity', 'shipped_qty', 'price', 'discount', 'discount_amount', 'tax_amount', 'line_total', 'status_id', 'created_at')->latest()->get();  

            if($this->pending_status == '2'){

                 return OrderDetails::with('orders','orders.createdbyname')->where('status_id',$this->pending_status)->whereHas('orders',function ($query)  {
                    if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                    {
                        $query->whereIn('created_by', $this->userids);
                    }
                    if($this->startdate)
                    {
                        $query->whereDate('created_at','>=',$this->startdate);
                    }
                    if($this->enddate)
                    {
                        $query->whereDate('created_at','<=',$this->enddate);
                    }
                    if($this->order_id)
                    {
                        $query->where('order_id',$this->order_id);
                    }  
                    if($this->dividion_id)
                    {
                        $order_ids = Order::where('product_cat_id', $this->dividion_id)->pluck('id');
                        $query->whereIn('order_id',$order_ids);
                        // $query->where('orders.product_cat_id',$this->dividion_id);
                    }  
                   
                })->latest()->get();

            }else{

            return OrderDetails::with('orders','orders.createdbyname')->whereHas('orders',function ($query)  {
                    if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                    {
                        $query->whereIn('created_by', $this->userids);
                    }
                    if($this->startdate)
                    {
                        $query->whereDate('created_at','>=',$this->startdate);
                    }
                    if($this->enddate)
                    {
                        $query->whereDate('created_at','<=',$this->enddate);
                    }
                    if($this->order_id)
                    {
                        $query->where('order_id',$this->order_id);
                    }  
                    if($this->dividion_id)
                    {
                        
                        $order_ids = Order::where('product_cat_id', $this->dividion_id)->pluck('id');
                        $query->whereIn('order_id',$order_ids);
                        // $query->where('orders.product_cat_id',$this->dividion_id);
                    }  
                   
                })->select('id','order_id', 'product_id', 'product_detail_id', 'quantity', 'shipped_qty', 'price', 'discount', 'discount_amount', 'tax_amount', 'line_total', 'status_id', 'created_at','scheme_name','scheme_discount','scheme_amount','cluster_discount','cluster_amount','deal_discount','deal_amount','distributor_discount','distributor_amount')->latest()->get();          


            }  


    }

    public function headings(): array
    {
        return ['Order Date','Order ID','Employee Code', 'User Name','Designation','Branch','Division','Customer','Customer Name', 'Dealer & Distributor Name' ,'Category','Subcategory', 'Product Model Name','Product Name','Product Stage','kW', 'HP','Suc x Del','Quantity','Rate(LP)','Trade Discount%','Scheme Discount%', 'Scheme Name', 'EBD Discount%', 'MOU Discount%','Special Discount%','Frieght Discount%','Cluster Discount%','Deal Dicount%','Cash Discount%','Tax%','Sub Total','Total', 'Order Remark','Discount Approvel Remark', 'Discount Approve By', 'Status'];
    }

    public function map($data): array
    {
            //$subtotal = $data['price']*$data['quantity'];
        
         $grandtotal;

         if(!empty($data['products']['productpriceinfo']['gst'])){
          $gstdis = $data['products']['productpriceinfo']['gst'];
            $gstamnt = $data['line_total']*$gstdis/100;

           $grandtotal = number_format($data['line_total']+$gstamnt, 2);

         }else{
          $grandtotal = number_format($data['line_total']);
         }

        $pending_qty = 0;
        $qty = $data['quantity']??0;
        $ship_qty = $data['shipped_qty']??0;
        $pending_qty = $qty-$ship_qty;


        return [
            isset($data['orders']['order_date']) ? date('Y-m-d', strtotime($data['orders']['order_date'])) :'',
            $data['id'],
            isset($data['orders']['getuserdetails']['employee_codes']) ? $data['orders']['getuserdetails']['employee_codes'] :'',
            isset($data['orders']['createdbyname']['name']) ? $data['orders']['createdbyname']['name'] :'',
            isset($data['orders']['getuserdetails']['getdesignation']['designation_name']) ? $data['orders']['getuserdetails']['getdesignation']['designation_name'] :'',
            isset($data['orders']['getuserdetails']['getbranch']['branch_name']) ? $data['orders']['getuserdetails']['getbranch']['branch_name'] :'',
            isset($data['orders']['getuserdetails']['getdivision']['division_name']) ? $data['orders']['getuserdetails']['getdivision']['division_name'] :'',
            isset($data['orders']['buyers']['customertypes']['customertype_name']) ? $data['orders']['buyers']['customertypes']['customertype_name'] :'',
            isset($data['orders']['buyers']['name']) ? $data['orders']['buyers']['name'] :'',
            isset($data['orders']['sellers']['name']) ? $data['orders']['sellers']['name'] :'',
            
         
            // isset($data['orders']['sub_total']) ? $data['orders']['sub_total'] :'',
            // isset($data['orders']['grand_total']) ? $data['orders']['grand_total'] :'',
            // isset($data['orders']['statusname']['status_name']) ? $data['orders']['statusname']['status_name'] :'',
            isset($data['products']['categories']['category_name']) ? $data['products']['categories']['category_name'] :'',
            isset($data['products']['subcategories']['subcategory_name']) ? $data['products']['subcategories']['subcategory_name'] :'',
            isset($data['products']['model_no']) ? $data['products']['model_no'] :'',
            isset($data['products']['product_name']) ? $data['products']['product_name'] :'',
            isset($data['products']['product_no']) ? $data['products']['product_no'] :'',
            isset($data['products']['part_no']) ? $data['products']['part_no'] :'',
            isset($data['products']['specification']) ? $data['products']['specification'] :'',
            isset($data['products']['suc_del']) ? $data['products']['suc_del'] :'',
            isset($data['quantity'])? $data['quantity'] :'',
             //isset($data['price'])? $data['price'] :'',
             isset($data['products']['productpriceinfo']['mrp']) ? $data['products']['productpriceinfo']['mrp'] :'',

            isset($data['products']['productpriceinfo']['discount']) ? $data['products']['productpriceinfo']['discount'] :'',

            // isset($data['products']['getSchemeDetail']['points']) ? $data['products']['getSchemeDetail']['points'] :'',

            // isset($data['products']['getSchemeDetail']['orderscheme']['scheme_name']) ? $data['products']['getSchemeDetail']['orderscheme']['scheme_name'] :'',

            isset($data['scheme_discount']) ? $data['scheme_discount'] :'',
            isset($data['scheme_name']) ? $data['scheme_name'] :'',
            isset($data['orders']['ebd_discount']) ? $data['orders']['ebd_discount'] :'',
            isset($data['orders']['distributor_discount']) ? $data['orders']['distributor_discount'] :'',
            isset($data['orders']['special_discount']) ? $data['orders']['special_discount'] :'',
            isset($data['orders']['frieght_discount']) ? $data['orders']['frieght_discount'] :'',
            isset($data['orders']['cluster_discount']) ? $data['orders']['cluster_discount'] :'',
            isset($data['orders']['deal_discount']) ? $data['orders']['deal_discount'] :'',
            isset($data['orders']['cash_discount']) ? $data['orders']['cash_discount'] :'',
            isset($data['products']['productpriceinfo']['gst']) ? $data['products']['productpriceinfo']['gst'] :'',
            isset($data['line_total'])? $data['line_total'] :'',
            
            //isset($data['line_total'])? $data['line_total'] :'',
            $grandtotal,
            isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] :'',
            isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] :'',
            isset($data['orders']['updatedbyname']) ? $data['orders']['updatedbyname']['name'] :'',

            isset($data['statusname']['status_name']) ? $data['statusname']['status_name'] :'',


        ];
    }

}
