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

class CouponsExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Coupons::select('id','coupon_code','generated_date','expiry_date', 'customer_code', 'invoice_date', 'invoice_no', 'product_code', 'status_id', 'product_id', 'coupon_profile_id')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','coupon_code','generated_date','expiry_date', 'customer_code', 'invoice_date', 'invoice_no', 'product_code', 'status_id', 'product_id', 'product_name','coupon_profile_id'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['coupon_code'],
            Date::dateTimeToExcel($data['generated_date']),
           	Date::dateTimeToExcel($data['expiry_date']),
            $data['customer_code'],
            Date::dateTimeToExcel($data['invoice_date']),
            $data['invoice_no'],
            $data['product_code'],
            $data['status']['status_name'],
            $data['product_id'],
            $data['products']['product_name'],
            $data['coupon_profile_id'],
        ];
    }

}