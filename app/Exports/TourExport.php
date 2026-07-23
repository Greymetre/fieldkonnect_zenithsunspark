<?php

namespace App\Exports;

use App\Models\TourProgramme;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class TourExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        
        $this->userids = getUsersReportingToAuth();

        $this->user_id = $request->input('executive_id');
        $this->division_id = $request->input('division_id');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');

    }

    public function collection()
    {     
        // return TourProgramme::with('tourdetails','userinfo')->where(function ($query)  {
        //                         if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

        //                         {
        //                             $query->whereIn('executive_id', $this->userids);
        //                         }
        //                     })->select('id','date', 'userid', 'town', 'objectives', 'type', 'status')->latest()->get();

        if(!empty($this->user_id) ||!empty($this->start_date) ||!empty($this->end_date)){
            return TourProgramme::with('tourdetails','userinfo')->where(function ($query)  {
                                if($this->user_id)
                                {
                                    $query->where('userid', $this->user_id);
                                }
                                if($this->division_id)
                                {
                                    $userIds = User::where('division_id', $this->division_id)->pluck('id');
                                    $query->whereIn('userid', $userIds);
                                }
                                if($this->start_date)
                                {
                                    $query->whereDate('date','>=',$this->start_date);
                                }
                                if($this->end_date)
                                {
                                    $query->whereDate('date','<=',$this->end_date);
                                }
                            })
                        ->select('id','date', 'userid', 'town', 'objectives', 'type', 'status')
                        //->latest()->get(); 
                        ->orderBy(DB::raw('YEAR(date)'), 'DESC')->orderBy(DB::raw('DATE(date)'), 'ASC')->get();    

           }else{

            return TourProgramme::with('tourdetails','userinfo')->where(function ($query)  {
                                if(!empty($this->division_id)){
                                    $userIds = User::where('division_id', $request['division_id'])->pluck('id');
                                    $query->whereIn('executive_id', $userIds);
                                }elseif(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('executive_id', $this->userids);
                                }
                            })->select('id','date', 'userid', 'town', 'objectives', 'type', 'status')
                           //->latest()->get();
                           ->orderBy(DB::raw('YEAR(date)'), 'DESC')->orderBy(DB::raw('DATE(date)'), 'ASC')->get();   

           }



    }

    public function headings(): array
    {
        return ['date','id','userid','username', 'town','Actual','objectives', 'type', 'city_id', 'visited_date','visited_cityid', 'last_visited','Employee Code','Branch','Division','Designation', 'Status'];
    }

    public function map($data): array
    {
        $cityname = '';
        $cityid = '';
        $visited_date = '';
        $visited_cityid = '';
        $visited_cityname = '';
        $last_visited = '';
        if($data->status == '0'){
            $status = "Pending";
        }elseif($data->status == '1'){
            $status = "Approved";
        }else{
            $status = "Rejected";
        }
        if(!empty($data['tourdetails']))
        {
            foreach ($data['tourdetails'] as $key => $detail) {


                $rowcityname = isset($detail['cityname']['city_name']) ? $detail['cityname']['city_name'].' , ' : '';
                $rowcityid = isset($detail['city_id']) ? $detail['city_id'].' , ' : '';
                $rowvisited_date = isset($detail['visited_date']) ? $detail['visited_date'].' , ' : '';
                $rowvisited_cityid = isset($detail['visited_cityid']) ? $detail['visited_cityid'].' , ' : '';
                $rowvisited_cityname = isset($detail['visitedcities']['city_name']) ? $detail['visitedcities']['city_name'].' , ' : '';
                $rowlast_visited = isset($detail['last_visited']) ? $detail['last_visited'].' , ' : '';
                $cityname = $cityname.' '.$rowcityname;
                $cityid = $cityid.' '.$rowcityid;
                $visited_date = $visited_date.' '.$rowvisited_date;
                $visited_cityid = $visited_cityid.' '.$rowvisited_cityid;
                $visited_cityname = $visited_cityname.' '.$rowvisited_cityname;
                $last_visited = $last_visited.' '.$rowlast_visited;
            }
        }
        return [
            isset($data['date']) ? date("d-m-Y", strtotime($data['date'])) :'',
            $data['id'],
            isset($data['userid']) ? $data['userid'] :'',
            isset($data['userinfo']['name']) ? $data['userinfo']['name'] : '',
            isset($data['town']) ? $data['town'] :'',
            isset($visited_cityname) ? $visited_cityname :'',
            isset($data['objectives']) ? $data['objectives'] :'',
            isset($data['type']) ? $data['type'] :'',
            $cityid,
            $visited_date,
            $visited_cityid,
            $last_visited,
            isset($data['userinfo']['employee_codes']) ? $data['userinfo']['employee_codes'] :'',
            isset($data['userinfo']['getbranch']['branch_name']) ? $data['userinfo']['getbranch']['branch_name'] :'',
            isset($data['userinfo']['getdepartment']['division_name']) ? $data['userinfo']['getdepartment']['division_name'] :'',
            isset($data['userinfo']['getdesignation']['designation_name']) ? $data['userinfo']['getdesignation']['designation_name'] :'',
            $status,
        ];
    }

}