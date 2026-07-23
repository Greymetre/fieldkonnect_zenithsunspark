<?php

namespace App\Exports;

use App\Models\BeatUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class BeatExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return BeatUser::with('beats', 'users')->where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('user_id', $this->userids);
                                }
                            })->select('id','beat_id', 'user_id')->latest()->get();   
    }

    public function headings(): array
    {
        return ['beat_id','beat_name','description','user_id','user name','state_id', 'state_name', 'district_id', 'district_name', 'city_id', 'city_name'];
    }

    public function map($data): array
    {
        return [
            $data['beat_id'],
            isset($data['beats']['beat_name']) ? $data['beats']['beat_name'] :'',
            isset($data['beats']['description']) ? $data['beats']['description'] :'',
            isset($data['user_id']) ? $data['user_id'] :'',
            isset($data['users']['name']) ? $data['users']['name'] :'',
            isset($data['beats']['state_id']) ? $data['beats']['state_id'] :'',
            isset($data['beats']['statename']['state_name']) ? $data['beats']['statename']['state_name'] :'',
            isset($data['beats']['district_id']) ? $data['beats']['district_id'] :'',
            isset($data['beats']['districtname']['district_name']) ? $data['beats']['districtname']['district_name'] :'',
            isset($data['beats']['city_id']) ? $data['beats']['city_id'] :'',
            isset($data['beats']['cityname']['city_name']) ? $data['beats']['cityname']['city_name'] :'',
        ];
    }

}
