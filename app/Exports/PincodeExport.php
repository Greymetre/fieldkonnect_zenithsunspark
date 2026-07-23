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

class PincodeExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Pincode::select('id','pincode', 'city_id')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','pincode', 'city_id','city_name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['pincode'],
            $data['city_id'],
            $data['cityname']['city_name'],
        ];
    }

}