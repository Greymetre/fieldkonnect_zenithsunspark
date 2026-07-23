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


class DistributorExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct()
    {
        
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return Customers::whereHas('customertypes', function($query){
                                $query->where('type_name', '=', 'distributor')->orWhere('type_name', '=', 'Dealer');
                            })
                            ->where(function ($query)  {
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('executive_id', $this->userids);
                                }
                            })
                        ->select('id','name', 'first_name', 'last_name', 'mobile', 'email', 'password', 'notification_id', 'latitude', 'longitude', 'device_type', 'gender', 'profile_image', 'customer_code', 'status_id','customertype', 'firmtype','created_at','created_by')
                        ->latest()->get();   
    }

    public function headings(): array
    {
        return ['Created Date','Distributor ID','Customer Type','Created by', 'Distributor Code','Firm Name', 'First Name', 'Last Name', 'Mobile', 'Email','Address', 'Gmap address','Pin Code','Market Place','City','District','State','Beat Name', 'Latitude', 'Longitude','GST No','Adhar No','Pan No','Other No', 'Shop Image', 'Status'];
    }

    public function map($data): array
    {
        return [
            $data['created_at'] = isset($data['created_at']) ? date("d-m-Y", strtotime($data['created_at'])) :'',
            $data['id'],
            $data['customertypes']['customertype_name'],
            $data['createdbyname']['name'],
            $data['customer_code'],
            $data['name'],
            $data['first_name'],
            $data['last_name'],
            $data['mobile'],
            $data['email'],
            isset($data['customeraddress']['address1']) ? $data['customeraddress']['address1'] : '',
            $data['gmap_address'] = isset($data['gmap_address']) ? '' :'',
            $data['pincode_id'] = isset($data['customeraddress']['pincodename']['pincode']) ? $data['customeraddress']['pincodename']['pincode'] : '',
            $data['landmark'] = isset($data['customeraddress']['landmark']) ? $data['customeraddress']['landmark'] : '',
            // $data['customeraddress']['cityname']['city_name'],
            // $data['customeraddress']['districtname']['district_name'],
            // $data['customeraddress']['statename']['state_name'],
            // $data['beatdetails']['beats']['beat_name'],
            $data['city_name'] = isset($data['customeraddress']['cityname']['city_name']) ? $data['customeraddress']['cityname']['city_name'] : '',
            $data['district_name'] = isset($data['customeraddress']['districtname']['district_name']) ? $data['customeraddress']['districtname']['district_name'] :'',
            $data['state_name'] = isset($data['customeraddress']['statename']['state_name']) ? $data['customeraddress']['statename']['state_name'] :'',
            $data['beat_name'] = isset($data['beatdetails']['beats']['beat_name']) ? $data['beatdetails']['beats']['beat_name'] :'',
            $data['latitude'],
            $data['longitude'],
            isset($data['customerdetails']['gstin_no']) ? $data['customerdetails']['gstin_no'] :'',
            isset($data['customerdetails']['aadhar_no']) ? $data['customerdetails']['aadhar_no'] :'',
            isset($data['customerdetails']['pan_no']) ? $data['customerdetails']['pan_no'] : '',
            isset($data['customerdetails']['otherid_no']) ? $data['customerdetails']['otherid_no'] : '',
            $data['shop_image'] = isset($data['profile_image']) ? $data['profile_image'] :'',
            isset($data['statusname']['status_name']) ? $data['statusname']['status_name'] :'',
            
        ];
    }

}