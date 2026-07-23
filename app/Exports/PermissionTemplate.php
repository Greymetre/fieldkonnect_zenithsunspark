<?php

namespace App\Exports;

use App\Models\Permission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class PermissionTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Permission::select('name', 'guard_name')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['name', 'guard_name'];
    }

}