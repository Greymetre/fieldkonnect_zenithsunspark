<?php

namespace App\Exports;

use App\Models\UserCityAssign;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class UserCityMapedExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        
        $this->page_number = $request->input('page_number');
        $this->page_length = $request->input('page_length'); 
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        // return UserCityAssign::where(function ($query)  {
        //                         if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

        //                         {
        //                             $query->whereIn('userid', $this->userids);
        //                         }
        //                     })
        //                     ->select('userid','reportingid','city_id')
        //                     ->orderBy('userid','asc')
        //                     ->get();

        $results_per_page = $this->page_length;
        $page_number = intval($this->page_number);
        $page_result = ($page_number-1) * $results_per_page;

        return UserCityAssign::with('userinfo','reportinginfo','cityname','cityname.districtname','cityname.districtname.statename')->where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('userid', $this->userids);
                                }
                            })
                            //->select('userid','reportingid','city_id')
                            ->orderBy('userid')
                            ->skip($page_result)
                            ->take($results_per_page)
                            ->get();



    }

    public function headings(): array
    {
        return ['user_id','user_name','reportingid', 'reporting_name', 'city_id', 'city_name', 'grade', 'district_id', 'district_name', 'status_id', 'state_name', 'Delete'];
    }

    public function map($data): array
    {
        return [
            isset($data['userid']) ? $data['userid'] : '' ,
            isset($data['userinfo']['name']) ? $data['userinfo']['name'] :'',
            isset($data['reportingid']) ? $data['reportingid'] : '' ,
            isset($data['reportinginfo']['name']) ? $data['reportinginfo']['name'] :'',
            isset($data['city_id']) ? $data['city_id'] : '' ,
            isset($data['cityname']['city_name']) ? $data['cityname']['city_name'] :'',
            isset($data['cityname']['grade']) ? $data['cityname']['grade'] :'',
            isset($data['cityname']['district_id']) ? $data['cityname']['district_id'] :'',
            isset($data['cityname']['districtname']['district_name']) ? $data['cityname']['districtname']['district_name'] :'',
            isset($data['cityname']['districtname']['state_id']) ? $data['cityname']['districtname']['state_id'] :'',
            isset($data['cityname']['districtname']['statename']['state_name']) ? $data['cityname']['districtname']['statename']['state_name'] :'',
        ];
    }
}