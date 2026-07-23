<?php

namespace App\Exports;

use App\Models\VisitReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class VisitReportTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return VisitReport::select('checkin_id', 'user_id', 'customer_id', 'visit_type_id', 'report_title', 'description')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['checkin_id', 'user_id', 'customer_id', 'visit_type_id', 'report_title', 'description'];
    }

}