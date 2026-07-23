<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Division;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\{ParentDetail, TransactionHistory, Redemption, MobileUserLoginDetails};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoyaltyDealerSummaryReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->branch_id = $request->input('branch_id');
        $this->dealer_id = $request->input('dealer_id');
    }

    public function collection()
    {
        return Customers::with('customertypes', 'firmtypes', 'createdbyname', 'getretailers', 'customeraddress.cityname', 'customeraddress.statename', 'getretailers.redemption', 'getretailers.transactions', 'userdetails.getbranch')->whereIn('customertype', ['1', '3'])->whereHas('getretailers')->where(function ($query) {
            $userids = getUsersReportingToAuth();
            if ($this->branch_id && $this->branch_id != '' && $this->branch_id != null) {
                $userIdsss = User::where('branch_id', $this->branch_id)->whereIn('id', $userids)->pluck('id');
                $query->whereIn('executive_id', $userIdsss);
            }

            if ($this->dealer_id && $this->dealer_id != '' && $this->dealer_id != null) {
                $query->where('id', $this->dealer_id);
            }
        })->limit(5000)->latest()->get();
    }

    public function headings(): array
    {

        return [
            'Dealer & Distributor Firm Name',
            'State',
            'City',
            'Branch',
            'Total Retailer Registred Nos',
            'Total Retailer Under Saarthi Nos',
            'Coupon Scan Nos',
            'Mobile App Donwload Nos',
            'Provision Point',
            'Active Point',
            'Total Point',
            'Redeem Gift',
            'Redeem Neft',
            'Total Redeem',
            'Balance Active Point'
        ];
    }




    public function map($data): array
    {
        // $allCustomerIds = $data->getretailers->pluck('customer_id');
        $customerIds = Customers::with('getemployeedetail.employee_detail')->whereHas('getemployeedetail.employee_detail', function ($q) {
            $q->where('division_id', '10');
        })->where(['customertype' => '2', 'active' => 'Y'])->whereHas('getparentdetail', function ($q) use ($data) { $q->where('parent_id', $data->id); })->pluck('id')->toArray();
        
        $total_registered_retailers = $customerIds ? count($customerIds) : '0';
        $total_retailers_under_saarthi =  TransactionHistory::whereIn('customer_id', $customerIds)
        ->select('customer_id')
        ->groupBy('customer_id')
        ->havingRaw('COUNT(*) > 1 OR SUM(CASE WHEN scheme_id IS NOT NULL THEN 1 ELSE 0 END) > 0')
        ->count();
        $coupon_scan_nos = TransactionHistory::whereIn('customer_id', $customerIds)->whereNotNull('scheme_id')->count();
        $mobile_app_downloads = MobileUserLoginDetails::whereIn('customer_id', $customerIds)->count();
        $active_point = 0;
        $provision_point = 0;
        $thistorys = TransactionHistory::whereIn('customer_id', $customerIds)->whereNotNull('scheme_id')->get();
        foreach ($thistorys as $thistory) {
            if ($thistory->status == '1') {
                $active_point += $thistory->point;
            } else {
                $active_point += $thistory->active_point;
                $provision_point += $thistory->provision_point;
            }
        }
        $total_point = TransactionHistory::whereIn('customer_id', $customerIds)->sum('point');
        $redeem_gift = Redemption::with('customer')->where('status', '!=', '2')->whereIn('customer_id', $customerIds)->where('redeem_mode', '1')->sum('redeem_amount');
        $redeem_neft = Redemption::with('customer')->where('status', '!=', '2')->whereIn('customer_id', $customerIds)->where('redeem_mode', '2')->sum('redeem_amount');
        $total_redeem = $redeem_gift + $redeem_neft;
        $balance_active_point = $total_point - $total_redeem;


        return [

            $data['name'] ?? '',
            $data['customeraddress']['statename']['state_name'] ?? '',
            $data['customeraddress']['cityname']['city_name'] ?? '',
            $data['userdetails']['getbranch']['branch_name'] ?? '',
            $total_registered_retailers ?? '0',
            $total_retailers_under_saarthi ?? '0',
            $coupon_scan_nos ?? '0',
            $mobile_app_downloads ?? '0',
            $provision_point ?? '0',
            $active_point ?? '0',
            $total_point ?? '0',
            $redeem_gift ?? '0',
            $redeem_neft ?? '0',
            $total_redeem ?? '0',
            $balance_active_point ?? '0',

        ];
    }
}
