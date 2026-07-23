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

class PermissionExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Permission::select('id','name', 'guard_name')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','name', 'guard_name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['name'],
            $data['guard_name'],
        ];
    }

}