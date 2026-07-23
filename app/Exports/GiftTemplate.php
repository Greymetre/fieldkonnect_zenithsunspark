<?php

namespace App\Exports;

use App\Models\Gifts;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GiftTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Gifts::limit(0)->get();
    }

    public function headings(): array
    {
        return ['category_id', 'subcategory_id', 'brand_id', 'model_id', 'customer_type_id', 'product_name', 'display_name', 'description', 'mrp', 'price', 'points'];
    }
}
