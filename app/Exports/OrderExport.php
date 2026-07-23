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


class OrderExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    public function __construct($request)
    {
        $this->pending_status = $request->input('pending_status');
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->order_id = $request->input('order_id');
        $this->user_id = $request->input('user_id');
        $this->dividion_id = $request->input('dividion_id');
        $this->customer_type_id = $request->input('customer_type_id');

        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        $final = collect(); // Final result

        // return OrderDetails::with('orders','orders.createdbyname')->whereHas('orders',function ($query)  {
        //                         if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

        //                         {
        //                             $query->whereIn('created_by', $this->userids);
        //                         }
        //                     })->select('id','order_id', 'product_id', 'product_detail_id', 'quantity', 'shipped_qty', 'price', 'discount', 'discount_amount', 'tax_amount', 'line_total', 'status_id', 'created_at')->latest()->get();  

        if ($this->pending_status != '' && $this->pending_status != NULL) {
            $query = OrderDetails::with('orders', 'orders.createdbyname', 'orders.buyers')->whereHas('orders', function ($query) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('created_by', $this->userids);
                }
                if ($this->pending_status == '0') {
                    $query->where('status_id', NULL);
                } else {
                    $query->where('status_id', $this->pending_status);
                }
                if ($this->startdate) {
                    $query->where('order_date', '>=', $this->startdate);
                }
                if ($this->enddate) {
                    $query->where('order_date', '<=', $this->enddate);
                }
                if ($this->order_id) {
                    $query->where('id', $this->order_id);
                }
                if ($this->user_id) {
                    $query->where('created_by', $this->user_id);
                }
                if ($this->dividion_id) {
                    $order_ids = Order::where('product_cat_id', $this->dividion_id)->pluck('id');
                    $query->whereIn('id', $order_ids);
                    // $query->where('orders.product_cat_id',$this->dividion_id);
                }

                if ($this->customer_type_id && $this->customer_type_id != '') {
                    $Order_ids = Order::with('buyers')
                        ->whereHas('buyers', function ($query) {
                            $query->where('customertype', $this->customer_type_id);
                        })->pluck('id');
                    $query->whereIn('order_id', $order_ids);
                }
            });

            $query->chunk(1000, function ($results) use (&$final) {
                foreach ($results as $row) {
                    $final->push($row);
                }
            });

            return $final;
        } else {
            $query =  OrderDetails::with('orders', 'orders.createdbyname')->whereHas('orders', function ($query) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('created_by', $this->userids);
                }

                if ($this->order_id) {
                    $query->where('id', $this->order_id);
                }
                if ($this->dividion_id) {
                    $order_ids = Order::where(function ($query) {
                        $query->where('product_cat_id', $this->dividion_id)
                            ->orWhereNull('product_cat_id');
                    });
                    if ($this->startdate) {
                        $order_ids->where('order_date', '>=', $this->startdate);
                    }
                    if ($this->enddate) {
                        $order_ids->where('order_date', '<=', $this->enddate);
                    }
                    $order_ids = $order_ids->pluck('id');
                    $query->whereIn('id', $order_ids);
                    if ($this->order_id) {
                        $query->where('id', $this->order_id);
                    }
                    if ($this->user_id) {
                        $query->where('created_by', $this->user_id);
                    }
                    if ($this->dividion_id) {
                        $order_ids = Order::where('product_cat_id', $this->dividion_id)->pluck('id');
                        $query->whereIn('id', $order_ids);
                        // $query->where('orders.product_cat_id',$this->dividion_id);
                    }

                    if ($this->customer_type_id && $this->customer_type_id != '') {
                        $Order_ids = Order::with('buyers')
                            ->whereHas('buyers', function ($query) {
                                $query->where('customertype', $this->customer_type_id);
                            })->pluck('id');
                        $query->whereIn('order_id', $order_ids);
                    }
                    // $query->where('orders.product_cat_id',$this->dividion_id);
                } else {
                    if ($this->startdate) {
                        $query->where('order_date', '>=', $this->startdate);
                    }
                    if ($this->enddate) {
                        $query->where('order_date', '<=', $this->enddate);
                    }
                    if ($this->order_id) {
                        $query->where('id', $this->order_id);
                    }
                    if ($this->user_id) {
                        $query->where('created_by', $this->user_id);
                    }
                    if ($this->dividion_id) {
                        $order_ids = Order::where('product_cat_id', $this->dividion_id)->pluck('id');
                        $query->whereIn('id', $order_ids);
                        // $query->where('orders.product_cat_id',$this->dividion_id);
                    }

                    if ($this->customer_type_id && $this->customer_type_id != '') {
                        $Order_ids = Order::with('buyers')
                            ->whereHas('buyers', function ($query) {
                                $query->where('customertype', $this->customer_type_id);
                            })->pluck('id');
                        $query->whereIn('order_id', $order_ids);
                    }
                }
            })->select('id', 'order_id', 'product_id', 'product_detail_id', 'quantity', 'shipped_qty', 'price', 'discount', 'discount_amount', 'tax_amount', 'line_total', 'status_id', 'created_at', 'scheme_name', 'scheme_discount', 'scheme_amount', 'cluster_discount', 'cluster_amount', 'deal_discount', 'deal_amount', 'distributor_discount', 'distributor_amount');


            $query->chunk(1000, function ($results) use (&$final) {
                foreach ($results as $row) {
                    $final->push($row);
                }
            });

            return $final;
        }
    }

    public function headings(): array
    {
        if ($this->dividion_id == '1') {
            return ['id', 'Order Date', 'Employee Code', 'User Name', 'Branch', 'Division', 'Designation', 'Retailer ID', 'Customer', 'Customer Name', 'Dealer ID', 'Dealer & Distributor Name', 'Dealer & Distributor BP Code', 'Order No', 'Order ID', 'Category', 'Subcategory', 'Product Code', 'Product Name', 'Product ID', 'Product Stage', 'kW', 'HP', 'Suc x Del', 'Quantity', 'Shipped Qty', 'Pending Qty', 'Rate(LP)', 'Trade Discount%', 'Scheme Discount%', 'Scheme Name', 'EBD Discount%', 'MOU Discount%', 'Special Discount%', 'Frieght Discount%', 'Cluster Discount%', 'Deal Dicount%', 'Cash Discount%', 'Total Discount%', 'Tax%', 'Sub Total', 'Total', 'Order Remark', 'Discount Approvel Remark', 'Discount Approve By', 'Status'];
        } elseif ($this->dividion_id == '2') {
            return ['id', 'Order Date', 'Employee Code', 'User Name', 'Branch', 'Division', 'Designation', 'Retailer ID', 'Customer', 'Customer Name', 'Dealer ID', 'Dealer & Distributor Name', 'Dealer & Distributor BP Code', 'Order No', 'Order ID', 'Category', 'Subcategory', 'Product Code', 'Product Name', 'Product ID', 'Quantity', 'Shipped Qty', 'Pending Qty', 'Rate(LP)', 'DOD Discount%', 'Special Distribution Discount%', 'Distribution Margin Discount%', 'Cash Discount%', 'Total Discount%', 'Total Discount', 'Tax%', 'Sub Total', 'Total', 'Order Remark', 'Discount Approvel Remark', 'Discount Approve By', 'Status'];
        } else {
            return ['id', 'Order Date', 'Employee Code', 'User Name', 'Branch', 'Division', 'Designation', 'Retailer ID', 'Customer', 'Customer Name', 'Dealer ID', 'Dealer & Distributor Name', 'Dealer & Distributor BP Code', 'Order No', 'Order ID', 'Category', 'Subcategory', 'Product Code', 'Product Name', 'Product ID', 'Product Stage', 'kW', 'HP', 'Suc x Del', 'Quantity', 'Shipped Qty', 'Pending Qty', 'Rate(LP)', 'Tax%', 'Sub Total', 'Total', 'Order Remark', 'Discount Approvel Remark', 'Discount Approve By', 'Status'];
        }
    }

    public function map($data): array
    {
        //$subtotal = $data['price']*$data['quantity'];

        $grandtotal;

        if (!empty($data['products']['productpriceinfo']['gst'])) {
            $gstdis = $data['products']['productpriceinfo']['gst'];
            $gstamnt = $data['line_total'] * $gstdis / 100;

            $grandtotal = number_format($data['line_total'] + $gstamnt, 2);
        } else {
            $grandtotal = number_format($data['line_total']);
        }

        $pending_qty = 0;
        $qty = $data['quantity'] ?? 0;
        $ship_qty = $data['shipped_qty'] ?? 0;
        $pending_qty = $qty - $ship_qty;

        if ($this->dividion_id == '1') {
            return [
                $data['id'],
                isset($data['orders']['order_date']) ? date('Y-m-d', strtotime($data['orders']['order_date'])) : '',
                isset($data['orders']['getuserdetails']['employee_codes']) ? $data['orders']['getuserdetails']['employee_codes'] : '',
                isset($data['orders']['createdbyname']['name']) ? $data['orders']['createdbyname']['name'] : '',
                isset($data['orders']['getuserdetails']['getbranch']['branch_name']) ? $data['orders']['getuserdetails']['getbranch']['branch_name'] : '',
                isset($data['orders']['getuserdetails']['getdivision']['division_name']) ? $data['orders']['getuserdetails']['getdivision']['division_name'] : '',
                isset($data['orders']['getuserdetails']['getdesignation']['designation_name']) ? $data['orders']['getuserdetails']['getdesignation']['designation_name'] : '',
                isset($data['orders']['buyer_id']) ? $data['orders']['buyer_id'] : '',
                isset($data['orders']['buyers']['customertypes']['customertype_name']) ? $data['orders']['buyers']['customertypes']['customertype_name'] : '',
                isset($data['orders']['buyers']['name']) ? $data['orders']['buyers']['name'] : '',
                isset($data['orders']['seller_id']) ? $data['orders']['seller_id'] : '',
                isset($data['orders']['sellers']['name']) ? $data['orders']['sellers']['name'] : '',
                isset($data['orders']['sellers']['sap_code']) ? $data['orders']['sellers']['sap_code'] : '',
                isset($data['orders']['orderno']) ? $data['orders']['orderno'] : '',
                isset($data['orders']['id']) ? $data['orders']['id'] : '',

                isset($data['products']['categories']['category_name']) ? $data['products']['categories']['category_name'] : '',
                isset($data['products']['subcategories']['subcategory_name']) ? $data['products']['subcategories']['subcategory_name'] : '',
                isset($data['products']['product_code']) ? $data['products']['product_code'] : '',
                isset($data['products']['product_name']) ? $data['products']['product_name'] : '',
                isset($data['product_id']) ? $data['product_id'] : '',
                isset($data['products']['product_no']) ? $data['products']['product_no'] : '',
                isset($data['products']['part_no']) ? $data['products']['part_no'] : '',
                isset($data['products']['specification']) ? $data['products']['specification'] : '',
                isset($data['products']['suc_del']) ? $data['products']['suc_del'] : '',
                isset($data['quantity']) ? $data['quantity'] : '',
                isset($data['shipped_qty']) ? $data['shipped_qty'] : '',
                $pending_qty ?? 0,

                isset($data['products']['productpriceinfo']['mrp']) ? $data['products']['productpriceinfo']['mrp'] : '',

                isset($data['products']['productpriceinfo']['discount']) ? $data['products']['productpriceinfo']['discount'] : '',

                isset($data['scheme_discount']) ? $data['scheme_discount'] : '',
                isset($data['scheme_name']) ? $data['scheme_name'] : '',
                isset($data['orders']['ebd_discount']) ? $data['orders']['ebd_discount'] : '',
                isset($data['orders']['distributor_discount']) ? $data['orders']['distributor_discount'] : '',
                isset($data['orders']['special_discount']) ? $data['orders']['special_discount'] : '',
                isset($data['orders']['frieght_discount']) ? $data['orders']['frieght_discount'] : '',
                isset($data['orders']['cluster_discount']) ? $data['orders']['cluster_discount'] : '',
                isset($data['orders']['deal_discount']) ? $data['orders']['deal_discount'] : '',
                isset($data['orders']['cash_discount']) ? $data['orders']['cash_discount'] : '',
                $data['products'] && isset($data['products']['productpriceinfo'], $data['products']['productpriceinfo']['mrp'], $data['quantity']) && $data['products']['productpriceinfo']['mrp'] > 0 && $data['quantity'] > 0 ? number_format(((1 - ($data['line_total'] / ($data['products']['productpriceinfo']['mrp'] * $data['quantity']))) * 100), 2) : '0',
                isset($data['products']['productpriceinfo']['gst']) ? $data['products']['productpriceinfo']['gst'] : '',
                isset($data['line_total']) ? $data['line_total'] : '',
                isset($data['orders']['grand_total'])  ? $data['orders']['grand_total'] : '',
                // $grandtotal,
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['updatedbyname']) ? $data['orders']['updatedbyname']['name'] : '',

                isset($data['orders']['statusname']) ? $data['orders']['statusname']['status_name'] : 'Pending',
            ];
        } elseif ($this->dividion_id == '2') {
            return [
                $data['id'],
                isset($data['orders']['order_date']) ? date('Y-m-d', strtotime($data['orders']['order_date'])) : '',
                isset($data['orders']['getuserdetails']['employee_codes']) ? $data['orders']['getuserdetails']['employee_codes'] : '',
                isset($data['orders']['createdbyname']['name']) ? $data['orders']['createdbyname']['name'] : '',
                isset($data['orders']['getuserdetails']['getbranch']['branch_name']) ? $data['orders']['getuserdetails']['getbranch']['branch_name'] : '',
                isset($data['orders']['getuserdetails']['getdivision']['division_name']) ? $data['orders']['getuserdetails']['getdivision']['division_name'] : '',
                isset($data['orders']['getuserdetails']['getdesignation']['designation_name']) ? $data['orders']['getuserdetails']['getdesignation']['designation_name'] : '',
                isset($data['orders']['buyer_id']) ? $data['orders']['buyer_id'] : '',
                isset($data['orders']['buyers']['customertypes']['customertype_name']) ? $data['orders']['buyers']['customertypes']['customertype_name'] : '',
                isset($data['orders']['buyers']['name']) ? $data['orders']['buyers']['name'] : '',
                isset($data['orders']['seller_id']) ? $data['orders']['seller_id'] : '',
                isset($data['orders']['sellers']['name']) ? $data['orders']['sellers']['name'] : '',
                isset($data['orders']['sellers']['sap_code']) ? $data['orders']['sellers']['sap_code'] : '',
                isset($data['orders']['orderno']) ? $data['orders']['orderno'] : '',
                isset($data['orders']['id']) ? $data['orders']['id'] : '',

                isset($data['products']['categories']['category_name']) ? $data['products']['categories']['category_name'] : '',
                isset($data['products']['subcategories']['subcategory_name']) ? $data['products']['subcategories']['subcategory_name'] : '',
                isset($data['products']['product_code']) ? $data['products']['product_code'] : '',
                isset($data['products']['product_name']) ? $data['products']['product_name'] : '',
                isset($data['product_id']) ? $data['product_id'] : '',
                isset($data['quantity']) ? $data['quantity'] : '',
                isset($data['shipped_qty']) ? $data['shipped_qty'] : '',
                $pending_qty ?? 0,

                isset($data['products']['productpriceinfo']['mrp']) ? $data['products']['productpriceinfo']['mrp'] : '',

                isset($data['orders']['dod_discount']) ? $data['orders']['dod_discount'] : '',
                isset($data['orders']['special_distribution_discount']) ? $data['orders']['special_distribution_discount'] : '',
                isset($data['orders']['distribution_margin_discount']) ? $data['orders']['distribution_margin_discount'] : '',
                isset($data['orders']['cash_discount']) ? $data['orders']['cash_discount'] : '',
                isset($data['orders']['total_fan_discount']) ? $data['orders']['total_fan_discount'] : '',
                ($data['orders']['sub_total'] / (1 - $data['orders']['total_fan_discount'] / 100)) - $data['orders']['sub_total'],

                isset($data['products']['productpriceinfo']['gst']) ? $data['products']['productpriceinfo']['gst'] : '',
                isset($data['line_total']) ? $data['line_total'] : '',
                isset($data['orders']['grand_total'])  ? $data['orders']['grand_total'] : '',
                // $grandtotal,
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['updatedbyname']) ? $data['orders']['updatedbyname']['name'] : '',

                isset($data['orders']['statusname']) ? $data['orders']['statusname']['status_name'] : 'Pending',
            ];
        } else {
            return [
                $data['id'],
                isset($data['orders']['order_date']) ? date('Y-m-d', strtotime($data['orders']['order_date'])) : '',
                isset($data['orders']['getuserdetails']['employee_codes']) ? $data['orders']['getuserdetails']['employee_codes'] : '',
                isset($data['orders']['createdbyname']['name']) ? $data['orders']['createdbyname']['name'] : '',
                isset($data['orders']['getuserdetails']['getbranch']['branch_name']) ? $data['orders']['getuserdetails']['getbranch']['branch_name'] : '',
                isset($data['orders']['getuserdetails']['getdivision']['division_name']) ? $data['orders']['getuserdetails']['getdivision']['division_name'] : '',
                isset($data['orders']['getuserdetails']['getdesignation']['designation_name']) ? $data['orders']['getuserdetails']['getdesignation']['designation_name'] : '',
                isset($data['orders']['buyer_id']) ? $data['orders']['buyer_id'] : '',
                isset($data['orders']['buyers']['customertypes']['customertype_name']) ? $data['orders']['buyers']['customertypes']['customertype_name'] : '',
                isset($data['orders']['buyers']['name']) ? $data['orders']['buyers']['name'] : '',
                isset($data['orders']['seller_id']) ? $data['orders']['seller_id'] : '',
                isset($data['orders']['sellers']['name']) ? $data['orders']['sellers']['name'] : '',
                isset($data['orders']['sellers']['sap_code']) ? $data['orders']['sellers']['sap_code'] : '',
                isset($data['orders']['orderno']) ? $data['orders']['orderno'] : '',
                isset($data['orders']['id']) ? $data['orders']['id'] : '',

                isset($data['products']['categories']['category_name']) ? $data['products']['categories']['category_name'] : '',
                isset($data['products']['subcategories']['subcategory_name']) ? $data['products']['subcategories']['subcategory_name'] : '',
                isset($data['products']['product_code']) ? $data['products']['product_code'] : '',
                isset($data['products']['product_name']) ? $data['products']['product_name'] : '',
                isset($data['product_id']) ? $data['product_id'] : '',
                isset($data['products']['product_no']) ? $data['products']['product_no'] : '',
                isset($data['products']['part_no']) ? $data['products']['part_no'] : '',
                isset($data['products']['specification']) ? $data['products']['specification'] : '',
                isset($data['products']['suc_del']) ? $data['products']['suc_del'] : '',
                isset($data['quantity']) ? $data['quantity'] : '',
                isset($data['shipped_qty']) ? $data['shipped_qty'] : '',
                $pending_qty ?? 0,

                isset($data['products']['productpriceinfo']['mrp']) ? $data['products']['productpriceinfo']['mrp'] : '',

                isset($data['products']['productpriceinfo']['gst']) ? $data['products']['productpriceinfo']['gst'] : '',
                isset($data['line_total']) ? $data['line_total'] : '',
                isset($data['orders']['grand_total'])  ? $data['orders']['grand_total'] : '',
                // $grandtotal,
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['order_remark']) ? $data['orders']['order_remark'] : '',
                isset($data['orders']['updatedbyname']) ? $data['orders']['updatedbyname']['name'] : '',

                isset($data['orders']['statusname']) ? $data['orders']['statusname']['status_name'] : 'Pending',
            ];
        }
    }
}
