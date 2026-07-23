<?php

namespace App\Exports;

use App\Models\PrimarySales;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class BranchStockTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return new Collection([
           
        ]);   
    }

    public function headings(): array
    {
        return [
            'Branch ID',  
            'Branch Name',
            'Warehouse ID',
            'Division ID',
            'Year',
            'Quarter',
            '0-30',
            '31-60',
            '61-90',
            '91-150',
            '>150',
        ];
    }

}