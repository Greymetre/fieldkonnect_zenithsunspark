<?php

namespace App\Exports;

use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class StatusExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Status::select('id','status_name', 'display_name', 'status_message', 'module')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','status_name', 'display_name', 'status_message', 'module'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['status_name'],
            $data['display_name'],
            $data['status_message'],
            $data['module'],
        ];
    }

}
