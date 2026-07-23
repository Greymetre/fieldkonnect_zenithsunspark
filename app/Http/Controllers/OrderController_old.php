<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Customers;
use App\Models\Status;
use App\Models\City;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\OrderDataTable;
use App\Imports\OrderImport;
use App\Exports\OrderExport;
use App\Exports\OrderTemplate;
use App\Http\Requests\OrderRequest;

class OrderController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->orders = new Order();
        
        
    }

    public function index(OrderDataTable $dataTable)
    {
        abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('orders.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $products = Product::where('active','=','Y')->select('id', 'product_name','product_image')->get();
        $userids = getUsersReportingToAuth();
        $sellers = Customers::whereHas('customertypes', function($query){
                                $query->where('type_name', '=', 'distributor');
                            })
                            ->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('executive_id',$userids);
                                }
                            })
                            ->where('active','=','Y')
                            ->select('id', 'name','mobile')
                            ->get();
        $buyers = Customers::whereIn('customertype', ['2','3','4','5','6'])
                            ->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('executive_id',$userids);
                                }
                            })
                            ->where('active','=','Y')
                            ->select('id', 'name','mobile')
                            ->get();

        $users = User::where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name')->orderBy('id','desc')->get();                    



        return view('orders.create',compact('products','sellers','buyers','users'))->with('orders',$this->orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        try
        { 
            abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['created_by'] = Auth::user()->id;
            $request['orderno'] = isset($request['orderno']) ? $request['orderno'] : date('Ymd').'_'.autoIncrementId('Order','id') ;
            $response =  $this->orders->save_data($request);
            if($response['status'] == 'success')
            {
                $orderdetail = collect([]);
                foreach ($request['orderdetail'] as $key => $rows) {
                    $orderdetail->push([
                        'active' => 'Y',
                        'order_id' => isset($response['order_id']) ? $response['order_id'] :null,
                        'product_id' => isset($rows['product_id']) ? $rows['product_id'] :null,
                        'product_detail_id' => isset($rows['product_detail']) ? $rows['product_detail'] :null,
                        'quantity' => isset($rows['quantity']) ? $rows['quantity'] :0,
                        'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] :0,
                        'price' => isset($rows['price']) ? $rows['price'] :0.00,
                        'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] :0.00,
                        'line_total' => isset($rows['line_total']) ? $rows['line_total'] :0.00,
                        'created_at' => getcurentDateTime(),
                    ]);
                }

                if($orderdetail->isNotEmpty())
                {
                    OrderDetails::insert($orderdetail->toArray());
                }
                return Redirect::to('orders')->with('message_success', 'Order Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Purchases Store')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $orders = $this->orders->with('sellers')->find($id);
        $orderdetails = OrderDetails::with('products')->where('order_id','=',$id)->get();
        return view('orders.show',compact('orderdetails','orders'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $userids = getUsersReportingToAuth();
        $orders = $this->orders->with('orderdetails')->find($id);
        $orderdetail = OrderDetails::with('products')->where('order_id','=',$id)->get();
        $products = Product::where('active','=','Y')->select('id', 'display_name','product_image')->get();
        $sellers = Customers::whereHas('customertypes', function($query){
                                $query->where('type_name', '=', 'distributor');
                            })
                            ->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('executive_id',$userids);
                                }
                            })
                            ->where('active','=','Y')
                            ->select('id', 'name','mobile')
                            ->get();
        $buyers = Customers::whereIn('customertype', ['2','3','4','5','6'])
                            ->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('executive_id',$userids);
                                }
                            })
                            ->where('active','=','Y')
                            ->select('id', 'name','mobile')
                            ->get();

        $users = User::where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name')->orderBy('id','desc')->get();                        


        return view('orders.create',compact('products','sellers','buyers','orderdetail','users'))->with('orders',$orders);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(OrderRequest $request, $id)
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $orders = Order::with('orderdetails')->find($id);
        $orders->buyer_id = isset($request['buyer_id']) ? $request['buyer_id'] :null ;
        $orders->seller_id = isset($request['seller_id']) ? $request['seller_id'] :null ;
        $orders->order_date = isset($request['order_date']) ? $request['order_date'] :null ;
        $orders->total_gst = isset($request['total_gst']) ? $request['total_gst'] : 0.00 ;
        $orders->total_discount = isset($request['total_discount']) ? $request['total_discount'] : 0.00 ;
        $orders->extra_discount = isset($request['extra_discount']) ? $request['extra_discount'] : 0.00 ;
        $orders->sub_total = isset($request['sub_total']) ? $request['sub_total'] : 0.00 ;
        $orders->grand_total = isset($request['grand_total']) ? $request['grand_total'] : 0.00 ;
        $orders->order_taking = isset($request['order_taking']) ? $request['order_taking'] :'' ;
        $orders->suc_del = isset($request['suc_del']) ? $request['suc_del'] :'' ;
        $orders->updated_by = Auth::user()->id ;
        if($orders->save())
        {
            foreach ($request['orderdetail'] as $key => $rows) {
                    OrderDetails::updateOrCreate(['product_id' => $request['product_id'], 'order_id' => $id], [
                        'order_id' => $id,
                        'product_id' => isset($rows['product_id']) ? $rows['product_id'] :null,
                        'product_detail_id' => isset($rows['product_detail']) ? $rows['product_detail'] :null,
                        'quantity' => isset($rows['quantity']) ? $rows['quantity'] :0,
                        'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] :0,
                        'price' => isset($rows['price']) ? $rows['price'] :0.00,
                        'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] :0.00,
                        'line_total' => isset($rows['line_total']) ? $rows['line_total'] :0.00,
                        'created_at' => getcurentDateTime(),
                    ]);
                }
           return Redirect::to('orders')->with('message_success', 'Order update Successfully');
        }
        return redirect()->back()->with('message_danger', 'Error in Purchases Store')->withInput();
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        OrderDetails::where('order_id',$id)->delete();
        $product = Order::find($id);
        if($product->delete())
        {
            return response()->json(['status' => 'success','message' => 'Order deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in User Delete!']);
    }
    
    public function active(Request $request)
    {
        if(Order::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Order '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
        abort_if(Gate::denies('order_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new OrderImport,request()->file('import_file'));
        return back();
    }
    public function download(Request $request)
    {
        abort_if(Gate::denies('order_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new OrderExport($request), 'orders.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('order_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new OrderTemplate, 'orders.xlsx');
    }

    public function ordersInfo(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::with('sellers','buyers')
                            ->latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function($data)
                    {
                        return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                    })
                    ->editColumn('order_date', function($data)
                    {
                        return isset($data->order_date) ? showdateformat($data->order_date) : '';
                    })
                    ->addColumn('action', function ($query) {
                          $btn = '';
                          if(auth()->user()->can(['order_show']))
                          {
                            $btn = $btn.'<a href="'.url("orders/".encrypt($query->id)).'" class="btn btn-theme btn-just-icon btn-sm" title="'.trans('panel.global.show').' '.trans('panel.orders.title_singular').'">
                                            <i class="material-icons">visibility</i>
                                        </a>';
                          }
                          return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                        '.$btn.'
                                    </div>';
                    })
                    ->filter(function ($query) use ($request) {
                        if(!empty($request['buyer_id']))
                        {
                            $query->where('buyer_id', $request['buyer_id'])->orWhere('seller_id', $request['buyer_id']);
                        }
                        if(!empty($request['seller_id']))
                        {
                            $query->where('seller_id', $request['seller_id'])->orWhere('buyer_id', $request['buyer_id']);
                        }
                        if(!empty($request['created_by']))
                        {
                            $query->where('created_by', $request['created_by']);
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

    public function ordertopoint()
    {

        $orders = Order::with('orderdetails')->whereNotNull(['buyer_id','seller_id'])->select('orderno as invoice_no','id','buyer_id','seller_id','grand_total','order_date as invoice_date','id as order_id','total_qty','shipped_qty','total_gst','status_id')->get();

        foreach ($orders as $key => $order) {
            $details = collect([]);
            $data = collect([
                'order_id' => isset($order['order_id']) ? $order['order_id'] :null,
                'invoice_no' => isset($order['invoice_no']) ? $order['invoice_no'] :null,
                'buyer_id' => isset($order['buyer_id']) ? $order['buyer_id'] :null,
                'seller_id' => isset($order['seller_id']) ? $order['seller_id'] :null,
                'grand_total' => isset($order['grand_total']) ? $order['grand_total'] :0.00,
                'invoice_date' => isset($order['invoice_date']) ? $order['invoice_date'] :null,
                'order_id' => isset($order['order_id']) ? $order['order_id'] :null,
                'total_qty' => isset($order['total_qty']) ? $order['total_qty'] :null,
                'shipped_qty' => isset($order['shipped_qty']) ? $order['shipped_qty'] :null,
                'total_gst' => isset($order['total_gst']) ? $order['total_gst'] :null,
                'status_id' => isset($order['status_id']) ? $order['status_id'] :null,
            ]);
            if(!empty($order['orderdetails']))
            {
                foreach ($order['orderdetails'] as $key => $rows) {
                    $details->push([
                        'order_id' => isset($rows['order_id']) ? $rows['order_id'] :null,
                        'product_id' => isset($rows['product_id']) ? $rows['product_id'] :null,
                        'quantity' => isset($rows['quantity']) ? $rows['quantity'] :0,
                        'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] :0,
                        'price' => isset($rows['price']) ? $rows['price'] :0.00,
                        'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] :0.00,
                        'line_total' => isset($rows['line_total']) ? $rows['line_total'] :0.00,
                    ]);
                }
            }
            $data['saledetail'] =  $details;
            $finaldata = collect([$data]);
            insertSales($finaldata);
        }
    }

    public function orderDispatched($orderid)
    {
        $orderid = decrypt($orderid);
        $status_id = Status::where('status_name','=','Dispatched')->pluck('id')->first();
        Order::where('id','=',$orderid)->update(['status_id' => $status_id]);
        $orders = $this->orders->with('orderdetails')->find($orderid);
        $orders['invoice_date'] = date('Y-m-d');
        $orders['invoice_no'] = $orderid.'-'.autoIncrementId('Sales','id') ;
        $orders['order_id'] = $orderid ;
        $orders['saledetail'] = $orders['orderdetails'];
        $data = collect([$orders]);
        $response = insertSales($data);
        if($response['status'] == 'success')
        {
            OrderDetails::where('order_id','=',$orderid)->update(['status_id' => $status_id]);
          return Redirect::to('orders')->with('message_success', 'Sales Store Successfully');
        }
        else
        {
            Order::where('id','=',$orderid)->update(['status_id' => null]);
        }
    }

    public function orderPartiallyDispatched($orderid)
    {
        $orderid = decrypt($orderid);
        $orders = $this->orders->with('orderdetails')->find($orderid);
        return view('orders.dispatched')->with('orders',$orders);
    }

    public function submitDispatched(Request $request)
    {
        try
        { 
            $request['active'] = 'Y';
            $request['created_by'] = Auth::user()->id;
            $validator = Validator::make($request->all(), [
                'buyer_id' => 'required',
                'seller_id' => 'required',
                'invoice_no' => 'required',
                'order_id' => 'required',
                'grand_total' => 'required',
            ]); 
            if ($validator->fails()) {
                return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
            }
            $request['saledetail'] = $request['orderdetails'];
            $data = collect([$request]);
            $response = insertSales($data);
            if($response['status'] == 'success')
            {
                $status_id = Status::where('status_name','=','Dispatched')->pluck('id')->first();
                $partiallystatus = Status::where('status_name','=','Partially Dispatched')->pluck('id')->first();
                

                if($request['orderdetail'])
                {
                    foreach ($request['orderdetail'] as $key => $rows) {

                        $orderdetail = OrderDetails::where('order_id','=',$request['order_id'])
                                                    ->where('product_detail_id','=',$rows['product_detail'])->first();
                        if($orderdetail['shipped_qty'] + $rows['quantity'] == $orderdetail['quantity'] )
                        {
                            $orderdetail->status_id = $status_id ;
                        }
                        else
                        {
                            $orderdetail->status_id = $partiallystatus ;
                        }
                        $orderdetail->increment('shipped_qty',$rows['quantity']);
                        $orderdetail->save();
                    }
                }

                if(OrderDetails::where('order_id','=',$request['order_id'])->where('status_id','=',$partiallystatus)->exists())
                {
                    Order::where('id','=',$request['order_id'])->update(['status_id' => $partiallystatus]);
                }
                else
                {
                    Order::where('id','=',$request['order_id'])->update(['status_id' => $status_id]);
                }
              return Redirect::to('sales')->with('message_success', 'Sales Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Sales Store')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function expectedDelivery(Request $request)
    {
        $cities = City::select('id','city_name')->get();
        $palaces =PlaceDispatch::select('city_name','pincode','days')->get();
        return view('orders.delivery',compact('cities','palaces') );
    }

    public function submitExpectedDelivery(Request $request)
    {
        foreach ($request['detail'] as $key => $rows) {
            if(!empty($rows['pincode']))
            {
                PlaceDispatch::updateOrCreate(['pincode' => $rows['pincode']],[
                    'city_name'      => isset($rows['city_name']) ? $rows['city_name'] :null,
                    'pincode'      => isset($rows['pincode']) ? $rows['pincode'] :null,
                    'days'      => isset($rows['days']) ? $rows['days'] :null,
                ]);
            }
            
        }
        return Redirect::to('expected-delivery')->with('message_success', 'PlaceDispatch Update Successfully');
    }
}
