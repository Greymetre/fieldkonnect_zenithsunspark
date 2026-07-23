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

class StatusTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Status::select('status_name', 'display_name', 'status_message', 'module')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['status_name', 'display_name', 'status_message', 'module'];
    }
}
