<?php

namespace App\Exports;

use App\Models\Coupons;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class CouponsTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Coupons::select('coupon_code','generated_date','expiry_date', 'customer_code', 'invoice_date', 'invoice_no', 'product_code', 'status_id', 'product_id', 'coupon_profile_id')->limit(0)->get();   
    }

    public function headings(): array
    {
        return ['coupon_code','generated_date','expiry_date', 'customer_code', 'invoice_date', 'invoice_no', 'product_code', 'status_id', 'product_id', 'coupon_profile_id'];
    }

}
