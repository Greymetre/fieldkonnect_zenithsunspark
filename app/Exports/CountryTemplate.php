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

class CountryTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Country::select('country_name')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['country_name'];
    }

}