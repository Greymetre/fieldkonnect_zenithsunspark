<?php

namespace App\Exports;

use App\Models\ServiceChargeDivision;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ServiceProductDivisionExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return ServiceChargeDivision::select('id','division_name')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','Division Name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['division_name'],
        ];
    }

}