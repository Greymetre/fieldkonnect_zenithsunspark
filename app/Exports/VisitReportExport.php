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


class VisitReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }
    
    public function collection()
    {
        return VisitReport::where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('user_id', $this->userids);
                                }
                            })->select('id','checkin_id', 'user_id', 'customer_id', 'visit_type_id', 'report_title', 'description','created_at')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','checkin_id', 'user_id','user_name', 'customer_id','Firm Name','customer name', 'Mobile', 'visit_type', 'report_title', 'description', 'Created Date'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['checkin_id'],
            $data['user_id'],
            $data['users']['name'],
            $data['customer_id'],
            isset($data['customers']['name'])?$data['customers']['name'] :'',
            isset($data['customers']['first_name'])?$data['customers']['first_name'].' '.$data['customers']['last_name'] :'',
            isset($data['customers']['mobile'])?$data['customers']['mobile'] :'',
            isset($data['visittypename']['type_name'])?$data['visittypename']['type_name'] :'',
            $data['report_title'],
            $data['description'],
            date("d-m-Y", strtotime($data['created_at']))
        ];
    }

}