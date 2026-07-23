<?php

namespace App\Exports;

use App\Models\SchemeDetails;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class PrimarySchemeTepmlate implements FromCollection,WithHeadings,ShouldAutoSize
{
    
    public function collection()
    {
        return SchemeDetails::limit(0)->get();   
    }

    public function headings(): array
    {
        return ['Group Type', 'Group Name', 'Min', 'Max', 'Points'];
    }

}