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

class UnitExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return UnitMeasure::select('id','unit_name', 'unit_code')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','unit_name', 'unit_code'];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->unit_name,
            $data->unit_code,
        ];
    }

}