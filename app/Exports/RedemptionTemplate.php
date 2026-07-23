<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;

class RedemptionTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Brand::limit(0)->get();   
    }

    public function headings(): array
    {
        return [['Redemption Id','Status','Transaction id UTR No','Details','TDS', 'Payment Date', 'Invoice Number'],
        ['Note : Status will be 0=Pendding,1=Approved,2=Reject,3=Success,4=Fail, Please enter number only in status column and remove this row before upload.']];
    }

}