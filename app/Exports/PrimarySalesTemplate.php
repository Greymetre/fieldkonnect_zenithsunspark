<?php

namespace App\Exports;

use App\Models\PrimarySales;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class PrimarySalesTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return PrimarySales::select('active','invoiceno','invoice_date','month','division','dealer',
        'city','state','final_branch','sales_person','product_name','model_name','quantity','rate','net_amount','tax_amount','cgst_amount','sgst_amount','igst_amount','total_amount',   'store_name','group_name','new_group_name','product_id','created_at','updated_at')->limit(0)->get();   
    }

    public function headings(): array
    {
        return [
            'Invoice No',  
            'Invoice Date',
            'Month',
            'DIV',
            'BP Code', 
            'Dealer',
            'City',
            'State',
            'Final Branch',
            'Branch ID',
            'Sales person',
            'Emp Code',
            'Model Name',
            'Product Name',
            'Quantity',
            'Rate',
            'Net Amount',
            'Tax %',
            'CGST Amt',
            'SGST Amt',
            'IGST Amt',
            'Total',
            'Store Name',
            'Group',
            'Branch',
            'New Group Name',
            'Product ID',
            'Customer Id',
            'group_1',
            'group_2',
            'group_3',
            'group_4'
        ];
    }

}