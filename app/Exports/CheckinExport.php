<?php

namespace App\Exports;

use App\Models\CheckIn;
use App\Models\OrderDetails;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class CheckinExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->user_id = $request->input('user_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
        $this->userids = getUsersReportingToAuth();
    }
    public function collection()
    {
        return CheckIn::with('beatschedules', 'customers', 'users', 'orders', 'visitreports', 'visitreports.visittypename', 'orders_sum')->where(function ($query) {
            if ($this->user_id) {
                $query->where('user_id', $this->user_id);
            } elseif (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Sub_Admin') && !Auth::user()->hasRole('HR_Admin')) {
                $query->whereIn('user_id', $this->userids);
            }
            if ($this->division_id && $this->division_id != null && $this->division_id != '') {
                $query->whereHas('users', function ($query) {
                    $query->where('division_id', $this->division_id);
                });
            }
            if ($this->branch_id && $this->branch_id != null && $this->branch_id != '') {
                $query->whereHas('users', function ($query) {
                    $query->where('branch_id', $this->branch_id);
                });
            }
            if ($this->startdate) {
                $query->whereDate('checkin_date', '>=', $this->startdate);
            }
            if ($this->enddate) {
                $query->whereDate('checkin_date', '<=', $this->enddate);
            }
        })
            ->select('id', 'customer_id', 'user_id', 'checkin_date', 'checkin_time', 'checkout_date', 'checkout_time' ,'time_interval', 'checkin_address', 'checkout_address', 'distance', 'beatscheduleid', 'created_at')
            ->latest()->get();
    }

    public function headings(): array
    {
        return ['id', 'Checkin Date', 'User ID', 'Employee Code', 'User Name', 'Designation', 'Division', 'Branch', 'Checkin Time', 'Checkout Time', 'Spend Time', 'Checkin Address', 'Checkout Address', 'Distance', 'Customer Id', 'Customer Type', 'Customer Name', 'Customer Mobile', 'Beat Name', 'City', 'District', 'Address', 'Existing', 'Visit Type', 'Visit Remark', 'Order Qty', 'Order Value'];
    }

    public function map($data): array
    {
        $sum_qty = 0;
        // if (!empty($data['orders_sum'])) {
        //     foreach ($data['orders_sum'] as $key_new => $datas) {
        //         $order_id = $datas->id;
        //         $sum_qty += OrderDetails::where('order_id', $order_id)->sum('quantity') ?? 0;
        //     }
        // }



        return [
            $data['id'],
            isset($data['checkin_date']) ? $data['checkin_date'] : '',
            isset($data['user_id']) ? $data['user_id'] : '',
            isset($data['users']['employee_codes']) ? $data['users']['employee_codes'] : '',
            isset($data['users']['name']) ? $data['users']['name'] : '',
            isset($data['users']['getdesignation']['designation_name']) ? $data['users']['getdesignation']['designation_name'] : '',
            isset($data['users']['getdivision']['division_name']) ? $data['users']['getdivision']['division_name'] : '',
            isset($data['users']['getbranch']['branch_name']) ? $data['users']['getbranch']['branch_name'] : '',

            isset($data['checkin_time']) ? $data['checkin_time'] : '',
            isset($data['checkout_time']) ? $data['checkout_time'] : '',
            isset($data['time_interval']) ? $data['time_interval'] : '-',
            isset($data['checkin_address']) ? $data['checkin_address'] : '',
            isset($data['checkout_address']) ? $data['checkout_address'] : '',
            isset($data['distance']) ? $data['distance'] : '',

            isset($data['customer_id']) ? $data['customer_id'] : '',
            isset($data['customers']['customertypes']['customertype_name']) ? $data['customers']['customertypes']['customertype_name'] : '',
            isset($data['customers']['name']) ? $data['customers']['name'] : '',
            isset($data['customers']['mobile']) ? $data['customers']['mobile'] : '',
            isset($data['beatschedules']['beats']['beat_name']) ? $data['beatschedules']['beats']['beat_name'] : '',
            isset($data['customers']['customeraddress']['cityname']['city_name']) ? $data['customers']['customeraddress']['cityname']['city_name'] : '',

            isset($data['customers']['customeraddress']['districtname']['district_name']) ? $data['customers']['customeraddress']['districtname']['district_name'] : '',


            isset($data['customers']['customeraddress']['address1']) ? $data['customers']['customeraddress']['address1'] . ' ' . $data['customers']['customeraddress']['address2'] : '',
            $data['customers'] ? ((date("Y-m-d", strtotime($data['customers']['created_at'])) == date("Y-m-d", strtotime($data['checkin_date']))) ? 'New' : 'Existing') : '-',
            isset($data['visitreports']['visittypename']['type_name']) ? $data['visitreports']['visittypename']['type_name'] : '',
            isset($data['visitreports']['description']) ? $data['visitreports']['description'] : '',
            // isset($data['orders']) ?$data['orders']->sum('total_qty') : 0,
            // isset($data['orders']) ? $data['orders']->sum('grand_total') : 0,
            $sum_qty,
            (!empty($data['orders_sum'])) ? $data['orders_sum']->sum('grand_total') : 0,
        ];
    }
}
