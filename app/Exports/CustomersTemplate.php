<?php

namespace App\Exports;

use App\Models\Customers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class CustomersTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return Customers::limit(0)->get();   
    }

    public function headings(): array
    {
        return ['firm_name', 'first_name', 'last_name', 'mobile', 'email', 'password', 'notification_id', 'latitude', 'longitude', 'device_type', 'gender', 'profile_image', 'customer_code', 'status_id', 'region_id', 'customertype', 'firmtype','address1', 'address2', 'landmark', 'locality', 'country_id', 'state_id','district_id' ,'city_id', 'pincode_id','gstin_no', 'pan_no', 'aadhar_no', 'otherid_no', 'enrollment_date', 'approval_date','employee_id','parent_id','contact_number'];
    }

}