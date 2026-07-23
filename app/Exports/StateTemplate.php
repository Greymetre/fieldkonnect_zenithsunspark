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

class StateTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return State::select('state_name', 'country_id')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['state_name', 'country_id'];
    }

}
