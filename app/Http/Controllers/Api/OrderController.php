<?php

namespace App\Http\Controllers\Api;

use App\Exports\OrderEmailExport;
use App\Http\Controllers\Controller;
use App\Mail\OrderMailWithAttachment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

use Validator;
use Gate;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Attachment;
use App\Models\Cart;
use App\Models\Customers;
use App\Models\Product;
use App\Models\User;
use App\Models\Sales;
use App\Models\Status;
use Excel;
use Illuminate\Support\Facades\Mail;
use stdClass;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->orders = new Order();

        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }

    public function getOrderList(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $customer_id = $request->customer_id ?? '';
            $user_ids = getUsersReportingToAuth($user->id);
            $pageSize = $request->input('pageSize');
            $query = $this->orders->latest();
            $start_date = $request->startdate ?? '';
            $end_date   = $request->enddate ?? '';
            $selecteduser_id = $request->user_id ?? '';
            $selectedstatus_id = $request->status_id ?? '';

            if (!empty($start_date) && !empty($end_date)) {
                $startDate = date('Y-m-d', strtotime($start_date));
                $endDate = date('Y-m-d', strtotime($end_date));
                $query->whereDate('order_date', '>=', $startDate)
                    ->whereDate('order_date', '<=', $endDate);
            }

            if (!empty($customer_id)) {
                $query->where(function ($query) use ($customer_id) {
                    $query->where('buyer_id', '=', $customer_id)
                        ->orWhere('seller_id', '=', $customer_id);
                });
            }

            if (!empty($selecteduser_id)) {
                $query->where('created_by', $selecteduser_id);
            } else {
                $query->whereIn('created_by', $user_ids);
            }

            if ((isset($selectedstatus_id) || $selectedstatus_id == 0) && $selectedstatus_id != '') {
                if ($selectedstatus_id == 0) {
                    $query->whereNull('status_id');
                } else {
                    $query->where('status_id', $selectedstatus_id);
                }
            }
            // dd($query->toSql(), $selecteduser_id, $customer_id);
            // dd($request->all());
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            $users = User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 29);
            })->where('active', 'Y')->whereIn('id', $user_ids)->select('id', 'name')->orderBy('name', 'asc')->get();
            $all_status = [['id' => '0', 'name' => 'Pending'], ['id' => '1', 'name' => 'Dispatched'], ['id' => '2', 'name' => 'Partially Dispatched'], ['id' => '3', 'name' => 'Full Dispatch'], ['id' => '4', 'name' => 'Cancel']];
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'order_id' => isset($value['id']) ? $value['id'] : 0,
                        'seller_id' => isset($value['seller_id']) ? $value['seller_id'] : 0,
                        'seller_name' => isset($value['sellers']['name']) ? $value['sellers']['name'] : '',
                        'buyer_id' => isset($value['buyer_id']) ? $value['buyer_id'] : 0,
                        'buyer_name' => isset($value['buyers']['name']) ? $value['buyers']['name'] : '',
                        // 'total_qty' => isset($value['total_qty']) ? $value['total_qty'] : 0,
                        'total_qty' => $value->orderdetails->sum('quantity') ?? 0,
                        'shipped_qty' => isset($value['shipped_qty']) ? $value['shipped_qty'] : 0,
                        'orderno' => isset($value['orderno']) ? $value['orderno'] : '',
                        'order_date' => isset($value['order_date']) ? $value['order_date'] : '',
                        'completed_date' => isset($value['completed_date']) ? $value['completed_date'] : '',
                        'grand_total' => isset($value['grand_total']) ? $value['grand_total'] : 0.00,
                        'sub_total' => isset($value['sub_total']) ? $value['sub_total'] : 0.00,
                        'order_status' => isset($value['statusname']) ? $value['statusname']['status_name'] : 'Pending',
                        'order_status_id' => (isset($value['status_id']) && $value['status_id'] != NULL) ? $value['status_id'] : '0',
                        'creatd_by'    => isset($value['createdbyname']) ? $value['createdbyname']['name'] : '',
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'users' => $users,  'all_status' => $all_status], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data, 'users' => $users, 'all_status' => $all_status], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getOrderDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $user = $request->user();
            $user_id = $user->id;
            $order_id = $request->input('order_id');
            $data = $this->orders->with('orderdetails', 'orderdetails.products', 'statusname', 'orderdetails.productdetails', 'createdbyname', 'getsalesdetail')->where('id', $order_id)->first();
            $salesdetails = Sales::where('order_id', $order_id)->first() ?? [];

            $data['schme_amount'] = (string)$data['schme_amount'];
            $data['schme_val'] = (string)$data['schme_val'];
            $data['order_status'] = isset($data['statusname']) ? $data['statusname']['status_name'] : 'Pending';
            $data['dispatch_date'] = isset($salesdetails) ? (isset($salesdetails['dispatch_date']) ? Carbon::parse($salesdetails['dispatch_date'])->format('d-m-Y') : '') : '';
            $data['lr_no'] = isset($salesdetails) ? isset($salesdetails['lr_no']) ? (string)$salesdetails['lr_no']  : '' : '';
            $data['invoice_no'] = isset($salesdetails) ? (isset($salesdetails['invoice_no']) ? $salesdetails['invoice_no']  : '') : '';
            $data['invoice_date'] = isset($salesdetails) ? (isset($salesdetails['invoice_date']) ? Carbon::parse($salesdetails['invoice_date'])->format('d-m-Y') : '') : '';
            $data['transport_name'] = isset($salesdetails) ? (isset($salesdetails['transport_details']) ? $salesdetails['transport_details']  : '') : '';
            $data['ebd_amount'] = (string)$data['ebd_amount'];
            $data['ebd_discount'] = (string)$data['ebd_discount'];
            $data['special_discount'] = (string)$data['special_discount'];
            $data['special_amount'] = (string)$data['special_amount'];
            $data['cluster_discount'] = (string)$data['cluster_discount'];
            $data['cluster_amount'] = (string)$data['cluster_amount'];
            $data['deal_discount'] = (string)$data['deal_discount'];
            $data['deal_amount'] = (string)$data['deal_amount'];
            $data['distributor_discount'] = (string)$data['distributor_discount'];
            $data['distributor_amount'] = (string)$data['distributor_amount'];
            $data['frieght_discount'] = (string)$data['frieght_discount'];
            $data['frieght_amount'] = (string)$data['frieght_amount'];
            $data['cash_discount'] = (string)$data['cash_discount'];
            $data['cash_amount'] = (string)$data['cash_amount'];
            $data['total_discount'] = (string)$data['total_discount'];
            $data['total_amount'] = (string)$data['total_amount'];
            $data['gst5_amt'] = (string)$data['gst5_amt'];
            $data['gst12_amt'] = (string)$data['gst12_amt'];
            $data['gst18_amt'] = (string)$data['gst18_amt'];
            $data['gst28_amt'] = (string)$data['gst28_amt'];
            $data['status_id'] = ($data['status_id'] && $data['status_id'] != NULL) ? (string)$data['status_id'] : "0";
            $data['address_id'] = (string)$data['address_id'];
            $data['suc_del'] = (string)$data['suc_del'];
            $data['gst_amount'] = (string)$data['gst_amount'];
            $data['order_remark'] = (string)$data['order_remark'];
            $data['dod_discount'] = (string)$data['dod_discount'];
            $data['special_distribution_discount'] = (string)$data['special_distribution_discount'];
            $data['distribution_margin_discount'] = (string)$data['distribution_margin_discount'];
            $data['total_fan_discount'] = (string)$data['total_fan_discount'];
            $data['total_fan_discount_amount'] = (string)$data['total_fan_discount_amount'];
            $data['cash_discount'] = (string)$data['cash_discount'];
            $data['cash_amount'] = (string)$data['cash_amount'];
            $data['product_cat_id'] = (string)$data['product_cat_id'];
            $data['extra_discount_amount'] = isset($data['extra_discount_amount']) && $data['extra_discount_amount'] > 0  ? (string)$data['extra_discount_amount'] : '';
            $data['special_distribution_discount_amount'] = isset($data['special_distribution_discount_amount']) && $data['special_distribution_discount_amount'] > 0  ? (string)$data['special_distribution_discount_amount'] : '';
            $data['distribution_margin_discount_amount'] = isset($data['distribution_margin_discount_amount']) && $data['distribution_margin_discount_amount'] > 0  ? (string)$data['distribution_margin_discount_amount'] : '';
            $data['fan_extra_discount'] = isset($data['fan_extra_discount']) && $data['fan_extra_discount'] > 0  ? (string)$data['fan_extra_discount'] : '';
            $data['fan_extra_discount_amount'] = isset($data['fan_extra_discount_amount']) && $data['fan_extra_discount_amount'] > 0  ? (string)$data['fan_extra_discount_amount'] : '';
            $data['cash_discount'] = isset($data['cash_discount']) && $data['cash_discount'] > 0  ? (string)$data['cash_discount'] : '';
            $data['cash_amount'] = isset($data['cash_amount']) && $data['cash_amount'] > 0  ? (string)$data['cash_amount'] : '';
            $data['dod_discount_amount'] = isset($data['dod_discount_amount']) && $data['dod_discount_amount'] > 0  ? (string)$data['dod_discount_amount'] : '';


            if (!empty($data['orderdetails'])) {
                $orderdetails = collect([]);
                foreach ($data['orderdetails'] as $key => $value) {
                    $orderdetails->push([
                        'orderdetail_id' => isset($value['id']) ? $value['id'] : 0,
                        'product_id' =>  isset($value['product_id']) ? $value['product_id'] : 0,
                        'product_name' =>  isset($value['products']['product_name']) ? $value['products']['product_name'] : '',
                        'product_image' =>  isset($value['products']['product_image']) ? $value['products']['product_image'] : '',
                        'product_detail_id' =>  isset($value['product_detail_id']) ? $value['product_detail_id'] : $value['product_id'],
                        //'detail_title' =>  isset($value['productdetails']['detail_title']) ? $value['productdetails']['detail_title'] : '',
                        'detail_title' =>  isset($value['products']['product_no']) ? $value['products']['product_no'] : '',
                        'quantity' =>  isset($value['quantity']) ? $value['quantity'] : 0,
                        'ebd_amount' =>  isset($value['price']) ? $value['price'] : 0.00,
                        'gst' =>  isset($value['products']['productpriceinfo']) ? $value['products']['productpriceinfo']['gst'] : 0,
                        'shipped_qty'  =>  isset($value['shipped_qty']) ? $value['shipped_qty'] : 0,
                        'price'  =>  isset($value['price']) ? $value['price'] : 0.00,
                        'tax_amount'  =>  isset($value['tax_amount']) ? $value['tax_amount'] : 0.00,
                        'line_total'  =>  isset($value['line_total']) ? $value['line_total'] : 0.00,
                        'status_id'  =>  isset($value['status_id']) ? $value['status_id'] : 0,
                        'specification' => isset($value['products']['suc_del']) ? $value['products']['suc_del'] : '',
                        'part_no' => isset($value['products']['part_no']) ? $value['products']['part_no'] : '',
                        'product_no' => isset($value['products']['product_no']) ? $value['products']['product_no'] : '',
                        'hp' => isset($value['products']['specification']) ? $value['products']['specification'] : '',
                        'model_no' => isset($value['products']['model_no']) ? $value['products']['model_no'] : '',
                        'phase' => isset($value['products']['phase']) ? $value['products']['phase'] : '',
                        'brand_name' => isset($value['products']['brands']) ? $value['products']['brands']['brand_name'] : '',
                    ]);
                }
                unset($data['orderdetails']);
                $data['seller_name'] = isset($data['sellers']['name']) ? $data['sellers']['name'] : '';
                $data['seller_address'] = isset($data['sellers']['customeraddress']) ? $data['sellers']['customeraddress'] : '';
                $data['buyer_name'] = isset($data['buyers']['name']) ? $data['buyers']['name'] : '';
                $data['buyer_address'] = isset($data['buyers']['customeraddress']) ? $data['buyers']['customeraddress'] : '';
                $data['buyer_type'] = isset($data['buyers']['customertypes']) ? $data['buyers']['customertypes']['customertype_name'] : '';
                $data['orderdetails'] = $orderdetails;
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function insertOrder(Request $request)
    {
        try {
            $user = $request->user();
            $request['created_by'] = $user->id;
            $validator = Validator::make($request->all(), $this->orders->insertrules(), $this->orders->message());
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->messages()->all()], $this->badrequest);
            }
            $request['order_remark'] = $request['remark'] ?? '';
            $fprodu = Product::find($request->orderdetail[0]['product_id']);
            $request['product_cat_id'] = $fprodu->category_id;
            $response =  $this->orders->save_data($request);
            if ($response['status'] == 'success') {
                $orderdetail = collect([]);
                foreach ($request->orderdetail as $key => $rows) {

                    $product = Product::with('productpriceinfo')->find($rows['product_id']);
                    if ($product) {
                        $rows['discount'] = $product->productpriceinfo->discount;
                        if ($product->productpriceinfo->gst == '5') {
                            $gst = 5;
                            $gst_amount = (($rows['quantity'] * $rows['price']) * 5) / 100;
                        } elseif ($product->productpriceinfo->gst == '12') {
                            $gst = 12;
                            $gst_amount = (($rows['quantity'] * $rows['price']) * 12) / 100;
                        } elseif ($product->productpriceinfo->gst == '18') {
                            $gst = 18;
                            $gst_amount = (($rows['quantity'] * $rows['price']) * 18) / 100;
                        } elseif ($product->productpriceinfo->gst == '28') {
                            $gst = 28;
                            $gst_amount = (($rows['quantity'] * $rows['price']) * 28) / 100;
                        } else {
                            $gst = 0;
                            $gst_amount = 0;
                        }
                    }


                    // calculate lime total for retailer/ please remove this code after adding app live
                    // if(isset($rows['line_total']) && isset($rows['quantity']) && isset($rows['price'])){
                    //     if($rows['quantity'] > 1){
                    //         $rows['line_total'] =   $rows['line_total'] == $rows['ebd_amount'] ? $rows['quantity']*$rows['ebd_amount'] :  $rows['line_total'];
                    //     }
                    // }


                    $orderdetail->push([
                        'active' => 'Y',
                        'order_id' => isset($response['order_id']) ? $response['order_id'] : null,
                        'product_id' => isset($rows['product_id']) ? $rows['product_id'] : null,
                        'product_detail_id' => isset($rows['product_detail_id']) ? $rows['product_detail_id'] : null,
                        'quantity' => isset($rows['quantity']) ? $rows['quantity'] : 0,
                        'discount' => isset($rows['discount']) ? $rows['discount'] : 0.00,
                        'discount_amount' => isset($rows['discount_amount']) ? $rows['discount_amount'] : 0.00,
                        'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] : 0,
                        'price' => isset($rows['price']) ? $rows['price'] : 0.00,
                        'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] : 0.00,
                        'line_total' => isset($rows['line_total']) ? $rows['line_total'] : 0.00,
                        'created_at' => getcurentDateTime(),
                        'ebd_amount' => isset($rows['ebd_amount']) ? $rows['ebd_amount'] : 0.00,
                        // 'agri_standard_dis' => isset($rows['agri_standard_dis']) ? $rows['agri_standard_dis'] : 0.00,
                        // 'agri_standard_dis_amounts' => isset($rows['agri_standard_dis_amounts']) ? $rows['agri_standard_dis_amounts'] : 0.00,
                        // 'ebd_amount' => isset($rows['ebd_amount']) ? $rows['ebd_amount'] :0.00,
                        // 'cluster_discount' => isset($rows['cluster_discount']) ? $rows['cluster_discount'] :0.00,
                        // 'cluster_amount' => isset($rows['cluster_amount']) ? $rows['cluster_amount'] :0.00,
                        // 'distributor_discount' => isset($rows['distributor_discount']) ? $rows['distributor_discount'] :0.00,
                        // 'distributor_amount' => isset($rows['distributor_amount']) ? $rows['distributor_amount'] :0.00,
                        // 'deal_discount' => isset($rows['deal_discount']) ? $rows['deal_discount'] :0.00,
                        // 'deal_amount' => isset($rows['deal_amount']) ? $rows['deal_amount'] :0.00,
                        // 'frieght_discount' => isset($rows['frieght_discount']) ? $rows['frieght_discount'] :0.00,
                        // 'frieght_amount' => isset($rows['frieght_amount']) ? $rows['frieght_amount'] :0.00,
                        'gst' => $gst ?? 0,
                        'gst_amount' => $gst_amount ?? 0,


                    ]);
                }

                if ($orderdetail->isNotEmpty()) {
                    OrderDetails::insert($orderdetail->toArray());
                    $exportData = new Request();
                    $exportData->merge([
                        'order_id' => $response['order_id'],
                    ]);

                    Excel::store(new OrderEmailExport($exportData), '/assets/orderDetails.xlsx', 'local');

                    if ($user->userinfo->order_mails  && $user->userinfo->order_mails != null && $user->userinfo->order_mails != '') {
                        $mail_id_array = explode(',', $user->userinfo->order_mails);
                        $buyer = Customers::find($request['buyer_id']);
                        $seller = Customers::find($request['seller_id']);
                        $attachmentPath = base_path('storage/app/assets/orderDetails.xlsx');

                        // foreach ($mail_id_array as $k => $val) {
                        //  Mail::to($val)->send(new OrderMailWithAttachment($attachmentPath, $orderdetail, Order::find($response['order_id'])));
                        // }
                    }
                }
                // $useractivity = array(
                //     'userid' => $user->id, 
                //     'latitude' => $request['latitude'], 
                //     'longitude' => $request['longitude'], 
                //     'type' => 'Order',
                //     'description' => $user->name.' Order to Submited',
                // );
                // submitUserActivity($useractivity);
                $customername = Customers::where('id', '=', $request['buyer_id'])->pluck('name')->first();

                $adminnotify = collect([
                    'title' => 'Order collected',
                    'body' =>  $user->name . ' has collected order at ' . $customername
                ]);
                sendNotification(39, $adminnotify);

                $zsmnotify = collect([
                    'title' => 'Order collected',
                    'body' =>  $user->name . ' has collected order at ' . $customername
                ]);
                sendNotification($user->reportingid, $zsmnotify);
                $asmnotify = collect([
                    'title' => 'Order successfully placed',
                    'body' =>  'Your order is successfully placed at ' . $customername
                ]);
                sendNotification($user->id, $asmnotify);
                return response()->json($response, $this->successStatus);
            }
            return response()->json($response, $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function addCartItems(Request $request)
    {
        try {
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'product_detail_id' => 'required|exists:product_details,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $data =  Cart::create([
                'customer_id' => isset($request->customer_id) ? $request->customer_id : null,
                'product_id' => isset($request->product_id) ? $request->product_id : null,
                'product_detail_id' => isset($request->product_detail_id) ? $request->product_detail_id : null,
                'quantity' => isset($request->quantity) ? $request->quantity : 1,
                'price' => isset($request->price) ? $request->price : 0.00,
                'discount' => isset($request->discount) ? $request->discount : 0.00,
                'total' => isset($request->total) ? $request->total : 0.00,
                'user_id' => isset($request->user_id) ? $request->user_id : $user->id,
                'created_at' => getcurentDateTime(),
            ]);
            if ($data) {
                return response(['status' => 'success', 'message' => 'Cart item added successfully.', 'data' => $data], 200);
            }
            return response(['status' => 'error', 'message' => 'Error in cart added.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getCartItems(Request $request)
    {
        try {
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $data =  Cart::with(array('products' => function ($query) {
                $query->select('id', 'product_name', 'product_image');
            }, 'productdetails' => function ($query) {
                $query->select('id', 'detail_title');
            }))->where('customer_id', $request->customer_id)->get();
            if ($data) {
                $date = strtotime("+5 day");
                $expected_date = date('M d, Y', $date);
                return response(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'expected_date' => $expected_date], 200);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getOrderPfd(Request $request)
    {
        try {
            $user = $request->user();
            $validator = Validator::make($request->all(), [
                'order_id' => 'required',
                'customer_type_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $data = [
                'order' => Order::with('sellers', 'buyers', 'orderdetails', 'orderdetails.products', 'orderdetails.products.productdetails')->find($request->order_id),
            ];
            if ($request->customer_type_id == '2') {
                $html = view('order_pdf.order_pdf_retailer', $data)->render();
                $pdfDirectory = public_path('pdf/orders/');
                File::makeDirectory($pdfDirectory, $mode = 0755, true, true);
                $pdfFilePath = $pdfDirectory . 'order_retailer_' . $request->order_id . '.pdf';
            } else {
                $html = view('order_pdf.order_pdf_dealer', $data)->render();
                $pdfDirectory = public_path('pdf/orders/');
                File::makeDirectory($pdfDirectory, $mode = 0755, true, true);
                $pdfFilePath = $pdfDirectory . 'order_dealer_' . $request->order_id . '.pdf';
            }

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            file_put_contents($pdfFilePath, $dompdf->output());
            $data_main['pdf_url'] = $url = url(str_replace('/var/www/html/', '', $pdfFilePath));
            return response(['status' => 'Success', 'message' => 'Data retrieved successfully.', 'data' => $data_main], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getClusterOrderList(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $user_ids = getUsersReportingToAuth($user_id);

            $query = $this->orders->where(function ($query) use ($user_ids) {
                $query->whereIn('created_by', $user_ids);
            })
                ->latest()
                ->where('cluster_discount', '!=', NULL);

            $start_date = $request->startdate ?? '';
            $end_date   = $request->enddate ?? '';
            $selecteduser_id = $request->user_id ?? '';
            $selectedstatus_id = $request->status_id ?? '';

            if (!empty($start_date) && !empty($end_date)) {
                $startDate = date('Y-m-d', strtotime($start_date));
                $endDate = date('Y-m-d', strtotime($end_date));
                $query->whereDate('order_date', '>=', $startDate)
                    ->whereDate('order_date', '<=', $endDate);
            }

            if (!empty($selecteduser_id)) {
                $query->where('created_by', $selecteduser_id);
            }

            if ((isset($selectedstatus_id) || $selectedstatus_id == 0) && $selectedstatus_id != '') {
                $query->where('discount_status', $selectedstatus_id);
            }
            $all_status = [['id' => '0', 'name' => 'Pending'], ['id' => '1', 'name' => 'Approved'], ['id' => '2', 'name' => 'Reject']];
            $users = User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 29);
            })->where('active', 'Y')->whereIn('id', $user_ids)->select('id', 'name')->orderBy('name', 'asc')->get();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'order_id' => isset($value['id']) ? $value['id'] : 0,
                        'seller_id' => isset($value['seller_id']) ? $value['seller_id'] : 0,
                        'seller_name' => isset($value['sellers']['name']) ? $value['sellers']['name'] : '',
                        'buyer_id' => isset($value['buyer_id']) ? $value['buyer_id'] : 0,
                        'buyer_name' => isset($value['buyers']['name']) ? $value['buyers']['name'] : '',
                        // 'total_qty' => isset($value['total_qty']) ? $value['total_qty'] : 0,
                        'total_qty' => $value->orderdetails->sum('quantity') ?? 0,
                        'shipped_qty' => isset($value['shipped_qty']) ? $value['shipped_qty'] : 0,
                        'orderno' => isset($value['orderno']) ? $value['orderno'] : '',
                        'order_date' => isset($value['order_date']) ? $value['order_date'] : '',
                        'completed_date' => isset($value['completed_date']) ? $value['completed_date'] : '',
                        'grand_total' => isset($value['grand_total']) ? $value['grand_total'] : 0.00,
                        'cluster_discount' => isset($value['cluster_discount']) ? $value['cluster_discount'] : 0.00,
                        'cluster_amount' => isset($value['cluster_amount']) ? $value['cluster_amount'] : 0.00,
                        'sub_total' => isset($value['sub_total']) ? $value['sub_total'] : 0.00,
                        'discount_status' => (($value['discount_status'] == '1') ? 'Approved' : (($value['discount_status'] == '2') ? 'Reject' : 'Pending')),
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'all_users' => $users, 'all_status' => $all_status], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data, 'all_users' => $users, 'all_status' => $all_status], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getSpecialOrderList(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $user_ids = getUsersReportingToAuth($user_id);

            $query = $this->orders->where(function ($query) use ($user_ids) {
                $query->whereIn('created_by', $user_ids);
            })
                ->latest()
                ->where(function ($query) {
                    $query->where('special_discount', '>', 0)
                        ->orWhere('deal_discount', '>', 0);
                });

            $start_date = $request->startdate ?? '';
            $end_date   = $request->enddate ?? '';
            $selecteduser_id = $request->user_id ?? '';
            $selectedstatus_id = $request->status_id ?? '';

            if (!empty($start_date) && !empty($end_date)) {
                $startDate = date('Y-m-d', strtotime($start_date));
                $endDate = date('Y-m-d', strtotime($end_date));
                $query->whereDate('order_date', '>=', $startDate)
                    ->whereDate('order_date', '<=', $endDate);
            }

            if (!empty($selecteduser_id)) {
                $query->where('created_by', $selecteduser_id);
            }

            if ((isset($selectedstatus_id) || $selectedstatus_id == 0) && $selectedstatus_id != '') {
                $query->where('discount_status', $selectedstatus_id);
            }
            $all_status = [['id' => '0', 'name' => 'Pending'], ['id' => '1', 'name' => 'Approved'], ['id' => '2', 'name' => 'Reject']];
            $users = User::whereDoesntHave('roles', function ($query) {
                $query->where('id', 29);
            })->where('active', 'Y')->whereIn('id', $user_ids)->select('id', 'name')->orderBy('name', 'asc')->get();
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'order_id' => isset($value['id']) ? $value['id'] : 0,
                        'seller_id' => isset($value['seller_id']) ? $value['seller_id'] : 0,
                        'seller_name' => isset($value['sellers']['name']) ? $value['sellers']['name'] : '',
                        'buyer_id' => isset($value['buyer_id']) ? $value['buyer_id'] : 0,
                        'buyer_name' => isset($value['buyers']['name']) ? $value['buyers']['name'] : '',
                        // 'total_qty' => isset($value['total_qty']) ? $value['total_qty'] : 0,
                        'total_qty' => $value->orderdetails->sum('quantity') ?? 0,
                        'shipped_qty' => isset($value['shipped_qty']) ? $value['shipped_qty'] : 0,
                        'orderno' => isset($value['orderno']) ? $value['orderno'] : '',
                        'order_date' => isset($value['order_date']) ? $value['order_date'] : '',
                        'completed_date' => isset($value['completed_date']) ? $value['completed_date'] : '',
                        'grand_total' => isset($value['grand_total']) ? $value['grand_total'] : 0.00,
                        'special_discount' => isset($value['special_discount']) ? $value['special_discount'] : 0.00,
                        'special_amount' => isset($value['special_amount']) ? $value['special_amount'] : 0.00,
                        'deal_discount' => isset($value['deal_discount']) ? $value['deal_discount'] : 0.00,
                        'deal_amount' => isset($value['deal_amount']) ? $value['deal_amount'] : 0.00,
                        'sub_total' => isset($value['sub_total']) ? $value['sub_total'] : 0.00,
                        'discount_status' => (($value['discount_status'] == '1') ? 'Approved' : (($value['discount_status'] == '2') ? 'Reject' : 'Pending')),
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'all_users' => $users, 'all_status' => $all_status], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data, 'all_users' => $users, 'all_status' => $all_status], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function updateClusterOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
                'sub_total' => 'required',
                'grand_total' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $order = Order::find($request->order_id);
            if ($order) {
                $order->sub_total = $request->sub_total;
                $order->grand_total = $request->grand_total;
                $order->gst_amount = $request->gst_amount ?? '';
                $order->cluster_discount = $request->cluster_discount ?? '';
                $order->cluster_amount = $request->cluster_amount ?? '';
                $order->deal_discount = $request->deal_discount ?? '';
                $order->deal_amount = $request->deal_amount ?? '';
                $order->distributor_discount = $request->distributor_discount ?? '';
                $order->distributor_amount = $request->distributor_amount ?? '';
                $order->frieght_discount = $request->frieght_discount ?? '';
                $order->frieght_amount = $request->frieght_amount ?? '';
                $order->discount_status = $request->discount_status ?? '0';
                $order->sp_discount_status = $request->sp_discount_status ?? '0';
                $order->cash_discount = $request->cash_discount ?? '0';
                $order->cash_amount = $request->cash_amount ?? '0';
                $order->total_discount = $request->total_discount ?? '0';
                $order->total_amount = $request->total_amount ?? '0';
                $order->gst5_amt = $request->gst5_amt ?? NULL;
                $order->gst12_amt = $request->gst12_amt ?? NULL;
                $order->gst18_amt = $request->gst18_amt ?? NULL;
                $order->gst28_amt = $request->gst28_amt ?? NULL;
                $order->ebd_discount = $request->ebd_discount ?? NULL;
                $order->ebd_amount = $request->ebd_amount ?? NULL;
                $order->special_discount = $request->special_discount ?? NULL;
                $order->special_amount = $request->special_amount ?? NULL;
                $order->updated_at = getcurentDateTime();
                $order->updated_by = auth()->user()->id;
                $order->save();
                return response()->json(['status' => 'success', 'message' => 'Data updated successfully.', 'data' => $order], 200);
            } else {
                return response(['status' => 'error', 'message' => 'Order Not Found.', 'data' => NULL], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function deleteOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
        }

        OrderDetails::where('order_id', $request->order_id)->delete();
        Order::where('id', $request->order_id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Order deleted successfully.'], 200);
    }

    public function submitFullyDispatched(Request $request)
    {
        //$orderid = decrypt($orderid);
        try {

            $validator = Validator::make($request->all(), [
                'invoice_no'       => 'required',
                'order_id'         => 'required',
                'invoice_date'     => 'required',
                // 'transport_name'   => 'required',
                // 'lr_no'            => 'required',
                'dispatch_date'    => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $orderid = $request['order_id'];
            
            $status_id = Status::where('status_name', '=', 'Dispatched')->pluck('id')->first();
            // Order::where('id', '=', $orderid)->update(['status_id' => $status_id, 'cash_discount' => $request->cash_discount, 'cash_amount' => $request->cash_amount, 'sub_total' => $request->sub_total, 'grand_total' => $request->grand_total, 'order_remark' => $request->order_remark]);
            $orders = $this->orders->with('orderdetails')->find($orderid);
            $orders['invoice_date'] = $request['invoice_date'];
            $orders['invoice_no'] = $request['invoice_no'];
            $orders['transport_name'] = $request['transport_name'];
            $orders['lr_no'] = $request['lr_no'];
            $orders['dispatch_date'] = $request['dispatch_date'];
            $orders['transport_details'] = $request['transport_details'];
            $orders['order_id'] = $orderid;
            $orders['status_id'] = $status_id;
            $orders['saledetail'] = $orders['orderdetails'];
            $data = collect([$orders]);
            
            
            // dd($data);
            $response = insertSales($data);
            if ($response['status'] == 'success') {

                $status_id = Status::where('status_name', '=', 'Dispatched')->pluck('id')->first();

                Order::where('id', '=', $request['order_id'])->update(['status_id' => $status_id]);

                return response(['status' => 'success', 'message' => 'Order Dispatched Successfully.'], 200);
            } else {
                Order::where('id', '=', $orderid)->update(['status_id' => null]);
                return response(['status' => 'error', 'message' => 'Order Status Not Updated.'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function submitPartiallyDispatched(Request $request)
    {
        try {
            $user = $request->user();
            $request['active'] = 'Y';
            $request['created_by'] = $user->id;
            $validator = Validator::make($request->all(), [
                'buyer_id' => 'required',
                'seller_id' => 'required',
                'invoice_no' => 'required',
                'invoice_date' => 'required',
                'order_id' => 'required',
                'grand_total' => 'required',
                'lr_no'            => 'required',
                'dispatch_date'    => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $order = Order::where('id', '=', $request['order_id'])->first();
            
            $request['orderno'] = $order->orderno;
            $request['saledetail'] = $request['orderdetail'];
            // $request['status_id'] = status_id;
            $data = collect([$request]);
            $response = insertSales($data);
            if ($response['status'] == 'success') {
                $partiallystatus = $request['status_id'];
                if (isset($request['orderdetail'])) {
                    foreach ($request['orderdetail'] as $key => $rows) {
                        $orderdetail = OrderDetails::where('order_id', '=', $request['order_id'])
                            ->where('product_id', '=', ($rows['product_id'] ?? ''))->first();
                        if (isset($orderdetail)) {
                            // $orderdetail->cash_dis = $rows['cash_dis'];
                            // $orderdetail->cash_amounts = $rows['cash_amounts'];
                            $orderdetail->status_id = $request['status_id'];
                            $orderdetail->increment('shipped_qty', $rows['quantity']);
                            $orderdetail->save();
                        }
                    }
                }
                if (OrderDetails::where('order_id', '=', $request['order_id'])->where('status_id', '=', $partiallystatus)->exists()) {
                    Order::where('id', '=', $request['order_id'])->update(['status_id' => $partiallystatus, 'cash_discount' => $request->cash_discount, 'cash_amount' => $request->cash_amount, 'order_remark' => $request->order_remark]);
                } else {
                    Order::where('id', '=', $request['order_id'])->update(['status_id' => $partiallystatus, 'cash_discount' => $request->cash_discount, 'cash_amount' => $request->cash_amount, 'order_remark' => $request->order_remark]);
                }
                return response(['status' => 'success', 'message' => 'Order Partially Dispatched Successfully.'], 200);
            }
            return response(['status' => 'error', 'message' => 'Order Status Not Updated.'], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
