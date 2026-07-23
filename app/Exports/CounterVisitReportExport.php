<?php

namespace App\Exports;
use App\Models\User;
use App\Models\Attendance;
use App\Models\CheckIn;
use App\Models\Customers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CounterVisitReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->start_date = $request->start_date;
        $this->end_date = $request->end_date;
        
        $this->userids = getUsersReportingToAuth();
    }
    public function collection()
    {
        return User::where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('id', $this->userids);
                                }
                            })->select('id','name', 'mobile','location')->latest()->get();   
    }

    public function headings(): array
    {
        return ['FOS Id','FOS Name', 'Mobile','Location', 'Non Field Working Days','Field Working Days', 'New Visit Counters', 'Revisited Counters', 'Visits Per day', 'Remark','Cumulative Non Field Working Days','Cumulative Field Working Days', 'Cumulative New Visit Counters', 'Cumulative Revisited Counters', 'Cumulative Visits Per day', 'Cumulative Remark'];
    }

    public function map($data): array
    {

        $workings = Attendance::where('user_id',$data['id'])->select('id','punchin_date','worked_time','working_type')->get();
        $attendances = $workings->map(function ($item) {
            $days = 0;
            switch ($item->worked_time) {
                case (date('H',strtotime($item->worked_time))  >= 4 && date('H',strtotime($item->worked_time)) < 7):
                    $days = 0.5;
                    break;
                case (date('H',strtotime($item->worked_time)) >= 7):
                    $days = 1;
                    break;
                default:
                    break;
            }
            $item['field_working_days'] = ($item->working_type =='fields') ? $days : 0 ;
            $item['working_days'] = ($item->working_type !='fields') ? $days : 0 ;
            $item['range_field_working_days'] = ((date('Y-m-d', strtotime($item->punchin_date)) >= date('Y-m-d', strtotime($this->start_date))) && (date('Y-m-d', strtotime($item->punchin_date)) <= date('Y-m-d', strtotime($this->end_date))) && $item->working_type =='fields') ? $days : 0 ;
            $item['range_working_days'] = (date('Y-m-d', strtotime($item->punchin_date)) >= date('Y-m-d', strtotime($this->start_date))) && (date('Y-m-d', strtotime($item->punchin_date)) <= date('Y-m-d', strtotime($this->end_date)) && $item->working_type !='fields') ? $days : 0 ;
            return $item ;
        });
        $counters = CheckIn::with('customers:id,created_at')->where('user_id',$data['id'])->select('customer_id', 'checkin_date')->get();
        $revisited = $counters->map(function ($item) {
            $item['revisited_counters'] = (date("Y-m-d", strtotime($item['customers']['created_at'])) != date("Y-m-d", strtotime($item['checkin_date']))) ? 1 : 0 ;
            $item['range_revisited_counters'] = (date('Y-m-d', strtotime($item->checkin_date)) >= date('Y-m-d', strtotime($this->start_date))) && (date('Y-m-d', strtotime($item->checkin_date)) <= date('Y-m-d', strtotime($this->end_date)) && (date("Y-m-d", strtotime($item['customers']['created_at'])) != date("Y-m-d", strtotime($item['checkin_date'])))) ? 1 : 0 ;
            return $item ;
        });
        $customers = Customers::where('created_by',$data['id'])->select('created_at');
        $new_visit_counters = $customers->count();   
        $revisited_counters = $revisited->sum('revisited_counters');  
        $range_new_visit_counters = $customers->whereDate('created_at', '>=', date('Y-m-d',strtotime($this->start_date)))->whereDate('created_at', '<=', date('Y-m-d',strtotime($this->end_date)))->count();    
        $range_revisited_counters = $revisited->sum('range_revisited_counters'); 

        //$attendances = Attendance::where('user_id',$data['id'])->selectRaw('user_id, count(*) as working_days, COUNT(CASE working_type WHEN "fields" THEN 1 ELSE NULL END) AS field_working_days')->groupBy('user_id')->first();


        // $range_attendances = Attendance::where('user_id',$data['id'])->where(function ($query)  {
        //     if($this->start_date)
        //     {
        //         $query->where('punchin_date', '>=', date('Y-m-d',strtotime($this->start_date)));
        //     }
        //     if($this->end_date)
        //     {
        //         $query->where('punchin_date', '<=', date('Y-m-d',strtotime($this->end_date)));
        //     }
        // })
        // ->selectRaw('user_id, count(*) as working_days, COUNT(CASE working_type WHEN "fields" THEN 1 ELSE NULL END) AS field_working_days')->groupBy('user_id')->first();

        // $counters = CheckIn::where('user_id',$data['id'])->select('customer_id', DB::raw('count(*) as visit_counters'))->groupBy('customer_id')->get();
        // $range_counters = CheckIn::where('user_id',$data['id'])->where(function ($query)  {
        //     if($this->start_date)
        //     {
        //         $query->where('checkin_date', '>=', date('Y-m-d',strtotime($this->start_date)));
        //     }
        //     if($this->end_date)
        //     {
        //         $query->where('checkin_date', '<=', date('Y-m-d',strtotime($this->end_date)));
        //     }
        // })
        // ->select('customer_id', DB::raw('count(*) as visit_counters'))->groupBy('customer_id')->get();
        // $new_visit_counters = $counters->where('visit_counters',1)->sum('visit_counters');   
        // $revisited_counters = $counters->where('visit_counters','>=',2)->sum('visit_counters');  
        // $range_new_visit_counters = $range_counters->where('visit_counters',1)->sum('visit_counters');   
        // $range_revisited_counters = $range_counters->where('visit_counters','>=',2)->sum('visit_counters'); 
        return [
            $data['id'],
            isset($data['name']) ? $data['name'] :'',
            isset($data['mobile']) ? $data['mobile'] :'',
            isset($data['location']) ? $data['location'] :'',
            $attendances->sum('range_working_days'),
            $attendances->sum('range_field_working_days'),
            $range_new_visit_counters,
            $range_revisited_counters,
            ($attendances->sum('range_field_working_days') >= 1) ? floor(($range_new_visit_counters + $range_revisited_counters)/$attendances->sum('range_field_working_days')) : 0,
            '',
            $attendances->sum('working_days'),
            $attendances->sum('field_working_days'),
            $new_visit_counters,
            $revisited_counters,
            ($attendances->sum('field_working_days') >= 1) ? floor(($new_visit_counters + $revisited_counters)/$attendances->sum('field_working_days')) : '',
            '',
        ];
    }
}