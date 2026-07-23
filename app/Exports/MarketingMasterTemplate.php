<?php

namespace App\Exports;

use App\Models\PrimarySales;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class MarketingMasterTemplate implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        return PrimarySales::limit(0)->get();
    }

    public function headings(): array
    {
        return [
            'Event Date',
            'Division',
            'Event Center',
            'Place of Participant',
            'Event District',
            'State',
            'Event Under Type',
            'Event Under Name',
            'Branch',
            'Name Responsible for Event',
            'Branding Team Member',
            'Name of Participant',
            'Category of Participant',
            'Mob No of Participant',
            'id',
            'Delete'
        ];
    }
}
