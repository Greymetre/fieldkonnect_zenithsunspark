<?php

namespace App\Http\Controllers;

use App\Models\Coupons;
use Illuminate\Http\Request;
use App\Models\CouponProfile;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\DataTables\CouponsDataTable;
use App\DataTables\CouponProfileDataTable;
use App\Imports\CouponsImport;
use App\Exports\CouponsExport;
use App\Exports\CouponsTemplate;
use App\Http\Requests\CouponsRequest;

class CouponsController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->coupons = new Coupons();
        $this->couponprofile = new CouponProfile();
        
    }
    
    public function index(CouponsDataTable $dataTable)
    {
        abort_if(Gate::denies('coupon_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('coupons.index');
    }

    public function couponprofile(CouponProfileDataTable $dataTable)
    {
        abort_if(Gate::denies('couponprofile_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('coupons.couponprofile');
    }

    public function create()
    {
        abort_if(Gate::denies('coupon_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('coupons.create')->with('coupons',$this->coupons);
    }

    public function store(CouponsRequest $request)
    {
        try
        { 
            abort_if(Gate::denies('coupon_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if($coupon_profile_id = $this->couponprofile->insertGetId([
                'active' => 'Y',
                'profile_name' => isset($request['profile_name']) ? $request['profile_name'] : '',
                'coupon_length' => isset($request['coupon_length']) ? $request['coupon_length'] : null, 
                'excluding_character' => isset($request['excluding_character']) ? $request['excluding_character'] : null,
                'coupon_count' => isset($request['coupon_count']) ? $request['coupon_count'] : null,
                'created_by' => Auth::user()->id,
                'created_at' => getcurentDateTime(),
            ]) )
            {
                $existcodes = $this->coupons->pluck('coupon_code');
                $strings = $this->generate_string($request);
                $newcoupons = collect([]);
                for ($i=1; $i <= $request['coupon_count']; $i++  ) {

                    $coupon_code = $this->generate_coupons($request['coupon_length'] , $strings);
                    if($existcodes->contains($coupon_code))
                    {
                        $i--;
                    }
                    else
                    {
                        $newcoupons->push([
                            'active' => 'Y',
                            'coupon_code' => $coupon_code,
                            'expiry_date' => null,
                            'generated_date' => date('Y-m-d'),
                            'customer_code' => '',
                            'invoice_date' => '',
                            'invoice_no' => '',
                            'product_code' => '',
                            'coupon_profile_id' => $coupon_profile_id,
                            'created_at' => getcurentDateTime(),
                        ]);
                        $existcodes->push($coupon_code);
                    }
                    
                }
                if($newcoupons->isNotEmpty())
                {
                    foreach($newcoupons->chunk(1000) as $coupons)
                    {
                        Coupons::insert($coupons->toArray());
                    }
                    return Redirect::to('coupons')->with('message_success', 'Coupons Generated Successfully');
                } 
            }
            return Redirect::to('coupons')->with('message_danger', 'Error in Coupons Generated');  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function generate_string($request) 
    {
        if($request['coupon_type'] == 'numeric')
        {
            $strings = "0123456789";
        }
        elseif ($request['coupon_type'] == 'alphabetic') {
            $strings = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        else
        {
            $strings = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        $remove_str = explode(',', $request['excluding_character']);
        foreach($remove_str as $value) {
           $strings = str_replace($value, '', $strings);
        }
        return $strings;
    }

    function generate_coupons($length,$strings) 
    {
        return substr(str_shuffle(str_repeat($strings, $length)), 0, $length);
    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('coupon_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new CouponsImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('coupon_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CouponsExport, 'coupons.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('coupon_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CouponsTemplate, 'coupons.xlsx');
    }
}
