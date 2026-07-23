<?php

namespace App\Exports;

use App\Models\ClaimGenerationDetail;
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

class ClaimExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    protected $month;
    protected $year;
    protected $formatted_date;
    protected $service_center;
    protected $formatted_date_start;
    protected $formatted_date_end;
    protected $claim_date_start;
    protected $claim_date_end;

    public function __construct(Request $request)
    {

        if (!empty($request->input('start_month')) && !empty($request->input('end_month'))) { 
            $this->formatted_date_start = Carbon::createFromFormat('F Y', $request->input('start_month'))->startOfMonth();
            $this->formatted_date_end = Carbon::createFromFormat('F Y', $request->input('end_month'))->endOfMonth(); // Use endOfMonth for full range

            $this->claim_date_start = $this->formatted_date_start->format("Y-m-d");
            $this->claim_date_end = $this->formatted_date_end->format("Y-m-d");
        }

        if(!empty($request->input('service_center'))){
            $this->service_center = $request->input('service_center');
        }

    }

    public function collection()
    {
        $query = ClaimGenerationDetail::with(['claim.service_center_details' , 'complaints.service_bill.service_bill_products' , 'complaints.purchased_branch_details' , 'complaints.complaint_work_dones']);

        // Apply date range filter
        if (!empty($this->claim_date_start) && !empty($this->claim_date_end)) {
            $query->whereHas('claim', function ($subquery) {
                $subquery->whereBetween('claim_date', [$this->claim_date_start, $this->claim_date_end]);
            });
        }

        if (!empty($this->service_center)) {
            $query->whereHas('claim', function ($subquery) {
                $subquery->where([
                    'service_center_id' => $this->service_center,
                ]);
            });
        }
        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'SC Code',
            'SC Name',
            'Claim No',
            'Comp No',
            'Comp Date',
            'SB Approved Date',
            'Service',
            'Prod Sr',
            'Prod Code',
            'HP',
            'Stage',
            'Phase',
            'Cust Bill Date',
            'Company Sale Bill Date',
            'BRANCH',
            'Repaired / Replacement',
            'Service Location',
            'Site Visit Category',
            'SERVICE CHARGE',
            'Site Visit Charge',
            'Rewinding Charge',
            'Local Spare Charges',
            'Spare Parts Charge',
            'Appreciation Convince Charge',
            'Ttl Charg (W/o Tax)'
        ];
    }


    public function map($data): array
    {

        $serce_bill_approve_date = '';
        $service_charge = 0.0;
        $site_visit_charge = 0.0;
        $rewinding_charge = 0.0;
        $local_spare_charges = 0.0;
        $spare_parts_charge = 0.0;
        $appreciation_convince_charge = 0.0;
        if(isset($data['complaints']['service_bill'])){
            $serce_bill_approve_date = $data['complaints']['service_bill']['status'] == 3 ? cretaDateForFront($data['complaints']['service_bill']['updated_at']) : "Not Approved";
        }
        $service_charge = getServiceCharge($data, 1);
        $site_visit_charge = getServiceCharge($data, 3);
        $rewinding_charge = getServiceCharge($data, 5);
        $local_spare_charges = getServiceCharge($data, 4);
        $spare_parts_charge = getServiceCharge($data, 2);
        $appreciation_convince_charge = getServiceCharge($data, 6);        

        return [
             $data['claim']['service_center_details']['customer_code'] ?? '',
             $data['claim']['service_center_details']['name'] ?? '',
             $data['claim']['claim_number'] ?? '',
             $data['complaints']['complaint_number'] ?? '',
             isset($data['complaints']['complaint_date']) ?  cretaDateForFront($data['complaints']['complaint_date']) : '',
             $serce_bill_approve_date ?? '',
             $data['complaints']['service_type'] ?? '',
             $data['complaints']['product_serail_number'] ?? '',
             $data['complaints']['product_code'] ?? '',
             $data['complaints']['specification'] ?? '',
             $data['complaints']['product_no'] ?? '',
             $data['complaints']['phase'] ?? '',
             isset($data['complaints']['customer_bill_date']) ?  cretaDateForFront($data['complaints']['customer_bill_date']) : '',
             isset($data['complaints']['company_sale_bill_date']) ?  cretaDateForFront($data['complaints']['company_sale_bill_date']) : '',
             $data['complaints']['purchased_branch_details']['branch_name'] ?? '',
             $data['complaints']['complaint_work_dones'][0]['done_by'] ?? '',
             $data['complaints']['service_bill']['service_location'] ?? '',
             $data['complaints']['service_bill']['category'] ?? '',
            number_format($service_charge ?? 0.0, 2, '.', ''),
            number_format($site_visit_charge ?? 0.0, 2, '.', ''),
            number_format($rewinding_charge ?? 0.0, 2, '.', ''),
            number_format($local_spare_charges ?? 0.0, 2, '.', ''),
            number_format($spare_parts_charge ?? 0.0, 2, '.', ''),
            number_format($appreciation_convince_charge ?? 0.0, 2, '.', ''),
            number_format(optional($data['complaints']['service_bill']['service_bill_products'])->sum('subtotal') ?? 0.0, 2, '.', ''),

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
