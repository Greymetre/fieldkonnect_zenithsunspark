<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Expenses;
use App\Models\Order;
use App\Models\PrimarySales;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use DB;

class SendBmChZmCosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:head-costing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send ZM, RM and CH Costing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertZmRmChCosting";
        $today = Carbon::now('Asia/Kolkata');

        // Get current and previous financial years
        $currentFYStart = Carbon::create($today->year - ($today->month < 4 ? 1 : 0), 4, 1);
        $currentFYEnd   = Carbon::create($currentFYStart->year + 1, 3, 31);
        $previousFYStart = $currentFYStart->copy()->subYear();
        $previousFYEnd   = $currentFYEnd->copy()->subYear();

        // Adjust current FY end if it's in the future
        if ($currentFYEnd->greaterThan($today)) {
            $currentFYEnd = $today;
        }

        // Fetch users for both periods with a flag for identification
        $users = collect();

        $users_previous = $this->processUsersForPeriod($previousFYStart, $previousFYEnd)
            ->map(function ($user) use ($previousFYStart) {
                $user['f_year'] = $previousFYStart->year . '-' . ($previousFYStart->year + 1);
                return $user;
            });

        $users_current = $this->processUsersForPeriod($currentFYStart, $currentFYEnd)
            ->map(function ($user) use ($currentFYStart) {
                $user['f_year'] = $currentFYStart->year . '-' . ($currentFYStart->year + 1);
                return $user;
            });

        $users = $users->merge($users_previous)->merge($users_current);

        $formatteddata = $users->map(function ($user) {
            return [
                'f_year' => $user['f_year'],
                'branch' => $user['branch'],
                'division' => $user['division'],
                'emp_code' => $user['emp_code'],
                'emp_name' => $user['emp_name'],
                'designation' => $user['designation'],
                'sales' => $user['sales'],
                'salary' => $user['salary'],
                'ta_da' => $user['ta_da'],
                'incentive' => '0',
                'total_exp' => $user['total_exp'],
                'sal_exp_per' => $user['cost'],
            ];
        });

        // Send data in chunks
        $formatteddata->chunk(100)->each(function ($chunk) use ($url) {
            $payload = ['all_costing' => $chunk->toArray()];
            $response = Http::timeout(240)->post($url, $payload);

            if ($response->successful()) {
                $this->info(count($chunk) . ' records sent successfully.');
            } else {
                $this->error('Failed to send sales data: ' . $response->body());
            }
        });
    }

    function processUsersForPeriod($startDate, $endDate)
    {
        $startDateFormatted = $startDate->toDateString();
        $endDateFormatted = $endDate->toDateString();
        $all_months = getMonthsBetween($startDate, $endDate);

        $query = User::with([
            'getdesignation',
            'getdivision',
            'userinfo'
        ])
            ->where('active', 'Y')
            ->where('sales_type', 'Primary')
            ->whereHas('roles', function ($query) {
                $query->whereIn('id', ['2','22','32','31','27','23','3','33','21','13','25','6']);
            });
        $users = $query->get();

        $main_data = collect();

        // Prepare per-user calculations
        foreach ($users as $user) {
            $user_ids = getUsersReportingToAuth($user->id);
            $emp_code = User::whereIn('id', $user_ids)->pluck('employee_codes')->toArray();
            $salary = $user->userinfo->gross_salary_monthly * count($all_months);
            $ta_da = Expenses::whereIn('user_id', $user_ids)->whereBetween('date', [$startDateFormatted, $endDateFormatted])->sum('claim_amount');
            $total_exp = $salary + $ta_da;
            $primarySales = PrimarySales::whereIn('emp_code', $emp_code)
                ->whereBetween('invoice_date', [$startDateFormatted, $endDateFormatted])
                ->groupBy('division')
                ->sum('net_amount');
            $branchs = Branch::whereIn('id', explode(',', $user->branch_id))
                ->pluck('branch_name')  // get all branch names
                ->implode(',');         // join them with commas

            $cost = $primarySales > 0 ? number_format(($total_exp / $primarySales) * 100, 2, '.', '') : '0';


            $main_data[] = [
                'branch' => $branchs,
                'division' => $user->getdivision?->division_name,
                'emp_code' => $user->employee_codes,
                'emp_name' => $user->name,
                'designation' => $user->getdesignation->designation_name,
                'doj' => $user->userinfo->date_of_joining,
                'salary' => $salary,
                'ta_da' => $ta_da,
                'total_exp' => $total_exp,
                'sales' => $primarySales,
                'cost' => $cost,
            ];
        }

        return $main_data;
    }
}
