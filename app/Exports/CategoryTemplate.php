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

class CategoryTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Category::select('category_name', 'sap_code')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['category_name', 'sap_code'];
    }

}