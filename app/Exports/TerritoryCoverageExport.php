<?php

namespace App\Exports;

use App\Models\UserCityAssign;
use App\Models\Customers;
use App\Models\CheckIn;
use App\Models\Wallet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class TerritoryCoverageExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->fromdate = date("Y-m-01");
        $this->todate = date("Y-m-t");
        $this->userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
    }

    public function collection()
    {
        return  UserCityAssign::with('cityname')->where('userid','=',$this->userid)->select('city_id')->get();
    }

    public function headings(): array
    {
        return ['city_id','City Name','Category', 'Total Dealer', 'Total Mechanics', 'Dealer Visited', 'Mechanincs Visited', 'Coupons', 'MRP', 'Total Dealer Visited', 'Total Mechanincs Visited', 'Total Coupons', 'Total MRP'];
    }

    public function map($data): array
    {
        $customers = Customers::whereHas('customeraddress', function ($query) use($data) {
                                    $query->where('city_id','=',$data['city_id']);
                                })
                                ->select('id','customertype')
                                ->get();

        $checkins = CheckIn::with('customers')
                            ->whereIn('customer_id',$customers->pluck('id')->toArray())
                            ->whereDate('checkin_date', '>=', $this->fromdate)
                            ->whereDate('checkin_date', '<=', $this->todate)
                            ->where('user_id','=',$data['userid'])
                            ->select('customer_id')
                            ->get();
        $points = Wallet::with('customers')
                            ->whereIn('customer_id',$customers->pluck('id')->toArray())
                            ->where('userid','=',$this->userid)
                            ->where('transaction_at', '>=', $this->fromdate)
                            ->where('transaction_at', '<=', $this->todate)
                            ->select('point_type','points','quantity')
                            ->get();


        return [
            isset($data['city_id']) ? $data['city_id'] : '' ,
            isset($data['cityname']['city_name']) ? $data['cityname']['city_name'] :'',
            isset($data['cityname']['grade']) ? $data['cityname']['grade'] :'',
            $customers->where('customertype','=','2')->count(),
            $customers->where('customertype','=','4')->count(),
            $checkins->where('customers.customertype','=','2')->count(),
            $checkins->where('customers.customertype','=','4')->count(),
            $points->where('point_type','=','gift coupon')->sum('points'),
            $points->where('point_type','=','mrp lable')->sum('points'),
            $checkins->where('customers.customertype','=','2')->count(),
            $checkins->where('customers.customertype','=','4')->count(),
            $points->where('point_type','=','gift coupon')->sum('points'),
            $points->where('point_type','=','mrp lable')->sum('points'),
        ];
    }
}