<?php

namespace App\Exports;

use App\Models\GiftCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
class GiftCategoryExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return GiftCategory::select('id','category_name', 'category_image')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','category_name', 'category_image'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['category_name'],
            $data['category_image'],
        ];
    }

}
