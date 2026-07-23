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

class DistrictTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return District::select('district_name', 'state_id')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['district_name', 'state_id'];
    }

}