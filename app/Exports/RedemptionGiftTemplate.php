<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;

class RedemptionGiftTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Brand::limit(0)->get();   
    }

    public function headings(): array
    {
        return [['Redemption Id','Acual Dispatch Product','Status','Approve Date','Dispatch Date','Dispatch Number','Gift Recived Date','Received Remark','Redemption No','Purchase Rate','Gst','Total Purchase','Purchase Invoice No','Purchase Return no','Client Invoice No',],
        ['Note : Status will be 0=Pendding,1=Approved,2=Reject,3=Dispatch,4=Success, Please enter number only in status column and remove this row before upload.']];
    }

}