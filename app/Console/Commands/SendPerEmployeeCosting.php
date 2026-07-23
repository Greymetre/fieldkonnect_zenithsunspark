<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendPerEmployeeCosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:per-employee-costing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Per Employee Costing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertEmployeeCosting";
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
                $user->f_year = $previousFYStart->year . '-' . ($previousFYStart->year + 1);
                return $user;
            });

        $users_current = $this->processUsersForPeriod($currentFYStart, $currentFYEnd)
            ->map(function ($user) use ($currentFYStart) {
                $user->f_year = $currentFYStart->year . '-' . ($currentFYStart->year + 1);
                return $user;
            });

        $users = $users->merge($users_previous)->merge($users_current);

        $formatteddata = $users->map(function ($record) {
            $user = $record->user;
        
            $manager = User::where('division_id', $user->division_id)->where('active', 'Y')
                ->whereRaw('FIND_IN_SET(?, branch_id)', [$user->branch_id])
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', [
                        'PUMPCH',
                        'AGRIGM/CH/ZM/RM/SH',
                        'FAN/CH/GM/SH'
                    ]);
                })
                ->first();
        
            return [
                'f_year' => $record->month < '04' ? ($record->month - 1) . '-' . $record->month : $record->month,
                'month' => $record->month,
                'quarter' => $record->quarter,
                'division' => $user->getdivision?->division_name,
                'branch' => $user->getbranch?->branch_name ?? 'Not Applicable',
                'branch_cluster' => $manager->name ?? 'Anil Srivastava',
                'emp_code' => $user->employee_codes,
                'emp_name' => $user->name,
                'designation' => $user->getdesignation->designation_name,
                'doj' => $user->userinfo->date_of_joining,
                'sales' => $record->sales,
                'salary' => $record->salary,
                'ta_da' => $record->expenses,
                'incentive' => '0',
                'total_exp' => $record->total_exp,
                'sal_exp_per' => $record->sal_exp,
            ];
        });
        

        // Send data in chunks
        $formatteddata->chunk(100)->each(function ($chunk) use ($url) {
            dd($chunk, 'new changes please check first');
            $payload = ['employee_costing' => $chunk->toArray()];
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
        $monthlyData = collect();
        $monthQuarters = getMonthQuarterPairs($startDate, $endDate);

        $users = User::with([
            'primarySales:id,emp_code,invoice_date,net_amount',
            'getbranch',
            'getdesignation',
            'getdivision',
            'userinfo',
            'expenses'
        ])
            ->where('active', 'Y')
            ->where('sales_type', 'Primary')
            ->where(function ($q) {
                $q->where('designation_id', '1')
                    ->orWhereHas('roles', function ($roleQuery) {
                        $roleQuery->whereIn('id', ['22', '32']);
                    });
            })->get();

        foreach ($monthQuarters as $mq) {
            foreach ($users as $user) {
                $monthly_salary = $user->userinfo->gross_salary_monthly;

                $expenses = $user->expenses
                    ->whereBetween('date', [$mq['start'], $mq['end']])
                    ->sum('claim_amount');

                $salesSum = $user->primarySales
                    ->whereBetween('invoice_date', [$mq['start'], $mq['end']])
                    ->sum('net_amount');

                $salesLakhs = $salesSum > 0 ? number_format($salesSum / 100000, 2) : 0;
                $totalExp = $monthly_salary + $expenses;
                $salExp = $salesLakhs > 0
                    ? number_format(($totalExp / 100000) / $salesLakhs * 100, 2)
                    : 0;

                $monthlyData->push((object)[
                    'user' => $user,
                    'month' => $mq['month'],
                    'quarter' => $mq['quarter'],
                    'salary' => $monthly_salary,
                    'expenses' => $expenses,
                    'sales' => $salesLakhs,
                    'total_exp' => $totalExp,
                    'sal_exp' => $salExp,
                ]);
            }
        }

        return $monthlyData;
    }
}
