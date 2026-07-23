<?php

namespace App\Exports;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\BranchWiseTarget;
use App\Models\City;
use App\Models\CustomerOutstanting;
use App\Models\Customers;
use App\Models\District;
use App\Models\EmployeeDetail;
use App\Models\MobileUserLoginDetails;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\PrimarySales;
use App\Models\Redemption;
use App\Models\SalesTargetUsers;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\UserPmsRemark;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;
use Excel;
use DB;


class CHRatingDetailReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->start_date = '';
        $this->end_date = '';
        $this->designation_id = $request->input('designation_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
        $this->role_id = $request->input('role_id');
        $this->srno = 0;
    }

    public function collection()
    {
        $user_ids = getUsersReportingToAuth();
        $query = User::with('getbranch', 'getdivision', 'getdesignation', 'all_attendance_details', 'visits', 'customers', 'userinfo', 'target', 'primarySales');
        if ($this->user_id && $this->user_id != '' && $this->user_id != NULL) {
            $query->where('id', $this->user_id);
        } else {
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
        if ($this->role_id && $this->role_id != '' && $this->role_id != NULL && count($this->role_id) > 0) {
            $query->whereHas('roles', function ($query) {
                $query->whereIn('id', $this->role_id);
            });
        }


        $query = $query->where('sales_type', 'Primary')->whereIn('designation_id', ['5', '6', '7'])->latest()->get();

        if ($this->month && $this->financial_year) {
            $f_year_array = explode('-', $this->financial_year);

            // Month mapping to numbers
            $monthMap = [
                "Apr" => 4,
                "May" => 5,
                "Jun" => 6,
                "Jul" => 7,
                "Aug" => 8,
                "Sep" => 9,
                "Oct" => 10,
                "Nov" => 11,
                "Dec" => 12,
                "Jan" => 1,
                "Feb" => 2,
                "Mar" => 3
            ];

            // Convert selected months to numbers
            $monthNumbers = array_map(fn($month) => $monthMap[$month], $this->month);

            // Separate months into financial year groups
            $currentYearMonths = array_filter($monthNumbers, fn($m) => $m >= 4 && $m <= 12);
            $nextYearMonths = array_filter($monthNumbers, fn($m) => $m >= 1 && $m <= 3);

            // Get the correct first and last months
            $firstMonthNumber = !empty($currentYearMonths) ? min($currentYearMonths) : min($nextYearMonths);
            $lastMonthNumber = !empty($nextYearMonths) ? max($nextYearMonths) : max($currentYearMonths);

            // Assign years based on financial year rules
            $startYear = in_array($firstMonthNumber, range(4, 12)) ? $f_year_array[0] : $f_year_array[1];
            $endYear = in_array($lastMonthNumber, range(1, 3)) ? $f_year_array[1] : $f_year_array[0];

            // Create Carbon instances
            $firstDate = Carbon::createFromDate($startYear, $firstMonthNumber, 1)->startOfMonth();
            $lastDate = Carbon::createFromDate($endYear, $lastMonthNumber, 1)->endOfMonth();

            // Set start and end date
            $this->start_date = $firstDate->toDateString();
            $this->end_date = $lastDate->toDateString();
        } elseif ($this->financial_year && $this->financial_year != '' && $this->financial_year != null) {
            $f_year_array = explode('-', $this->financial_year);

            $this->start_date = $f_year_array[0] . '-04-01';
            $this->end_date = $f_year_array[1] . '-03-31';
            if ($this->end_date > now()->toDateString()) {
                $this->end_date = now()->toDateString();
            }
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            ['Division', 'Department', 'Branch', 'Emp Code', 'User Name', 'Designation', 'Reporting Manager', 'Email', 'Mobile Number', 'Head Quarter', 'Date Of Birth', 'Education', 'Age', 'Company TENURE(in month)', 'Previous Exp(in year)', 'Total Exp(in year)', 'AOP', '', '', '', '', 'GOLY', '', '', '', '', 'New Channel Sale-40% of Total sale', '', '', '', '', 'New Product sale-60%', '', '', '', '', 'Debtors', '', '', '', '', '', 'Inventory', '', '', '', '', '', 'Bonus Points', '', 'Final rating', 'Gross Salary Monthly', 'Last Yr Gross Increments Value', 'Last Yr Increments %','Last Promostion','Recommended CY Increment%', 'Remark By Reporting manager', 'Current Year Increment'],
            ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Tar', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'LY-Tar', 'ACH', 'GOLY', 'For Rating %', 'Final Rating', 'Tar', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Tar', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'TOTAL SALES CURRENT FINANCIAL YEAR', 'AVR PER DAYS SALES', 'TOTAL DEBTORS', 'DAYS', 'For Rating %', 'Final Rating', 'TOTAL SALES CURRENT FINANCIAL YEAR', 'AVR PER DAYS SALES', 'TOTAL INVENTORY', 'DAYS', 'For Rating %', 'Final Rating', 'ach >110%', 'Goly >125%', '']
        ];
    }

    public function map($query): array
    {

        $f_year_array = explode('-', $this->financial_year);
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        $monthCount = $startDate->diffInMonths($endDate) + 1;
        $selectedmonths = [];
        while ($startDate->lessThanOrEqualTo($endDate)) {
            $selectedmonths[] = $startDate->format('M');
            $startDate->addMonth();
        }

        $lastyrstartdate = Carbon::createFromFormat('Y-m-d', $this->start_date)->subYear()->format('Y-m-d');
        $lastyrenddate = Carbon::createFromFormat('Y-m-d', $this->end_date)->subYear()->format('Y-m-d');

        $user_ids = getUsersReportingToAuth($query->id);
        $emp_codes = User::whereIn('id', $user_ids)->pluck('employee_codes');
        $branch_ids = explode(',', $query->branch_id);

        $targets = SalesTargetUsers::with('user')->whereIn('branch_id', $branch_ids)->whereIn('month', $selectedmonths)->where('type', 'primary')->whereHas('user', function ($query) {
            $query->whereIn('division_id', ['10', '18']);
        })->sum('target');
        $achiv = PrimarySales::whereIn('branch_id', $branch_ids)->where('invoice_date', '>=', $this->start_date)->where('invoice_date', '<=', $this->end_date)->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount') / 100000;
        $ly_targets = PrimarySales::whereIn('branch_id', $branch_ids)->where('invoice_date', '>=', $lastyrstartdate)->where('invoice_date', '<=', $lastyrenddate)->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount') / 100000;

        $new_achiv_dealer = PrimarySales::whereIn('branch_id', $branch_ids)->where('invoice_date', '>=', $this->start_date)->where('invoice_date', '<=', $this->end_date)->where('new_dealer', 'Y')->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount') / 100000;
        $new_achiv_product = PrimarySales::whereIn('branch_id', $branch_ids)->where('invoice_date', '>=', $this->start_date)->where('invoice_date', '<=', $this->end_date)->where('new_product', 'Y')->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount') / 100000;

        $branch_names = Branch::whereIn('id', $branch_ids)->pluck('branch_name')->toArray();

        $debtors_start_date = $f_year_array[0] . '-04-01';
        // $debtors_end_date = $f_year_array[0] . '-12-31';
        $debtors_end_date = now()->toDateString();

        $debtors_start_date_or = Carbon::createFromFormat('Y-m-d', $f_year_array[0] . '-04-01');
        $debtors_end_date_or = now();

        $days_difference = $debtors_start_date_or->diffInDays($debtors_end_date_or);
        // $days_difference = 270;

        $debtors_sales = PrimarySales::whereIn('branch_id', $branch_ids)->where('invoice_date', '>=', $debtors_start_date)->where('invoice_date', '<=', $debtors_end_date)->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount');
        $total_debtors = CustomerOutstanting::whereIn('branch_id', $branch_ids)->whereIn('division_id', ['10', '18'])->where('year', $f_year_array[0])->sum('amount');
        $total_inventory = BranchStock::whereIn('branch_id', $branch_ids)->whereIn('division_id', ['10', '18'])->where('year', $f_year_array[0])->sum('amount');

        $degree_name = array();
        if (!empty($query['geteducation'])) {
            foreach ($query['geteducation'] as $key_new => $datas) {
                $degree_name[] = isset($datas->degree_name) ? $datas->degree_name : '';
            }
        }

        $pmsData = UserPmsRemark::where(['user_id' => $query['id'], 'fyear' => $this->financial_year])->first();

        return [
            $query['getdivision'] ? $query['getdivision']['division_name'] : '-',
            $query['getdepartment'] ? $query['getdepartment']['name'] : '-',
            count($branch_names) > 0 ? implode(',', $branch_names) : '-',
            $query['employee_codes'],
            $query['name'] ?? '-',
            $query['getdesignation'] ? $query['getdesignation']['designation_name'] : '-',
            $query['reportinginfo'] ? $query['reportinginfo']['name'] : '-',
            $query['email'] ?? '-',
            $query['mobile'] ?? '-',
            $query['location'] ?? '-',
            $query['userinfo'] ? date('d M Y', strtotime($query['userinfo']['date_of_birth'])) : '',
            count($degree_name) > 0 ? implode(',', $degree_name) : '-',
            Carbon::parse($query['userinfo']['date_of_birth'] ?? 0)->age,
            $query['userinfo'] ? $query['userinfo']['current_company_tenture'] : '0',
            $query['userinfo'] ? $query['userinfo']['previous_exp'] : '0',
            $query['userinfo'] ? $query['userinfo']['total_exp'] : '0',

            $targets > 0 ? $targets : '0',
            round($achiv, 0),
            $targets > 0 ? round(($achiv / $targets) * 100, 0) . '%' : '0%',
            $targets > 0 ? (round(($achiv / $targets) * 100, 0) >= 100 ? '100%' : round(($achiv / $targets) * 100, 0) . '%') : '0%',
            $aop = $targets > 0 ? (round(($achiv / $targets) * 100, 0) >= 100 ? '25' : round((25 * ($achiv / $targets) * 100 / 100), 0)) : '0',

            $ly_targets > 0 ? round($ly_targets, 0) : '0',
            round($achiv, 0),
            $ly_targets > 0 ? round((($achiv - $ly_targets) / $ly_targets) * 100, 0) . '%' : '0%',
            $ly_targets > 0 ? (round((($achiv - $ly_targets) / $ly_targets) * 100, 0) >= 100 ? '100%' : round((($achiv - $ly_targets) / $ly_targets) * 100, 0) . '%') : '0%',
            $goly = $ly_targets > 0 ? (round((($achiv - $ly_targets) / $ly_targets) * 100, 0) >= 100 ? '25' : round((25 * (($achiv - $ly_targets) / $ly_targets) * 100 / 100), 0)) : '0',

            round((($achiv * 40) / 100), 0),
            round($new_achiv_dealer, 0),
            ($achiv * 40) / 100 > 0 ? round(($new_achiv_dealer / (($achiv * 40) / 100)) * 100, 0) . '%' : '0%',
            ($achiv * 40) / 100 > 0 ? (round(($new_achiv_dealer / (($achiv * 40) / 100)) * 100, 0) >= 100 ? '100%' : round(($new_achiv_dealer / (($achiv * 40) / 100)) * 100, 0) . '%') : '0%',
            $new_chanel = ($achiv * 40) / 100 > 0 ? (round(($new_achiv_dealer / (($achiv * 40) / 100)) * 100, 0) >= 100 ? '15' : round((15 * ($new_achiv_dealer / (($achiv * 40) / 100)) * 100 / 100), 0)) : '0',

            round((($achiv * 60) / 100), 0),
            round($new_achiv_product, 0),
            ($achiv * 60) / 100 > 0 ? round(($new_achiv_product / (($achiv * 60) / 100)) * 100, 0) . '%' : '0%',
            ($achiv * 60) / 100 > 0 ? (round(($new_achiv_product / (($achiv * 60) / 100)) * 100, 0) >= 100 ? '100%' : round(($new_achiv_product / (($achiv * 60) / 100)) * 100, 0) . '%') : '0%',
            $new_product = ($achiv * 60) / 100 > 0 ? (round(($new_achiv_product / (($achiv * 60) / 100)) * 100, 0) >= 100 ? '5' : round((5 * ($new_achiv_product / (($achiv * 60) / 100)) * 100 / 100), 0)) : '0',

            $debtors_sales > 0 ? round(($debtors_sales / 100000), 2) : '0',
            $debtors_sales > 0 ? round((($debtors_sales / 100000) / $days_difference), 2) : '0',
            $total_debtors > 0 ? round($total_debtors, 1) : '0',
            $days = ($debtors_sales / 100000) / 270 > 0 && $total_debtors > 0 ? round(($total_debtors / (($debtors_sales / 100000) / $days_difference)), 0) : '100',
            $percentage = $days <= 30 ? '100%' : ($days <= 60 ? '80%' : ($days <= 90 ? '50%' : '0%')),
            $debtor = (20 * (int)$percentage) / 100,

            $debtors_sales > 0 ? round(($debtors_sales / 100000), 2) : '0',
            $debtors_sales > 0 ? round((($debtors_sales / 100000) / $days_difference), 2) : '0',
            $total_inventory > 0 ? round($total_inventory, 2) : '0',
            $inv_days = ($debtors_sales / 100000) / 270 > 0 && $total_inventory > 0 ? round(($total_inventory / (($debtors_sales / 100000) / $days_difference)), 0) : '100',
            $percentage = $inv_days <= 30 ? '100%' : ($inv_days <= 60 ? '80%' : ($inv_days <= 90 ? '50%' : '0%')),
            $inventory = (10 * (int)$percentage) / 100,
            '0',
            '0',
            (int)$aop + (int)$goly + (int)$new_chanel + (int)$new_product + (int)$debtor + (int)$inventory,

            $query['userinfo'] ? $query['userinfo']['gross_salary_monthly'] : '',
            $query['userinfo'] ? $query['userinfo']['last_year_increments'] : '',
            $query['userinfo'] ? $query['userinfo']['last_year_increment_percent'] : '',
            $query['userinfo'] ? $query['userinfo']['last_promotion'] : '',

            $pmsData ? $pmsData->recommended_increment.'%' : '',
            $pmsData ? $pmsData->remark : '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();
                $rowCount = $event->sheet->getHighestDataRow();
                $event->sheet->mergeCells('A1:A2');
                $event->sheet->mergeCells('B1:B2');
                $event->sheet->mergeCells('C1:C2');
                $event->sheet->mergeCells('D1:D2');
                $event->sheet->mergeCells('E1:E2');
                $event->sheet->mergeCells('F1:F2');
                $event->sheet->mergeCells('G1:G2');
                $event->sheet->mergeCells('H1:H2');
                $event->sheet->mergeCells('I1:I2');
                $event->sheet->mergeCells('J1:J2');
                $event->sheet->mergeCells('K1:K2');
                $event->sheet->mergeCells('L1:L2');
                $event->sheet->mergeCells('M1:M2');
                $event->sheet->mergeCells('N1:N2');
                $event->sheet->mergeCells('O1:O2');
                $event->sheet->mergeCells('P1:P2');
                $event->sheet->mergeCells('Q1:U1');
                $event->sheet->mergeCells('V1:Z1');
                $event->sheet->mergeCells('AA1:AE1');
                $event->sheet->mergeCells('AF1:AJ1');
                $event->sheet->mergeCells('AK1:AP1');
                $event->sheet->mergeCells('AQ1:AV1');
                $event->sheet->mergeCells('AW1:AX1');
                $event->sheet->mergeCells('AY1:AY2');
                $event->sheet->mergeCells('AZ1:AZ2');
                $event->sheet->mergeCells('BA1:BA2');
                $event->sheet->mergeCells('BA1:BA2');
                $event->sheet->mergeCells('BB1:BB2');
                $event->sheet->mergeCells('BC1:BC2');
                $event->sheet->mergeCells('BD1:BD2');
                $event->sheet->mergeCells('BE1:BE2');
                $event->sheet->mergeCells('BF1:BF2');


                // for ($row = 1; $row <= $rowCount; $row++) {
                //     $cellValue = $event->sheet->getCell('AC' . $row)->getValue();
                //     $color = self::getColorBasedOnValue($cellValue);

                //     $event->sheet->getStyle('AC' . $row)->applyFromArray([
                //         'fill' => [
                //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                //             'startColor' => ['rgb' => $color],
                //         ],
                //     ]);
                //     $event->sheet->getStyle('C' . $row)->applyFromArray([
                //         'fill' => [
                //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                //             'startColor' => ['rgb' => $color],
                //         ],
                //     ]);
                // }

                $event->sheet->getStyle('A1:BF2')->applyFromArray([
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

                $event->sheet->getStyle('A' . $lastRow . ':BF' . $lastRow)->applyFromArray([
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
                $event->sheet->getStyle('A2:' . $lastColumn . '' . ($lastRow - 2))->applyFromArray([
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

    private static function getColorBasedOnValue($value)
    {
        if ($value <= 24.99) {
            return 'FF0000'; // Red
        } elseif ($value >= 25 && $value <= 29.99) {
            return 'FFFF00'; // Yellow
        } elseif ($value >= 29.99) {
            return '00FF00'; // Green
        }
    }
}
