<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
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

class SalesTargetUsersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithStyles
{

    private $rowIndex = 3;

    public function __construct($request)
    {
        $this->user_id = $request->input('user');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
        $this->target = $request->input('target');
        $this->branch_id = $request->input('branch_id');
        $this->user_id = $request->input('user_id');
        $this->division = $request->input('division');
        $this->type = $request->input('type');
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);


        // $data = SalesTargetUsers::with(['user'])->whereBetween('year', $f_year_array)->toSql();
        $userid = auth()->user()->id;
        $all_users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->get();
        if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Sub_Admin') && !auth()->user()->hasRole('HR_Admin') && !auth()->user()->hasRole('HO_Account')  && !auth()->user()->hasRole('Sub_Support') && !auth()->user()->hasRole('Accounts Order') && !auth()->user()->hasRole('Service Admin') && !auth()->user()->hasRole('All Customers') && !auth()->user()->hasRole('Sub billing') && !auth()->user()->hasRole('Sales Admin')) {
            $all_ids_array = array($userid);
            $test = getAllChild(array($userid), $all_users);
            while (count($test) > 0) {
                $all_ids_array = array_merge($all_ids_array, $test);
                $test = getAllChild($test, $all_users);
            }
        } elseif (auth()->user()->hasRole('Accounts Order')) {
            $all_ids_array = User::where('active', 'Y')->whereIn('branch_id', explode(',', auth()->user()->branch_show))->pluck('id')->toArray();
            $test = getAllChild(array($userid), $all_users);
            while (count($test) > 0) {
                $all_ids_array = array_merge($all_ids_array, $test);
                $test = getAllChild($test, $all_users);
            }
        } else {
            $all_ids_array = User::pluck('id')->toArray();
        }
        $data = SalesTargetUsers::with(['user', 'user.getdesignation', 'user.getdivision', 'branch'])->whereIn('user_id', $all_ids_array)->select([
            DB::raw('GROUP_CONCAT(target) as targets'),
            DB::raw('GROUP_CONCAT(achievement) as achievements'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(year) as years'),
            DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
            DB::raw('user_id'),
            DB::raw('branch_id'),
            DB::raw('type'),
        ]);


        $data->where(function ($query) use ($f_year_array) {
            $query->where(function ($query) use ($f_year_array) {
                if ($this->month == '' && empty($this->month)) {
                    $query->where(function ($query) use ($f_year_array) {
                        $query->where('year', '=', $f_year_array[0])
                            ->where('month', '>=', 'Apr');
                    })->orWhere(function ($query) use ($f_year_array) {
                        $query->where('year', '=', $f_year_array[1])
                            ->where('month', '<=', 'Mar');
                    });
                } else {
                    $query->where(function ($query) use ($f_year_array) {
                        $query->where('year', '=', $f_year_array[0])
                            ->where('month', '>=', $this->month);
                    })->orWhere(function ($query) use ($f_year_array) {
                        $query->where('year', '=', $f_year_array[1])
                            ->where('month', '<=', $this->month);
                    });
                }
            });
        });


        if ($this->branch_id && $this->branch_id != '' && $this->branch_id != null) {
            $userIds = User::where('branch_id', $this->branch_id)->pluck('id');
            $data->whereIn('user_id', $userIds);
        }

        if ($this->user_id && $this->user_id != '' && $this->user_id != null) {
            $userIds = User::where('id', $this->user_id)->pluck('id');
            $data->whereIn('user_id', $userIds);
        }

        if ($this->division && $this->division != '' && $this->division != null) {
            $divisionIds = User::where('division_id', $this->division)->pluck('id');
            $data->whereIn('user_id', $divisionIds);
        }

        if ($this->type && $this->type != '' && $this->type != null) {
            $data->where('type', $this->type);
        }
        // dd($data->toSql(), $f_year_array,  $this->type);
        $data = $data->groupBy('user_id', 'branch_id')->orderBy('month')->get();

        return $data;
    }


    public function headings(): array
    {
        $f_year_array = explode('-', $this->financial_year);

        $startYear = $f_year_array[0];

        $endYear = $f_year_array[1];

        $headings = ['Emp Code', 'User Name', 'Date Of Joining', 'Designation', 'Branch Id', 'Branch Name', 'Division', 'Sales Type'];

        $quarterNames = ['Q1', 'Q2', 'Q3', 'Q4'];

        $quarterIndex = 0;

        for ($year = $startYear; $year <= $endYear; $year++) {
            $startMonth = ($year == $startYear) ? 4 : 1;
            $endMonth = ($year == $endYear) ? 3 : 12;


            for ($month = $startMonth; $month <= $endMonth; $month++) {
                $formattedMonth = Carbon::createFromDate(null, $month, 1)->format('F');
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
        $headings[] = 'User Active';

        $sub_headings = ['', '', '', '', '', '', '', '', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%', 'Tgt', 'Ach', 'Ach%'];

        $final_heading = [$headings, $sub_headings];

        return $final_heading;
    }


    public function map($data): array
    {
        $response = array();
        $response[0] = $data['user']['employee_codes'] ?? '';
        $response[1] = $data['user']['name'] ?? '';
        $response[2] = $data['user']['userinfo'] ? ($data['user']['userinfo']['date_of_joining'] ? date('d-M-Y', strtotime($data['user']['userinfo']['date_of_joining'])) : '') : '';
        $response[3] = $data['user']['getdesignation'] ? $data['user']['getdesignation']['designation_name'] : '';
        $response[4] = $data['branch_id'];
        $response[5] = $data['branch']['branch_name'] ?? '';
        $response[6] = $data['user']['getdivision']['division_name'] ?? '';
        $response[7] = $data['type'] ?? '';
        $f_year_array = explode('-', $this->financial_year);
        $data['months'] = explode(',', $data['months']);
        $data['targets'] = explode(',', $data['targets']);
        $data['achievements'] = explode(',', $data['achievements']);
        $data['achievement_percents'] = explode(',', $data['achievement_percents']);


        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);

            if ($month == 'Apr' && $f_year_array[0] == $year[$key]) {
                $response[8] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[9] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[9] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[8]) && isset($response[9]) && !empty($response[9]) && !empty($response[8])) {
                    $achievementPercent = number_format(($response[8] == 0) ? 0 : ($response[9] * 100 / $response[8]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[10] = $achievementPercent;
            } else {
                if (!isset($response[8])) {
                    $response[8] = '';
                }
                if (!isset($response[9])) {
                    $response[9] = '';
                }
                if (!isset($response[10])) {
                    $response[10] = '';
                }
            }
        }

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'May' && $f_year_array[0] == $year[$key]) {
                $response[11] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[12] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[12] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[11]) && isset($response[12]) && !empty($response[12]) && !empty($response[11])) {
                    $achievementPercent = number_format(($response[11] == 0) ? 0 : ($response[12] * 100 / $response[11]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[13] = $achievementPercent;
            } else {
                if (!isset($response[11])) {
                    $response[11] = '';
                }
                if (!isset($response[12])) {
                    $response[12] = '';
                }
                if (!isset($response[13])) {
                    $response[13] = '';
                }
            }
        }

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Jun' && $f_year_array[0] == $year[$key]) {
                $response[14] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[15] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[15] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[14]) && $response[14] > 0 && isset($response[15]) && !empty($response[15]) && !empty($response[14])) {
                    $achievementPercent = number_format(($response[15] == 0) ? 0 : ($response[15] * 100 / $response[14]), 2, '.', '');
                } else {
                    $achievementPercent = '';
                }
                $response[16] = $achievementPercent;
            } else {
                if (!isset($response[14])) {
                    $response[14] = '';
                }
                if (!isset($response[15])) {
                    $response[15] = '';
                }
                if (!isset($response[16])) {
                    $response[16] = '';
                }
            }
        }

        $response[17] =
            (float) ($response[8] ?? 0) +
            (float) ($response[11] ?? 0) +
            (float) ($response[14] ?? 0);

        $response[18] =
            (float) ($response[9] ?? 0) +
            (float) ($response[12] ?? 0) +
            (float) ($response[15] ?? 0);

        $response[19] = $response[17] > 0
            ? number_format(($response[18] / $response[17]) * 100, 2) . '%'
            : '0.00%';


        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Jul' && $f_year_array[0] == $year[$key]) {
                $response[20] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[21] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[21] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[20]) && isset($response[21]) && !empty($response[21]) && !empty($response[20])) {
                    $achievementPercent = number_format(($response[20] == 0) ? 0 : ($response[21] * 100 / $response[20]), 2, '.', '');
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
                $response[23] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[24] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[24] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[23]) && isset($response[24]) && !empty($response[24]) && !empty($response[23])) {
                    $achievementPercent = number_format(($response[23] == 0) ? 0 : ($response[24] * 100 / $response[23]), 2, '.', '');
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
                $response[26] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[27] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[27] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[26]) && isset($response[27]) && !empty($response[27]) && !empty($response[26])) {
                    $achievementPercent = number_format(($response[26] == 0) ? 0 : ($response[27] * 100 / $response[26]), 2, '.', '');
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

        $response[29] =
            (float) ($response[20] ?? 0) +
            (float) ($response[23] ?? 0) +
            (float) ($response[26] ?? 0);

        $response[30] =
            (float) ($response[21] ?? 0) +
            (float) ($response[24] ?? 0) +
            (float) ($response[27] ?? 0);

        $response[31] = $response[29] > 0
            ? number_format(($response[30] / $response[29]) * 100, 2) . '%'
            : '0.00%';


        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Oct' && $f_year_array[0] == $year[$key]) {
                $response[32] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[33] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[33] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[32]) && isset($response[33]) && !empty($response[33]) && !empty($response[32])) {
                    $achievementPercent = number_format(($response[32] == 0) ? 0 : ($response[33] * 100 / $response[32]), 2, '.', '');
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
                $response[35] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();
                    $response[36] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[36] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[35]) && isset($response[36]) && !empty($response[36]) && !empty($response[35])) {
                    $achievementPercent = number_format(($response[35] == 0) ? 0 : ($response[36] * 100 / $response[35]), 2, '.', '');
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
                $response[38] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[39] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[39] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[38]) && isset($response[39]) && !empty($response[39]) && !empty($response[38])) {
                    $achievementPercent = number_format(($response[38] == 0) ? 0 : ($response[39] * 100 / $response[38]), 2, '.', '');
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

        $response[41] =
            (float) ($response[32] ?? 0) +
            (float) ($response[35] ?? 0) +
            (float) ($response[38] ?? 0);

        $response[42] =
            (float) ($response[33] ?? 0) +
            (float) ($response[36] ?? 0) +
            (float) ($response[39] ?? 0);

        $response[43] = $response[41] > 0
            ? number_format(($response[42] / $response[41]) * 100, 2) . '%'
            : '0.00%';

        foreach ($data['months'] as $key => $month) {
            $year = explode(',', $data['years']);
            if ($month == 'Jan' && $f_year_array[1] == $year[$key]) {
                $response[44] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[45] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[45] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[44]) && isset($response[45]) && !empty($response[45]) && !empty($response[44])) {
                    $achievementPercent = number_format(($response[44] == 0) ? 0 : ($response[45] * 100 / $response[44]), 2, '.', '');
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
                $response[47] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[48] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[48] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[47]) && isset($response[48]) && !empty($response[48]) && !empty($response[47])) {
                    $achievementPercent = number_format(($response[47] == 0) ? 0 : ($response[48] * 100 / $response[47]), 2, '.', '');
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
                $response[50] = $data['targets'][$key];
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $month")->month;
                    $firstDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($year[$key], $monthNumber, 1)->endOfMonth()->toDateString();

                    $response[51] = number_format(($data->user->primarySales->where('branch_id', $data['branch_id'])->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                } else {
                    $response[51] = $data['achievements'][$key] ?? '';
                }
                if (isset($response[50]) && isset($response[51]) && !empty($response[51]) && !empty($response[50])) {
                    $achievementPercent = number_format(($response[51] == 0) ? 0 : ($response[51] * 100 / $response[50]), 2, '.', '');
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

        $response[53] =
            (float) ($response[44] ?? 0) +
            (float) ($response[47] ?? 0) +
            (float) ($response[50] ?? 0);

        $response[54] =
            (float) ($response[45] ?? 0) +
            (float) ($response[48] ?? 0) +
            (float) ($response[51] ?? 0);

        $response[55] = $response[53] > 0
            ? number_format(($response[54] / $response[53]) * 100, 2) . '%'
            : '0.00%';

        $response[56] =
            (float) ($response[17] ?? 0) +
            (float) ($response[29] ?? 0) +
            (float) ($response[41] ?? 0) +
            (float) ($response[53] ?? 0);

        $response[57] =
            (float) ($response[18] ?? 0) +
            (float) ($response[30] ?? 0) +
            (float) ($response[42] ?? 0) +
            (float) ($response[54] ?? 0);

        $response[58] = $response[56] > 0
            ? number_format(($response[57] / $response[56]) * 100, 2) . '%'
            : '0.00%';
        $response[59] = $data['user']['active'] ?? '';

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
        $sheet->mergeCells('F1:F2');
        $sheet->mergeCells('G1:G2');
        $sheet->mergeCells('H1:H2');
        $sheet->mergeCells('I1:K1');
        $sheet->mergeCells('L1:N1');
        $sheet->mergeCells('O1:Q1');
        $sheet->mergeCells('R1:T1');
        $sheet->mergeCells('U1:W1');
        $sheet->mergeCells('X1:Z1');
        $sheet->mergeCells('AA1:AC1');
        $sheet->mergeCells('AD1:AF1');
        $sheet->mergeCells('AG1:AI1');
        $sheet->mergeCells('AJ1:AL1');
        $sheet->mergeCells('AM1:AO1');
        $sheet->mergeCells('AP1:AR1');
        $sheet->mergeCells('AS1:AU1');
        $sheet->mergeCells('AV1:AX1');
        $sheet->mergeCells('AY1:BA1');
        $sheet->mergeCells('BB1:BD1');
        $sheet->mergeCells('BE1:BG1');

        $sheet->getStyle('A1:BH2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // White font color
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '87CEEB'], // Sky blue background color
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Thin border
                    'color' => ['rgb' => '000000'], // Black border color
                ],
            ],
        ]);


        $sheet->getStyle('A3:BH300')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
    }
}
