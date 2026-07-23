<?php

namespace App\Exports;

use App\Models\City;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class CityExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return City::with('statename','districtname','districtname.statename')->select('id','city_name', 'district_id','grade','state_id')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','city_name', 'district_id','district_name','grade','state_id', 'state_name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['city_name'],
            $data['district_id'],
            isset($data['districtname']['district_name']) ? $data['districtname']['district_name'] : '',
            isset($data['grade']) ? $data['grade'] : '',
            isset($data['districtname']['state_id']) ? $data['districtname']['state_id'] : '',
            isset($data['districtname']['statename']['state_name']) ? $data['districtname']['statename']['state_name'] : '',

        ];
    }

}