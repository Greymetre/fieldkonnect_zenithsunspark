<?php

namespace App\Exports;

use App\Models\TourDetail;
use App\Models\Wallet;
use App\Models\CheckIn;
use App\Models\Customers;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class PerformanceParameterExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->fromdate = date("Y-m-01");
        $this->todate = date("Y-m-t");
        $this->userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        
    }

    public function collection()
    {
        return collect([
            collect(["name" => "days_toured", "parameter" => "NO. OF DAYS TOURED"]), 
            collect(["name" => "cities_covered", "parameter" => "NO. OF CITIES COVERED"]),
            collect(["name" => "mechanic_visited", "parameter" => "NO. OF MECHANIC VISTED"]),
            collect(["name" => "dealer_visited", "parameter" => "NO. OF DEALER VISITED"]),
            collect(["name" => "gift_collected", "parameter" => "MECHANIC GIFT POINT COLLECTED"]),
            collect(["name" => "mechanic_registered", "parameter" => "NO. OF MECHANIC REGISTERED"]),
            collect(["name" => "gift_settled", "parameter" => "NOS. OF GIFT SETTLED"]),
            collect(["name" => "mechanic_points", "parameter" => "NO. OF MECHANIC POINTS GIVEN"]),
            collect(["name" => "order_collected", "parameter" => "ORDER COLLECTED(VALUE IN LAC)"])
        ]);
    }

    public function headings(): array
    {
        return ['Parameter Of Visit','JAN','FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT','NOV','DEC', 'TOTAL', 'APM'];
    }

    public function map($data): array
    {
        switch ($data['name']) {
            case 'days_toured':
                $tours = TourDetail::with('tourinfo')->whereHas('tourinfo', function ($query) {
                                $query->where('userid','=',$this->userid);
                                $query->whereYear('date','=',date('Y'));
                            })
                            ->whereNotNull('visited_cityid')
                            ->select('tourid','visited_cityid')->get();
                $data['jan'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-01-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-01-31"))))->count();
                $data['feb'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-02-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-02-29"))))->count();
                $data['mar'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-03-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-03-31"))))->count();
                $data['apr'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-04-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-04-30"))))->count();
                $data['may'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-05-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-05-31"))))->count();
                $data['jun'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-06-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-06-30"))))->count();
                $data['jul'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-07-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-07-31"))))->count();
                $data['aug'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-08-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-08-31"))))->count();
                $data['sep'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-09-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-09-30"))))->count();
                $data['oct'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-10-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-10-31"))))->count();
                $data['nov'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-11-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-11-30"))))->count();
                $data['dec'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-12-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-12-31"))))->count();
                $data['total'] = $tours->count();
                $data['apm'] =  $tours->count();
                break;
            case 'cities_covered':
                $tours = TourDetail::with('tourinfo')->whereHas('tourinfo', function ($query) {
                                $query->where('userid','=',$this->userid);
                                $query->whereYear('date','=',date('Y'));
                            })
                            ->whereNotNull('visited_cityid')
                            ->select('tourid','visited_cityid')->get();
                $data['jan'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-01-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-01-31"))))->unique('visited_cityid')->count();
                $data['feb'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-02-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-02-29"))))->unique('visited_cityid')->count();
                $data['mar'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-03-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-03-31"))))->unique('visited_cityid')->count();
                $data['apr'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-04-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-04-30"))))->unique('visited_cityid')->count();
                $data['may'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-05-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-05-31"))))->unique('visited_cityid')->count();
                $data['jun'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-06-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-06-30"))))->unique('visited_cityid')->count();
                $data['jul'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-07-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-07-31"))))->unique('visited_cityid')->count();
                $data['aug'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-08-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-08-31"))))->unique('visited_cityid')->count();
                $data['sep'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-09-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-09-30"))))->unique('visited_cityid')->count();
                $data['oct'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-10-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-10-31"))))->unique('visited_cityid')->count();
                $data['nov'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-11-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-11-30"))))->unique('visited_cityid')->count();
                $data['dec'] = $tours->where('tourinfo.date', '>=', date('Y-m-d',strtotime(date("Y-12-01"))))->where('tourinfo.date', '<=', date('Y-m-d',strtotime(date("Y-12-31"))))->unique('visited_cityid')->count();
                $data['total'] = $tours->unique('visited_cityid')->count();
                $data['apm'] =  $tours->unique('visited_cityid')->count();
                break;
            case 'mechanic_visited':
                $mechanicvisited = CheckIn::whereHas('customers', function ($query) {
                                            $query->where('customertype','=',4);
                                        })
                                        ->where('user_id','=',$this->userid)
                                        ->whereYear('checkin_date','=',date('Y'))
                                        ->select('checkin_date','customer_id')->get();
                $data['jan'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-01-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-01-31"))))->unique('customer_id')->count();
                $data['feb'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-02-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-02-29"))))->unique('customer_id')->count();
                $data['mar'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-03-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-03-31"))))->unique('customer_id')->count();
                $data['apr'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-04-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-04-30"))))->unique('customer_id')->count();
                $data['may'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-05-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-05-31"))))->unique('customer_id')->count();
                $data['jun'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-06-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-06-30"))))->unique('customer_id')->count();
                $data['jul'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-07-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-07-31"))))->unique('customer_id')->count();
                $data['aug'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-08-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-08-31"))))->unique('customer_id')->count();
                $data['sep'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-09-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-09-30"))))->unique('customer_id')->count();
                $data['oct'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-10-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-10-31"))))->unique('customer_id')->count();
                $data['nov'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-11-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-11-30"))))->unique('customer_id')->count();
                $data['dec'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-12-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-12-31"))))->unique('customer_id')->count();
                $data['total'] = $mechanicvisited->unique('customer_id')->count();
                $data['apm'] =  $mechanicvisited->unique('customer_id')->count();
                break;
            case 'dealer_visited':
                $dealervisited = CheckIn::whereHas('customers', function ($query) {
                                                        $query->where('customertype','=',2);
                                                    })
                                                    ->where('user_id','=',$this->userid)
                                                    ->whereYear('checkin_date','=',date('Y'))
                                                    ->select('checkin_date')->get();
                $data['jan'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-01-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-01-31"))))->unique('customer_id')->count();
                $data['feb'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-02-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-02-29"))))->unique('customer_id')->count();
                $data['mar'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-03-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-03-31"))))->unique('customer_id')->count();
                $data['apr'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-04-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-04-30"))))->unique('customer_id')->count();
                $data['may'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-05-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-05-31"))))->unique('customer_id')->count();
                $data['jun'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-06-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-06-30"))))->unique('customer_id')->count();
                $data['jul'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-07-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-07-31"))))->unique('customer_id')->count();
                $data['aug'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-08-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-08-31"))))->unique('customer_id')->count();
                $data['sep'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-09-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-09-30"))))->unique('customer_id')->count();
                $data['oct'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-10-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-10-31"))))->unique('customer_id')->count();
                $data['nov'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-11-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-11-30"))))->unique('customer_id')->count();
                $data['dec'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d',strtotime(date("Y-12-01"))))->where('checkin_date', '<=', date('Y-m-d',strtotime(date("Y-12-31"))))->unique('customer_id')->count();
                $data['total'] = $dealervisited->unique('customer_id')->count();
                $data['apm'] =  $dealervisited->unique('customer_id')->count();
                break;
            case 'gift_collected':
                $points = Wallet::with('customers')->where('userid','=',$this->userid)
                                        ->whereYear('transaction_at','=',date('Y'))
                                        ->where('transaction_type','=','Cr')
                                        ->select('transaction_at','points','point_type','quantity','transaction_type')->get();
                $data['jan'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-01-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-01-31"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['feb'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-02-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-02-29"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['mar'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-03-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-03-31"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['apr'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-04-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-04-30"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['may'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-05-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-05-31"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['jun'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-06-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-06-30"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['jul'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-07-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-07-31"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['aug'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-08-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-08-31"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['sep'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-09-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-09-30"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['oct'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-10-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-10-31"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['nov'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-11-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-11-30"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['dec'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-12-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-12-31"))))->where('customers.customertype','=','4')->sum('quantity');
                $data['total'] = $points->where('customers.customertype','=','4')->sum('quantity');
                $data['apm'] =  $points->where('customers.customertype','=','4')->sum('quantity');
                break;
            case 'mechanic_registered':
                $customers = Customers::where('created_by','=',$this->userid)
                                        ->whereYear('created_at','=',date('Y'))
                                        ->where('customertype','=',4)
                                        ->select('created_at')->get();
                $data['jan'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-01-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-01-31"))))->count();
                $data['feb'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-02-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-02-29"))))->count();
                $data['mar'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-03-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-03-31"))))->count();
                $data['apr'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-04-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-04-30"))))->count();
                $data['may'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-05-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-05-31"))))->count();
                $data['jun'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-06-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-06-30"))))->count();
                $data['jul'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-07-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-07-31"))))->count();
                $data['aug'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-08-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-08-31"))))->count();
                $data['sep'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-09-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-09-30"))))->count();
                $data['oct'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-10-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-10-31"))))->count();
                $data['nov'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-11-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-11-30"))))->count();
                $data['dec'] = $customers->where('created_at', '>=', date('Y-m-d',strtotime(date("Y-12-01"))))->where('created_at', '<=', date('Y-m-d',strtotime(date("Y-12-31"))))->count();
                $data['total'] = $customers->count();
                $data['apm'] =  $customers->count();
                break;
            case 'gift_settled':
                $points = Wallet::with('customers')
                                    ->where('userid','=',$this->userid)
                                    ->whereYear('transaction_at','=',date('Y'))
                                    ->where('transaction_type','=','Cr')
                                    ->select('transaction_at','points','point_type','quantity','transaction_type')->get();
                 $data['jan'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-01-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-01-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['feb'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-02-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-02-29"))))->where('customers.customertype','=','4')->sum('points');

                $data['mar'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-03-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-03-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['apr'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-04-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-04-30"))))->where('customers.customertype','=','4')->sum('points');

                $data['may'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-05-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-05-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['jun'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-06-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-06-30"))))->where('customers.customertype','=','4')->sum('points');

                $data['jul'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-07-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-07-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['aug'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-08-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-08-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['sep'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-09-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-09-30"))))->where('customers.customertype','=','4')->sum('points');

                $data['oct'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-10-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-10-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['nov'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-11-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-11-30"))))->where('customers.customertype','=','4')->sum('points');

                $data['dec'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-12-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-12-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['total'] = $points->where('customers.customertype','=','4')->sum('points');
                
                $data['apm'] =  $points->where('customers.customertype','=','4')->sum('points');
                break;
            case 'mechanic_points':
                $points = Wallet::with('customers')->where('userid','=',$this->userid)
                                        ->whereYear('transaction_at','=',date('Y'))
                                        ->where('transaction_type','=','Cr')
                                        ->select('transaction_at','points','point_type','quantity','transaction_type')->get();

                $data['jan'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-01-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-01-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['feb'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-02-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-02-29"))))->where('customers.customertype','=','4')->sum('points');

                $data['mar'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-03-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-03-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['apr'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-04-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-04-30"))))->where('customers.customertype','=','4')->sum('points');

                $data['may'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-05-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-05-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['jun'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-06-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-06-30"))))->where('customers.customertype','=','4')->sum('points');

                $data['jul'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-07-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-07-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['aug'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-08-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-08-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['sep'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-09-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-09-30"))))->where('customers.customertype','=','4')->sum('points');

                $data['oct'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-10-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-10-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['nov'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-11-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-11-30"))))->where('customers.customertype','=','4')->sum('points');

                $data['dec'] = $points->where('transaction_at', '>=', date('Y-m-d',strtotime(date("Y-12-01"))))->where('transaction_at', '<=', date('Y-m-d',strtotime(date("Y-12-31"))))->where('customers.customertype','=','4')->sum('points');

                $data['total'] = $points->where('customers.customertype','=','4')->sum('points');

                $data['apm'] =  $points->where('customers.customertype','=','4')->sum('points');
                break;
            case 'order_collected':
                $orders = Order::where('created_by','=',$this->userid)
                                ->whereYear('order_date','=',date('Y'))
                                ->select('order_date','grand_total')
                                ->get();
                $data['jan'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-01-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-01-31"))))->sum('grand_total'));

                $data['feb'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-02-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-02-29"))))->sum('grand_total'));

                $data['mar'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-03-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-03-31"))))->sum('grand_total'));

                $data['apr'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-04-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-04-30"))))->sum('grand_total'));
                $data['may'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-05-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-05-31"))))->sum('grand_total'));

                $data['jun'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-06-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-06-30"))))->sum('grand_total'));

                $data['jul'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-07-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-07-31"))))->sum('grand_total'));

                $data['aug'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-08-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-08-31"))))->sum('grand_total'));

                $data['sep'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-09-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-09-30"))))->sum('grand_total'));

                $data['oct'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-10-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-10-31"))))->sum('grand_total'));

                $data['nov'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-11-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-11-30"))))->sum('grand_total'));

                $data['dec'] = amountInLac($orders->where('order_date', '>=', date('Y-m-d',strtotime(date("Y-12-01"))))->where('order_date', '<=', date('Y-m-d',strtotime(date("Y-12-31"))))->sum('grand_total'));

                $data['total'] = amountInLac($orders->sum('grand_total'));

                $data['apm'] =  amountInLac($orders->sum('grand_total'));
                break;
            }
        return [
            isset($data['parameter']) ? $data['parameter'] : '' ,
            isset($data['jan']) ? $data['jan'] :'',
            isset($data['feb']) ? $data['feb'] :'',
            isset($data['mar']) ? $data['mar'] :'',
            isset($data['apr']) ? $data['apr'] :'',
            isset($data['may']) ? $data['may'] :'',
            isset($data['jun']) ? $data['jun'] :'',
            isset($data['jul']) ? $data['jul'] :'',
            isset($data['aug']) ? $data['aug'] :'',
            isset($data['sep']) ? $data['sep'] :'',
            isset($data['oct']) ? $data['oct'] :'',
            isset($data['nov']) ? $data['nov'] :'',
            isset($data['dec']) ? $data['dec'] :'',
        ];
    }
}