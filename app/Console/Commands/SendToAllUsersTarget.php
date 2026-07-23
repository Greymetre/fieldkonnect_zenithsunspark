<?php

namespace App\Console\Commands;

use App\Models\PrimarySales;
use App\Models\SalesTargetUsers;
use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class SendToAllUsersTarget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:all-users-target';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send user targets to Power BI';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertUsersTarget";

        $users_target = SalesTargetUsers::where('type', 'primary')->get();

        if ($users_target->isEmpty()) {
            $this->info('No users target found.');
            return;
        }

        $users_target->chunk(200)->each(function ($chunk) use ($url) {
            $payload = [
                'users_target' => $chunk->map(function ($target) {
                    $month = $target->month; // Example: "Apr"
                    $year = $target->year; // Example: "2024"

                    // Convert month abbreviation to a valid date format
                    $carbonDate = Carbon::createFromFormat('M Y', "$month $year");

                    $startDate = $carbonDate->startOfMonth()->toDateString(); // e.g., "2024-04-01"
                    $endDate = $carbonDate->endOfMonth()->toDateString(); // e.g., "2024-04-30"

                    $primarySalesTotal = round(PrimarySales::where('emp_code', $target->user->employee_codes)->whereBetween('invoice_date', [$startDate, $endDate])
                        ->sum('net_amount') / 100000, 2);
                    $data = $target->toArray(); // Convert all fields to array
                    $data['emp_name'] = $target->user->name ?? null;
                    $data['division'] = $target->user->getdivision->division_name ?? null;
                    $data['branch_name'] = $target->branch->branch_name ?? null;
                    $data['achievement'] = $primarySalesTotal > 0 ? $primarySalesTotal : 0.00;

                    // Fetch a user with the same division_id and where branch_id exists in the branch_id column
                    $manager = User::where('division_id', $target->user->division_id)->where('active', 'Y')
                        ->whereRaw('FIND_IN_SET(?, branch_id)', [$target->branch_id])
                        ->whereHas('roles', function ($query) {
                            $query->whereIn('name', [
                                'PUMPCH',
                                'AGRIGM/CH/ZM/RM/SH',
                                'FAN/CH/GM/SH'
                            ]);
                        })
                        ->first();

                    $data['branch_cluster'] = $manager->name ?? null;

                    return $data;
                })->toArray()
            ];
            $response = Http::timeout(240)->post($url, $payload);

            if ($response->successful()) {
                $this->info(count($chunk) . ' records sent successfully.');
            } else {
                $this->error('Failed to send sales data: ' . $response->body());
            }
        });

        $this->info('Message sent to all users target.');
    }
}
