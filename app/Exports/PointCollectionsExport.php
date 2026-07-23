<?php

namespace App\Exports;

use App\Models\Wallet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class PointCollectionsExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->fromdate = date("Y-m-01");
        $this->todate = date("Y-m-t");
        $this->userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
    }

    public function collection()
    {
        return Wallet::with('customers')->where('userid','=',$this->userid)
                                    ->where('transaction_at', '>=', $this->fromdate)
                                    ->where('transaction_at', '<=', $this->todate)
                                    ->select('customer_id')
                                    ->groupBy('customer_id')
                                    ->get();
    }

    public function headings(): array
    {
        return ['customer_id','Name of Mechanic','City', 'Mobile No', 'Gift Points Coupon Prior', 'Gift Points Coupon Cummlative', 'MRP Label Value Prior', 'MRP Label Value Cummlative'];
    }

    public function map($data): array
    {
        $points = Wallet::where('customer_id','=',$data['customer_id'])
                            ->where('transaction_at', '>=', $this->fromdate)
                            ->where('transaction_at', '<=', $this->todate)
                            ->select('points','quantity','point_type')->get();
        return [
            isset($data['customer_id']) ? $data['customer_id'] : '' ,
            isset($data['customers']['name']) ? $data['customers']['name'] :'',
            isset($data['customers']['customeraddress']['cityname']['city_name']) ? $data['customers']['customeraddress']['cityname']['city_name'] :'',
            isset($data['customers']['mobile']) ? $data['customers']['mobile'] :'',
            $points->where('point_type','=','gift coupon')->sum('points'),
            $points->where('point_type','=','gift coupon')->sum('quantity'),
            $points->where('point_type','=','mrp lable')->sum('points'),
            $points->where('point_type','=','mrp lable')->sum('quantity'),
        ];
    }
}