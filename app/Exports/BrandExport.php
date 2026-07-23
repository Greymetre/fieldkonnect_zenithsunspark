<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
class BrandExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Brand::select('id','brand_name','brand_image')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','brand_name','brand_image'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['brand_name'],
            $data['brand_image'],
        ];
    }
}
