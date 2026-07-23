<?php

namespace App\Exports;

use App\Models\District;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class DistrictExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return District::with('statename')->select('id','district_name', 'state_id')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','district_name', 'state_id','state_name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['district_name'],
            $data['state_id'],
            $data['statename']['state_name'],
        ];
    }

}