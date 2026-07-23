<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
use App\Models\PrimarySales;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesTargetUsers;
use App\Models\SalesTargetCustomers;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesTargetDealersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithStyles, WithEvents
{

    private $rowIndex = 3;

    public function __construct($request)
    {
        // dd($request->all()); 
        $this->user_id = $request->input('user');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
        $this->target = $request->input('target');
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);
        $month = $this->month;


        $data = SalesTargetCustomers::with(['customer', 'customer.userdetails', 'customer.createdbyname'])->select([
            DB::raw('GROUP_CONCAT(target) as targets'),
            DB::raw('GROUP_CONCAT(achievement) as achievements'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(year) as years'),
            DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
            DB::raw('customer_id'),
            DB::raw('type'),
        ]);

        if ($this->month == '' && empty($this->month)) {
            $data->where(function ($query) use ($f_year_array) {
                $query->where('year', '=', $f_year_array[0])
                    ->where('month', '>=', 'Apr');
            })->orWhere(function ($query) use ($f_year_array) {
                $query->where('year', '=', $f_year_array[1])
                    ->where('month', '<=', 'Mar');
            });
        } else {
            if ($this->month != '' && !empty($this->month)) {
                if ($this->month == 'Jan' || $this->month == 'Feb' || $this->month == 'Mar') {
                    $data->where(function ($query) use ($f_year_array, $month) {
                        $query->where('year', '=', $f_year_array[1])
                            ->where('month', '=', $month);
                    });
                } else {
                    $data->where(function ($query) use ($f_year_array, $month) {
                        $query->where('year', '=', $f_year_array[0])
                            ->where('month', '=', $month);
                    });
                }
            } else {
                $data->where(function ($query) use ($f_year_array) {
                    $query->where('year', '=', $f_year_array[0])
                        ->where('month', '>=', $this->month);
                })->orWhere(function ($query) use ($f_year_array) {
                    $query->where('year', '=', $f_year_array[1])
                        ->where('month', '<=', $this->month);
                });
            }
        }

        $data = $data->groupBy('customer_id')->orderBy('month')->get();

        // dd($data);

        return $data;
    }


    public function headings(): array
    {
        $f_year_array = explode('-', $this->financial_year);

        $startYear = $f_year_array[0];

        $endYear = $f_year_array[1];

        // $headings = ['Dealer id', 'Employee Name', 'Firm Name', 'City', 'Branch', 'Division', 'Sales Type'];
        $headings = ['Branch', 'BP Code', 'Firm Name', 'City', 'State', 'Employee Name', 'Dealer Appointment Date'];

        $quarterNames = ['Q1', 'Q2', 'Q3', 'Q4'];

        $quarterIndex = 0;

        for ($year = $startYear; $year <= $endYear; $year++) {

            // if($this->month){
            //     $startMonth = date('m',strtotime($this->month));
            //     $endMonth =date('m',strtotime($this->month));
            // }else{
            //     $startMonth = ($year == $startYear) ? 4 : 1;
            //     $endMonth = ($year == $endYear) ? 3 : 12;
            // }
            $startMonth = ($year == $startYear) ? 4 : 1;
            $endMonth = ($year == $endYear) ? 3 : 12;


            for ($month = $startMonth; $month <= $endMonth; $month++) {

                $formattedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
                $headings[] = "$formattedMonth/$year";
                $headings[] = "";
                $headings[] = "";

                if ($month == '06' || $month == '09' || $month == '12' || $month == '03') {
                    $headings[] = $quarterNames[$quarterIndex];
                    $quarterIndex++;
                    $headings[] = "";
                    $headings[] = "";
                }
            }
        }

        $headings[] = 'Total';
        $headings[] = '';
        $headings[] = '';
        $headings[] = 'Pump Ach';
        $headings[] = 'Motor Ach';
        $headings[] = 'Return Amt';
        $headings[] = 'LY Sales Ach';
        $headings[] = 'GOLY %';
        $headings[] = 'Dealer ID';
        $headings[] = 'Division';
        $headings[] = 'Dealer Type';
        $headings[] = 'New Dealer Quarter';

        $sub_headings = ['', '', '', '', '', '', '', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%'];

        $final_heading = [$headings, $sub_headings];

        return $final_heading;
    }


    public function map($data): array
    {
        $response = array();

        $employee_name = PrimarySales::where('customer_id', $data['customer_id'])->orderBy('id', 'desc')->first();

        $response[0] = $data['customer']['userdetails']['getbranch']['branch_name'] ?? '-';
        $response[1] = $data['customer']['sap_code'] ?? '-';
        $response[2] = $data['customer']['name'];
        $response[3] = $data['customer']['customeraddress']['cityname']['city_name'] ?? '-';
        $response[4] = $data['customer']['customeraddress']['statename']['state_name'] ?? '-';
        $response[5] = $employee_name ? $employee_name->sales_person : '-';
        $response[6] = $data['customer']['creation_date'] ? date('d-M-Y', strtotime($data['customer']['creation_date'])) : '-';

        $f_year_array = explode('-', $this->financial_year);

        list($startYear, $endYear) = explode('-', $this->financial_year);
        $lastStartYear = $startYear - 1;
        $lastEndYear = $endYear - 1;
        $ly_start_date = Carbon::createFromFormat('Y-m-d', "$lastStartYear-04-01")->toDateString();
        $ly_end_date = Carbon::createFromFormat('Y-m-d', "$lastEndYear-03-31")->toDateString();
        $cy_start_date = Carbon::createFromFormat('Y-m-d', "$startYear-04-01")->toDateString();
        $cy_end_date = Carbon::createFromFormat('Y-m-d', "$endYear-03-31")->toDateString();

        $customerCreationDate = isset($data['customer']['creation_date']) ? $data['customer']['creation_date'] : null;
        $financialYearStartDate = "01-04-" . trim($f_year_array[0]);
        $financialYearStartTimestamp = strtotime($financialYearStartDate);

        $Cquarter = '-';

        if ($customerCreationDate) {
            $customerCreationTimestamp = strtotime($customerCreationDate);

            if ($customerCreationTimestamp < $financialYearStartTimestamp) {
                $DType = "Old";
            } else {
                $DType = "New";
                $month = (int) date('m', $customerCreationTimestamp);
                if ($month >= 4 && $month <= 6) {
                    $Cquarter = "Q1";
                } elseif ($month >= 7 && $month <= 9) {
                    $Cquarter = "Q2";
                } elseif ($month >= 10 && $month <= 12) {
                    $Cquarter = "Q3";
                } else {
                    $Cquarter = "Q4";
                }
            }
        } else {
            $DType = "-";
        }

        $ly_sales = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $ly_start_date)->where('invoice_date', '<=', $ly_end_date)->sum('net_amount')) / 100000, 2, '.', '');
        $pump_sales = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('division', 'LIKE', '%PUMP%')->where('invoice_date', '>=', $cy_start_date)->where('invoice_date', '<=', $cy_end_date)->sum('net_amount')) / 100000, 2, '.', '');
        $motor_sales = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('division', 'LIKE', '%MOTOR%')->where('invoice_date', '>=', $cy_start_date)->where('invoice_date', '<=', $cy_end_date)->sum('net_amount')) / 100000, 2, '.', '');

        $data['months'] = explode(',', $data['months']);
        $data['targets'] = explode(',', $data['targets']);
        $data['achievements'] = explode(',', $data['achievements']);
        $data['achievement_percents'] = explode(',', $data['achievement_percents']);


        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);

            if ($month == 'Apr' && $f_year_array[0] == $year[$key]) {
                $response[7] = $data['targets'][$key];
                $firstDate = Carbon::createFromDate($year[$key], 4, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 4, 1)->endOfMonth()->toDateString();
                $response[8] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[8] = $data['achievements'][$key]??'';
                if (isset($response[7]) && isset($response[8]) && !empty($response[8]) && !empty($response[7])) {
                    $achievementPercent = ($response[7] == 0) ? 0 : number_format(($response[8] * 100 / $response[7]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[9] = $achievementPercent;
            } else {
                if (!isset($response[7])) {
                    $response[7] = '';
                }
                if (!isset($response[8])) {
                    $response[8] = '';
                }
                if (!isset($response[9])) {
                    $response[9] = '';
                }
            }
        }

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'May' && $f_year_array[0] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 5, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 5, 1)->endOfMonth()->toDateString();
                $response[10] = $data['targets'][$key];
                $response[11] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[11] = $data['achievements'][$key]??'';
                if (isset($response[10]) && isset($response[11]) && !empty($response[11]) && !empty($response[10])) {
                    $achievementPercent = ($response[10] == 0) ? 0 : number_format(($response[11] * 100 / $response[10]), 2, '.', '');
                } else {
                    $achievementPercent = '0';
                }
                $response[12] = $achievementPercent;
            } else {
                if (!isset($response[10])) {
                    $response[10] = '0';
                }
                if (!isset($response[11])) {
                    $response[11] = '0';
                }
                if (!isset($response[12])) {
                    $response[12] = '0';
                }
            }
        }

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Jun' && $f_year_array[0] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 6, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 6, 1)->endOfMonth()->toDateString();
                $response[13] = $data['targets'][$key];
                $response[14] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[14] = $data['achievements'][$key]??'';
                if (isset($response[13]) && isset($response[14]) && !empty($response[14]) && !empty($response[13])) {
                    $achievementPercent = ($response[13] == 0) ? 0 : number_format(($response[14] * 100 / $response[13]), 2, '.', '');
                } else {
                    $achievementPercent = '0';
                }
                $response[15] = $achievementPercent;
            } else {
                if (!isset($response[13])) {
                    $response[13] = '0';
                }
                if (!isset($response[14])) {
                    $response[14] = '0';
                }
                if (!isset($response[15])) {
                    $response[15] = '0';
                }
            }
        }

        $response[17] = '=H' . $this->rowIndex . ' + K' . $this->rowIndex . ' + N' . $this->rowIndex;
        $response[18] = '=I' . $this->rowIndex . ' + L' . $this->rowIndex . ' + O' . $this->rowIndex;
        $response[19] = '=ROUND((J' . $this->rowIndex . ' + M' . $this->rowIndex . ' + P' . $this->rowIndex . ') / 3,2)';

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Jul' && $f_year_array[0] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 7, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 7, 1)->endOfMonth()->toDateString();
                $response[20] = $data['targets'][$key];
                $response[21] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[21] = $data['achievements'][$key]??'';
                if (isset($response[20]) && isset($response[21]) && !empty($response[21]) && !empty($response[20])) {
                    $achievementPercent = ($response[20] == 0) ? 0 : number_format(($response[21] * 100 / $response[20]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[22] = $achievementPercent;
            } else {
                if (!isset($response[20])) {
                    $response[20] = '';
                }
                if (!isset($response[21])) {
                    $response[21] = '';
                }
                if (!isset($response[22])) {
                    $response[22] = '';
                }
            }
        }

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Aug' && $f_year_array[0] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 8, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 8, 1)->endOfMonth()->toDateString();
                $response[23] = $data['targets'][$key];
                $response[24] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[24] = $data['achievements'][$key]??'';
                if (isset($response[23]) && isset($response[24]) && !empty($response[24]) && !empty($response[23])) {
                    $achievementPercent = ($response[23] == 0) ? 0 : number_format(($response[24] * 100 / $response[23]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[25] = $achievementPercent;
            } else {
                if (!isset($response[23])) {
                    $response[23] = '';
                }
                if (!isset($response[24])) {
                    $response[24] = '';
                }
                if (!isset($response[25])) {
                    $response[25] = '';
                }
            }
        }

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Sep' && $f_year_array[0] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 9, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 9, 1)->endOfMonth()->toDateString();
                $response[26] = $data['targets'][$key];
                $response[27] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[27] = $data['achievements'][$key]??'';
                if (isset($response[26]) && isset($response[27]) && !empty($response[27]) && !empty($response[26])) {
                    $achievementPercent = ($response[26] == 0) ? 0 : number_format(($response[27] * 100 / $response[26]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[28] = $achievementPercent;
            } else {
                if (!isset($response[26])) {
                    $response[26] = '';
                }
                if (!isset($response[27])) {
                    $response[27] = '';
                }
                if (!isset($response[28])) {
                    $response[28] = '';
                }
            }
        }

        $response[29] = '=T' . $this->rowIndex . ' + W' . $this->rowIndex . ' + Z' . $this->rowIndex;
        $response[30] = '=U' . $this->rowIndex . ' + X' . $this->rowIndex . ' + AA' . $this->rowIndex;
        $response[31] = '=ROUND((V' . $this->rowIndex . ' + Y' . $this->rowIndex . ' + AB' . $this->rowIndex . ') / 3,2)';


        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Oct' && $f_year_array[0] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 10, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 10, 1)->endOfMonth()->toDateString();
                $response[32] = $data['targets'][$key];
                $response[33] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[33] = $data['achievements'][$key]??'';
                if (isset($response[32]) && isset($response[33]) && !empty($response[33]) && !empty($response[32])) {
                    $achievementPercent = ($response[32] == 0) ? 0 : number_format(($response[33] * 100 / $response[32]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[34] = $achievementPercent;
            } else {
                if (!isset($response[32])) {
                    $response[32] = '';
                }
                if (!isset($response[33])) {
                    $response[33] = '';
                }
                if (!isset($response[34])) {
                    $response[34] = '';
                }
            }
        }

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Nov' && $f_year_array[0] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 11, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 11, 1)->endOfMonth()->toDateString();
                $response[35] = $data['targets'][$key];
                $response[36] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[36] = $data['achievements'][$key]??'';
                if (isset($response[35]) && isset($response[36]) && !empty($response[36]) && !empty($response[35])) {
                    $achievementPercent = ($response[35] == 0) ? 0 : number_format(($response[36] * 100 / $response[35]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[37] = $achievementPercent;
            } else {
                if (!isset($response[35])) {
                    $response[35] = '';
                }
                if (!isset($response[36])) {
                    $response[36] = '';
                }
                if (!isset($response[37])) {
                    $response[37] = '';
                }
            }
        }

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Dec' && $f_year_array[0] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 12, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 12, 1)->endOfMonth()->toDateString();
                $response[38] = $data['targets'][$key];
                $response[39] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[39] = $data['achievements'][$key]??'';
                if (isset($response[38]) && isset($response[39]) && !empty($response[39]) && !empty($response[38])) {
                    $achievementPercent = ($response[38] == 0) ? 0 : number_format(($response[39] * 100 / $response[38]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[40] = $achievementPercent;
            } else {
                if (!isset($response[38])) {
                    $response[38] = '';
                }
                if (!isset($response[39])) {
                    $response[39] = '';
                }
                if (!isset($response[40])) {
                    $response[40] = '';
                }
            }
        }

        $response[41] = '=AF' . $this->rowIndex . ' + AI' . $this->rowIndex . ' + AL' . $this->rowIndex;
        $response[42] = '=AG' . $this->rowIndex . ' + AJ' . $this->rowIndex . ' + AM' . $this->rowIndex;
        $response[43] = '=ROUND((AH' . $this->rowIndex . ' + AK' . $this->rowIndex . ' + AN' . $this->rowIndex . ') / 3,2)';

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Jan' && $f_year_array[1] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 1, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 1, 1)->endOfMonth()->toDateString();
                $response[44] = $data['targets'][$key];
                $response[45] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[45] = $data['achievements'][$key]??'';
                if (isset($response[44]) && isset($response[45]) && !empty($response[45]) && !empty($response[44])) {
                    $achievementPercent = ($response[44] == 0) ? 0 : number_format(($response[45] * 100 / $response[44]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[46] = $achievementPercent;
            } else {
                if (!isset($response[44])) {
                    $response[44] = '';
                }
                if (!isset($response[45])) {
                    $response[45] = '';
                }
                if (!isset($response[46])) {
                    $response[46] = '';
                }
            }
        }

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Feb' && $f_year_array[1] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 2, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 2, 1)->endOfMonth()->toDateString();
                $response[47] = $data['targets'][$key];
                $response[48] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[48] = $data['achievements'][$key]??'';
                if (isset($response[47]) && isset($response[48]) && !empty($response[48]) && !empty($response[47])) {
                    $achievementPercent = ($response[47] == 0) ? 0 : number_format(($response[48] * 100 / $response[47]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[49] = $achievementPercent;
            } else {
                if (!isset($response[47])) {
                    $response[47] = '';
                }
                if (!isset($response[48])) {
                    $response[48] = '';
                }
                if (!isset($response[49])) {
                    $response[49] = '';
                }
            }
        }

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Mar' && $f_year_array[1] == $year[$key]) {
                $firstDate = Carbon::createFromDate($year[$key], 3, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($year[$key], 3, 1)->endOfMonth()->toDateString();
                $response[50] = $data['targets'][$key];
                $response[51] = number_format((PrimarySales::where('customer_id', $data['customer_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                // $response[51] = $data['achievements'][$key]??'';
                if (isset($response[50]) && isset($response[51]) && !empty($response[51]) && !empty($response[50])) {
                    $achievementPercent = ($response[50] == 0) ? 0 : number_format(($response[51] * 100 / $response[50]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[52] = $achievementPercent;
            } else {
                if (!isset($response[50])) {
                    $response[50] = '';
                }
                if (!isset($response[51])) {
                    $response[51] = '';
                }
                if (!isset($response[52])) {
                    $response[52] = '';
                }
            }
        }

        $response[53] = '=AR' . $this->rowIndex . ' + AU' . $this->rowIndex . ' + AX' . $this->rowIndex;
        $response[54] = '=AS' . $this->rowIndex . ' + AV' . $this->rowIndex . ' + AY' . $this->rowIndex;
        $response[55] = '=ROUND((AT' . $this->rowIndex . ' + AW' . $this->rowIndex . ' + AZ' . $this->rowIndex . ') / 3,2)';

        $response[56] = '=Q' . $this->rowIndex . ' + AC' . $this->rowIndex . ' + AO' . $this->rowIndex . ' + BA' . $this->rowIndex;
        $response[57] = round((floatval($pump_sales) + floatval($motor_sales)), 2);
        $response[58] = '=ROUND((S' . $this->rowIndex . ' + AE' . $this->rowIndex . ' + AQ' . $this->rowIndex . ' + BC' . $this->rowIndex . ') / 4,2)';

        $response[60] = $pump_sales > 0 ? $pump_sales : "0.00";
        $response[61] = $motor_sales > 0 ? $motor_sales : "0.00";
        $response[62] = "-";
        $response[63] = $ly_sales > 0 ? $ly_sales : "0.00";
        $response[64] = floatval($ly_sales) > 0 ? round((((floatval($pump_sales) + floatval($motor_sales)) - floatval($ly_sales)) / floatval($ly_sales)) * 100, 2) . '%' : 'LY No Sales';
        $response[65] = $data['customer']['id'] ?? '';
        $response[66] = $data['customer']['userdetails']['getdivision']['division_name'] ?? '';
        $response[67] = $DType;
        $response[68] = $Cquarter;

        $this->rowIndex++;

        return $response;
    }

    public function styles(Worksheet $sheet) {}

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
                $event->sheet->mergeCells('E1:E2');
                $event->sheet->mergeCells('F1:F2');
                $event->sheet->mergeCells('G1:G2');
                $event->sheet->mergeCells('H1:J1');
                $event->sheet->mergeCells('K1:M1');
                $event->sheet->mergeCells('N1:P1');
                $event->sheet->mergeCells('Q1:S1');
                $event->sheet->mergeCells('T1:V1');
                $event->sheet->mergeCells('W1:Y1');
                $event->sheet->mergeCells('Z1:AB1');
                $event->sheet->mergeCells('AC1:AE1');
                $event->sheet->mergeCells('AF1:AH1');
                $event->sheet->mergeCells('AI1:AK1');
                $event->sheet->mergeCells('AL1:AN1');
                $event->sheet->mergeCells('AO1:AQ1');
                $event->sheet->mergeCells('AR1:AT1');
                $event->sheet->mergeCells('AU1:AW1');
                $event->sheet->mergeCells('AX1:AZ1');
                $event->sheet->mergeCells('BA1:BC1');
                $event->sheet->mergeCells('BD1:BF1');
                $event->sheet->mergeCells('BG1:BG2');
                $event->sheet->mergeCells('BH1:BH2');
                $event->sheet->mergeCells('BI1:BI2');
                $event->sheet->mergeCells('BJ1:BJ2');
                $event->sheet->mergeCells('BK1:BK2');
                $event->sheet->mergeCells('BL1:BL2');
                $event->sheet->mergeCells('BM1:BM2');
                $event->sheet->mergeCells('BN1:BN2');
                $event->sheet->mergeCells('BO1:BO2');

                $event->sheet->getStyle('A1:' . $lastColumn . '2')->applyFromArray([
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
