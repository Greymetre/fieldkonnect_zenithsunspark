<?php

namespace App\Exports;

use App\Models\Complaint;
use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ComplaintExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    protected $user_id;
    protected $start_date;
    protected $end_date;
    protected $filters;

    public function __construct(Request $request)
    {
        $this->filters = $request->all();

        // Handle date inputs separately
        if (!empty($request->input('start_date'))) {
            $this->filters['start_date'] = Carbon::parse($request->input('start_date'))->startOfDay();
        }

        if (!empty($request->input('end_date'))) {
            $this->filters['end_date'] = Carbon::parse($request->input('end_date'))->endOfDay();
        }
    }

   public function collection()
    {
        $query = Complaint::with([
            'party',
            'service_center_details',
            'customer',
            'complaint_type_details',
            'product_details.categories',
            'division_details',
            'complaint_time_line',
            'service_bill',
            'purchased_branch_details',
            'createdbyname',
            'complaint_work_dones',
            'warranty_details'
        ]);

        // Apply date range filter
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('created_at', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Sub_Admin') && !Auth::user()->hasRole('Service Admin') &&  !Auth::user()->hasRole('CRM_Support')){
           $query->where('assign_user' , Auth::user()->id);
        }

        // Loop through filters dynamically

        foreach ($this->filters as $key => $value) {
             if (isset($value))  {
                switch ($key) {
                    case 'complaint_number':
                    case 'seller':
                    case 'service_type':
                    case 'service_type_1':
                    case 'warranty_bill':
                    case 'customer_bill_no':
                    case 'under_warranty':
                    case 'company_sale_bill_no':
                    case 'register_by':
                    case 'description':
                        $query->where($key, 'like', "%$value%");
                        break;

                    case 'status':
                        $query->where('complaint_status', $value);
                        break;

                    case 'customer_complaint_type':
                        $query->whereHas('complaint_type_details', function ($q) use ($value) {
                            $q->where('name', 'like', "%$value%");
                        });
                        break;

                    case 'service_status':
                        $query->whereHas('service_bill', function ($q) use ($value) {
                            $q->where('status', $value);
                        });
                        break;

                    case 'service_branch':
                        $query->whereHas('purchased_branch_details', function ($q) use ($value) {
                            $q->whereRaw("CONCAT(branch_code, ' ', branch_name) LIKE ?", ["%$value%"]);
                        });
                        break;

                    case 'purchased_party_name':
                        $query->whereHas('customer', function ($q) use ($value) {
                            $q->whereRaw("CONCAT(customer_name, ' ', customer_number) LIKE ?", ["%$value%"]);
                        });
                        break;

                    case 'createdbyname_name':
                        $query->whereHas('createdbyname', function ($q) use ($value) {
                            $q->where('name', $value);
                        });
                        break;

                    case 'customer_bill_date':
                    case 'customer_bill_date_1':
                    case 'company_sale_bill_date':
                    case 'last_update_date':
                    case 'created_at':
                        try {
                            $formattedDate = Carbon::parse($value)->format('Y-m-d');
                            $query->whereDate($key, '=', $formattedDate);
                        } catch (\Exception $e) {
                            // Handle invalid date formats gracefully
                        }
                        break;

                    case 'service_center_name':
                         if (!empty($value)) {
                            $serviceCenterIds = is_array($value[0]) 
                                                ? $value 
                                                : explode(',', $value[0]); 
                            if (collect($serviceCenterIds)->filter()->isNotEmpty()) {
                                $query->whereIn('service_center', $serviceCenterIds);
                            }
                        }
                        break;

                    case 'assign_user':
                         if (!empty($value)) {
                            $assign_user_ids = is_array($value[0]) 
                                                ? $value 
                                                : explode(',', $value[0]); 
                            if (collect($assign_user_ids)->filter()->isNotEmpty()) {
                                $query->whereIn('assign_user', $assign_user_ids);
                            }
                        }
                        break;

                    case 'service_center_code':
                        $query->whereHas('service_center_details', function ($q) use ($value) {
                            $q->where('customer_code', 'like', "%$value%");
                        });
                        break;

                    case 'customer_name':
                    case 'customer_email':
                    case 'customer_number':
                    case 'customer_address':
                    case 'customer_place':
                    case 'customer_country':
                    case 'customer_state':
                    case 'customer_city':
                        $query->whereHas('customer', function ($q) use ($key, $value) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;

                    case 'pincode':
                        $query->whereHas('customer.pincodeDetails', function ($q) use ($value) {
                            $q->where('pincode', 'like', "%$value%");
                        });
                        break;

                    case 'category_name':
                    case 'category_name_1':
                        $query->whereHas('product_details.categories', function ($q) use ($value) {
                            $q->where('category_name', 'like', "%$value%");
                        });
                        break;

                    case 'product_name':
                    case 'product_code':
                    case 'product_serail_number':
                    case 'specification':
                    case 'product_no':
                    case 'phase':
                        $query->whereHas('product_details', function ($q) use ($key, $value) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                }
            }
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Complaint Number',
            'Service Center Name',
            'Service Center Code',
            'Seller Name',
            'Customer Name',
            'Customer Email',
            'Customer Number',
            'Address',
            'Place',
            'Pincode',
            'Country',
            'State',
            'City',
            'Customer Complaint Type', // Keep the reference point
            
            // Newly added fields
            // 'Division',
            'Product Name',
            'Product Code',
            'Product Category',
            'Product Serial No',
            'HP',
            'Stage',
            'Phase',
            'Warranty Customer Bill Date',
            'Service Paid/Free',
            'Work Done Time',
            'Action Done By ASC',
            'Service Center Remark',
            'Last Status Update Time',
            'Pending TAT',
            'Open TAT',
            'Cancelled TAT',
            'Work Done TAT',
            'Completed TAT',
            'Close TAT',
            'service_bill_status',
            'service_bill_approved_date',
            'description',
            'service_branch',
            'purchased_party_name',
            'warranty_bill',
            'customer_bill_no',
            'customer_bill_date',
            'under_warranty',
            'service_type',
            'company_sale_bill_no',
            'company_sale_bill_date',
            'service_centre_remarks',
            'complaint_registered_by',
            'division_name',
            'work_completed_duration',
            'open_duration',
            'closed_date',
            // 'complaint_feedback_type', // New Column
            // 'feedback', // New Column
            'created_by',
            'created_at',
            'complaint_status',

            // // Remaining existing fields
            // 'Customer Number',
            // 'End User',
            // 'Complaint Date',
            // 'Complaint Type',
            // 'Purchased Party Name',
            // 'Service Center',
            // 'User Assign',
            // 'Product Category',
            // 'Product Serial Number',
            // 'Product Code',
            // 'Complaint Status',
        ];
    }


    public function map($data): array
    {
        $pending_tat = calcalutedTatByStatus(1,$data,$data['created_at']);;
        $open_tat = calcalutedTatByStatus(0,$data,$data['created_at']);
        $cancelled_tat = calcalutedTatByStatus(5,$data,$data['created_at']);
        $work_done_tat = calcalutedTatByStatus(2,$data,$data['created_at']);
        $completed_tat = calcalutedTatByStatus(3,$data,$data['created_at']);
        $close_tat = calcalutedTatByStatus(4,$data,$data['created_at']);

        $work_complated_duration = calcalutedTatByStatus(3,$data,$data['created_at']);
        $open_duration = calcalutedTatByStatus(0,$data,$data['created_at']);

        $closed_date = 'Not Closed Yet';
        $complaint_status = $data->complaint_time_line->where('status' , 4)->sortByDesc('id')->first();
        if(isset($complaint_status->created_at)){
            $closed_date = getDateInIndFomate($complaint_status->created_at) ?? '';
        }

        $service_bill_status = "No Action";
        if(isset($data['service_bill'])){
            if($data['service_bill']['status'] == '0'){
                $service_bill_status = 'Draft';
            }elseif($data['service_bill']['status']== '1'){
                $service_bill_status = 'Claimed';
            }elseif($data['service_bill']['status'] == '2'){
                $service_bill_status = 'Customer Payable';
            }elseif($data['service_bill']['status'] == '3'){
                $service_bill_status = 'Approved';
            }elseif($data['service_bill']['status'] == '4'){
                $service_bill_status = 'Cancelled';
            }
        }

        $status = '';
        if($data->complaint_status == '0'){
            $status = 'Open';
        }elseif($data->complaint_status == '1'){
            $status = 'Pending';
        }elseif($data->complaint_status == '2'){
            $status = 'Work Done';
        }elseif($data->complaint_status == '3'){
            $status = 'Completed';
        }elseif($data->complaint_status == '4'){
            $status = 'Closed';
        }elseif($data->complaint_status == '5'){
            $status = 'Cancel';
        }


        return [
            getDateInIndFomate($data['complaint_date']) ?? '',
            $data['complaint_number'] ?? '',
            $data['service_center_details']['name'] ?? '',
            $data['service_center_details']['customer_code'] ?? '',
            $data['seller'] ?? '',
            $data['customer']['customer_name'] ??  '',
            $data['customer']['customer_email']?$data['customer']['customer_email'] : '',
            $data['customer']['customer_number']?$data['customer']['customer_number'] : '',
            $data['customer']['customer_address']?$data['customer']['customer_address'] : '',
            $data['customer']['customer_place']?$data['customer']['customer_place'] : '',
            getPincode($data['customer']['customer_pindcode'])?? '',
            $data['customer']['customer_country']?$data['customer']['customer_country'] : '',
            $data['customer']['customer_state']?$data['customer']['customer_state'] : '',
            $data['customer']['customer_city']?$data['customer']['customer_city'] : '',
            $data['complaint_type_details']['name'] ??  '',
            // $data['product_details']['categories']['category_name'] ??  '',
            $data['product_details']['product_name'] ??  '',
            $data['product_details']['product_code'] ??  '',
            $data['product_details']['categories']['category_name'] ??  '',
            $data['product_serail_number'] ??  '',
            $data['product_details']['specification'] ??  '',
            $data['product_details']['product_no'] ??  '',
            $data['product_details']['phase'] ??  '',
            isset($data['warranty_details']['warranty_date']) ? getDateInIndFomate($data['warranty_details']['warranty_date']) : '',
            $data['service_type'] ??  '',
            isset($data['complaint_time_line']->where('status',2)->sortByDesc('id')->first()->created_at) ?Carbon::parse($data['complaint_time_line']->where('status',2)->sortByDesc('id')->first()->created_at)->format('d-m-Y h:i a') : '',
            isset($data['complaint_work_dones']->sortByDesc('id')->first()->done_by) ? $data->complaint_work_dones->sortByDesc('id')->first()->done_by : '',
            isset($data['complaint_work_dones']->sortByDesc('id')->first()->remark) ? $data['complaint_work_dones']->sortByDesc('id')->first()->remark : '',
            $data['updated_at'] ?? '',
            $pending_tat ?? "00:00:00",
            $open_tat ?? "00:00:00",
            $cancelled_tat ?? "00:00:00",
            $work_done_tat ?? "00:00:00",
            $completed_tat ?? "00:00:00",
            $close_tat ?? "00:00:00",
            $service_bill_status ?? '',
            (isset($data['service_bill']) && $data['service_bill']['status'] == '3') ?  $data['service_bill']['updated_at'] : 'Not Done Yet',
            $data['description']??  '',
            isset($data->purchased_branch_details) ? ($data->purchased_branch_details->branch_code ?? '-') . ' ' . ($data->purchased_branch_details->branch_name ?? '-') : '',
            isset($data['customer']['customer_name']) ?  $data['customer']['customer_name'] : '',
            $data['warranty_bill']??  '',
            $data['customer_bill_no']??  '',
            isset($data['customer_bill_date']) ? getDateInIndFomate($data['customer_bill_date']) : '',
            $data['under_warranty']??  '',
            $data['service_type']??  '',
            $data['company_sale_bill_no'] ?? '',
            isset($data['company_sale_bill_date']) ? getDateInIndFomate($data['company_sale_bill_date']): '',
            isset($data['complaint_work_dones']->sortByDesc('id')->first()->remark) ? $data['complaint_work_dones']->sortByDesc('id')->first()->remark : '',
            $data['register_by']??  '',
            isset($data['product_details']['categories']['category_name']) ? $data['product_details']['categories']['category_name'] : '',
            $work_complated_duration ?? '',
            $open_duration ?? '',
            $closed_date ?? '',
            $data['createdbyname']['name'] ?? '',
            $data->created_at ? Carbon::parse($data->created_at)->format('d-m-Y h:i:s') : '',
            $status ??'',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestDataRow();
                $lastColumn = $sheet->getHighestDataColumn();

                $firstRowRange = 'A1:' . $lastColumn . '1';
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getStyle($firstRowRange)->getAlignment()->setWrapText(true);
                $sheet->getStyle($firstRowRange)->getFont()->setSize(14);

                $event->sheet->getStyle($firstRowRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '00aadb'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->getStyle('A1:' . $lastColumn . '' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
            },
        ];
    }
}
