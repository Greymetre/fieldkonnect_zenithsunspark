<?php

namespace App\Console\Commands;

use App\Models\Customers;
use App\Models\PrimarySales;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendCutomerWithDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:customer-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Cutomer With Details';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertCustomerDetails";

        Customers::whereHas('getemployeedetail')
            ->whereIn('customertype', ['1', '2', '3'])
            ->select('id', 'name', 'customertype')
            ->chunk(5000, function ($customers) use ($url) {

                // Fetch sales data in bulk to avoid multiple queries in the loop
                $salesData = PrimarySales::whereBetween('invoice_date', ['2024-04-01', '2025-03-31'])
                    ->whereIn('customer_id', $customers->pluck('id'))
                    ->groupBy('customer_id')
                    ->selectRaw('customer_id, ROUND(SUM(net_amount) / 100000, 2) as total_sales')
                    ->pluck('total_sales', 'customer_id'); // Get an associative array [customer_id => total_sales]

                // Fetch related customer details in a single query
                $customerData = Customers::with([
                    'customertypes:id,customertype_name',
                    'getemployeedetail' => function ($query) {
                        $query->select('customer_id', 'user_id')->with([
                            'employee_detail' => function ($query) {
                                $query->select('id', 'branch_id', 'division_id')
                                    ->with([
                                        'getdivision:id,division_name',
                                        'getbranch:id,branch_name'
                                    ]);
                            }
                        ]);
                    }
                ])->whereIn('id', $customers->pluck('id'))->get();

                // Map data efficiently
                $formattedCustomers = $customerData->map(function ($customer) use ($salesData) {
                    return [
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'customer_type' => $customer->customertypes->customertype_name ?? null,
                        'division' => optional(optional($customer->getemployeedetail->first())->employee_detail)->getdivision->division_name ?? null,
                        'branch' => optional(optional($customer->getemployeedetail->first())->employee_detail)->getbranch->branch_name ?? null,
                        'total_sales' => $salesData[$customer->id] ?? 0.00,
                    ];
                });

                // Send data in chunks
                $formattedCustomers->chunk(200)->each(function ($chunk) use ($url) {
                    $payload = ['customer_details' => $chunk->toArray()];
                    $response = Http::timeout(240)->post($url, $payload);

                    if ($response->successful()) {
                        $this->info(count($chunk) . ' records sent successfully.');
                    } else {
                        $this->error('Failed to send sales data: ' . $response->body());
                    }
                });
            });

        $this->info('All customer details sent successfully.');
    }
}
