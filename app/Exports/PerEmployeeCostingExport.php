<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\PrimarySales;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use DB;

class PerEmployeeCostingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{

    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->branch_id = $request->input('branch_id');
        $this->division_id = $request->input('division');
        $this->dealer_id = $request->input('dealer_id');
        $this->product_model = $request->input('product_model');
        $this->new_group = $request->input('new_group');
        $this->executive_id = $request->input('executive_id');
        $this->financial_year = $request->input('financial_year');
        $this->month = $request->input('month');
        $this->months = [];
        $this->t_data = '';
        $this->startDateFormatted = '';
        $this->endDateFormatted = '';
    }
    public function collection()
    {
        $currentDate = Carbon::now();
        DB::statement("SET SESSION group_concat_max_len = 10000000");

        // Financial Year & Date Filter Logic
        $startDateFormatted = $endDateFormatted = null;
        if ($this->month && is_array($this->month) && count($this->month) > 0 && $this->financial_year) {
            $f_year_array = explode('-', $this->financial_year);
            $isJanToMar = in_array('Jan', $this->month) || in_array('Feb', $this->month) || in_array('Mar', $this->month);
            $currentYear = $isJanToMar ? $f_year_array[1] : $f_year_array[0];
            $startDate = Carbon::createFromFormat('Y-M', "$currentYear-{$this->month[0]}")->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-M', "$currentYear-{$this->month[count($this->month) - 1]}")->endOfMonth();
        } elseif ($this->financial_year) {
            $f_year_array = explode('-', $this->financial_year);
            $startDate = Carbon::createFromFormat('Y-m-d', "{$f_year_array[0]}-04-01");
            $endDate = Carbon::createFromFormat('Y-m-d', "{$f_year_array[1]}-03-31");
        } else {
            $startDate = Carbon::now()->subMonthsNoOverflow(3)->startOfMonth();
            $endDate = Carbon::now()->subMonthNoOverflow()->endOfMonth();
        }

        // Ensure end date does not exceed today
        $today = Carbon::now('Asia/Kolkata');
        if ($endDate->greaterThan($today)) {
            $endDate = $today->subMonth()->endOfMonth();
        }
        
        $startDateFormatted = $startDate->toDateString();
        $endDateFormatted = $endDate->toDateString();
        
        $this->startDateFormatted = $startDateFormatted;
        $this->endDateFormatted = $endDateFormatted;

        // Build Query
        $query = User::with(['primarySales', 'getdesignation', 'getbranch', 'getdivision', 'userinfo', 'expenses'])
            ->where('active', 'Y')
            ->whereHas('roles', function ($query) {
                $query->whereIn('id', ['13', '6', '3', '2']);
            });
            
            if ($this->division_id && count($this->division_id) > 0) {
                $query->whereIn('division_id', $this->division_id);
            }
        // Apply Filters
        $filters = [
            'branch_id' => $this->branch_id,
            'dealer' => $this->dealer_id ? ['like', "%{$this->dealer_id}%"] : null,
            'model_name' => $this->product_model,
            'new_group' => $this->new_group,
            'id' => $this->executive_id
        ];

        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                $query->where($field, $value);
            }
        }

        // Get the result
        $users = $query->get();

        // Prepare Calculations
        $all_months = getMonthsBetween($startDate, $endDate);

        foreach ($users as $user) {
            $user->userinfo->gross_salary_monthly *= count($all_months);

            $expensesSum = $user->expenses->whereBetween('date', [$startDateFormatted, $endDateFormatted])->sum('claim_amount');
            $user->total_expe = $expensesSum + $user->userinfo->gross_salary_monthly;

            if ($user->sales_type == 'Primary') {
                $salesSum = $user->primarySales->whereBetween('invoice_date', [$startDateFormatted, $endDateFormatted])->sum('net_amount');
            } else {
                $salesSum = Order::where('created_by', $user->id)
                    ->whereBetween('order_date', [$startDateFormatted, $endDateFormatted])
                    ->sum('sub_total');
            }

            $user->sales = $salesSum > 0 ? number_format($salesSum / 100000, 2) : 0;
            // Calculate Salary/Expense ratio
            $user->sal_exp = $user->sales > 0
                ? number_format(($user->total_expe / 100000) / $user->sales * 100, 2)
                : 0;
        }

        return $users->sortByDesc('sal_exp');
    }

    public function headings(): array
    {
        $label1 = [
            'Division',
            'Branch',
            'Emp Code',
            'Empolyee Name',
            'Designation',
            'DOJ',
            'Sales/Othe/FOS/FOS-C'
        ];

        if ($this->month && is_array($this->month) && count($this->month) > 0 && $this->financial_year && !empty($this->financial_year)) {
            $f_year_array = explode('-', $this->financial_year);

            // Determine if months are in Jan-Mar and set the correct year
            $isJanToMar = in_array('Jan', $this->month) || in_array('Feb', $this->month) || in_array('Mar', $this->month);
            $currentYear = $isJanToMar ? $f_year_array[1] : $f_year_array[0];

            // Get the first and last months from the array
            $firstMonth = $this->month[0];
            $lastMonth = $this->month[count($this->month) - 1];

            // Format the month and create start and end dates
            $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$firstMonth")->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$lastMonth")->endOfMonth();

            // Convert to date strings
            $this->startDateFormatted = $startDate->toDateString();
            $this->endDateFormatted = $endDate->toDateString();
        } elseif ($this->financial_year && $this->financial_year != '' && $this->financial_year != null) {
            $f_year_array = explode('-', $this->financial_year);
            $this->startDateFormatted = $f_year_array[0] . '-04-01';
            $this->endDateFormatted = $f_year_array[1] . '-03-31';
        } else {
            $currentDate = Carbon::now();
            $this->startDateFormatted = $currentDate->copy()->subMonthsNoOverflow(3)->firstOfMonth()->format('Y-m-d');
            $this->endDateFormatted = $currentDate->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d');
        }

        $startDate = Carbon::createFromFormat('Y-m-d', $this->startDateFormatted);
        $endDate = Carbon::createFromFormat('Y-m-d', $this->endDateFormatted);
        $today = Carbon::now('Asia/Kolkata');
        if ($endDate->greaterThan($today)) {
            $endDate = $today->subMonth()->endOfMonth();
            $this->endDateFormatted = $endDate->toDateString();
        }
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $monthName = $currentDate->format('F');
            if (!in_array($monthName, $this->months)) {
                $this->months[] = $monthName;
            }
            $currentDate->addMonth()->startOfMonth();
        }

        $label2 = [];

        $label2[] = 'Total';
        $label2[] = '';
        $label2[] = '';
        $label2[] = '';
        $label2[] = '';
        $label2[] = '';
        $label2[] = '';
        $headings2 = ['', '', '', '', '', '', '', 'Sales(L)', 'Salary', 'TA-DA', 'Incentive', 'TOTAL EXP', 'T-Expenes(L)', 'SALARY/EXP %'];
        foreach ($this->months as $key => $value) {
            $label2[] = $value;
            $label2[] = '';
            $label2[] = '';
            $label2[] = '';
            $label2[] = '';
            $label2[] = '';
            $label2[] = '';
            $headings2[] = 'Sales(L)';
            $headings2[] = 'Salary';
            $headings2[] = 'TA-DA';
            $headings2[] = 'Incentive';
            $headings2[] = 'TOTAL EXP';
            $headings2[] = 'T-Expenes(L)';
            $headings2[] = 'SALARY/EXP %';
        }

        $headings1 = array_merge($label1, $label2);
        $main_head = array($headings1, $headings2);

        return $main_head;
    }

    public function map($data): array
    {
        
        if (count($data->expenses) > 0) {
            $data->total_expe = $data->expenses->where('date', '>=', $this->startDateFormatted)->where('date', '<=', $this->endDateFormatted)->sum('claim_amount') > 0 ? number_format(($data->expenses->where('date', '>=', $this->startDateFormatted)->where('date', '<=', $this->endDateFormatted)->sum('claim_amount') + $data->userinfo->gross_salary_monthly), 2, '.', '') : "0";
        } else {
            $data->total_expe = $data->userinfo->gross_salary_monthly;
        }
        if ($data->sales_type == 'Primary') {
            if (count($data->primarySales) > 0) {
                $data->sales = $data->primarySales->where('invoice_date', '>=', $this->startDateFormatted)->where('invoice_date', '<=', $this->endDateFormatted)->sum('net_amount') > 0 ? number_format(($data->primarySales->where('invoice_date', '>=', $this->startDateFormatted)->where('invoice_date', '<=', $this->endDateFormatted)->sum('net_amount') / 100000), 2, '.', '') : "0";
            } else {
                $data->sales = "0";
            }
        } else {
            $data->sales = Order::where('created_by', $data->id)->where('order_date', '>=', $this->startDateFormatted)->where('order_date', '<=', $this->endDateFormatted)->sum('sub_total') > 0 ? number_format((Order::where('created_by', $data->id)->where('order_date', '>=', $this->startDateFormatted)->where('order_date', '<=', $this->endDateFormatted)->sum('sub_total') / 100000), 2, '.', '') : "0";
        }

        $response[0] = $data->getdivision ? $data->getdivision->division_name : '-';
        $response[1] = $data->getbranch ? $data->getbranch->branch_name : '-';
        $response[2] = $data->employee_codes ? $data->employee_codes : '-';
        $response[3] = $data->name ? $data->name : '-';
        $response[4] = $data->getdesignation ? $data->getdesignation->designation_name : '-';
        $response[5] = $data->userinfo ? date('d M Y', strtotime($data->userinfo->date_of_joining)) : '-';
        $response[6] = '-';
        $response[7] = $data->sales;
        $response[8] = $data->userinfo->gross_salary_monthly;
        $response[9] = $data->expenses->where('date', '>=', $this->startDateFormatted)->where('date', '<=', $this->endDateFormatted)->sum('claim_amount') > 0 ? number_format($data->expenses->where('date', '>=', $this->startDateFormatted)->where('date', '<=', $this->endDateFormatted)->sum('claim_amount'), 2, '.', '') : 0;
        $response[10] = '-';
        $response[11] = $data->total_expe ?? "0";
        $response[12] = $data->total_expe > 0 ? $data->total_expe / 100000 : "0";
        $response[13] = $data->sal_exp > 0 ? $data->sal_exp : "0";
        $check = 0;
        foreach ($this->months as $k => $val) {
            $f_year_array = explode('-', $this->financial_year);

            if ($val == 'January' || $val == 'February' || $val == 'March') {
                $currentYear = $f_year_array[1];
                $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$val")->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$val")->endOfMonth();
                $startDateMonthly = $startDate->toDateString();
                $endDateMonthly = $endDate->toDateString();
            } else {
                $currentYear = $f_year_array[0];
                $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$val")->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$val")->endOfMonth();
                $startDateMonthly = $startDate->toDateString();
                $endDateMonthly = $endDate->toDateString();
            }

            if ($data->sales_type == 'Primary') {
                if (count($data->primarySales) > 0) {
                    $monthly_sales = $data->primarySales->where('invoice_date', '>=', $startDateMonthly)->where('invoice_date', '<=', $endDateMonthly)->sum('net_amount') > 0 ? number_format(($data->primarySales->where('invoice_date', '>=', $startDateMonthly)->where('invoice_date', '<=', $endDateMonthly)->sum('net_amount') / 100000), 2, '.', '') : "0";
                } else {
                    $monthly_sales = "0";
                }
            } else {
                $monthly_sales = Order::where('created_by', $data->id)->where('order_date', '>=', $startDateMonthly)->where('order_date', '<=', $endDateMonthly)->sum('sub_total') > 0 ? number_format((Order::where('created_by', $data->id)->where('order_date', '>=', $startDateMonthly)->where('order_date', '<=', $endDateMonthly)->sum('sub_total') / 100000), 2, '.', '') : "0";
            }

            if (count($data->expenses) > 0) {
                $monthly_exp = $data->expenses->where('date', '>=', $startDateMonthly)->where('date', '<=', $endDateMonthly)->sum('claim_amount') > 0 ? number_format($data->expenses->where('date', '>=', $startDateMonthly)->where('date', '<=', $endDateMonthly)->sum('claim_amount'), 2, '.', '') : "0";
            } else {
                $monthly_exp = $data->userinfo->gross_salary_monthly / count($this->months);
            }

            $monthly_tottal_exp = $monthly_exp+($data->userinfo->gross_salary_monthly / count($this->months));

            $response[14 + $check] = $monthly_sales;
            $response[15 + $check] = $data->userinfo->gross_salary_monthly / count($this->months);
            
            
            $response[16 + $check] = $monthly_exp;
            $response[17 + $check] = '-';

            $response[18 + $check] = $monthly_tottal_exp;
            $response[19 + $check] = $monthly_tottal_exp/100000;
            $response[20 + $check] = $monthly_sales > 0 ? number_format(((($monthly_tottal_exp / 100000) / $monthly_sales) * 100), 2, '.', '') . "%" : "0%";;
            $check += 7;
        }

        return $response;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();
                $rowCount = $event->sheet->getHighestDataRow();
                for ($row = 1; $row <= $rowCount; $row++) {
                    $cellValue = $event->sheet->getCell('N'.$row)->getValue();
                    if($cellValue > 5){
                        $event->sheet->getStyle('N'.$row)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FF0000'],
                            ],
                        ]);
                    }
                }

                $event->sheet->mergeCells('A1:A2');
                $event->sheet->mergeCells('B1:B2');
                $event->sheet->mergeCells('C1:C2');
                $event->sheet->mergeCells('D1:D2');
                $event->sheet->mergeCells('E1:E2');
                $event->sheet->mergeCells('F1:F2');
                $event->sheet->mergeCells('G1:G2');

                $startColumn = 'H';
                $columnsPerMerge = 7;

                // Convert column letters to numbers
                $startColNum = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($startColumn);
                $lastColNum = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn);

                // Loop through and merge columns in groups of 7
                for ($colNum = $startColNum; $colNum <= $lastColNum; $colNum += $columnsPerMerge) {
                    $endColNum = min($colNum + $columnsPerMerge - 1, $lastColNum);
                    $startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colNum);
                    $endColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endColNum);
                    $event->sheet->mergeCells("{$startColLetter}1:{$endColLetter}1");
                }

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
                $event->sheet->getStyle('A2:' . $lastColumn . '2')->applyFromArray([
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

                $event->sheet->getStyle('A3:' . $lastColumn . '' . ($lastRow - 2))->applyFromArray([
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
