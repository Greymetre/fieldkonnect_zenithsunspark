<?php

namespace App\Exports;

use App\Models\ServiceChargeChargeType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ServiceProductChargeTypeExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return ServiceChargeChargeType::select('id','charge_type')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','Charge Type'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['charge_type'],
        ];
    }

}