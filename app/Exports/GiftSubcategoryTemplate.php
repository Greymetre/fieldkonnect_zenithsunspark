<?php

namespace App\Exports;

use App\Models\Subcategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class GiftSubcategoryTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Subcategory::select('subcategory_name', 'subcategory_image','category_id')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['subcategory_name', 'subcategory_image','category_id'];
    }

}