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
class SchemeTepmlate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return SchemeDetails::limit(0)->get();
    }

    public function headings(): array
    {
        return ['Product Id', 'Product Name', 'Category Id', 'Category Name', 'Sub Category Id', 'Sub Category Name', 'Point'];
    }

}
