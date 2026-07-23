<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\BeatSchedule;
use App\Models\CheckIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AdherenceDetailReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        
        $this->start_date = $request->start_date;
        $this->end_date = $request->end_date;
    }
    public function collection()
    {
        return BeatSchedule::with('users','beats:id,beat_name','beatcustomers','beatcheckininfo','beatscheduleorders')
                            ->where(function ($query)  {
                                if($this->start_date)
                                {
                                    $query->whereDate('beat_date', '>=', date('Y-m-d',strtotime($this->start_date)));
                                }
                                if($this->end_date)
                                {
                                    $query->whereDate('beat_date', '<=', date('Y-m-d',strtotime($this->end_date)));
                                }
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('user_id', getUsersReportingToAuth());
                                }
                            })
                        ->select('id','beat_date','user_id','beat_id')
                        ->latest()
                        ->get();    
    }

    public function headings(): array
    {
        return ['User ID', 'User Name', 'Emp Code', 'Branch', 'Location', 'Beat Date', 'Beat Name', 'Total Beat Counter', 'Attendance Punch In Time', 'Attendance Punch Out Time', 'First Check In Counter Time', 'last Check out  Counter Time', 'Without beat Counter Visit', 'Beat Counter Visit', 'Total Visited Counter', 'Beat Adherance %', 'Total Order Unique Counter', 'Beat Productivity %' , 'New Counter Add', 'Unique SKU Count', 'Beat Order Qty', 'Beat Order Value', 'Telephonic Order Qty', 'Telephonic Order Value', 'Total order Qty', 'Total order Value', 'Mobile Apps Download', 'Saarthi First time Active'];
    }

    public function map($data): array
    {
        $attendance_details = Attendance::where(['punchin_date' => $data['beat_date'], 'user_id' => $data['user_id']])->first();
        $checkin_details = CheckIn::where(['checkin_date' => $data['beat_date'], 'user_id' => $data['user_id']])->first();
        $checkout_details = CheckIn::where(['checkin_date' => $data['beat_date'], 'user_id' => $data['user_id']])->orderBy('checkin_time', 'desc')->first();
        $without_beat_visit = CheckIn::where(['checkin_date' => $data['beat_date'], 'user_id' => $data['user_id'], 'beatscheduleid' => NULL])->count('id');
        $beatcounters = !empty($data['beatcustomers']) ? $data['beatcustomers']->count() : 0 ;
        $visitedcounter = !empty($data['beatcheckininfo']) ? $data['beatcheckininfo']->unique('customer_id','checkin_date')->count() : 0 ;
        $totalorder = !empty($data['beatscheduleorders']) ? $data['beatscheduleorders']->count() : 0 ;
        return [
            $data['user_id'],
            isset($data['users']['name']) ? $data['users']['name'] :'',
            isset($data['users']['employee_codes']) ? $data['users']['employee_codes'] :'',
            isset($data['users']['getbranch']) ? $data['users']['getbranch']['branch_name'] :'',
            isset($data['users']['location']) ? $data['users']['location'] :'',
            isset($data['beat_date']) ? $data['beat_date'] :'',
            isset($data['beats']['beat_name']) ? $data['beats']['beat_name'] :'',
            (isset($beatcounters) && $beatcounters > 0) ? $beatcounters :'0',
            $attendance_details?($attendance_details->punchin_time?date('h:i A', strtotime($attendance_details->punchin_time)):''):'',
            $attendance_details?($attendance_details->punchout_time?date('h:i A', strtotime($attendance_details->punchout_time)):''):'',
            $checkin_details?($checkin_details->checkin_time?date('h:i A', strtotime($checkin_details->checkin_time)):''):'',
            $checkout_details?($checkout_details->checkout_time?date('h:i A', strtotime($checkout_details->checkout_time)):''):'',
            $without_beat_visit > 0 ? $without_beat_visit : '0',
            (isset($visitedcounter) && $visitedcounter > 0) ? $visitedcounter :'0',
            isset($visitedcounter) ? ((($visitedcounter+$without_beat_visit) > 0)?$visitedcounter+$without_beat_visit:'0') :'0',
            ($beatcounters === 0) ? 0 : number_format((float)($visitedcounter * 100) / $beatcounters, 1, '.', '').' %',
            isset($totalorder) ? $totalorder :'',
            ($visitedcounter === 0) ? 0 :  number_format((float)($totalorder * 100) / $visitedcounter, 1, '.', '').' %',
            !empty($data['beatschedulecustomer']) ? $data['beatschedulecustomer']->count() : 0,
            !empty($data['beatscheduleorders']) ? $data['beatscheduleorders']->sum('total_qty') : 0,
            !empty($data['beatscheduleorders']['orderdetails']) ? $data['beatscheduleorders']['orderdetails']->sum('total_qty') : 0,
            !empty($data['beatscheduleorders']) ? $data['beatscheduleorders']->sum('grand_total') : 0,
        ];
    }
}