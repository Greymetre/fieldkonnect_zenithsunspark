<?php

namespace App\Exports;

use App\Models\TransactionHistory;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Customers;
use App\Models\SchemeDetails;
use App\Models\EmployeeDetail;
use App\Models\ParentDetail;
use App\Models\SchemeHeader;
use App\Models\Services;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class TransactionHistoryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->branch_id = $request->input('branch_id');
        $this->parent_customer = $request->input('parent_customer');
        $this->scheme_name = $request->input('scheme_name');
        $this->customer_id = $request->input('customer_id');
        $this->designation = $request->input('designation');
    }

    public function collection()
    {
        $data = TransactionHistory::with(['customer', 'scheme', 'scheme_details']);

        $userids = getUsersReportingToAuth();

        if (!empty($this->branch_id) && count($this->branch_id) > 0) {
            $branch_user_id = User::whereIn('branch_id', $this->branch_id)
                ->whereIn('id', $userids)
                ->pluck('id');

            if ($branch_user_id->isNotEmpty()) {
                $branch_customer_id = Customers::whereIn('executive_id', $branch_user_id)->pluck('id');

                if ($branch_customer_id->isNotEmpty()) {
                    $data->whereIn('customer_id', $branch_customer_id);
                }
            }
        } else {
            $userid = Auth::user()->id;
            $userinfo = User::find($userid);

            if (!$userinfo->hasRole('superadmin') && !$userinfo->hasRole('Admin')) {
                // $data->where('user_id', $userid);
            }
        }

        if ($this->startdate) {
            $data->whereDate('created_at', '>=', $this->startdate);
        }

        if ($this->enddate) {
            $data->whereDate('created_at', '<=', $this->enddate);
        }

        if ($this->parent_customer) {
            $parentCustomerIds = Customers::where('parent_id', $this->parent_customer)->pluck('id');
            $data->whereIn('customer_id', $parentCustomerIds);
        }

        if ($this->scheme_name) {
            $data->whereHas('scheme_details', function ($query) {
                $query->where('id', 'LIKE', '%' . $this->scheme_name . '%');
            });
        }

        if ($this->customer_id) {
            $data->where('customer_id', $this->customer_id);
        }

        $collection = new Collection();

        $data->chunk(1000, function($chunk) use ($collection) {
            foreach ($chunk as $transaction) {
                $collection->push($transaction);
            }
        });

        return $collection;
    }

    public function headings(): array
    {
        return ['Transaction Id', 'Date', 'Customer Id', 'Firm Name', 'Contact Person', 'Parent Code', 'Parent Id', 'Parent Name', 'Mobile Number', 'City', 'District', 'State', 'Branch', 'Div', 'Coupon Code', 'Sub Category', 'Prodcut Id', 'Prodcut Name', 'Scheme Name', 'Active Point', 'Provision Point', 'Points', 'Remark', 'Emp Code', 'User Name'];
    }

    public function map($data): array
    {
        $employee = array();
        $employee_id = array();

        if (!empty($data['customer']['getemployeedetail'])) {
            foreach ($data['customer']['getemployeedetail'] as $key_new => $datas) {
                if($this->designation && !empty($this->designation) && $datas->employee_detail && !empty($datas->employee_detail)){
                    if($datas->employee_detail->designation_id == $this->designation){
                        $employee[] = isset($datas->employee_detail->name) ? $datas->employee_detail->name : '';
                        $employee_id[] = isset($datas->user_id) ? $datas->user_id : '';
                    }
                }
            }
        }

        $parent = array();
        $parent_id = array();
        $parent_code = array();
        if (!empty($data['customer']['getparentdetail'])) {
            foreach ($data['customer']['getparentdetail'] as $key => $parent_data) {
                $parent[] = isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name : '';
                $parent_code[] = isset($parent_data->parent_detail->customer_code) ? $parent_data->parent_detail->customer_code : '';
                $parent_id[] = isset($parent_data->parent_id) ? $parent_data->parent_id : '';
            }
        }


        $getdesignation_arr = array();
        $branch_arr = array();
        $division_arr = array();
        $empcode_arr = array();
        if (!empty($data['customer']['getemployeedetail'])) {
            foreach ($data['customer']['getemployeedetail'] as $key_new => $datas) {
                $getdesignation_arr[] = isset($datas->employee_detail->getdesignation->designation_name) ? $datas->employee_detail->getdesignation->designation_name : '';
                $branch_arr[] = isset($datas->employee_detail->getbranch->branch_name) ? $datas->employee_detail->getbranch->branch_name : '';
                $division_arr[] = isset($datas->employee_detail->getdivision->division_name) ? $datas->employee_detail->getdivision->division_name : '';
                $empcode_arr[] = isset($datas->employee_detail->employee_codes) ? $datas->employee_detail->employee_codes : '';
            }
        }
        if ($data['scheme'] && $data['scheme']['product']) {
            $scheme_details = SchemeDetails::where('product_id', $data['scheme']['product']['id'])->first();
        }

        $branch_arr = collect($branch_arr)->unique()->values()->toArray();
        $division_arr = collect($division_arr)->unique()->values()->toArray();


        //new fields end
        if ($data['status'] == '1') {
            return [
                $data['id'],
                $data['created_at'] = isset($data['created_at']) ? date("d-M-Y", strtotime($data['created_at'])) : '',
                $data['customer']['id'],
                $data['customer']['name'],
                $data['customer']['first_name'] . ' ' . $data['customer']['last_name'],
                // implode(',', $employee),
                implode(',', $parent_code),
                implode(',', $parent_id),
                implode(',', $parent),
                (string)$data['customer']['mobile'],
                $data['city_name'] = isset($data['customer']['customeraddress']['cityname']['city_name']) ? $data['customer']['customeraddress']['cityname']['city_name'] : '',
                $data['district_name'] = isset($data['customer']['customeraddress']['districtname']['district_name']) ? $data['customer']['customeraddress']['districtname']['district_name'] : '',
                $data['state_name'] = isset($data['customer']['customeraddress']['statename']['state_name']) ? $data['customer']['customeraddress']['statename']['state_name'] : '',
                implode(',', $branch_arr),
                implode(',', $division_arr),
                $data['coupon_code'],
                $data['scheme'] ? ($data['scheme']['product']?$data['scheme']['product']['subcategories']['subcategory_name'] : '') : '',
                $data['scheme'] ? ($data['scheme']['product']?$data['scheme']['product']['id'] : '') : '',
                $data['scheme'] ? ($data['scheme']['product']?$data['scheme']['product']['product_name'] : '') : '',
                (isset($scheme_details)) ? $scheme_details->scheme->scheme_name : '',
                $data['active_point'] + $data['provision_point'],
                '',
                $data['point'],
                $data['remark'],
                implode(',', $empcode_arr),
                implode(',', $employee),
            ];
        } else {
            return [
                $data['id'],
                $data['created_at'] = isset($data['created_at']) ? date("d-M-Y", strtotime($data['created_at'])) : '',
                $data['customer']['id'],
                $data['customer']['name'],
                $data['customer']['first_name'] . ' ' . $data['customer']['last_name'],
                // implode(',', $employee),
                implode(',', $parent_code),
                implode(',', $parent_id),
                implode(',', $parent),
                (string)$data['customer']['mobile'],
                $data['city_name'] = isset($data['customer']['customeraddress']['cityname']['city_name']) ? $data['customer']['customeraddress']['cityname']['city_name'] : '',
                $data['district_name'] = isset($data['customer']['customeraddress']['districtname']['district_name']) ? $data['customer']['customeraddress']['districtname']['district_name'] : '',
                $data['state_name'] = isset($data['customer']['customeraddress']['statename']['state_name']) ? $data['customer']['customeraddress']['statename']['state_name'] : '',
                implode(',', $branch_arr),
                implode(',', $division_arr),
                $data['coupon_code'],
                $data['scheme'] ? ($data['scheme']['product']?$data['scheme']['product']['subcategories']['subcategory_name'] : '') : '',
                $data['scheme'] ? ($data['scheme']['product']?$data['scheme']['product']['id'] : '') : '',
                $data['scheme'] ? ($data['scheme']['product']?$data['scheme']['product']['product_name'] : '') : '',
                (isset($scheme_details)) ? $scheme_details->scheme->scheme_name : '',
                $data['active_point'],
                $data['provision_point'],
                $data['point'],
                $data['remark'],
                implode(',', $empcode_arr),
                implode(',', $employee),
            ];
        }
    }
}
