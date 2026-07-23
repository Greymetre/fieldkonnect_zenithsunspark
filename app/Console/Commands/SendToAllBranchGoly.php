<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\BranchWiseTarget;
use App\Models\PrimarySales;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Http;

class SendToAllBranchGoly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:all-branch-goly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Branch Goly to Power BI';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertBranchGoly";


        $currentFinancialYearStart = Carbon::now()->month >= 4 ? Carbon::now()->year : Carbon::now()->year - 1;
        $currentFinancialYearEnd = $currentFinancialYearStart + 1;

        $lastFinancialYearStart = $currentFinancialYearStart - 1;
        $lastFinancialYearEnd = $currentFinancialYearStart;

        $data = BranchWiseTarget::select(
            'branch_name',
            'branch_id',
            DB::raw('SUM(CASE 
                    WHEN (year = ' . $currentFinancialYearStart . ' AND month IN ("Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec")) 
                    OR (year = ' . $currentFinancialYearEnd . ' AND month IN ("Jan", "Feb", "Mar")) 
                    THEN target ELSE 0 END) as aop'),
            DB::raw('SUM(CASE 
                    WHEN (year = ' . $lastFinancialYearStart . ' AND month IN ("Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec")) 
                    OR (year = ' . $lastFinancialYearEnd . ' AND month IN ("Jan", "Feb", "Mar")) 
                    THEN achievement ELSE 0 END) as ymt_ls')
        )
            ->groupBy('branch_name', 'branch_id')
            ->get();

        if ($data->isEmpty()) {
            $this->info('No users found.');
            return;
        }

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

        $lastyearstartdate = ($financialYearStart - 1) . '-04-01';
        $lastyearenddate = ($financialYearEnd - 1) . '-03-31';

        foreach ($data as $key => $value) {
            $data[$key]['branch_name'] = Branch::find($value->branch_id)->branch_name;
            $data[$key]['ymt_sales'] = round(PrimarySales::where('branch_id', $value->branch_id)->whereIn('division', ['PUMP','MOTOR'])
                ->whereBetween('invoice_date', [$currentstartdate, $currentenddate])
                ->sum('net_amount') / 100000, 2) ?? 0.00;

            $data[$key]['ymt_ls'] = round(PrimarySales::where('branch_id', $value->branch_id)->whereIn('division', ['PUMP','MOTOR'])
                ->whereBetween('invoice_date', [$lastyearstartdate, $lastyearenddate])
                ->sum('net_amount') / 100000, 2) ?? 0.00;

            $data[$key]['aop_per'] = $value->aop == 0 ? 0 : round($data[$key]['ymt_sales'] / $value->aop * 100, 2) ?? 0.00;
            if ($data[$key]['ymt_ls'] == 0) {
                $data[$key]['ymt_goly_per'] = 0;
            } else {
                $data[$key]['ymt_goly_per'] = round(($data[$key]['ymt_sales'] -$data[$key]['ymt_ls']) /$data[$key]['ymt_ls'] * 100, 2) ?? 0.00;
            }
        }

        $data->chunk(100)->each(function ($chunk) use ($url) {
            $payload = ['branch_goly' => $chunk->toArray()];
            $response = Http::timeout(240)->post($url, $payload);

            if ($response->successful()) {
                $this->info(count($chunk) . ' records sent successfully.');
            } else {
                $this->error('Failed to send sales data: ' . $response->body());
            }
        });

        $this->info('Branch Goly data sent successfully.');
    }
}
