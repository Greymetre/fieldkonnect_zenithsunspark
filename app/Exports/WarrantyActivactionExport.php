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
use App\Models\WarrantyActivation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class WarrantyActivactionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    public function __construct($request)
    {
        $this->status = $request->input('status');
        $this->product_id = $request->input('product_id');
        $this->branch_id = $request->input('branch_id');
        $this->parent_customer = $request->input('parent_customer');
        $this->state_id = $request->input('state_id');
        // $this->customer_id = $request->input('customer_id');
    }

    public function collection()
    {

        $data = WarrantyActivation::with('customer', 'seller_details', 'product_details');
        if ($this->branch_id && $this->branch_id != null && $this->branch_id != '') {
            $data->where('branch_id', $this->branch_id);
        }
        if ($this->parent_customer && $this->parent_customer != null  && $this->parent_customer != '') {
            $data->where('customer_id', $this->parent_customer);
        }
        if ($this->product_id && $this->product_id != null  && $this->product_id != '') {
            $data->where('product_id', $this->product_id);
        }
        if ($this->status && $this->status != null  && $this->status != '') {
            $data->where('status', $this->status);
        }
        if ($this->state_id && $this->state_id != null  && $this->state_id != '') {
            $all_end_users = EndUser::where('state_id', $this->state_id)->pluck('id');
            if (count($all_end_users) > 0) {
                $data->whereIn('end_user_id', $all_end_users);
            } else {
                $data->where('id', '0');
            }
        }


        $data = $data->latest()->get();
        return $data;
    }

    public function headings(): array
    {
        return ['activation status', 'Name', 'Email', 'Contact No', 'Product Serial No', 'Product Name', 'Product Code', 'Warranty Start Date', 'Warranty End Date', 'Seller Name', 'Seller Code', 'Sale Bill Date', 'Dealer Warranty Date', 'CO Sale Bill No'];
    }

    public function map($data): array
    {

        if($data['status'] == '0'){
            $status = 'In Verification';
        }elseif($data['status'] == '1'){
            $status = 'Activated';
        }elseif($data['status'] == '2'){
            $status = 'Pending Activated';
        }elseif($data['status'] == '3'){
            $status = 'Reject';
        }
        $expire_count = $data['product_details']?$data['product_details']['expiry_interval_preiod']:"18";
        $expire_type = $data['product_details']?strtolower($data['product_details']['expiry_interval'].'s'):"months";
        return [
            $status,
            $data['customer']['customer_name'],
            $data['customer']['customer_email'],
            $data['customer']['customer_number'],
            $data['product_serail_number'],
            $data['product_details']['product_name']??'',
            $data['product_details']['product_code']??'',
            date('d M Y' ,strtotime($data['created_at'])),
            date('d M Y', strtotime($data['created_at'] . ' +'.$expire_count.' '.$expire_type)),
            $data['seller_details']['name'],
            $data['seller_details']['customer_code'],
            date('d M Y' ,strtotime($data['sale_bill_date'])),
            date('d M Y' ,strtotime($data['warranty_date'])),
            $data['sale_bill_no'],
        ];
    }
}
