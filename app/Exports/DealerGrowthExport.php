<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\PrimarySales;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use DB;

class DealerGrowthExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
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
        $this->remark = $request->input('remark');
        $this->months = [];
        $this->t_data = '';
    }
    public function collection()
    {
        $currentDate = Carbon::now();
        DB::statement("SET SESSION group_concat_max_len = 100000000");
        $query = PrimarySales::with('user')->select(
            'dealer',
            'customer_id',
            'final_branch',
            'city',
            'emp_code',
            DB::raw('SUM(net_amount) as total_net_amounts'),
            DB::raw('GROUP_CONCAT(net_amount) as net_amounts'),
            DB::raw('GROUP_CONCAT(division) as divisions'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(invoice_date) as invoice_dates'),
            DB::raw('0 as last_year_net_amounts'),
            DB::raw('0 as last_year_net_amounts_array'),
            DB::raw('0 as last_year_month_array'),
            DB::raw('0 as last_year_division_array'),
            DB::raw('0 as last_year_invoice_date_array'),
        );

        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $userids = getUsersReportingToAuth();
            $customer_ids = Customers::whereIn('executive_id', $userids)->orWhereIn('created_by', $userids)->pluck('id');
            $query->whereIn('customer_id', $customer_ids);
        }

        // Determine the financial year date range
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
            $financial_year_start = $startDate->toDateString();
            $financial_year_end = $endDate->toDateString();
        } elseif ($this->financial_year && $this->financial_year != '' && $this->financial_year != null) {
            $f_year_array = explode('-', $this->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';
        } else {
            $currentDate = Carbon::now();

            $currentYear = $currentDate->year;
            $financialYearStart = Carbon::create($currentYear, 4, 1);
            $financialYearEnd = Carbon::create($currentYear + 1, 3, 31);

            if ($currentDate->lt($financialYearStart)) {
                $financialYearStart = Carbon::create($currentYear - 1, 4, 1);
                $financialYearEnd = Carbon::create($currentYear, 3, 31);
            }

            $financial_year_start = $financialYearStart->format('Y-m-d');
            $financial_year_end = $financialYearEnd->format('Y-m-d');
        }

        // Adjust financial_year_end if it is greater than today
        $today = Carbon::today();
        if (Carbon::parse($financial_year_end)->greaterThan($today)) {
            $financial_year_end = $today->format('Y-m-d');
        }
        // Calculate last year start and end dates after potentially adjusting financial_year_end
        $last_year_start = Carbon::parse($financial_year_start)->subYear()->format('Y-m-d');
        $last_year_end = Carbon::parse($financial_year_end)->subYear()->format('Y-m-d');

        // Filter by financial year
        $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);

        // Additional filters
        if ($this->division_id && $this->division_id != '' && count($this->division_id) > 0) {
            $query->whereIn('division', $this->division_id);
        }

        if ($this->branch_id && $this->branch_id != '' && $this->branch_id != null) {
            $query->where('final_branch', $this->branch_id);
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
            $query->where('sales_person', $this->executive_id);
        }

        // Grouping and ordering
        $query->whereIn('division', ['PUMP', 'MOTOR'])->groupBy('dealer', 'customer_id', 'final_branch', 'city');

        // Execute the primary query
        $results = $query->get();

        // Calculate the last year's net amounts
        DB::statement("SET SESSION group_concat_max_len = 100000000");
        $lastYearAmounts = PrimarySales::select(
            'dealer',
            'customer_id',
            'final_branch',
            'city',
            DB::raw('SUM(net_amount) as last_year_net_amounts'),
            DB::raw('GROUP_CONCAT(net_amount) as last_year_net_amounts_array'),
            DB::raw('GROUP_CONCAT(division) as last_year_division_array'),
            DB::raw('GROUP_CONCAT(month) as last_year_month_array'),
            DB::raw('GROUP_CONCAT(invoice_date) as last_year_invoice_date_array'),
        )
            ->whereBetween('invoice_date', [$last_year_start, $last_year_end])
            ->whereIn('division', ['PUMP', 'MOTOR'])
            ->groupBy('dealer', 'customer_id', 'final_branch', 'city')
            ->get();

            // Merge the results
            $results = $results->map(function ($item) use ($lastYearAmounts) {
                $lastYearAmount = $lastYearAmounts->firstWhere(function ($value) use ($item) {
                    // return $value->customer_id == $item->customer_id;
                    return $value->customer_id == $item->customer_id && $value->dealer == $item->dealer;

                    // return $value->dealer == $item->dealer &&
                    // $value->final_branch == $item->final_branch;

                    // return $value->dealer == $item->dealer &&
                    // $value->final_branch == $item->final_branch &&
                    // $value->city == $item->city;
                });

            $item->last_year_net_amounts = $lastYearAmount ? $lastYearAmount->last_year_net_amounts : 0;
            $item->last_year_net_amounts_array = $lastYearAmount ? $lastYearAmount->last_year_net_amounts_array : 0;
            $item->last_year_division_array = $lastYearAmount ? $lastYearAmount->last_year_division_array : 0;
            $item->last_year_month_array = $lastYearAmount ? $lastYearAmount->last_year_month_array : 0;
            $item->last_year_invoice_date_array = $lastYearAmount ? $lastYearAmount->last_year_invoice_date_array : 0;

            // Calculate growthPercent
            $currentYearAchievements = $item->total_net_amounts;
            $lastYearAchievements = $item->last_year_net_amounts;

            $growthPercent = 0;
            if ($lastYearAchievements != null) {
                $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                $growthPercent = ROUND($growthPercent, 2);
            } else {
                if ($lastYearAchievements == null || $lastYearAchievements == 0) {
                    if (($currentYearAchievements == null || $currentYearAchievements == 0) && ($lastYearAchievements == null || $lastYearAchievements == 0)) {
                        $growthPercent = 0;
                    } elseif (($lastYearAchievements == null || $lastYearAchievements == 0) && isset($currentYearAchievements) && ($currentYearAchievements != null && $currentYearAchievements > 0)) {
                        $growthPercent = 0;
                    }
                }
            }

            $item->growthPercent = $growthPercent;

            return $item;
        });
        if ($this->remark && $this->remark != '' && $this->remark != null) {
            if ($this->remark == '1') {
                // INACTIVE DEALER
                $results = $results->filter(function ($item) {
                    return $item->total_net_amounts == 0;
                });
            } elseif ($this->remark == '2') {
                // LY -NO SALE
                $results = $results->filter(function ($item) {
                    return $item->last_year_net_amounts == 0;
                });
            } elseif ($this->remark == '3') {
                // DE-GROWTH
                $results = $results->filter(function ($item) {
                    return $item->growthPercent < 0;
                });
            } elseif ($this->remark == '4') {
                // GROWTH DEALER
                $results = $results->filter(function ($item) {
                    return $item->growthPercent > 0;
                });
            }
        }
        $results = $results->sortByDesc('growthPercent');

        return $results;
    }

    public function headings(): array
    {

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
            $financial_year_start = $startDate->toDateString();
            $financial_year_end = $endDate->toDateString();
        } elseif ($this->financial_year && $this->financial_year != '' && $this->financial_year != null) {
            $f_year_array = explode('-', $this->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';
        } else {
            $currentDate = Carbon::now();

            $currentYear = $currentDate->year;
            $financialYearStart = Carbon::create($currentYear, 4, 1);
            $financialYearEnd = Carbon::create($currentYear + 1, 3, 31);

            if ($currentDate->lt($financialYearStart)) {
                $financialYearStart = Carbon::create($currentYear - 1, 4, 1);
                $financialYearEnd = Carbon::create($currentYear, 3, 31);
            }

            $financial_year_start = $financialYearStart->format('Y-m-d');
            $financial_year_end = $financialYearEnd->format('Y-m-d');
        }

        // Adjust financial_year_end if it is greater than today
        $today = Carbon::today();
        if (Carbon::parse($financial_year_end)->greaterThan($today)) {
            $financial_year_end = $today;
        }
        $currentDate = Carbon::parse($financial_year_start);


        while ($currentDate <= $financial_year_end) {
            $monthName = $currentDate->format('F');
            if (!in_array($monthName, $this->months)) {
                $this->months[] = $monthName;
            }
            $currentDate->addMonth()->startOfMonth();
        }
        $flabel1 = [
            'Branch',
            'Party Name',
            'Employee',
            'Sales Return',
        ];
        $f_year_array = explode('-', $this->financial_year);
        $month_blank_array = array();
        for ($i = 1; $i < ((count($this->months) + 1) * 2); $i++) {
            array_push($month_blank_array, "");
        }
        $flabel2 = [
            ($f_year_array[0] - 1) . '-' . $f_year_array[0],
        ];
        $flabel3 = [
            $this->financial_year,
        ];
        $flabel4 = [
            'LYTD',
            'CYTD',
            'GOLY%',
            'Remarks',
        ];

        $heading1 = array_merge($flabel1, $flabel2, $month_blank_array, $flabel3, $month_blank_array, $flabel4);


        $slabel1 = ['', '', '', ''];

        $slabel2 = [];

        foreach ($this->months as $key => $value) {
            $slabel2[] = $value;
            $slabel2[] = '';
        }

        $slabel2[] = 'Total';
        $slabel2[] = '';

        foreach ($this->months as $key => $value) {
            $slabel2[] = $value;
            $slabel2[] = '';
        }

        $slabel2[] = 'Total';
        $slabel2[] = '';

        $heading2 = array_merge($slabel1, $slabel2);

        $tlabel1 = ['', '', '', ''];
        $tlabel2 = [];
        foreach ($this->months as $key => $value) {
            $tlabel2[] = 'Motor';
            $tlabel2[] = 'Pump';
        }

        $tlabel2[] = 'Motor';
        $tlabel2[] = 'Pump';

        foreach ($this->months as $key => $value) {
            $tlabel2[] = 'Motor';
            $tlabel2[] = 'Pump';
        }

        $tlabel2[] = 'Motor';
        $tlabel2[] = 'Pump';

        $heading3 = array_merge($tlabel1, $tlabel2);

        $headings = [$heading1, $heading2, $heading3];

        return $headings;
    }

    public function map($data): array
    {
        $invoice_dates = explode(',', $data->invoice_dates);
        $net_amounts = explode(',', $data->net_amounts);
        $divisions = explode(',', $data->divisions);
        $last_year_invoice_dates = explode(',', $data->last_year_invoice_date_array);
        $last_year_net_amounts = explode(',', $data->last_year_net_amounts_array);
        $last_year_divisions = explode(',', $data->last_year_division_array);
        $response[0] = $data->final_branch;
        $response[1] = $data->dealer;
        $response[2] = $data->user ? $data->user->name . '(' . $data->emp_code . ')' : '-';
        $response[3] = '';
        $indx = 0;
        $lytptsale = 0;
        $lytmtsale = 0;
        $cytptsale = 0;
        $cytmtsale = 0;
        foreach ($this->months as $k => $val) {
            $ptsale = 0;
            $mtsale = 0;
            foreach ($last_year_invoice_dates as $key => $value) {
                if ($value != 0) {
                    $invDate = Carbon::createFromFormat('Y-m-d', $value);
                    $currentDate = $invDate->copy();
                    $monthName = $currentDate->format('F');
                    if ($monthName == $val) {
                        if ($last_year_divisions[$key] == 'PUMP') {
                            $ptsale += $last_year_net_amounts[$key];
                        } elseif ($last_year_divisions[$key] == 'MOTOR') {
                            $mtsale += $last_year_net_amounts[$key];
                        }
                    }
                }
            }
            $lytptsale += $ptsale;
            $lytmtsale += $mtsale;
            if ($mtsale > 0) {
                $response[4 + $indx] = number_format(($mtsale / 100000), 2, '.', '');
                $indx++;
            } else {
                $response[4 + $indx] = "0";
                $indx++;
            }
            if ($ptsale > 0) {
                $response[4 + $indx] = number_format(($ptsale / 100000), 2, '.', '');
                $indx++;
            } else {
                $response[4 + $indx] = "0";
                $indx++;
            }
        }
        $response[5 + $indx] = (string)number_format(($lytmtsale / 100000), '2', '.', '');
        $response[6 + $indx] = (string)number_format(($lytptsale / 100000), '2', '.', '');
        foreach ($this->months as $k => $val) {
            $ptsale = 0;
            $mtsale = 0;
            foreach ($invoice_dates as $key => $value) {
                $invDate = Carbon::createFromFormat('Y-m-d', $value);
                $currentDate = $invDate->copy();
                $monthName = $currentDate->format('F');
                if ($monthName == $val) {
                    if ($divisions[$key] == 'PUMP') {
                        $ptsale += $net_amounts[$key];
                    } elseif ($divisions[$key] == 'MOTOR') {
                        $mtsale += $net_amounts[$key];
                    }
                }
            }
            $cytptsale += $ptsale;
            $cytmtsale += $mtsale;
            if ($mtsale > 0) {
                $response[7 + $indx] = number_format(($mtsale / 100000), 2, '.', '');
                $indx++;
            } else {
                $response[7 + $indx] = "0";
                $indx++;
            }
            if ($ptsale > 0) {
                $response[7 + $indx] = number_format(($ptsale / 100000), 2, '.', '');
                $indx++;
            } else {
                $response[7 + $indx] = "0";
                $indx++;
            }
        }

        $response[8 + $indx] = (string)number_format(($cytmtsale / 100000), '2', '.', '');
        $response[9 + $indx] = (string)number_format(($cytptsale / 100000), '2', '.', '');

        $response[10 + $indx] = $data->last_year_net_amounts > 0 ? number_format(($data->last_year_net_amounts / 100000), 2, '.', '') : "0";
        $response[11 + $indx] = $data->total_net_amounts > 0 ? number_format(($data->total_net_amounts / 100000), 2, '.', '') : "0";
        $response[12 + $indx] = (string)$data->growthPercent;

        return $response;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();

                $secondLastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn) - 1;
                $cyLastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn) - 2;
                $lyLastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastColumn) - 3;
                $secondLastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($secondLastColumn);
                $cyLastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cyLastColumn);
                $lyLastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lyLastColumn);

                $rowCount = $event->sheet->getHighestDataRow();
                for ($row = 4; $row <= $rowCount; $row++) {
                    $cellValue = $event->sheet->getCell($secondLastColumnLetter . '' . $row)->getValue();
                    $cycellValue = $event->sheet->getCell($cyLastColumnLetter . '' . $row)->getValue();
                    $lycellValue = $event->sheet->getCell($lyLastColumnLetter . '' . $row)->getValue();
                    if ($cycellValue <= 0) {
                        $event->sheet->setCellValue($lastColumn . '' . $row, 'INACTIVE DEALER');
                        $event->sheet->getStyle($lastColumn . '' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFFF00'],
                            ],
                        ]);
                    } elseif ($lycellValue <= 0) {
                        $event->sheet->setCellValue($lastColumn . '' . $row, 'LY -NO SALE');
                        $event->sheet->getStyle($lastColumn . '' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFFFFF'],
                            ],
                        ]);
                    } elseif ($cellValue <= 0) {
                        $event->sheet->setCellValue($lastColumn . '' . $row, 'DE-GROWTH');
                        $event->sheet->getStyle($lastColumn . '' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FF0000'],
                            ],
                        ]);
                    } elseif ($cellValue > 0) {
                        $event->sheet->setCellValue($lastColumn . '' . $row, 'GROWTH DEALER');
                        if ($cellValue > 30) {
                            $event->sheet->getStyle($lastColumn . '' . $row)->applyFromArray([
                                'fill' => [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => '00FF00'],
                                ],
                            ]);
                        } else {
                            $event->sheet->getStyle($lastColumn . '' . $row)->applyFromArray([
                                'fill' => [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'FFFF00'],
                                ],
                            ]);
                        }
                    }
                }

                $event->sheet->mergeCells('A1:A3');
                $event->sheet->mergeCells('B1:B3');
                $event->sheet->mergeCells('C1:C3');
                $event->sheet->mergeCells('D1:D3');

                $offset = 5;
                $endColumnIndex = $offset + (count($this->months) + 1) * 2 - 1;
                $endColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endColumnIndex);
                $secondoffset = $endColumnIndex + 1;
                $secontendColumnIndex = $secondoffset + (count($this->months) + 1) * 2 - 1;
                $secondstartColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($secondoffset);
                $secondendColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($secontendColumnIndex);
                $toffset = $secontendColumnIndex + 1;
                $foffset = $toffset + 1;
                $fioffset = $foffset + 1;
                $tColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($toffset);
                $fColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($foffset);
                $fiColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($fioffset);

                $event->sheet->mergeCells('E1:' . $endColumnLetter . '1');
                for ($i = $offset; $i <= $endColumnIndex; $i += 2) {
                    $startColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $endColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);

                    $cellRange = $startColumnLetter . '2:' . $endColumnLetter . '2';

                    $event->sheet->mergeCells($cellRange);
                }

                $event->sheet->mergeCells($secondstartColumnLetter . '1:' . $secondendColumnLetter . '1');
                for ($i = $secondoffset; $i <= $secontendColumnIndex; $i += 2) {
                    $startColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
                    $endColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);

                    $cellRange = $startColumnLetter . '2:' . $endColumnLetter . '2';

                    $event->sheet->mergeCells($cellRange);
                }

                $event->sheet->mergeCells($tColumnLetter . '1:' . $tColumnLetter . '3');
                $event->sheet->mergeCells($fColumnLetter . '1:' . $fColumnLetter . '3');
                $event->sheet->mergeCells($fiColumnLetter . '1:' . $fiColumnLetter . '3');


                $event->sheet->getStyle('A1:' . $lastColumn . '3')->applyFromArray([
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

                $event->sheet->getStyle('A4:' . $lastColumn . '' . ($lastRow - 2))->applyFromArray([
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
