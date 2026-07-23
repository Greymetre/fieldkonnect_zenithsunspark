<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\DealerAppointment;
use App\Models\User; // Make sure to import the User model
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class DealerAppointmentExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->designation_id = $request->input('designation_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
        $this->created_by = $request->input('created_by');
        $this->status_id = $request->input('status_id');
        $allColumns =  \Schema::getColumnListing((new DealerAppointment())->getTable());
        $excludeColumns = ['id', 'created_at', 'updated_at', 'created_by'];
        $this->columns = array_diff($allColumns, $excludeColumns);
    }

    public function collection()
    {
        $query = DealerAppointment::with('branch_details', 'district_details', 'city_details', 'createdbyname', 'appointment_kyc_detail','sales_approve_user','ho_approve_user');

        if ($this->designation_id) {
            $query->where('designation_id', $this->designation_id);
        }
        if ($this->division_id) {
            $query->where('division', $this->division_id);
        }
        if ($this->branch_id) {
            $query->where('branch', $this->branch_id);
        }
        if ($this->created_by) {
            $query->where('created_by', $this->created_by);
        }
        if ($this->status_id != '' && $this->status_id != NULL) {
            $query->where('approval_status', $this->status_id);
        }
        if ($this->startdate && $this->enddate) {
            $startDate = date('Y-m-d', strtotime($this->startdate));
            $endDate = date('Y-m-d', strtotime($this->enddate));
            $query = $query->whereDate('appointment_date', '>=', $startDate)
                ->whereDate('appointment_date', '<=', $endDate);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return ['Submission Date', 'Appointment Date', 'Approval Status','Approve By Sales Name','Approve By HO Name', 'Created By Name', 'Created By Emp Code', 'Division', 'Branch', 'Customertype', 'Firm Type', 'ORGANISATION Status', 'Name of the Company/Firm', 'Gst Type', 'Gst No', 'Cin No', 'Related Firm Name', 'Line Business', 'State', 'District', 'City', 'Place', 'Office Address', 'Office Pincode', 'Office Mobile', 'Office Email', 'Godown Address', 'Godown Pincode', 'Godown Mobile', 'Godown Email', 'Are you already working on another division of Silver/Bediya?', 'Old Division', 'Old Firm Name', 'Old Gst', 'Contact Person Name', 'Mobile Email', 'Bank Name', 'Bank Address', 'Account Type', 'Account Number', 'Ifsc Code', 'Ppd Name 1', 'Ppd Adhar 1', 'Ppd Pan 1', 'Ppd Name 2', 'Ppd Adhar 2', 'Ppd Pan 2', 'Ppd Name 3', 'Ppd Adhar 3', 'Ppd Pan 3', 'Ppd Name 4', 'Ppd Adhar 4', 'Ppd Pan 4', 'Security Deposit', 'SDPUMPMOTORS', 'SDF&A', 'SDAGRI', 'Payment Term', 'Credit Period', 'Cheque No 1', 'Cheque Account Number 1', 'Cheque Bank 1', 'Cheque No 2', 'Cheque Account Number 2', 'Cheque Bank 2', 'Manufacture Company 1', 'Manufacture Product 1', 'Manufacture Business 1', 'Manufacture Turn Over 1', 'Manufacture Company 2', 'Manufacture Product 2', 'Manufacture Business 2', 'Manufacture Turn Over 2', 'Present Annual Turnover', 'Motor Anticipated Business 1', 'Motor Next Year Business 1', 'Pump Anticipated Business 1', 'Pump Next Year Business 1', 'F&A Anticipated Business 1', 'F&A Next Year Business 1', 'Lighting Anticipated Business 1', 'Lighting Next Year Business 1', 'Agri Anticipated Business 1', 'Agri Next Year Business 1', 'Solar Anticipated Business 1', 'Solar Next Year Business 1', 'Anticipated Business Total','Dealer Code'];
    }

    public function map($row): array
    {
        if($row->approval_status == '0'){
            $aproval_status = 'Pending';
        }elseif($row->approval_status == '1'){
            $aproval_status = 'Approved By Sales Team';
        }elseif($row->approval_status == '2'){
            $aproval_status = 'Approved By Account';
        }elseif($row->approval_status == '3'){
            $aproval_status = 'Approved By HO';
        }elseif($row->approval_status == '4'){
            $aproval_status = 'Rejected';
        }
        return [
            date('d M Y', strtotime($row->created_at)),
            date('d M Y', strtotime($row->appointment_date)),
            $aproval_status,
            $row->sales_approve_user ? $row->sales_approve_user->name : '-',
            $row->ho_approve_user ? $row->ho_approve_user->name : '-',
            $row->createdbyname ? $row->createdbyname->name : '-',
            $row->createdbyname ? $row->createdbyname->employee_codes : '-',
            $row->division ? $row->division : '-',
            $row->branch_details ? $row->branch_details->branch_name : '-',
            $row->customertype ? $row->customertype : '-',
            $row->firm_type ? $row->firm_type : '-',
            $row->status ? $row->status : '-',
            $row->firm_name ? $row->firm_name : '-',
            $row->gst_type ? $row->gst_type : '-',
            $row->gst_no ? $row->gst_no : '-',
            $row->cin_no ? $row->cin_no : '-',
            $row->related_firm_name ? $row->related_firm_name : '-',
            $row->line_business ? $row->line_business : '-',
            $row->district_details ? $row->district_details->statename->state_name : '-',
            $row->district_details ? $row->district_details->district_name : '-',
            $row->city_details ? $row->city_details->city_name : '-',
            $row->place ? $row->place : '-',
            $row->office_address ? $row->office_address : '-',
            $row->office_pincode ? $row->office_pincode : '-',
            $row->office_mobile ? $row->office_mobile : '-',
            $row->office_email ? $row->office_email : '-',
            $row->godown_address ? $row->godown_address : '-',
            $row->godown_pincode ? $row->godown_pincode : '-',
            $row->godown_mobile ? $row->godown_mobile : '-',
            $row->godown_email ? $row->godown_email : '-',
            $row->old_user ? $row->old_user : '-',
            $row->old_division ? $row->old_division : '-',
            $row->old_firm_name ? $row->old_firm_name : '-',
            $row->old_gst ? $row->old_gst : '-',
            $row->contact_person_name ? $row->contact_person_name : '-',
            $row->mobile_email ? $row->mobile_email : '-',
            $row->bank_name ? $row->bank_name : '-',
            $row->bank_address ? $row->bank_address : '-',
            $row->account_type ? $row->account_type : '-',
            $row->account_number ? (string)$row->account_number : '-',
            $row->ifsc_code ? $row->ifsc_code : '-',
            $row->ppd_name_1 ? $row->ppd_name_1 : '-',
            $row->ppd_adhar_1 ? $row->ppd_adhar_1 : '-',
            $row->ppd_pan_1 ? $row->ppd_pan_1 : '-',
            $row->ppd_name_2 ? $row->ppd_name_2 : '-',
            $row->ppd_adhar_2 ? $row->ppd_adhar_2 : '-',
            $row->ppd_pan_2 ? $row->ppd_pan_2 : '-',
            $row->ppd_name_3 ? $row->ppd_name_3 : '-',
            $row->ppd_adhar_3 ? $row->ppd_adhar_3 : '-',
            $row->ppd_pan_3 ? $row->ppd_pan_3 : '-',
            $row->ppd_name_4 ? $row->ppd_name_4 : '-',
            $row->ppd_adhar_4 ? $row->ppd_adhar_4 : '-',
            $row->ppd_pan_4 ? $row->ppd_pan_4 : '-',
            $row->security_deposit ? $row->security_deposit : '-',
            '10000',
            '5000',
            '100000',
            $row->payment_term ? $row->payment_term : '-',
            $row->credit_period ? $row->credit_period : '-',
            $row->cheque_no_1 ? $row->cheque_no_1 : '-',
            $row->cheque_account_number_1 ? $row->cheque_account_number_1 : '-',
            $row->cheque_bank_1 ? $row->cheque_bank_1 : '-',
            $row->cheque_no_2 ? $row->cheque_no_2 : '-',
            $row->cheque_account_number_2 ? $row->cheque_account_number_2 : '-',
            $row->cheque_bank_2 ? $row->cheque_bank_2 : '-',
            $row->manufacture_company_1 ? $row->manufacture_company_1 : '-',
            $row->manufacture_product_1 ? $row->manufacture_product_1 : '-',
            $row->manufacture_business_1 ? $row->manufacture_business_1 : '-',
            $row->manufacture_turn_over_1 ? $row->manufacture_turn_over_1 : '-',
            $row->manufacture_company_2 ? $row->manufacture_company_2 : '-',
            $row->manufacture_product_2 ? $row->manufacture_product_2 : '-',
            $row->manufacture_business_2 ? $row->manufacture_business_2 : '-',
            $row->manufacture_turn_over_2 ? $row->manufacture_turn_over_2 : '-',
            $row->present_annual_turnover ? $row->present_annual_turnover : '-',
            $row->motor_anticipated_business_1 ? $row->motor_anticipated_business_1 : '-',
            $row->motor_next_year_business_1 ? $row->motor_next_year_business_1 : '-',
            $row->pump_anticipated_business_1 ? $row->pump_anticipated_business_1 : '-',
            $row->pump_next_year_business_1 ? $row->pump_next_year_business_1 : '-',
            $row['F&Aanticipated_business_1'] ? $row['F&A_anticipated_business_1'] : '-',
            $row['F&A_next_year_business_1'] ? $row['F&A_next_year_business_1'] : '-',
            $row->lighting_anticipated_business_1 ? $row->lighting_anticipated_business_1 : '-',
            $row->lighting_next_year_business_1 ? $row->lighting_next_year_business_1 : '-',
            $row->agri_anticipated_business_1 ? $row->agri_anticipated_business_1 : '-',
            $row->agri_next_year_business_1 ? $row->agri_next_year_business_1 : '-',
            $row->solar_anticipated_business_1 ? $row->solar_anticipated_business_1 : '-',
            $row->solar_next_year_business_1 ? $row->solar_next_year_business_1 : '-',
            $row->anticipated_business_total ? $row->anticipated_business_total : '-',
            $row->appointment_kyc_detail ? ($row->appointment_kyc_detail->dealer_code??'-') : '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 1;
                $lastColumn = $event->sheet->getHighestDataColumn();

                $event->sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
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
                        'startColor' => ['rgb' => '336677'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->getStyle('A2:' . $lastColumn . '' . ($lastRow - 1))->applyFromArray([
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
