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

class SupportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Support::select('id','subject', 'description', 'department_id', 'user_id', 'status_id', 'customer_id', 'name', 'mobile', 'email', 'priority', 'last_reply')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','subject', 'description', 'department_id', 'user_id', 'status_id', 'customer_id', 'name', 'mobile', 'email', 'priority', 'last_reply'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['subject'],
            $data['description'],
            $data['department_id'],
            $data['user_id'],
            $data['status_id'],
            $data['customer_id'],
            $data['name'],
            $data['mobile'],
            $data['email'],
            $data['priority'],
            $data['last_reply'],
        ];
    }

}