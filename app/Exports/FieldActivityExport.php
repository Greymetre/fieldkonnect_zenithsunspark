<?php

namespace App\Exports;

use App\Models\User;
use App\Models\UserCityAssign;
use App\Models\CheckIn;
use App\Models\TourDetail;
use App\Models\Wallet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class FieldActivityExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        $this->sixmonth = date("Y-m-d",strtotime("-6 Months"));
        $this->threemonth = date("Y-m-d",strtotime("-3 Months"));
        $this->fromdate = date("Y-m-01");
        $this->todate = date("Y-m-t");
        
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return User::with('cities','roles')->where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('id', $this->userids);
                                }
                            })->whereHas('roles', function($query){
                                $query->whereNotIn('name',['superadmin','Admin']);
                            })->where('active','=','Y')->select('id','name','location')->get();  
    }

    public function headings(): array
    {
        return ['ID','Name of ASM', 'Month','No.of Stations A', 'No.of Stations B','No.of Stations C', 'Total No.of Stations', 'A Stations Visited', 'B Stations Visited', 'C Stations Visited','Total Stations Visited','New Dealer Visited', 'Existing Dealer Visited', 'Total Dealer Visited', 'New Mechanic Visited', 'Existing Mechanic Visited', 'Total Mechanic Visited' , 'Garage Points Collected' , 'Dealer Points Collected', 'A Stations Visited Last Three months' , 'Total ABC Stations Visited Last Three months', 'A Stations Visited Last Six months' , 'Total ABC Stations Visited Last Six months', 'Name of Station', 'Sales Blitz', 'Nukkad', 'Road Show', 'Van Campaign', 'Activity Expenses', 'Remarks'];
    }

    public function map($data): array
    {
        $cities = UserCityAssign::with('cityname')->where('userid','=',$data['id'])->select('city_id')->get();
        $tourdetails = TourDetail::with('visitedcities','tourinfo')
                            ->whereHas('tourinfo', function ($query) use($data){
                                $query->whereDate('date', '>=', $this->sixmonth);
                                $query->whereDate('date', '<=', $this->todate);
                                $query->where('userid','=',$data['id']);
                            })
                            ->whereNotNull('visited_cityid')
                            ->select('visited_cityid','tourid')
                            ->get();
        $checkins =  CheckIn::with('customers')->where(function ($query) use($data)  {
                                $query->where('checkin_date', '>=', $this->fromdate);
                                $query->where('checkin_date', '<=', $this->todate);
                                $query->where('user_id','=',$data['id']);
                            })
                            ->select('customer_id','checkin_date')->get();
        $points = Wallet::with('customers')->where(function ($query) use($data)  {
                                $query->where('transaction_at', '>=', $this->fromdate);
                                $query->where('transaction_at', '<=', $this->todate);
                                $query->where('transaction_type','=','Cr');
                                $query->where('userid','=',$data['id']);
                            })
                            ->select('points','customer_id')->get();

        $visited = collect([
                    'new_dealer_visited' => 0,
                    'existing_dealer_visited' => 0,
                    'total_dealer_visited' => 0,
                    'new_mechanic_visited' => 0,
                    'existing_mechanic_visited' => 0,
                    'total_mechanic_visited' => 0,
                ]);
        $checkins->map(function ($item2) use($visited) {
                if(date("Y-m-d", strtotime($item2['customers']['created_at'])) != date("Y-m-d", strtotime($item2['checkin_date'])))
                {
                    if($item2['customers']['customertype'] == 2)
                    {
                        $visited['existing_dealer_visited'] = $visited['existing_dealer_visited'] + 1;
                        $visited['total_dealer_visited'] = $visited['total_dealer_visited'] + 1;
                    }
                    if($item2['customers']['customertype'] == 4)
                    {
                        $visited['existing_mechanic_visited'] = $visited['existing_mechanic_visited'] + 1;
                        $visited['total_mechanic_visited'] = $visited['total_mechanic_visited'] + 1;
                    }
                }
                else
                {
                    if($item2['customers']['customertype'] == 2)
                    {
                        $visited['new_dealer_visited'] = $visited['new_dealer_visited'] + 1;
                        $visited['total_dealer_visited'] = $visited['total_dealer_visited'] + 1;
                    }
                    if($item2['customers']['customertype'] == 4)
                    {
                        $visited['new_mechanic_visited'] = $visited['new_mechanic_visited']+ 1;
                        $visited['total_mechanic_visited'] = $visited['total_mechanic_visited'] + 1;
                    }
                }
            });
        
        return [
            $data['id'],
            isset($data['name']) ? $data['name'] :'',
            date('M'),
            $cities->where('cityname.grade','=','A')->count(),
            $cities->where('cityname.grade','=','B')->count(),
            $cities->where('cityname.grade','=','C')->count(),
            $cities->count(),
            $tourdetails->where('tourinfo.date', '>=', $this->fromdate)->where('visitedcities.grade','=','A')->unique('visited_cityid')->count(),
            $tourdetails->where('tourinfo.date', '>=', $this->fromdate)->where('visitedcities.grade','=','B')->unique('visited_cityid')->count(),
            $tourdetails->where('tourinfo.date', '>=', $this->fromdate)->where('visitedcities.grade','=','C')->unique('visited_cityid')->count(),
            $tourdetails->where('tourinfo.date', '>=', $this->fromdate)->unique('visited_cityid')->count(),
            isset($visited['new_dealer_visited']) ? $visited['new_dealer_visited'] :0,
            isset($visited['existing_dealer_visited']) ? $visited['existing_dealer_visited'] :0,
            isset($visited['total_dealer_visited']) ? $visited['total_dealer_visited'] :0,
            isset($visited['new_mechanic_visited']) ? $visited['new_mechanic_visited'] :0,
            isset($visited['existing_mechanic_visited']) ? $visited['existing_mechanic_visited'] :0,
            isset($visited['total_mechanic_visited']) ? $visited['total_mechanic_visited'] :0,
            $points->where('customers.customertype','=',4)->sum('points'),
            $points->where('customers.customertype','=',2)->sum('points'),
            $tourdetails->where('tourinfo.date', '>=', $this->threemonth)->unique('visited_cityid')->count(),
            $tourdetails->where('tourinfo.date', '>=', $this->threemonth)->whereIn('visitedcities.grade',['A','B','C'])->unique('visited_cityid')->count(),
            $tourdetails->unique('visited_cityid')->count(),
            $tourdetails->whereIn('visitedcities.grade',['A','B','C'])->unique('visited_cityid')->count(),
            isset($data['location']) ? $data['location'] :'',
        ];
    }
}