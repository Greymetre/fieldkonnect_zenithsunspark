<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\PrimarySales;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use DB;

class SendBranchCosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:branch-costing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send branch costing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertBranchCosting";
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

        $users_previous = $this->processBranchForPeriod($previousFYStart, $previousFYEnd)
            ->map(function ($user) use ($previousFYStart) {
                $user['f_year'] = $previousFYStart->year . '-' . ($previousFYStart->year + 1);
                return $user;
            });

        $users_current = $this->processBranchForPeriod($currentFYStart, $currentFYEnd)
            ->map(function ($user) use ($currentFYStart) {
                $user['f_year'] = $currentFYStart->year . '-' . ($currentFYStart->year + 1);
                return $user;
            });

        $users = $users->merge($users_previous)->merge($users_current);

        $formatteddata = $users->map(function ($user) {
            return [
                'f_year' => $user['f_year'],
                'branch' => $user['branch_name'],
                'division' => $user['division'],
                'branch_manager' => $user['branch_manager'],
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
            $payload = ['branch_costing' => $chunk->toArray()];
            $response = Http::timeout(240)->post($url, $payload);

            if ($response->successful()) {
                $this->info(count($chunk) . ' records sent successfully.');
            } else {
                $this->error('Failed to send sales data: ' . $response->body());
            }
        });
    }

    function processBranchForPeriod($startDate, $endDate)
    {
        $startDateFormatted = $startDate->toDateString();
        $endDateFormatted = $endDate->toDateString();
        $all_months = getMonthsBetween($startDate, $endDate);

        $branches = Branch::with([
            'getBranchUsers.userinfo',
            'getBranchUsers.expenses' => function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->whereBetween('date', [$startDateFormatted, $endDateFormatted]);
            },
        ])
            ->where('active', 'Y')
            ->whereNotIn('id', ['45', '22', '40', '42'])
            ->whereHas('getBranchUsers', function ($query) {
                $query->where('active', 'Y')->where('sales_type', 'Primary');
            })->get();

        $main_data = collect();

        foreach ($branches as $branch) {
            $primarySales = PrimarySales::select('division', DB::raw('SUM(net_amount) as total_net_amount'))
                ->where('branch_id', $branch->id)
                ->whereBetween('invoice_date', [$startDateFormatted, $endDateFormatted])
                ->groupBy('division')
                ->get();
            foreach ($primarySales as $sales) {
                $users = User::with('userinfo', 'expenses')->whereHas('getdivision', function ($q) use ($sales) {
                    $q->where('division_name', 'LIKE', '%' . $sales->division . '%');
                })
                ->whereIn('id', $branch->getBranchUsers->pluck('id'))
                ->get();
                
    
                $branch_manager = User::whereRaw("FIND_IN_SET(?, branch_id)", [$branch->id])
                    ->where('active', 'Y')
                    ->whereIn('division_id', ['10', '13'])
                    ->whereHas('roles', fn($q) => $q->where('id', '3'))
                    ->first();
    
                $salary = $users->sum(fn($u) => $u->userinfo->gross_salary_monthly ?? 0);
                $ta_da = $users->sum(function ($u) {
                    return $u->expenses instanceof \Illuminate\Support\Collection
                        ? $u->expenses->sum('claim_amount')
                        : 0;
                });
    
                $total_exp = $salary * count($all_months) + $ta_da;
                $cost = $sales->total_net_amount > 0 ? number_format(($total_exp / $sales->total_net_amount) * 100, 2, '.', '') : '0';
    
                $main_data[] = [
                    'branch_name' => $branch->branch_name,
                    'division' => $sales->division,
                    'branch_manager' => $branch_manager?->name ?? '-',
                    'salary' => $salary * count($all_months),
                    'ta_da' => $ta_da,
                    'total_exp' => $total_exp,
                    'sales' => $sales->total_net_amount,
                    'cost' => $cost,
                ];   
            }
        }

        return $main_data;
    }
}
