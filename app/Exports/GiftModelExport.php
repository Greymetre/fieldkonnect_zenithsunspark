<?php

namespace App\Exports;

use App\Models\GiftModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class GiftModelExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return GiftModel::latest()->get();   
    }

    public function headings(): array
    {
        return ['id','Model Name', 'Model Image','Sub Category id','Sub Category Name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['model_name'],
            $data['model_image'],
            $data['sub_category_id'],
            $data['subCategories']['subcategory_name'],
        ];
    }

}