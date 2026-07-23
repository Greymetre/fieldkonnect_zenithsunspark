<?php

namespace App\Console\Commands;

use App\Models\PrimarySales;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SendToAllBranchContribution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:all-branch-contribution';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Branch Contribution to Power BI';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertBranchContribution";



        $currentMonth = date('n');
        $currentYear = date('Y');

        if ($currentMonth >= 4) {
            $financialYearStart = $currentYear;
            $financialYearEnd = $currentYear + 1;
        } else {
            $financialYearStart = $currentYear - 1;
            $financialYearEnd = $currentYear;
        }

        $currentstartdate = $financialYearStart . '-04-01';
        $currentenddate = $financialYearEnd . '-03-31';

        $currentstartdate = '2024-04-01';
        $currentenddate = '2025-03-31';

        $totalSales = PrimarySales::with('branch')->whereBetween('invoice_date', [$currentstartdate, $currentenddate])->whereIn('division', ['PUMP','MOTOR'])
            ->sum('net_amount');

        $data = PrimarySales::with('branch')->select(
            'branch_id',
            DB::raw('SUM(net_amount)/100000 as total_sale'),
            DB::raw('(SUM(net_amount) / ' . $totalSales . ') * 100 as contribution')
        )
            ->whereBetween('invoice_date', [$currentstartdate, $currentenddate])            
            ->whereIn('division', ['PUMP','MOTOR'])
            ->groupBy('branch_id')
            ->get();

            foreach ($data as $k => $value) {
                $data[$k]->contribution = round($value->contribution, 2);
                $data[$k]->branch_name = $value->branch ? $value->branch->branch_name : '';
            }

        $data->chunk(100)->each(function ($chunk) use ($url) {
            $payload = ['branch_contribution' => $chunk->toArray()];
            $response = Http::timeout(240)->post($url, $payload);

            if ($response->successful()) {
                $this->info(count($chunk) . ' records sent successfully.');
            } else {
                $this->error('Failed to send sales data: ' . $response->body());
            }
        });

        $this->info('Branch Contribution data sent successfully.');
    }
}
