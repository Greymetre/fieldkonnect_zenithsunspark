<?php

namespace App\Exports;

use App\Models\User;
use App\Models\DealIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerAnalysisExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->fromdate = date("Y-m-01");
        $this->todate = date("Y-m-t");
        $this->userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return DealIn::with('customers:id,name,mobile,executive_id','customeraddress:customer_id,state_id')->select('customer_id', DB::raw('count(*) as total'))->groupBy('customer_id')->get();
    }

    public function headings(): array
    {
        return ['Customer ID', 'Firm Name', 'Mobile', 'State', 'User Name', 'TATA HCV', 'TATA MAV', 'TATA LCV', 'TATA Other', 'Leyland HCV', 'Leyland MAV', 'Leyland Other', 'M&M LMV', 'M&M LCV', 'M&M Other', 'Tractor'];
    }

    public function map($data): array
    {
        $deals = DealIn::where('customer_id','=',$data['customer_id'])->select('types', 'hcv', 'mav', 'lmv', 'lcv', 'other', 'tractor')->get();
        $tata = $deals->where('types','=','TATA')->first();
        $leyland = $deals->where('types','=','LEYLAND')->first();
        $mahindra = $deals->where('types','=','M&M')->first();
        $tractor = $deals->where('types','=','Tractor')->first();
        return [
            !empty($data['customer_id']) ? $data['customer_id'] : '' ,
            !empty($data['customers']['name']) ? $data['customers']['name'] : '' ,
            !empty($data['customers']['mobile']) ? $data['customers']['mobile'] : '' ,
            !empty($data['customeraddress']['statename']['state_name']) ? $data['customeraddress']['statename']['state_name'] : '' ,
            !empty($data['customers']['employeename']['name']) ? $data['customers']['employeename']['name'] : '' ,
            !empty($tata['hcv']) ? 'Yes' : 'No' ,
            !empty($tata['mav']) ? 'Yes' : 'No' ,
            !empty($tata['lmv']) ? 'Yes' : 'No' ,  
            !empty($tata['other']) ? 'Yes' : 'No' ,    
            !empty($leyland['hcv']) ? 'Yes' : 'No' ,
            !empty($leyland['mav']) ? 'Yes' : 'No' ,
            !empty($leyland['other']) ? 'Yes' : 'No' , 
            !empty($mahindra['lmv']) ? 'Yes' : 'No' ,
            !empty($mahindra['lcv']) ? 'Yes' : 'No' ,
            !empty($mahindra['other']) ? 'Yes' : 'No' , 
            !empty($tractor['tractor']) ? 'Yes' : 'No' ,
        ];
    }
}