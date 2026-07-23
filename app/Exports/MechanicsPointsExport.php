<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Customers;
use App\Models\SalesDetails;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class MechanicsPointsExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->fromdate = date("Y-m-01");
        $this->todate = date("Y-m-t");
        $this->userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        
    }

    public function collection()
    {
        $userinfo = User::with('reportinginfo')->where('id', $this->userid)->select('name','location','reportingid')->first();

        return collect([
              collect(["start_date" => date("Y-01-01"), "end_date" => date("Y-01-31"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-02-01"), "end_date" => date("Y-02-29"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-03-01"), "end_date" => date("Y-03-31"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-04-01"), "end_date" => date("Y-04-30"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-05-01"), "end_date" => date("Y-05-31"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-06-01"), "end_date" => date("Y-06-30"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-07-01"), "end_date" => date("Y-07-31"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-08-01"), "end_date" => date("Y-08-31"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-09-01"), "end_date" => date("Y-09-30"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-10-01"), "end_date" => date("Y-10-31"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-11-01"), "end_date" => date("Y-11-30"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
              collect(["start_date" => date("Y-12-01"), "end_date" => date("Y-12-31"), "user_name" => isset($userinfo['name']) ? $userinfo['name'] : '', "location" => isset($userinfo['location']) ? $userinfo['location'] : '' , "state" => isset($userinfo['location']) ? $userinfo['location'] : '' , "reporting" => isset($userinfo['reportinginfo']['name']) ? $userinfo['reportinginfo']['name'] : '' ]), 
            ]);
    }

    public function headings(): array
    {
        return ['Name of ASM','Month','H.Q', 'Territory', 'Name of ZSM', 'No. of Mech Coupon Scheme', 'No. of Mech MRP Scheme', 'Collection till Month End Coupons value', 'Collection till Month End MRP Value', 'Collection in Coupons value', 'Collection in MRP Value','Secondary Sales GGL','Secondary Sales GPD', 'Secondary Sales Dif', 'Secondary Sales Total'];
    }

    public function map($data): array
    {
        $customers = Customers::where('created_by','=',$this->userid)
                                ->where('created_at', '>=', $data['start_date'])
                                ->where('created_at', '<=', $data['end_date'])
                                ->select('customertype')->get();
        $points = Wallet::with('customers')
                            ->where('userid','=',$this->userid)
                            ->where('transaction_at', '>=', $data['start_date'])
                            ->where('transaction_at', '<=', $data['end_date'])
                            ->where('transaction_type','=','Cr')
                            ->select('points','point_type','quantity')
                            ->get();
        $sales = SalesDetails::with('products')->whereHas('sales',function($query) use($data) {
                                    $query->where('created_by','=',$this->userid);
                                    $query->where('invoice_date', '>=', $data['start_date']);
                                    $query->where('invoice_date', '<=', $data['end_date']);
                                })
                                ->select('product_id','line_total')
                                ->get();

        return [
            isset($data['user_name']) ? $data['user_name'] : '' ,
            date('Y/m',strtotime($data['start_date'])),
            isset($data['state']) ? $data['state'] :'',
            isset($data['location']) ? $data['location'] :'',
            isset($data['reporting']) ? $data['reporting'] :'',  
            $points->where('customers.customertype','=','4')->where('point_type','=','gift coupon')->unique('customer_id')->count(),
            $points->where('customers.customertype','=','4')->where('point_type','=','mrp lable')->unique('customer_id')->count(),
            $points->where('customers.customertype','=','4')->where('point_type','=','gift coupon')->sum('quantity'),
            $points->where('customers.customertype','=','4')->where('point_type','=','mrp lable')->sum('quantity'),
            $sales->where('products.category_id','=','3')->sum('line_total'),
            $sales->where('products.category_id','=','2')->sum('line_total'),
            $sales->where('products.category_id','=','1')->sum('line_total'),
            $sales->sum('line_total'),
        ];
    }
}