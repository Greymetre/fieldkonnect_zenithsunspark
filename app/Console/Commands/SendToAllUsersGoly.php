<?php

namespace App\Console\Commands;

use App\Models\PrimarySales;
use App\Models\User;
use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Http;

class SendToAllUsersGoly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:all-users-goly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a message to all users GOLY';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertMonthlyGoly";

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

        $lastlastyearstartdate = ($financialYearStart - 2) . '-04-01';
        $lastlastyearenddate = ($financialYearEnd - 2) . '-03-31';

        $slastlastyearstartdate = ($financialYearStart - 3) . '-04-01';
        $slastlastyearenddate = ($financialYearEnd - 3) . '-03-31';

        // dd($currentstartdate, $currentenddate, $lastyearstartdate, $lastyearenddate);


        $data = PrimarySales::select([
                'primary_sales.month',
                'primary_sales.division',
                // DB::raw('SUM(CASE WHEN invoice_date BETWEEN "' . $lastlastyearstartdate . '" AND "' . $lastlastyearenddate . '" THEN net_amount ELSE 0 END) as lastlastyearsale'),
                // DB::raw('SUM(CASE WHEN invoice_date BETWEEN "' . $lastyearstartdate . '" AND "' . $lastyearenddate . '" THEN net_amount ELSE 0 END) as lastyearsale'),
                // DB::raw('SUM(CASE WHEN invoice_date BETWEEN "' . $currentstartdate . '" AND "' . $currentenddate . '" THEN net_amount ELSE 0 END) as currentyearsale'),
                DB::raw('
                        CASE 
                    WHEN SUM(CASE WHEN invoice_date BETWEEN "' . $lastyearstartdate . '" AND "' . $lastyearenddate . '" THEN net_amount ELSE 0 END) = 0 
                    THEN "LYNS"
                    ELSE 
                        ROUND(
                            (
                                (SUM(CASE WHEN invoice_date BETWEEN "' . $currentstartdate . '" AND "' . $currentenddate . '" THEN net_amount ELSE 0 END) -
                                SUM(CASE WHEN invoice_date BETWEEN "' . $lastyearstartdate . '" AND "' . $lastyearenddate . '" THEN net_amount ELSE 0 END))
                                /
                                SUM(CASE WHEN invoice_date BETWEEN "' . $lastyearstartdate . '" AND "' . $lastyearenddate . '" THEN net_amount ELSE 0 END) 
                            ) * 100, 2
                        ) 
                END as goly
                '),
                DB::raw('
                        CASE 
                    WHEN SUM(CASE WHEN invoice_date BETWEEN "' . $lastlastyearstartdate . '" AND "' . $lastlastyearenddate . '" THEN net_amount ELSE 0 END) = 0 
                    THEN "LYNS"
                    ELSE 
                        ROUND(
                            (
                                (SUM(CASE WHEN invoice_date BETWEEN "' . $lastyearstartdate . '" AND "' . $lastyearenddate . '" THEN net_amount ELSE 0 END) -
                                SUM(CASE WHEN invoice_date BETWEEN "' . $lastlastyearstartdate . '" AND "' . $lastlastyearenddate . '" THEN net_amount ELSE 0 END))
                                /
                                SUM(CASE WHEN invoice_date BETWEEN "' . $lastlastyearstartdate . '" AND "' . $lastlastyearenddate . '" THEN net_amount ELSE 0 END) 
                            ) * 100, 2
                        ) 
                END as golyl
                '),
                DB::raw('
                        CASE 
                    WHEN SUM(CASE WHEN invoice_date BETWEEN "' . $slastlastyearstartdate . '" AND "' . $slastlastyearenddate . '" THEN net_amount ELSE 0 END) = 0 
                    THEN "LYNS"
                    ELSE 
                        ROUND(
                            (
                                (SUM(CASE WHEN invoice_date BETWEEN "' . $lastlastyearstartdate . '" AND "' . $lastlastyearenddate . '" THEN net_amount ELSE 0 END) -
                                SUM(CASE WHEN invoice_date BETWEEN "' . $slastlastyearstartdate . '" AND "' . $slastlastyearenddate . '" THEN net_amount ELSE 0 END))
                                /
                                SUM(CASE WHEN invoice_date BETWEEN "' . $slastlastyearstartdate . '" AND "' . $slastlastyearenddate . '" THEN net_amount ELSE 0 END) 
                            ) * 100, 2
                        ) 
                END as golysl
                '),
            ])
            ->groupBy('primary_sales.month', 'primary_sales.division')
            ->get();



        if ($data->isEmpty()) {
            $this->info('No users found.');
            return;
        }

        $data->chunk(200)->each(function ($chunk) use ($url) {
            $payload = ['users_goly' => $chunk->toArray()];
            $response = Http::timeout(240)->post($url, $payload);

            if ($response->successful()) {
                $this->info(count($chunk) . ' records sent successfully.');
            } else {
                $this->error('Failed to send sales data: ' . $response->body());
            }
        });

        $this->info('Message sent to all users.');
    }
}
