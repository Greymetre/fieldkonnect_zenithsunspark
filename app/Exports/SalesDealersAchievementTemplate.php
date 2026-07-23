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

class SalesDealersAchievementTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return SalesTargetCustomers::select('customer_id', 'month', 'year', 'achievement')->limit(0)->get();   
    }

    public function headings(): array
    {
        return [
            ['Customer Id', 'Dealer/Distributor Name', 'Type', 'Month', 'Achievement'],['','','Add primary or secondary value only.please remove this row before upload.']
        ];
    }

}