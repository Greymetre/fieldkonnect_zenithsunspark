<?php

namespace App\Exports;

use App\Models\Tasks;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class TasksTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Tasks::select('user_id', 'title', 'descriptions', 'datetime', 'reminder', 'completed_at', 'completed', 'is_done', 'customer_id', 'status_id', 'created_by', 'created_at')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['user_id', 'title', 'descriptions', 'Datetime', 'reminder', 'completed_at', 'completed', 'is_done', 'status_id'];
    }

}