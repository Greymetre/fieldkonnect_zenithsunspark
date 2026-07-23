<?php

namespace App\Exports;


use App\Models\GiftBrand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;


class GiftBrandExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return GiftBrand::latest()->get();   
    }

    public function headings(): array
    {
        return ['id','Brand Name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['brand_name'],
        ];
    }

}
