<?php

namespace App\Exports;

use App\Models\TourProgramme;
use App\Models\Wallet;
use App\Models\CheckIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class MovementReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->fromdate = date("Y-m-01");
        $this->todate = date("Y-m-t");
        $this->userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
    }

    public function collection()
    {
        return TourProgramme::with('tourdetails')->where('userid','=',$this->userid)
                                    ->where('date', '>=', $this->fromdate)
                                    ->where('date', '<=', $this->todate)
                                    ->select('id','date','objectives','town','type','status','userid')
                                    ->get();
    }

    public function headings(): array
    {
        return ['Date','Planned','Actual', 'Category', 'Status', 'Dealer Visited', 'Mechanincs Visited', 'STUs Visited', 'Fleet Owner Visited', 'Total Visited', 'Coupons Collection','Total Points','No.of Gifts', 'Gifts Value'];
    }

    public function map($data): array
    {
        $category = collect([]);
        $cityname = collect([]);
        $checkins = CheckIn::with('customers')->where('user_id','=',$data['userid'])->whereDate('checkin_date','=',$data['date'])->select('customer_id','checkin_date')->get();
        $points = Wallet::where('userid','=',$data['userid'])->whereDate('transaction_at','=',$data['date'])->select('points','quantity')->get();

        foreach ($data['tourdetails'] as $key => $detail) {
            if(!empty($detail['visitedcities']))
            {
                $category->push($detail['visitedcities']['grade']);
                $cityname->push($detail['visitedcities']['city_name']);
            }
        }
        switch ($data['type']) {
            case 'Tour':
                $data['type'] = 'T' ;
                break;
            case 'Office Work':
                $data['type'] = 'O' ;
                break;
            case 'Suburban':
                $data['type'] = 'S' ;
                break;
            case 'Central Market':
                $data['type'] = 'C' ;
                break;
            case 'Holiday':
                $data['type'] = 'H' ;
                break;
            case 'Leave':
                $data['type'] = 'L' ;
                break;
            default:
                $data['type'] = '' ;
                break;
        }
        return [
            isset($data['date']) ? $data['date'] : '' ,
            isset($data['town']) ? $data['town'] :'',
            implode(',', $cityname->unique()->toArray()),
            implode(',', $category->unique()->toArray()),
            isset($data['type']) ? $data['type'] : '',
            $checkins->where('customers.customertype','=','2')->unique('customer_id','checkin_date')->count(),
            $checkins->where('customers.customertype','=','4')->unique('customer_id','checkin_date')->count(),
            $checkins->where('customers.customertype','=','5')->unique('customer_id','checkin_date')->count(),
            $checkins->where('customers.customertype','=','6')->unique('customer_id','checkin_date')->count(),
            $checkins->unique('customer_id','checkin_date')->count(),
            $points->where('point_type','=','mrp lable')->sum('quantity'),
            $points->where('point_type','=','mrp lable')->sum('points'),
            $points->where('point_type','=','gift coupon')->sum('quantity'),
            $points->where('point_type','=','gift coupon')->sum('points'),
        ];
    }
}