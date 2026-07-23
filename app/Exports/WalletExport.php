<?php

namespace App\Exports;

use App\Models\Wallet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class WalletExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }
    
    public function collection()
    {
        return Wallet::with('customers','usersinfo')->where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('user_id', $this->userids);
                                }
                            })->select('id','customer_id', 'points', 'point_type', 'transaction_at', 'transaction_type', 'created_at', 'checkinid', 'quantity', 'userid')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','customer_id','customer name' ,'points', 'point_type', 'transaction_at','quantity', 'userid', 'user name'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['customer_id'],
            isset($data['customers']['name'])?$data['customers']['name'] :'',
            $data['points'],
            isset($data['point_type'])?$data['point_type'] :'',
            date("d-m-Y", strtotime($data['transaction_at'])),
            isset($data['quantity'])?$data['quantity'] :'',
            isset($data['userid'])?$data['userid'] :'',
            $data['usersinfo']['name'],
        ];
    }

}