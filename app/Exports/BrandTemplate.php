<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
class BrandTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Beat::select('beat_name','beat_date','user_id','description','customers')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['beat_name','beat_date','user_id','description','customers'];
    }

}