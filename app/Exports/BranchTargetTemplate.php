<?php

namespace App\Exports;

use App\Models\BranchWiseTarget;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class BranchTargetTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return BranchWiseTarget::select('user_id','user_name','div_id','division_name','branch_id','branch_name','type','month', 'year', 'target')->limit(0)->get();   
    }

    public function headings(): array
    {
        return [
            'User Id',  
            'User Name',
            'Div Id',
            'Div Name',
            'Branch Id',
            'Branch Name',
            'Type',
            'Month', 
            'Target Value'
        ];
    }

}