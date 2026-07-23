<?php

namespace App\Exports;

use App\Models\Support;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class SupportTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Support::select('subject', 'description', 'department_id', 'user_id', 'status_id', 'customer_id', 'name', 'mobile', 'email', 'priority', 'last_reply')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['subject', 'description', 'department_id', 'user_id', 'status_id', 'customer_id', 'name', 'mobile', 'email', 'priority', 'last_reply'];
    }

}