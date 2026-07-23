<?php

namespace App\Exports;

use App\Models\Gifts;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class GiftExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Gifts::with('categories','subcategories','brands','models','customer_types')->get();
    }

    public function headings(): array
    {
        return ['id', 'category_id', 'subcategory_id', 'brand_id', 'model_id', 'customer_type_id', 'category_name', 'subcategory_name', 'brand_name', 'model_name', 'customer_type', 'product_name', 'display_name', 'description', 'mrp', 'price', 'points'];
    }

    public function map($data): array
    {
        return [
            !empty($data['id']) ? $data['id'] : '' ,
            !empty($data['category_id']) ? $data['category_id'] : '' ,
            !empty($data['subcategory_id']) ? $data['subcategory_id'] : '' ,
            !empty($data['brand_id']) ? $data['brand_id'] : '' ,
            !empty($data['unit_id']) ? $data['unit_id'] : '' ,
            !empty($data['customer_type_id']) ? $data['customer_type_id'] : '' ,
            !empty($data['categories']['category_name']) ? $data['categories']['category_name'] : '' ,
            !empty($data['subcategories']['subcategory_name']) ? $data['subcategories']['subcategory_name'] : '' ,
            !empty($data['brands']['brand_name']) ? $data['brands']['brand_name'] : '' ,
            !empty($data['models']['model_name']) ? $data['models']['model_name'] : '' ,
            !empty($data['customer_types']['customertype_name']) ? $data['customer_types']['customertype_name'] : '' ,
            !empty($data['product_name']) ? $data['product_name'] : '' ,
            !empty($data['display_name']) ? $data['display_name'] : '' ,
            !empty($data['description']) ? $data['description'] : '' ,
            !empty($data['mrp']) ? $data['mrp'] : '' ,
            !empty($data['price']) ? $data['price'] : '' ,
            !empty($data['points']) ? $data['points'] : '' ,
        
        ];
    }
}
