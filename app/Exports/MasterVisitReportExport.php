<?php

namespace App\Exports;

use App\Models\VisitReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class MasterVisitReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return VisitReport::with('users','customers','customeraddress')->where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('user_id', $this->userids);
                                }
                            })->select('user_id','customer_id')->groupBy('customer_id','user_id')->get();    
    }

    public function headings(): array
    {
        return ['User ID','User Name','Customer ID', 'Firm Name', 'Customer Name', 'Mobile', 'Address', 'Market Place', 'City', 'District', 'State' , 'Pin', 'First Visit Date', 'First Visit Remark', 'Second Visit Date', 'Second Visit Remark', 'Third Visit Date', 'Third Visit Remark', 'Fourth Visit Date', 'Fourth Visit Remark', 'Fifth Visit Date', 'Fifth Visit Remark', 'Sixth Visit Date', 'Sixth Visit Remark', 'Seventh Visit Date', 'Seventh Visit Remark', 'Eighth Visit Date', 'Eighth Visit Remark', 'Ninth Visit Date', 'Ninth Visit Remark', 'Tenth Visit Date', 'Tenth Visit Remark'];
    }

    public function map($data): array
    {
        $visits = VisitReport::where('user_id','=',$data['user_id'])->where('customer_id','=',$data['customer_id'])->select('description','created_at')->limit(10)->latest()->get();  

        return [
            $data['user_id'],
            isset($data['users']['name']) ? $data['users']['name'] :'',
            $data['customer_id'],
            isset($data['customers']['name']) ? $data['customers']['name'] :'',
            isset($data['customers']['first_name']) ? $data['customers']['first_name'].' '.$data['customers']['last_name'] :'',
            isset($data['customers']['mobile']) ? $data['customers']['mobile'] :'',
            isset($data['customeraddress']['address1']) ? $data['customeraddress']['address1'] :'',
            isset($data['customeraddress']['landmark']) ? $data['customeraddress']['landmark'] : '',
            isset($data['customeraddress']['cityname']['city_name']) ? $data['customeraddress']['cityname']['city_name'] : '',
            isset($data['customeraddress']['districtname']['district_name']) ? $data['customeraddress']['districtname']['district_name'] :'',
            isset($data['customeraddress']['statename']['state_name']) ? $data['customeraddress']['statename']['state_name'] :'',
            isset($data['customeraddress']['zipcode']) ? $data['customeraddress']['zipcode'] : '',
            isset($visits[0]['created_at']) ? date("d-m-Y", strtotime($visits[0]['created_at'])) : '',
            isset($visits[0]['description']) ? $visits[0]['description'] : '',
            isset($visits[1]['created_at']) ? date("d-m-Y", strtotime($visits[1]['created_at'])) : '',
            isset($visits[1]['description']) ? $visits[1]['description'] : '',
            isset($visits[2]['created_at']) ? date("d-m-Y", strtotime($visits[2]['created_at'])) : '',
            isset($visits[2]['description']) ? $visits[2]['description'] : '',
            isset($visits[3]['created_at']) ? date("d-m-Y", strtotime($visits[3]['created_at'])) : '',
            isset($visits[3]['description']) ? $visits[3]['description'] : '',
            isset($visits[4]['created_at']) ? date("d-m-Y", strtotime($visits[4]['created_at'])) : '',
            isset($visits[4]['description']) ? $visits[4]['description'] : '',
            isset($visits[5]['created_at']) ? date("d-m-Y", strtotime($visits[5]['created_at'])) : '',
            isset($visits[5]['description']) ? $visits[5]['description'] : '',
            isset($visits[6]['created_at']) ? date("d-m-Y", strtotime($visits[6]['created_at'])) : '',
            isset($visits[6]['description']) ? $visits[6]['description'] : '',
            isset($visits[7]['created_at']) ? date("d-m-Y", strtotime($visits[7]['created_at'])) : '',
            isset($visits[7]['description']) ? $visits[7]['description'] : '',
            isset($visits[8]['created_at']) ? date("d-m-Y", strtotime($visits[8]['created_at'])) : '',
            isset($visits[8]['description']) ? $visits[8]['description'] : '',
            isset($visits[9]['created_at']) ? date("d-m-Y", strtotime($visits[9]['created_at'])) : '',
            isset($visits[9]['description']) ? $visits[9]['description'] : '',
        ];
    }

}