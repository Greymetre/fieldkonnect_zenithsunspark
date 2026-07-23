<?php

namespace App\Exports;

use App\Models\GiftSubcategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class GiftSubcategoryExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return GiftSubcategory::select('id','subcategory_name', 'subcategory_image','category_id')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','subcategory_name', 'subcategory_image','category_id','gift_category_name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['subcategory_name'],
            $data['subcategory_image'],
            $data['category_id'],
            $data['categories']['category_name'],
        ];
    }

}