<?php

namespace App\Exports;

use App\Models\UnitMeasure;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class UnitTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return UnitMeasure::select('unit_name', 'unit_code')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['unit_name', 'unit_code'];
    }

}