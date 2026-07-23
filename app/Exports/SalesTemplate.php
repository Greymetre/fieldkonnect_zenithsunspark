<?php

namespace App\Exports;

use App\Models\Sales;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class SalesTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Sales::select('buyer_id', 'seller_id', 'order_id', 'total_qty', 'shipped_qty', 'orderno', 'fiscal_year', 'sales_no', 'invoice_no', 'invoice_date', 'total_gst', 'sub_total', 'grand_total', 'description', 'status_id')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['buyer_id', 'seller_id', 'order_id', 'total_qty', 'shipped_qty', 'orderno', 'fiscal_year', 'sales_no', 'invoice_no', 'invoice_date', 'total_gst', 'sub_total', 'grand_total', 'description', 'status_id'];
    }

}