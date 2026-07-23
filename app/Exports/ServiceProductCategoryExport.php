<?php

namespace App\Exports;

use App\Models\ServiceChargeCategories;
use AWS\CRT\HTTP\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ServiceProductCategoryExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{

    public function __construct($request)
    {
        $this->division = $request->input('division');
    }

    public function collection()
    {
        $data = ServiceChargeCategories::with('createdbyname','division');
        if(!empty($this->division)){
            $data->where('division_id', $this->division);
        }
        
        return $data->latest()->get();
    }

    public function headings(): array
    {
        return ['id','Category Name', 'Division Id','Division Name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['category_name'],
            $data['division_id'],
            $data['division']['division_name'],
        ];
    }

}