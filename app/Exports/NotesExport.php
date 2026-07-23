<?php

namespace App\Exports;

use App\Models\Notes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class NotesExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Notes::with('users:id,name','customerinfo:id,name,mobile,executive_id,customertype','customeraddress:customer_id,state_id')->select('id','user_id', 'customer_id', 'note', 'purpose', 'callstatus', 'status_id', 'created_at')->get();   
    }
    
    public function headings(): array
    {
        return ['User Name','Customer id','Customer Name','Customer Type', 'Mobile', 'State','Calling Type','Status','Employee Name','Date', 'Remark'];
    }

    public function map($data): array
    {
        return [
            !empty($data['users']['name']) ? $data['users']['name'] : '' ,
            !empty($data['customer_id']) ? $data['customer_id'] : '' ,
            !empty($data['customerinfo']['name']) ? $data['customerinfo']['name'] : '' ,
            !empty($data['customerinfo']['customertypes']['customertype_name']) ? $data['customerinfo']['customertypes']['customertype_name'] : '' ,
            !empty($data['customerinfo']['mobile']) ? $data['customerinfo']['mobile'] : '' ,
            !empty($data['customeraddress']['statename']['state_name']) ? $data['customeraddress']['statename']['state_name'] : '' ,
            !empty($data['purpose']) ? $data['purpose'] : '' ,
            !empty($data['callstatus']) ? $data['callstatus'] : '' ,
            !empty($data['customerinfo']['employeename']['name']) ? $data['customerinfo']['employeename']['name'] : '' ,
            isset($data['created_at']) ? date("d-M-Y", strtotime($data['created_at'])) : '',
            !empty($data['note']) ? $data['note'] : '' ,
        ];
    }

}