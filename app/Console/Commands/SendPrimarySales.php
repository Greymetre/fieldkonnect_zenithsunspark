<?php

namespace App\Console\Commands;

use App\Models\City;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\PrimarySales;
use Illuminate\Support\Facades\Log;

class SendPrimarySales extends Command
{
    protected $signature = 'sales:send-primary';
    protected $description = 'Send primary sales data to external API';

    public function handle()
    {
        $geturl = "https://dashboard.fieldkonnect.io/power-bi/public/api/getPrimarySalesLastId";
        $getresponse = Http::timeout(240)->get($geturl);
        Log::channel('sapstock')->error("All primary sales data processed started.");


        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertPrimarySales";

        $salesData = PrimarySales::with('customer:id')->select(
            'id as main_id',
            'invoiceno',
            'invoice_date',
            'month',
            'division',
            'dealer',
            'customer_id',
            'new_dealer',
            'city',
            'state',
            'final_branch',
            'sales_person',
            'emp_code',
            'model_name',
            'product_name',
            'quantity',
            'rate',
            'net_amount',
            'total_amount',
            'group_name',
            'new_group',
            'branch',
            'created_at',
            'updated_at'
        )
            ->orderBy('id', 'desc')
            ->get();

        $salesData->chunk(200)->each(function ($chunk) use ($url) {
            foreach ($chunk as $key => $value) {
                $chunk[$key]['districts'] = $value?->customer?->customeraddress?->districtname?->district_name ?? null;
            }
            $payload = ['sales' => $chunk->toArray()];
            $response = Http::timeout(240)->post($url, $payload);

            if ($response->successful()) {
                $this->info(count($chunk) . ' records sent successfully.');
            } else {
                $this->error('Failed to send sales data: ' . $response->body());
            }
        });

        Log::channel('sapstock')->error("All primary sales data processed completed.");

        $this->info('All primary sales data processed.');
    }
}
