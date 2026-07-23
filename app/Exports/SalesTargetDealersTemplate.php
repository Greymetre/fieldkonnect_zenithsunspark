<?php

namespace App\Exports;

use App\Models\SalesTargetCustomers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class SalesTargetDealersTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return SalesTargetCustomers::select('customer_id', 'month', 'year', 'target')->limit(0)->get();   
    }

    public function headings(): array
    {
        return [['Branch Id','Customer Id','Div Id','Type','Dealer','04/25','05/25','06/25','07/25','08/25','09/25','10/25','11/25','12/25','01/26','02/26','03/26'],['','','','Add primary or secondary value only.please remove this row before upload.']];
    }

}