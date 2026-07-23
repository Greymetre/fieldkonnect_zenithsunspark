<?php

namespace App\Exports;

use App\Models\Country;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class CountryExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Country::select('id','country_name')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','country_name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['country_name'],
        ];
    }

}