<?php

namespace App\Exports;

use App\Models\OrderSchemeDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class OrderSchemeExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    public function collection()
    {
        // return OrderSchemeDetail::where('order_scheme_id', $this->id)->latest()->get();   
        return OrderSchemeDetail::where('order_scheme_id', $this->id)->get();  
    }

    public function headings(): array
    {
        return ['Product Id', 'Product Name', 'Category Id', 'Category Name', 'Sub Category Id', 'Sub Category Name', 'Discount'];
    }

    public function map($data): array
    {
        return [
            $data['product_id'],
            $data['products']['product_name'],
            $data['category_id'],
            $data['categories']['category_name'],
            $data['subcategory_id'],
            $data['subcategories']['subcategory_name'],
            $data['points'],
        ];
    }

}