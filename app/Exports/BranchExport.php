<?php

namespace App\Exports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class BranchExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return Branch::with('getuser')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','branch_name','branch_code','Branch SAP Code','created_by','updated_by','active'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            isset($data['branch_name']) ? $data['branch_name'] :'',
            isset($data['branch_code']) ? $data['branch_code'] :'',
            isset($data['branch_sap_code']) ? $data['branch_sap_code'] :'',
            isset($data['getuser']['name']) ? $data['getuser']['name'] :'',
            isset($data['updated_by']) ? $data['updated_by'] :'',
            isset($data['active']) ? $data['active'] :'',

        ];
    }

}
