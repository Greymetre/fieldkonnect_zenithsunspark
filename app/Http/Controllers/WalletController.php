<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Requests\WalletRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\Models\WalletDetail;
use App\DataTables\WalletDataTable;
use App\DataTables\RedeemedPointDataTable;
use App\Exports\WalletExport;
use App\Exports\BeatTemplate;
use App\Models\Customers;


class WalletController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->wallets = new Wallet();
        
        
    }

    public function index(WalletDataTable $dataTable)
    {
        abort_if(Gate::denies('wallet_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customers = Customers::whereHas('customertypes', function($query){
                                $query->where('type_name', '=', 'retailer');
                            })->select('id','name','mobile')->get();
        return $dataTable->render('wallets.index',compact('customers'));
    }


    public function show($id)
    {
        abort_if(Gate::denies('wallet_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $wallets = Wallet::find($id);
        return view('wallets.show')->with('wallets',$wallets);
    }

    public function store(Request $request)
    {
        $data = collect([ 
                    'customer_id' => $request['customer_id'],
                    'coupon_code' => $request['coupon_code']
                ]);
        $response = couponScans($data);
    }

    public function redeemedPoint(RedeemedPointDataTable $dataTable)
    {
         abort_if(Gate::denies('redeemedpoint_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('wallets.redeemedPoint');
    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('wallet_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new BeatImport,request()->file('import_file'));
        return back();
    }
    public function destroy($id)
    {
        ////abort_if(Gate::denies('customer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try
        { 
            WalletDetail::where('wallet_id',$id)->delete();
            $wallet = Wallet::find($id);
            if($wallet->delete())
            {
                return response()->json(['status' => 'success','message' => 'Wallet deleted successfully!']);
            }
            return response()->json(['status' => 'error','message' => 'Error in Wallet Delete!']);
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
    public function download()
    {
      abort_if(Gate::denies('wallet_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new WalletExport, 'wallets.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('wallet_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new BeatTemplate, 'beats.xlsx');
    }

    public function walletsInfo(Request $request)
    {
        if ($request->ajax()) {
            $data = Wallet::latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('transaction_at', function($data)
                    {
                        return isset($data->transaction_at) ? showdatetimeformat($data->transaction_at) : '';
                    })
                    ->filter(function ($query) use ($request) {
                        if(!empty($request['customer_id']))
                        {
                            $query->where('customer_id', $request['customer_id']);
                        }
                        if(!empty($request['coupon']))
                        {
                            $query->whereNotNull('coupon_code');
                        }
                        if(!empty($request['redeem']))
                        {
                            $query->where('transaction_type', 'Dr');
                        }
                    })
                    ->make(true);
        }
    }
}
