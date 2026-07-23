<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
class CategoryExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Category::select('id','category_name', 'sap_code')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','category_name', 'SAP Code'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['category_name'],
            $data['sap_code'],
        ];
    }

}
