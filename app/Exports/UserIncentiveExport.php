<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\CustomerOutstanting;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesTargetUsers;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserIncentiveExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithStyles, WithEvents
{

    private $rowIndex = 3;

    public function __construct($request)
    {
        $this->months = array();
        $this->branch_id = $request->input('branch_id');
        $this->financial_year = $request->input('financial_year');
        $this->quarter = $request->input('quarter');
        $this->quarter_name = '';
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);
        $userIds = getUsersReportingToAuth();
        $data = SalesTargetUsers::with(['user', 'user.getdesignation', 'user.getdivision', 'branch'])->whereIn('user_id', $userIds)->select([
            DB::raw('GROUP_CONCAT(target) as targets'),
            DB::raw('SUM(target) as total_target'),
            DB::raw('SUM(achievement) as total_achievement'),
            DB::raw('GROUP_CONCAT(achievement) as achievements'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(year) as years'),
            DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
            DB::raw('user_id'),
            DB::raw('branch_id'),
            DB::raw('type'),
        ]);

        if ($this->quarter && !empty($this->quarter)) {
            if ($this->quarter == '1') {
                $this->quarter_name = 'Q1';
                $data->where(function ($query) use ($f_year_array) {
                    $query->where('year', '=', $f_year_array[0])
                        ->whereIn('month', ['Apr', 'May', 'Jun']);
                });
                $this->months = ['Apr', 'May', 'Jun'];
            } elseif ($this->quarter == '2') {
                $this->quarter_name = 'Q2';
                $data->where(function ($query) use ($f_year_array) {
                    $query->where('year', '=', $f_year_array[0])
                        ->whereIn('month', ['Jul', 'Aug', 'Sep']);
                });
                $this->months = ['Jul', 'Aug', 'Sep'];
            } elseif ($this->quarter == '3') {
                $this->quarter_name = 'Q3';
                $data->where(function ($query) use ($f_year_array) {
                    $query->where('year', '=', $f_year_array[0])
                        ->whereIn('month', ['Oct', 'Nov', 'Dec']);
                });
                $this->months = ['Oct', 'Nov', 'Dec'];
            } elseif ($this->quarter == '4') {
                $this->quarter_name = 'Q4';
                $data->where(function ($query) use ($f_year_array) {
                    $query->where('year', '=', $f_year_array[1])
                        ->whereIn('month', ['Jan', 'Feb', 'Mar']);
                });
                $this->months = ['Oct', 'Nov', 'Dec'];
            }
        }

        $data = $data->groupBy('user_id', 'branch_id')->orderBy('month')->get();

        return $data;
    }


    public function headings(): array
    {
        $f_year_array = explode('-', $this->financial_year);

        $startYear = $f_year_array[0];

        $endYear = $f_year_array[1];

        $headings = ['Branch', 'Emp Code', 'Name', 'Joining Date', 'Gross Salary'];


        if ($this->quarter && !empty($this->quarter)) {
            if ($this->quarter == '1') {
                $headings[] = 'Apr-' . $startYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'May-' . $startYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Jun-' . $startYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Q1';
                $headings[] = '';
                $headings[] = '';
                $headings[] = '';
                $headings[] = '';
            } elseif ($this->quarter == '2') {
                $headings[] = 'Jul-' . $startYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Aug-' . $startYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Sep-' . $startYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Q2';
                $headings[] = '';
                $headings[] = '';
                $headings[] = '';
                $headings[] = '';
            } elseif ($this->quarter == '3') {
                $headings[] = 'Oct-' . $startYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Nov-' . $startYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Dec-' . $startYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Q3';
                $headings[] = '';
                $headings[] = '';
                $headings[] = '';
                $headings[] = '';
            } elseif ($this->quarter == '4') {
                $headings[] = 'Jan-' . $endYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Feb-' . $endYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Mar-' . $endYear;
                $headings[] = '';
                $headings[] = '';
                $headings[] = 'Q4';
                $headings[] = '';
                $headings[] = '';
                $headings[] = '';
                $headings[] = '';
            }
        }

        $headings[] = 'Total Outstanding Value';
        $headings[] = 'Outstanding Value (>60 Days)';
        $headings[] = 'Outstanding Value (>60 Days) %';
        $headings[] = 'Total Stock Value';
        $headings[] = 'Stock Value (>90 Days)';
        $headings[] = 'Stock Value (>90 Days) %';
        $headings[] = 'Total Incentive';
        $headings[] = 'Total Incentive as per weightage';

        $sub_headings = ['', '', '', '', '', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Fresh Sales Return', 'Net Sales', 'Target Achievement (%)'];

        $final_heading = [$headings, $sub_headings];

        return $final_heading;
    }


    public function map($data): array
    {
        // return [];
        $f_year_array = explode('-', $this->financial_year);
        if ($this->quarter == '4') {
            $total_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->where('year', $f_year_array[1])->where('quarter', 'Like', '%' . $this->quarter_name . '%')->sum('amount');
            $sixty_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->whereNotIn('days', ['0-30', '31-60'])->where('year', $f_year_array[1])->where('quarter', 'Like', '%' . $this->quarter_name . '%')->sum('amount');
            $total_stock = BranchStock::where('branch_id', $data->branch_id)->where('year', $f_year_array[1])->where('quarter', 'Like', '%' . $this->quarter_name . '%')->sum('amount');
            $ninty_stock = BranchStock::where('branch_id', $data->branch_id)->whereNotIn('days', ['0-30', '31-60', '61-90'])->where('year', $f_year_array[1])->where('quarter', 'Like', '%' . $this->quarter_name . '%')->sum('amount');
        } else {
            $total_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->where('year', $f_year_array[0])->where('quarter', 'Like', '%' . $this->quarter_name . '%')->sum('amount');
            $sixty_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->whereNotIn('days', ['0-30', '31-60'])->where('year', $f_year_array[0])->where('quarter', 'Like', '%' . $this->quarter_name . '%')->sum('amount');
            $total_stock = BranchStock::where('branch_id', $data->branch_id)->where('year', $f_year_array[0])->where('quarter', 'Like', '%' . $this->quarter_name . '%')->sum('amount');
            $ninty_stock = BranchStock::where('branch_id', $data->branch_id)->whereNotIn('days', ['0-30', '31-60', '61-90'])->where('year', $f_year_array[0])->where('quarter', 'Like', '%' . $this->quarter_name . '%')->sum('amount');
        }


        $response = array();
        $response[0] = $data['branch']['branch_name'] ?? '';
        $response[1] = $data['user']['employee_codes'] ?? '';
        $response[2] = $data['user']['name'] ?? '';
        $response[3] = $data['user']['userinfo'] ? $data['user']['userinfo']['date_of_joining'] : '-';
        $response[4] = $data['user']['userinfo'] ? $data['user']['userinfo']['gross_salary_monthly'] : '-';
        $data['months'] = explode(',', $data['months']);
        $data['targets'] = explode(',', $data['targets']);
        $data['achievements'] = explode(',', $data['achievements']);
        $data['achievement_percents'] = explode(',', $data['achievement_percents']);
        $year = explode(',', $data['years']);

        $fmonth = $this->months[0];
        $lmonth = $this->months[2];
        if ($this->quarter == '4') {
            $monthNumber = Carbon::parse("1 $fmonth")->month;
            $monthNumber2 = Carbon::parse("1 $lmonth")->month;
            $firstDate = Carbon::createFromDate($f_year_array[1], $monthNumber, 1)->startOfMonth()->toDateString();
            $lastDate = Carbon::createFromDate($f_year_array[1], $monthNumber2, 1)->endOfMonth()->toDateString();
        } else {
            $monthNumber = Carbon::parse("1 $fmonth")->month;
            $monthNumber2 = Carbon::parse("1 $lmonth")->month;
            $firstDate = Carbon::createFromDate($f_year_array[0], $monthNumber, 1)->startOfMonth()->toDateString();
            $lastDate = Carbon::createFromDate($f_year_array[0], $monthNumber2, 1)->endOfMonth()->toDateString();
        }

        $total_achiv =  number_format(($data->user->primarySales->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');

        $index = 0;

        foreach ($this->months as $key => $month) {

            if (in_array($month, $data['months'])) {
                $vaueKey = array_search($month, $data['months']);
                $response[5 + $index] = $data['targets'][$vaueKey];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[6 + $index] = number_format(($data->user->primarySales->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[6 + $index] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[5]) && isset($response[6]) && !empty($response[6]) && !empty($response[5])) {
                    $achievementPercent = number_format(($response[5] == 0) ? 0 : ($response[6] * 100 / $response[5]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[7 + $index] = $achievementPercent;
            } else {
                if (!isset($response[5])) {
                    $response[5 + $index] = '0';
                }
                if (!isset($response[6])) {
                    $response[6 + $index] = '0';
                }
                if (!isset($response[7])) {
                    $response[7 + $index] = '0';
                }
            }
            $index += 3;
        }
        $total_achievement = $data->total_achievement ?? $total_achiv;
        $total_target = $data->total_target ?? 0;
        $sixty_outstanding_per = $total_outstanding > 0 ? ($sixty_outstanding / $total_outstanding) * 100 : 0;
        $ninty_stock_per = $total_stock > 0 ? ($ninty_stock / $total_stock) * 100 : 0;
        $response[8 + $index] = $total_target;
        $response[9 + $index] = $total_achievement;
        $response[10 + $index] = '0';
        $response[11 + $index] = $total_achievement;
        $response[12 + $index] = number_format((($total_achievement / $total_target) * 100), 2, '.', '');
        $response[13 + $index] = $total_outstanding > 0 ? $total_outstanding : '0';
        $response[14 + $index] = $sixty_outstanding > 0 ? $sixty_outstanding : '0';
        $response[15 + $index] = $sixty_outstanding_per > 0 ? $sixty_outstanding_per : '0';
        $response[16 + $index] = $total_stock > 0 ? $total_stock : '0';
        $response[17 + $index] = $ninty_stock > 0 ? $ninty_stock : '0';
        $response[18 + $index] = $ninty_stock_per > 0 ? $ninty_stock_per : '0';


        if ($response[12 + $index] >= 70 && $response[12 + $index] <= 79.99) {
            $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 50) / 100 : 0;
            $fincentive = $incentive;
            $wincentive = $incentive;
            if ($sixty_outstanding_per > 10) {
                $fincentive = '0';
                $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
            }
            if ($ninty_stock_per > 20) {
                $fincentive = '0';
                $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
            }
            $response[19 + $index] = $fincentive;
            $response[20 + $index] = $wincentive;
        } elseif ($response[12 + $index] >= 80 && $response[12 + $index] <= 89.99) {
            $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 100) / 100 : '0';
            $fincentive = $incentive;
            $wincentive = $incentive;
            if ($sixty_outstanding_per > 10) {
                $fincentive = '0';
                $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
            }
            if ($ninty_stock_per > 20) {
                $fincentive = '0';
                $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
            }
            $response[19 + $index] = $fincentive;
            $response[20 + $index] = $wincentive;
        } elseif ($response[12 + $index] >= 90 && $response[12 + $index] <= 99.99) {
            $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 150) / 100 : '0';
            $fincentive = $incentive;
            $wincentive = $incentive;
            if ($sixty_outstanding_per > 10) {
                $fincentive = '0';
                $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
            }
            if ($ninty_stock_per > 20) {
                $fincentive = '0';
                $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
            }
            $response[19 + $index] = $fincentive;
            $response[20 + $index] = $wincentive;
        } elseif ($response[12 + $index] >= 100) {
            $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 200) / 100 : '0';
            $fincentive = $incentive;
            $wincentive = $incentive;
            if ($sixty_outstanding_per > 10) {
                $fincentive = '0';
                $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
            }
            if ($ninty_stock_per > 20) {
                $fincentive = '0';
                $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
            }
            $response[19 + $index] = $fincentive;
            $response[20 + $index] = $wincentive;
        } else {
            $response[19 + $index] = '0';
            $response[20 + $index] = '0';
        }
        $this->rowIndex++;
        return $response;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:D2');
        $sheet->mergeCells('E1:E2');
        $sheet->mergeCells('F1:H1');
        $sheet->mergeCells('I1:K1');
        $sheet->mergeCells('L1:N1');
        $sheet->mergeCells('O1:S1');
        $sheet->mergeCells('T1:T2');
        $sheet->mergeCells('U1:U2');
        $sheet->mergeCells('V1:V2');
        $sheet->mergeCells('W1:W2');
        $sheet->mergeCells('X1:X2');
        $sheet->mergeCells('Y1:Y2');
        $sheet->mergeCells('Z1:Z2');
        $sheet->mergeCells('AA1:AA2');

        $sheet->getStyle('A1:AA2')->applyFromArray([
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
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();

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
