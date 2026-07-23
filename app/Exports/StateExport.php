<?php

namespace App\Exports;

use App\Models\State;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class StateExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return State::select('id','state_name', 'country_id', 'gst_code')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','state_name', 'country_id','country_name','gst_code'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['state_name'],
            $data['country_id'],
            $data['countryname']['country_name'],
            $data['gst_code'],
        ];
    }

}