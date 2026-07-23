<?php

namespace App\Exports;

use App\Models\SchemeDetails;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class SchemeExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    public function collection()
    {
        return SchemeDetails::where('scheme_id', $this->id)->latest()->get();   
    }

    public function headings(): array
    {
        return ['Product Id', 'Product Name', 'Category Id', 'Category Name', 'Sub Category Id', 'Sub Category Name', 'Active Point','Provision Point', 'Points'];
    }

    public function map($data): array
    {
        return [
            $data['product_id'],
            $data['products']?$data['products']['product_name']:'-',
            $data['category_id'],
            $data['categories']?$data['categories']['category_name']:'',
            $data['subcategory_id'],
            $data['subcategories']?$data['subcategories']['subcategory_name']:'',
            $data['active_point']??"0",
            $data['provision_point']??"0",
            $data['points']??"0",
        ];
    }

}