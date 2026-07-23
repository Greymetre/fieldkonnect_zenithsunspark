<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ProductTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Product::select('product_name', 'display_name', 'description', 'subcategory_id', 'category_id', 'brand_id', 'product_image', 'unit_id', 'specification', 'part_no', 'product_no', 'model_no','suc_del')->limit(0)->get();
    }

    public function headings(): array
    {

        return ['product_name', 'display_name', 'description', 'subcategory_id', 'category_id', 'brand_id', 'product_image', 'unit_id','detail_title', 'detail_description', 'product_id', 'detail_image','mrp', 'price', 'discount', 'max_discount', 'selling_price', 'gst', 'isprimary', 'hsn_code', 'ean_code', 'hp', 'kw', 'product_stage', 'model_no','suc_del'];

        // return ['product_name', 'display_name', 'description', 'subcategory_id', 'category_id', 'brand_id', 'product_image', 'unit_id','detail_title', 'detail_description', 'product_id', 'detail_image','SUC x DEL','mrp', 'price', 'discount', 'max_discount', 'selling_price', 'gst', 'isprimary', 'hsn_code', 'ean_code', 'HP', 'kW', 'Product Stage', 'model_no','suc_del'];
    }

}
