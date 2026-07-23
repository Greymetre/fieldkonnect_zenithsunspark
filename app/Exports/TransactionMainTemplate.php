<?php

namespace App\Exports;

use App\Models\City;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class TransactionMainTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return City::select('city_name', 'district_id')->limit(0)->get();   
    }

    public function headings(): array
    {
        return [['Customer Id', 'Coupon Code'],['Please add comma seprated coupon code and only one customer id at one time, Also remove this row before upload.']];
    }

}