<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;


class UserSalesReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
        $this->designation_id = $request->input('designation_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
    }

    public function collection()
    {
        $user_ids = getUsersReportingToAuth();
        $query = User::with('reportinginfo', 'getbranch', 'getdivision', 'getdesignation', 'all_attendance_details', 'visits', 'customers');
        if ($this->user_id && $this->user_id != '' && $this->user_id != NULL) {
            $query->where('id', $this->user_id);
        }else{
            $query->whereIn('id', $user_ids);
        }
        if ($this->designation_id && $this->designation_id != '' && $this->designation_id != NULL) {
            $query->where('designation_id', $this->designation_id);
        }
        if ($this->division_id && $this->division_id != '' && $this->division_id != NULL) {
            $query->where('division_id', $this->division_id);
        }
        if ($this->branch_id && $this->branch_id != '' && $this->branch_id != NULL) {
            $query->where('branch_id', $this->branch_id);
        }
        $query = $query->latest()->get();

        return $query;
    }

    public function headings(): array
    {
        return ['Employees Code', 'User Name', 'Designation', 'Branch Name', 'Division', 'Reporting Manager', 'Field Working Days', 'Other Days', 'Total Days', 'Distributor Visit Total', 'Distributor Visit Unique', 'Dealer Visit Total', 'Dealer Visit Unique', 'Retailer Visit Total', 'Retailer Visit Unique', 'Service Center Visit Total', 'Service Center Visit Unique', 'Influencer Visit Total', 'Influencer Visit Unique', 'Total Visit', 'Total Visit Unique', 'Distributor New Registration', 'Dealer New Registration', 'Retailer New Registration', 'Service Center New Registration', 'Influencer-New Registration', 'Total New Registration'];
    }

    public function map($data): array
    {
        $start_date = Carbon::parse($this->start_date)->startOfDay();
        $end_date = Carbon::parse($this->end_date)->endOfDay();
        return [
            $data['employee_codes'] ?? '',
            $data['name'] ?? '',
            $data['getdesignation']['designation_name'] ?? '',
            $data['getbranch']['branch_name'] ?? '',
            $data['getdivision']['division_name'] ?? '',
            $data['reportinginfo']['name'] ?? '',

            (count($data->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Leave', 'Full Day Leave', 'Holiday'])->whereBetween('punchin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Leave', 'Holiday'])->whereBetween('punchin_date', [$this->start_date, $this->end_date])) : '0',

            (count($data->all_attendance_details->whereIn('working_type', ['Office Work', 'Leave', 'Full Day Leave', 'Holiday'])->whereBetween('punchin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->all_attendance_details->whereIn('working_type', ['Office Work', 'Leave', 'Holiday'])->whereBetween('punchin_date', [$this->start_date, $this->end_date])) : '0',

            (count($data->all_attendance_details->whereBetween('punchin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->all_attendance_details->whereBetween('punchin_date', [$this->start_date, $this->end_date])) : '0',

            (count($data->visits->where('customers.customertype', '1')->whereBetween('checkin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->visits->where('customers.customertype', '1')->whereBetween('checkin_date', [$this->start_date, $this->end_date])) : '0',

            (count($data->visits->where('customers.customertype', '1')->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) > 0) ? count($data->visits->where('customers.customertype', '1')->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) : '0',

            (count($data->visits->where('customers.customertype', '3')->whereBetween('checkin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->visits->where('customers.customertype', '3')->whereBetween('checkin_date', [$this->start_date, $this->end_date])) : '0',

            (count($data->visits->where('customers.customertype', '3')->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) > 0) ? count($data->visits->where('customers.customertype', '3')->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) : '0',

            (count($data->visits->where('customers.customertype', '2')->whereBetween('checkin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->visits->where('customers.customertype', '2')->whereBetween('checkin_date', [$this->start_date, $this->end_date])) : '0',

            (count($data->visits->where('customers.customertype', '2')->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) > 0) ? count($data->visits->where('customers.customertype', '2')->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) : '0',

            (count($data->visits->where('customers.customertype', '4')->whereBetween('checkin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->visits->where('customers.customertype', '4')->whereBetween('checkin_date', [$this->start_date, $this->end_date])) : '0',

            (count($data->visits->where('customers.customertype', '4')->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) > 0) ? count($data->visits->where('customers.customertype', '4')->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) : '0',

            (count($data->visits->where('customers.customertype', '5')) > 0) ? count($data->visits->where('customers.customertype', '5')) : '0',

            (count($data->visits->where('customers.customertype', '5')->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) > 0) ? count($data->visits->where('customers.customertype', '5')->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) : '0',

            (count($data->visits->whereBetween('checkin_date', [$this->start_date, $this->end_date])) > 0) ? count($data->visits->whereBetween('checkin_date', [$this->start_date, $this->end_date])) : '0',

            (count($data->visits->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) > 0) ? count($data->visits->whereBetween('checkin_date', [$this->start_date, $this->end_date])->groupBy('customers.id')) : '0',

            (count($data->customers->where('customertype', '1')->whereBetween('created_at', [$start_date, $end_date])) > 0) ? count($data->customers->where('customertype', '1')->whereBetween('created_at', [$start_date, $end_date])) : '0',

            (count($data->customers->where('customertype', '3')->whereBetween('created_at', [$start_date, $end_date])) > 0) ? count($data->customers->where('customertype', '3')->whereBetween('created_at', [$start_date, $end_date])) : '0',

            (count($data->customers->where('customertype', '2')->whereBetween('created_at', [$start_date, $end_date])) > 0) ? count($data->customers->where('customertype', '2')->whereBetween('created_at', [$start_date, $end_date])) : '0',

            (count($data->customers->where('customertype', '4')->whereBetween('created_at', [$start_date, $end_date])) > 0) ? count($data->customers->where('customertype', '4')->whereBetween('created_at', [$start_date, $end_date])) : '0',

            (count($data->customers->where('customertype', '5')->whereBetween('created_at', [$start_date, $end_date])) > 0) ? count($data->customers->where('customertype', '5')->whereBetween('created_at', [$start_date, $end_date])) : '0',

            (count($data->customers->whereBetween('created_at', [$start_date, $end_date])) > 0) ? count($data->customers->whereBetween('created_at', [$start_date, $end_date])) : '0'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $event->sheet->mergeCells('A' . $lastRow . ':F' . $lastRow);

                $event->sheet->getStyle('A1:AA1')->applyFromArray([
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

                $event->sheet->getStyle('A' . $lastRow . ':AA' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'], // Border color
                        ],
                    ],
                ]);
                $event->sheet->setCellValue('A' . $lastRow, 'Total');
                $event->sheet->setCellValue('G' . $lastRow, '=SUM(G3:G' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('H' . $lastRow, '=SUM(H3:H' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('I' . $lastRow, '=SUM(I3:I' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('J' . $lastRow, '=SUM(J3:J' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('K' . $lastRow, '=SUM(K3:K' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('L' . $lastRow, '=SUM(L3:L' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('M' . $lastRow, '=SUM(M3:M' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('N' . $lastRow, '=SUM(N3:N' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('O' . $lastRow, '=SUM(O3:O' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('P' . $lastRow, '=SUM(P3:P' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('Q' . $lastRow, '=SUM(Q3:Q' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('R' . $lastRow, '=SUM(R3:R' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('S' . $lastRow, '=SUM(S3:S' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('T' . $lastRow, '=SUM(T3:T' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('U' . $lastRow, '=SUM(U3:U' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('V' . $lastRow, '=SUM(V3:V' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('W' . $lastRow, '=SUM(W3:W' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('X' . $lastRow, '=SUM(X3:X' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('Y' . $lastRow, '=SUM(Y3:Y' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('Z' . $lastRow, '=SUM(Z3:Z' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('AA' . $lastRow, '=SUM(AA3:AA' . ($lastRow - 2) . ')');
            },
        ];
    }
}
