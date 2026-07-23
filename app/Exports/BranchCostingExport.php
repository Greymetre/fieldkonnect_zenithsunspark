<?php

namespace App\Exports;

use App\Models\Branch;
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

class BranchCostingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
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
        $query = Branch::with('getBranchUsers')->where('active', 'Y')->whereNotIn('id', ['45', '22', '40', '42']);

        $query->whereHas('getBranchUsers', function ($query) {

            if ($this->division_id && $this->division_id != '' && $this->division_id != NULL) {
                $query->where('division_id', $this->division_id);
            }

            if ($this->branch_id && $this->branch_id != '' && $this->branch_id != null) {
                $query->where('branch_id', $this->branch_id);
            }


            if ($this->dealer_id && $this->dealer_id != '' && $this->dealer_id != null) {
                $query->where('dealer', 'like', '%' . $this->dealer_id . '%');
            }

            if ($this->product_model && $this->product_model != '' && $this->product_model != null) {
                $query->where('model_name', $this->product_model);
            }

            if ($this->new_group && $this->new_group != '' && $this->new_group != null) {
                $query->where('new_group', $this->new_group);
            }

            if ($this->executive_id && $this->executive_id != '' && $this->executive_id != null) {
                $query->where('id', $this->executive_id);
            }
        });

        $query = $query->get();

        return $query;
    }

    public function headings(): array
    {
        $label1 = [
            'Cluster Head',
            'Reginal Manager',
            'Branch Manager',
            'Branch',
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
        $label2[] = '';
        $headings2 = ['', '', '', '', 'Salary', 'Incentive', 'TA-DA', 'TOTAL EXP', 'Total Sales', 'Sale/Emp Exp %', 'Sales Team Count', 'Per Manpower Avr Sale'];
        foreach ($this->months as $key => $value) {
            $label2[] = $value;
            $label2[] = '';
            $label2[] = '';
            $label2[] = '';
            $headings2[] = 'Salary';
            $headings2[] = 'Incentive';
            $headings2[] = 'TA-DA';
            $headings2[] = 'Sale';
        }

        $headings1 = array_merge($label1, $label2);
        $main_head = array($headings1, $headings2);

        return $main_head;
    }

    public function map($data): array
    {
        if ($data->id == '15') {
            $cluster_head = User::where('id', '135')->get();
        } else {
            $cluster_head = User::whereRaw("FIND_IN_SET(?, branch_id)", [$data->id])
                ->where('active', 'Y')
                ->where('division_id', $this->division_id)
                ->whereHas('roles', function ($query) {
                    $query->where('id', '6');
                })->get();
        }
        if ($data->id == '8') {
            $reginal_manager = User::where('id', '50')->get();
        } else {
            $reginal_manager = User::whereRaw("FIND_IN_SET(?, branch_id)", [$data->id])
                ->where('active', 'Y')
                ->where('division_id', $this->division_id)
                ->whereHas('roles', function ($query) {
                    $query->where('id', '13');
                })->get();
        }
        $branch_managver = User::whereRaw("FIND_IN_SET(?, branch_id)", [$data->id])
            ->where('active', 'Y')
            ->where('division_id', $this->division_id)
            ->whereHas('roles', function ($query) {
                $query->where('id', '3');
            })->get();
        $response[0] = count($cluster_head) > 0 ? implode(' / ', $cluster_head->pluck('name')->toArray()) : '-';
        $response[1] = count($reginal_manager) > 0 ? implode(' / ', $reginal_manager->pluck('name')->toArray()) : '-';
        $response[2] = count($branch_managver) > 0 ? implode(' / ', $branch_managver->pluck('name')->toArray()) : '-';
        $response[3] = $data->branch_name ? $data->branch_name : '-';
        $response[4] = $data->getTotalGrossSalary() > 0 ? number_format((($data->getTotalGrossSalary() / 100000) * count($this->months)), 2, '.', '') : '0';
        $total_exp = $data->getBranchUsers()->with('expenses')->get()->sum(function ($user) {
            return $user->expenses->where('date', '>=', $this->startDateFormatted)->where('date', '<=', $this->endDateFormatted)->sum('claim_amount');
        });
        $primary_sale = $data->getBranchUsers()->with('primarySales')->get()->sum(function ($user) {
            return $user->primarySales->where('invoice_date', '>=', $this->startDateFormatted)->where('invoice_date', '<=', $this->endDateFormatted)->sum('net_amount');
        });
        $other_sale = Order::where('created_by', $data->getBranchUsers->pluck('id')->toArray())->where('order_date', '>=', $this->startDateFormatted)->where('order_date', '<=', $this->endDateFormatted)->sum('sub_total');
        $response[5] = '0';
        $response[6] = number_format(($total_exp / 100000), 2, '.', '');
        $response[7] = number_format((($total_exp + ($data->getTotalGrossSalary() * count($this->months))) / 100000), 2, '.', '');
        $response[8] = number_format((($primary_sale + $other_sale) / 100000), 2, '.', '');
        if ($primary_sale > 0 || $other_sale > 0) {
            $response[9] = number_format(((($total_exp + ($data->getTotalGrossSalary() * count($this->months))) / ($primary_sale + $other_sale)) * 100), 2, '.', '');
        } else {
            $response[9] = '100';
        }
        $stc = $data->getBranchUsers()->whereIn('designation_id', [59, 1, 5, 11])->get();
        $response[10] = count($stc);
        if (count($stc) > 0) {
            $response[11] = number_format((((($primary_sale + $other_sale) / 100000) / count($stc)) / 4), 2, '.', '');
        } else {
            $response[11] = '0';
        }

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
            $total_exp = $data->getBranchUsers()->with('expenses')->get()->sum(function ($user) use ($startDateMonthly, $endDateMonthly) {
                return $user->expenses->where('date', '>=', $startDateMonthly)->where('date', '<=', $endDateMonthly)->sum('claim_amount');
            });
            $primary_sale = $data->getBranchUsers()->with('primarySales')->get()->sum(function ($user) use ($startDateMonthly, $endDateMonthly) {
                return $user->primarySales->where('invoice_date', '>=', $startDateMonthly)->where('invoice_date', '<=', $endDateMonthly)->sum('net_amount');
            });
            $other_sale = Order::where('created_by', $data->getBranchUsers->pluck('id')->toArray())->where('order_date', '>=', $startDateMonthly)->where('order_date', '<=', $endDateMonthly)->sum('sub_total');
            $response[12 + $check] = $data->getTotalGrossSalary() > 0 ? number_format(($data->getTotalGrossSalary() / 100000), 2, '.', '') : '0';
            $response[13 + $check] = "0";



            $response[14 + $check] = number_format(($total_exp / 100000), 2, '.', '');
            $response[15 + $check] = number_format((($primary_sale + $other_sale) / 100000), 2, '.', '');

            $check += 4;
        }

        return $response;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();

                $event->sheet->mergeCells('A1:A2');
                $event->sheet->mergeCells('B1:B2');
                $event->sheet->mergeCells('C1:C2');
                $event->sheet->mergeCells('D1:D2');
                $event->sheet->mergeCells('E1:L1');

                $startColumn = 'M';
                $columnsPerMerge = 4;

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
