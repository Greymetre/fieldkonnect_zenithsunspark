<?php

namespace App\Exports;

use App\Models\Pincode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class PincodeTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Pincode::select('pincode', 'city_id')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['pincode', 'city_id'];
    }

}