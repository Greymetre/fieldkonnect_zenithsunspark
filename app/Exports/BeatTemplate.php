<?php

namespace App\Exports;

use App\Models\Beat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class BeatTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Beat::select('beat_name','description','region_id','country_id','state_id','district_id','city_id')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['beat_name','description','region_id','country_id','state_id','district_id','city_id','userid','customers'];
    }

}
