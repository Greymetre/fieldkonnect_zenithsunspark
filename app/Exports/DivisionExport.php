<?php

namespace App\Exports;

use App\Models\Division;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class DivisionExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return Division::latest()->get();   
    }

    public function headings(): array
    {
        return ['id','division_name','created_by','updated_by','active'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            isset($data['division_name']) ? $data['division_name'] :'',
            isset($data['getuser']['name']) ? $data['getuser']['name'] :'',
            isset($data['updated_by']) ? $data['updated_by'] :'',
            isset($data['active']) ? $data['active'] :'',

        ];
    }

}
