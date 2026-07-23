<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gamification;
use App\Models\Customers;
use App\Models\CheckIn;
use App\Models\Order;
class EveryNight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'night:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Gamification daily At 23:00';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = array() ;
        $customers = Customers::whereDate('created_at', date('Y-m-d'))->whereNotNull('created_by')->select('id','created_by','customertype')->get();
        $checkins = CheckIn::with('customers:id,customertype')->whereDate('checkin_date', date('Y-m-d'))->whereNotNull('user_id')->select('customer_id','user_id')->get();
        $orders = Order::whereDate('order_date', date('Y-m-d'))->whereNotNull('buyer_id')->select('buyer_id','created_by')->get();
        foreach ($customers as $key => $customer) {
            $type = '';
            $points = 0;
            switch ($customer['customertype']) {
                case 2:
                    $type = 'DEALER_CREATE';
                    $points = env('GAM_DEALER_CREATE_POINTS', 0);
                    break;
                case 4:
                    $type = 'MECHANIC_CREATE';
                    $points = env('GAM_MECHANIC_CREATE_POINTS', 0);
                    break;
                default:
                    $type = 'OTHER_CUSTOMER_CREATE';
                    $points = env('GAM_OTHER_CUSTOMER_CREATE_POINTS', 0);
                    break;
            }
            $rows = array('user_id' => $customer['created_by'], 'customer_id' => $customer['id'], 'type' => $type,'points' => $points, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d'));
            array_push($data, $rows);
        }
        foreach ($checkins as $key => $checkin) {
            $type = '';
            $points = 0;
            switch ($checkin['customers']['customertype']) {
                case 2:
                    $type = 'DEALER_VISIT';
                    $points = env('GAM_DEALER_VISIT_POINTS', 0);
                    break;
                case 4:
                    $type = 'MECHANIC_VISIT';
                    $points = env('GAM_MECHANIC_VISIT_POINTS', 0);
                    break;
                default:
                    $type = 'OTHER_VISIT';
                    $points = env('GAM_OTHER_VISIT_POINTS', 0);
                    break;
            }
            $row2 = array('user_id' => $checkin['user_id'], 'customer_id' => $checkin['customer_id'], 'type' => $type,'points' => $points, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d'));
            array_push($data, $row2);
        }
        foreach ($orders as $key => $order) {
            $type = 'NEW_ORDER';
            $points = env('GAM_NEW_ORDER_POINTS', 0);
            $row2 = array('user_id' => $order['created_by'], 'customer_id' => $order['buyer_id'], 'type' => $type,'points' => $points, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d'));
            array_push($data, $row2);
        }
        Gamification::insert($data);
        return Command::SUCCESS;
    }
}
