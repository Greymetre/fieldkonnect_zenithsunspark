<?php

namespace App\Exports;

use App\Models\CustomerDetails;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Customers;
use App\Models\SchemeDetails;
use App\Models\EmployeeDetail;
use App\Models\ParentDetail;
use App\Models\SchemeHeader;
use App\Models\Services;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class CustomerKycExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->branch_id = $request->input('branch_id');
        $this->kyc_status = $request->input('kyc_status');
        $this->customer_type = $request->input('customer_type');
        //$this->customer_id = $request->input('customer_id');
    }

    public function collection()
    {

        $data = CustomerDetails::with(['customer', 'document_status_by']);
        if ($this->branch_id && $this->branch_id != null && $this->branch_id != '') {
            $branch_user_id = User::where('branch_id', $this->branch_id)->pluck('id');
            if (!empty($branch_user_id)) {
                $branch_customer_id = Customers::whereIn('executive_id', $branch_user_id)->pluck('id');
            }
            if (!empty($branch_customer_id)) {
                $data->whereIn('customer_id', $branch_customer_id);
            }
        }
        if ($this->kyc_status != null && $this->kyc_status != '') {
            if ($this->kyc_status == '5') {
                $data->whereNull('aadhar_no')->orWhere('aadhar_no', '');
            } elseif ($this->kyc_status == '0') {
                $data->where('aadhar_no_status', '0')->whereNotNull('aadhar_no')->where('aadhar_no', '!=', '');
            } else {
                $data->where('aadhar_no_status', $this->kyc_status);
            }
        }
        if ($this->customer_type && $this->customer_type != null && $this->customer_type != '') {
            $type_customer_id = Customers::where('customertype', $this->customer_type)->pluck('id');
            $data->whereIn('customer_id', $type_customer_id);
        }
        if ($this->startdate && $this->startdate != null && $this->startdate != '' && $this->enddate && $this->enddate != null && $this->enddate != '') {
            $startDate = date('Y-m-d', strtotime($this->startdate));
            $endDate = date('Y-m-d', strtotime($this->enddate));
            $data = $data->whereDate('updated_at', '>=', $startDate)
                ->whereDate('updated_at', '<=', $endDate);
        }
        $data = $data->latest()->get();
        return $data;
    }

    public function headings(): array
    {
        return ['KYC Created Date', 'Customer Id', 'User Name', 'Branch', 'Customer Firm Name', 'Customer Type', 'Contact Person', 'Mobile Number', 'State', 'District', 'City', 'KYC Status', 'GSTIN Number', 'PAN Number', 'Aadhar Number', 'Bank Passbook', 'Other Id', 'Approved Reject By'];
    }

    public function map($data): array
    {
        // $data['gmap_address'] = UserActivity::where('customerid','=',$data['id'])->where('type','=','Counter Created')->pluck('address')->first();
        $data['gmap_address'] = UserActivity::where('customerid', '=', $data['customer']['id'])->pluck('address')->first();


        //new fields start

        $employee = array();
        $employee_id = array();

        if (!empty($data['customer']['getemployeedetail'])) {
            foreach ($data['customer']['getemployeedetail'] as $key_new => $datas) {

                $employee[] = isset($datas->employee_detail->name) ? $datas->employee_detail->name : '';
                $employee_id[] = isset($datas->user_id) ? $datas->user_id : '';
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
        if ($data['scheme']) {
            $scheme_details = SchemeDetails::where('product_id', $data['scheme']['product']['id'])->first();
        }
        $status = '';
        $gstinstatus = '';
        $panstatus = '';
        $bankstatus = '';
        $otherstatus = '';
        if($data->aadhar_no == null || $data->aadhar_no == '' || !$data->aadhar_no){
            $status = 'Incomplete';
        }elseif($data->aadhar_no_status == '0'){
            $status = 'Submited';
        }elseif($data->aadhar_no_status == '1'){
            $status = 'Approved';
        }elseif($data->aadhar_no_status == '2'){
            $status = 'Reject';
        }

        if($data->otherid_no == null && $data->otherid_no == '' && !$data->otherid_no){
            $otherstatus = 'N/A';
        }elseif($data->otherid_no_status == '0'){
            $otherstatus = 'Submited';
        }elseif($data->otherid_no_status == '1'){
            $otherstatus = 'Approved';
        }elseif($data->otherid_no_status == '2'){
            $otherstatus = 'Reject';
        }

        if($data->account_number == null || $data->account_number == '' || !$data->account_number ||$data->account_holder == null || $data->account_holder == '' || !$data->account_holder){
            $bankstatus = 'N/A';
        }elseif($data->bank_status == '0'){
            $bankstatus = 'Submited';
        }elseif($data->bank_status == '1'){
            $bankstatus = 'Approved';
        }elseif($data->bank_status == '2'){
            $bankstatus = 'Reject';
        }

        if($data->gstin_no == null && $data->gstin_no == '' && !$data->gstin_no){
            $gstinstatus = 'N/A';
        }elseif($data->gstin_no_status == '0'){
            $gstinstatus = 'Submited';
        }elseif($data->gstin_no_status == '1'){
            $gstinstatus = 'Approved';
        }elseif($data->gstin_no_status == '2'){
            $gstinstatus = 'Reject';
        }

        if($data->pan_no == null && $data->pan_no == '' && !$data->pan_no){
            $panstatus = 'N/A';
        }elseif($data->pan_no_status == '0'){
            $panstatus = 'Submited';
        }elseif($data->pan_no_status == '1'){
            $panstatus = 'Approved';
        }elseif($data->pan_no_status == '2'){
            $panstatus = 'Reject';
        }


        //new fields end
        return [
            $data['updated_at'] = isset($data['updated_at']) ? date("d-M-Y", strtotime($data['updated_at'])) : '',
            $data['customer']['id'],
            implode(',', $employee),
            implode(',', $branch_arr),
            $data['customer']['name'],
            $data['customer']['customertypes']['customertype_name'],
            $data['customer']['first_name'] . ' ' . $data['customer']['last_name'],
            (string)$data['customer']['mobile'],
            $data['state_name'] = isset($data['customer']['customeraddress']['statename']['state_name']) ? $data['customer']['customeraddress']['statename']['state_name'] : '',
            $data['district_name'] = isset($data['customer']['customeraddress']['districtname']['district_name']) ? $data['customer']['customeraddress']['districtname']['district_name'] : '',
            $data['city_name'] = isset($data['customer']['customeraddress']['cityname']['city_name']) ? $data['customer']['customeraddress']['cityname']['city_name'] : '',
            $status,
            $gstinstatus,
            $panstatus,
            ($status == 'Incomplete')?'N/A':$status,
            $bankstatus,
            $otherstatus,
            $data['document_status_by']?$data['document_status_by']['name']:'',
        ];
    }
}
