<?php

namespace App\Exports;

use App\Models\ServiceChargeProducts;
use AWS\CRT\HTTP\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ServiceChargeProductExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{

    public function __construct($request)
    {
        $this->division = $request->input('division');
    }

    public function collection()
    {
        $data = ServiceChargeProducts::with('charge_type','division', 'category');
        if(!empty($this->division)){
            $data->where('division_id', $this->division);
        }
        
        return $data->latest()->get();
    }

    public function headings(): array
    {
        return ['id','Charge Type','Product Name','Division','Category','Price','Other Charge','Charge Type Id','Division Id','Category Id'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['charge_type']['charge_type'],
            $data['product_name'],
            $data['division']['division_name'],
            $data['category']['category_name'],
            $data['price'],
            $data['other_charge'],
            $data['charge_type_id'],
            $data['division_id'],
            $data['category_id'],
        ];
    }

}