<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class OrderTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Order::select('buyer_id', 'seller_id','total_qty', 'shipped_qty', 'orderno', 'order_date', 'completed_date', 'total_gst', 'sub_total', 'grand_total', 'status_id' , 'suc_del')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['buyer_id', 'seller_id','total_qty', 'shipped_qty', 'orderno', 'order_date', 'completed_date', 'total_gst', 'sub_total', 'grand_total', 'status_id' , 'suc_del'];
    }

}
