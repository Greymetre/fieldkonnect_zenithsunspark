<?php

namespace App\Exports;

use App\Models\BranchStock;
use App\Models\City;
use App\Models\CustomerOutstanting;
use App\Models\Customers;
use App\Models\District;
use App\Models\EmployeeDetail;
use App\Models\MobileUserLoginDetails;
use App\Models\MspActivity;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\ParentDetail;
use App\Models\PrimarySales;
use App\Models\Redemption;
use App\Models\TransactionHistory;
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
use Excel;
use DB;


class ASMRatingReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
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

        // $query = $query->where('sales_type', 'Primary')->latest()->get();



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

        $query = $query->withCount([
            'all_attendance_details as working_days' => function ($query) {
                $query->whereNotIn('working_type', ['Office Work', 'Office Meeting', 'Full Day Leave', 'Leave', 'Holiday'])
                    ->whereBetween('punchin_date', [$this->start_date, $this->end_date]);
            },
            'visits as visit_count' => function ($query) {
                $query->whereBetween('checkin_date', [$this->start_date, $this->end_date]);
            }
        ])
            ->latest()
            ->get();
        return $query;
    }

    public function headings(): array
    {
        return [['Branch', 'Emp Code', 'ASM', 'DOJ', 'Final Rating', 'Number of days  dedicated to market visits', '', '', '', 'All Customer Visit', '', '', '', 'Number of new market place (white) mapped', '', '', '', 'Target Vs Ach', '', '', '', '', 'sales from new dealers as 40% of total Sales', '', '', '', '', 'sales from New products as 60% of total  sales', '', '', '', '', 'Debtors', '', '', '', '', 'Weightage', 'Saarthi Activation', '', '', '', 'MSP activity', '', '', ''], ['', '', '', '', '', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Target', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Target', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Target', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'TOTAL SALES FROM APR TO AUG', 'AVR PER DAYS SALES', 'TOTAL DEBTORS', 'DAYS', 'For Rating %', 'Final Rating', 'Ach', '% ACHD', 'For Rating %', 'Final Rating', 'Ach', '% ACHD', 'For Rating %', 'Final Rating']];
    }

    public function map($query): array
    {
        $f_year_array = explode('-', $this->financial_year);

        if (isset($query['userinfo']['date_of_joining']) && $query['userinfo']['date_of_joining'] != null && $query['userinfo']['date_of_joining'] > $this->start_date && $query['userinfo']['date_of_joining'] < $this->end_date) {
            $startDate = Carbon::parse($startDate = Carbon::parse($query['userinfo']['date_of_joining']));
        } else {
            $startDate = Carbon::parse($this->start_date);
        }
        $endDate = Carbon::parse($this->end_date);
        $monthCount = $startDate->diffInMonths($endDate) + 1;

        $selectedmonths = [];
        while ($startDate->lessThanOrEqualTo($endDate)) {
            $selectedmonths[] = $startDate->format('M');
            $startDate->addMonth();
        }

        $working_days_trg = 20 * $monthCount;
        $visit_count_trg = 120 * $monthCount;
        $unique_visit_count_trg = 8 * $monthCount;
        $active_customer_trg = 8 * $monthCount;
        $msp_activity_trg = 4 * $monthCount;

        $unique_visit_count = $query->visits
            ->whereBetween('checkin_date', [$this->start_date, $this->end_date])
            ->filter(function ($visit) {
                $city_id = optional(optional($visit->customers)->customeraddress)->city_id;

                if (!$city_id) {
                    return true;
                }

                $existing_customer = Customers::whereHas('customeraddress', function ($q) use ($city_id) {
                    $q->where('city_id', $city_id);
                })
                    ->whereHas('createdbyname', function ($q) {
                        $q->where('division_id', $this->division_id);
                    })
                    ->where('created_at', '<', $visit->checkin_date)
                    ->exists();
                return !$existing_customer;
            })
            ->unique(fn($visit) => optional(optional($visit->customers)->customeraddress)->city_id)
            ->count();

        // if ($request->ip() != '111.118.252.250') {

        //     $uniqueVisits = $query->visits
        //         ->whereBetween('checkin_date', [$this->start_date, $this->end_date])
        //         ->filter(function ($visit) {
        //             $city_id = optional(optional($visit->customers)->customeraddress)->city_id;

        //             if (!$city_id) {
        //                 return true;
        //             }

        //             $existing_customer = Customers::whereHas('customeraddress', function ($q) use ($city_id) {
        //                 $q->where('city_id', $city_id);
        //             })
        //                 ->whereHas('createdbyname', function ($q) {
        //                     $q->where('division_id', $this->division_id);
        //                 })
        //                 ->where('created_at', '<', $visit->checkin_date)
        //                 ->exists();

        //             return !$existing_customer;
        //         })
        //         ->unique(fn($visit) => optional(optional($visit->customers)->customeraddress)->city_id)
        //         ->map(function ($visit) {
        //             $city_name = optional(optional($visit->customers)->customeraddress)->cityname->city_name ?? 'Unknown City';
        //             return [
        //                 'city_name' => $city_name,
        //                 'visit_date' => $visit->checkin_date,
        //                 'customer_id' => $visit->customer_id,
        //             ];
        //         });

        //     dd($uniqueVisits);
        // }

        $user_target = $query->target->whereIn('month', $selectedmonths)->sum('target');
        $user_achiv = $query->primarySales->where('invoice_date', '>=', $this->start_date)->where('invoice_date', '<=', $this->end_date)->sum('net_amount');
        $user_achiv_new_dealer = $query->primarySales()->where('invoice_date', '>=', $this->start_date)->where('invoice_date', '<=', $this->end_date)->where('new_dealer', 'Y')->sum('net_amount');
        $user_achiv_new_product = $query->primarySales()->where('invoice_date', '>=', $this->start_date)->where('invoice_date', '<=', $this->end_date)->where('new_product', 'Y')->sum('net_amount');
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $user_ids = getUsersReportingToAuth($query->id);

        $total_assign_customer_ids = EmployeeDetail::where('user_id', $query->id)
            ->pluck('customer_id')
            ->toArray();

        $child_customer_ids = ParentDetail::whereIn('parent_id', $total_assign_customer_ids)
            ->pluck('customer_id')
            ->toArray();

        $all_customer_ids = array_merge($total_assign_customer_ids, $child_customer_ids);
        $active_customer = 0;

        foreach (array_chunk($all_customer_ids, 500) as $chunk) {
            $active_customer += TransactionHistory::whereBetween('created_at', [$this->start_date, $this->end_date])
                ->whereIn('customer_id', $chunk)
                ->whereNotIn('customer_id', function ($query) {
                    $query->select('customer_id')
                        ->from('transaction_histories')
                        ->where('created_at', '<', $this->start_date);
                })
                ->groupBy('customer_id')
                ->selectRaw('customer_id')
                ->get()
                ->count();
        }

        $debtors_start_date = $f_year_array[0] . '-04-01';
        // $debtors_end_date = $f_year_array[0] . '-12-31';
        $debtors_end_date = now()->toDateString();

        $debtors_start_date_or = Carbon::createFromFormat('Y-m-d', $f_year_array[0] . '-04-01');
        $debtors_end_date_or = now();

        $days_difference = $debtors_start_date_or->diffInDays($debtors_end_date_or);
        // $days_difference = 270;

        $debtors_sales = PrimarySales::where('branch_id', $query->branch_id)->where('invoice_date', '>=', $debtors_start_date)->where('invoice_date', '<=', $debtors_end_date)->whereIn('division', ['PUMP', 'MOTOR'])->sum('net_amount');
        $total_debtors = CustomerOutstanting::where('branch_id', $query->branch_id)->whereIn('division_id', ['10', '18'])->where('year', $f_year_array[0])->sum('amount');

        $degree_name = array();
        if (!empty($query['geteducation'])) {
            foreach ($query['geteducation'] as $key_new => $datas) {
                $degree_name[] = isset($datas->degree_name) ? $datas->degree_name : '';
            }
        }

        $msp_activitys = MspActivity::where('emp_code', $query->employee_codes);
        if (isset($this->month) && count($this->month) > 0) {
            $msp_activitys->whereIn('month', $this->month);
        }
        $msp_activitys = $msp_activitys->where('fyear', getCurrentFinancialYear($this->financial_year))->sum('msp_count');

        static $rowNumber = 3;

        $result = [
            $query['getbranch'] ? $query['getbranch']['branch_name'] : '-',
            $query['employee_codes'],
            $query['name'] ?? '-',
            $query['userinfo'] ? date('d M Y', strtotime($query['userinfo']['date_of_joining'])) : '',
            "=AT{$rowNumber}",

            $query['working_days'] . ' (' . $working_days_trg . ')',
            $this->getPer($query['working_days'], $working_days_trg) . '%',
            $this->getPer($query['working_days'], $working_days_trg) >= 100 ? '100%' : $this->getPer($query['working_days'], $working_days_trg) . '%',
            $number_days = $this->getFR($this->getPer($query['working_days'], $working_days_trg), 5),

            $query['visit_count'] . ' (' . $visit_count_trg . ')',
            $this->getPer($query['visit_count'], $visit_count_trg) . '%',
            $this->getPer($query['visit_count'], $visit_count_trg) >= 100 ? '100%' : $this->getPer($query['visit_count'], $visit_count_trg) . '%',
            $all_cust = $this->getFR($this->getPer($query['visit_count'], $visit_count_trg), 5),

            $unique_visit_count . '(' . $unique_visit_count_trg . ')',
            $this->getPer($unique_visit_count, $unique_visit_count_trg) . '%',
            $this->getPer($unique_visit_count, $unique_visit_count_trg) >= 100 ? '100%' : $this->getPer($unique_visit_count, $unique_visit_count_trg) . '%',
            $uniq_cust = $this->getFR($this->getPer($unique_visit_count, $unique_visit_count_trg), 5),

            $user_target,
            $user_achiv > 0 ? round(($user_achiv / 100000), 2) : '0',
            $this->getPer($user_achiv / 100000, $user_target) . '%',
            $this->getPer($user_achiv / 100000, $user_target) >= 100 ? '100%' : $this->getPer($user_achiv / 100000, $user_target) . '%',
            $targets = $this->getFR($this->getPer($user_achiv / 100000, $user_target), 40),

            $fachiv = $user_achiv > 0 ? round((($user_achiv / 100000) * 40) / 100, 1) : '0',
            $user_achiv_new_dealer > 0 ? round(($user_achiv_new_dealer / 100000), 2) : '0',
            $this->getPer($user_achiv_new_dealer / 100000, $fachiv) . '%',
            $this->getPer($user_achiv_new_dealer / 100000, $fachiv) >= 100 ? '100%' : $this->getPer($user_achiv_new_dealer / 100000, $fachiv) . '%',
            $new_sale = $this->getFR($this->getPer($user_achiv_new_dealer / 100000, $fachiv), 10),

            $sachiv = $user_achiv > 0 ? round((($user_achiv / 100000) * 60) / 100, 2) : '0',
            $user_achiv_new_product > 0 ? round(($user_achiv_new_product / 100000), 2) : '0',
            $this->getPer($user_achiv_new_product / 100000, $sachiv) . '%',
            $this->getPer($user_achiv_new_product / 100000, $sachiv) >= 100 ? '100%' : $this->getPer($user_achiv_new_product / 100000, $sachiv) . '%',
            $newpro = $this->getFR($this->getPer($user_achiv_new_product / 100000, $sachiv), 5),

            $debtors_sales > 0 ? round(($debtors_sales / 100000), 2) : '0',
            $debtors_sales > 0 ? round((($debtors_sales / 100000) / $days_difference), 2) : '0',
            $total_debtors > 0 ? round($total_debtors, 1) : '0',
            $days = ($debtors_sales / 100000) / 270 > 0 && $total_debtors > 0 ? round(($total_debtors / (($debtors_sales / 100000) / $days_difference)), 0) : '100',
            $percentage = $days <= 30 ? '100%' : ($days <= 60 ? '80%' : ($days <= 90 ? '50%' : '0%')),
            $debtor = (20 * (int)$percentage) / 100,

            $active_customer > 0 ? $active_customer : '0',
            $this->getPer($active_customer, $active_customer_trg) . '%',
            $this->getPer($active_customer, $active_customer_trg) >= 100 ? '100%' : $this->getPer($active_customer, $active_customer_trg) . '%',
            $sarthi_custo = $this->getFR($this->getPer($active_customer, $active_customer_trg), 5),

            $msp_activitys > 0 ? $msp_activitys . ' (' . $msp_activity_trg . ')' : '0 (' . $msp_activity_trg . ')',
            $this->getPer($msp_activitys, $msp_activity_trg) . '%',
            $msp_final = $this->getFR($this->getPer($msp_activitys, $msp_activity_trg), 5),

            $sarthi_custo + $debtor + $newpro + $new_sale + $all_cust + $uniq_cust + $targets + $number_days + $msp_final,

        ];
        $rowNumber++;
        return $result;
    }

    public function getPer($achiv, $trg)
    {
        return $trg > 0 ? round(($achiv / $trg) * 100, 0) : '0';
    }
    public function getFR($achivper, $tpoint)
    {
        if ($achivper >= 100) {
            return $tpoint;
        } else {
            return round(($tpoint * $achivper) / 100, 0) > 0 ? round(($tpoint * $achivper) / 100, 0) : '0';
        }
        return round(($achiv / $trg) * 100, 0);
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
                $event->sheet->mergeCells('F1:I1');
                $event->sheet->mergeCells('J1:M1');
                $event->sheet->mergeCells('N1:Q1');
                $event->sheet->mergeCells('R1:V1');
                $event->sheet->mergeCells('W1:AA1');
                $event->sheet->mergeCells('AB1:AF1');
                $event->sheet->mergeCells('AG1:AK1');
                $event->sheet->mergeCells('AM1:AP1');
                $event->sheet->mergeCells('AQ1:AT1');

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

                $event->sheet->getStyle('A1:AT2')->applyFromArray([
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

                $event->sheet->getStyle('A' . $lastRow . ':AT' . $lastRow)->applyFromArray([
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
