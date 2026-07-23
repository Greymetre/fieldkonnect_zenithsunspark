<?php

namespace App\Http\Controllers;

use App\DataTables\VisitorDataTable;
use App\Exports\PrimarySalesExport;
use Illuminate\Http\Request;
use App\Models\{User, Customers, Order, Branch, Division, CheckIn, BeatSchedule, Sales, SalesTarget, OrderDetails, TourProgramme, Wallet, Product, UserActivity, UserCityAssign, Address, TourDetail, TransactionHistory, EmployeeDetail, SalesTargetUsers, Attendance, DealerPortalSettings, ParentDetail, VisitReport, PrimarySales, Redemption};
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Imports\PrimarySalesImport;
use DataTables;
use Validator;
use Gate;
use Carbon\Carbon;
use App\Exports\PrimarySalesTemplate;
use Excel;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->DAILY_VISIT_TARGET = 15;
    }
    public function index(Request $request)
    {
        abort_if(Gate::denies('dashboard_access'), Response::HTTP_FORBIDDEN, 'Forbidden,' . PHP_EOL . 'You don\'t have the right permissions. Please contact to the admin.');
        return view('dashboard.index');
        $users_ids = getUsersReportingToAuth();
        // $users= User::where('active','=','Y')->whereIn('reportingid', $users_ids)->select('id','name')->get();
        $branches = Branch::latest()->get();
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);
        $retailers = Customers::where('customertype', '2')->get();
        $sales_persons = User::latest()->get();
        $uniqueProductsNewGroup = Product::latest()->get()->unique('new_group');
        $dealers_and_distibutors = Customers::where('customertype', [3, 4])->get();
        $users = User::latest()->get();
        $products = Product::latest()->get()->unique('model_no');

        $ps_branches = PrimarySales::select('final_branch')->distinct()->get();
        $ps_divisions = PrimarySales::select('division')->distinct()->get();
        $ps_months = PrimarySales::select('month')->distinct()->get();
        $ps_dealers = PrimarySales::select('dealer')->distinct()->get();
        $ps_new_group_names = PrimarySales::select('new_group')->distinct()->get();
        $ps_product_models = PrimarySales::select('product_name')->distinct()->get();
        $ps_sales_persons = PrimarySales::select('sales_person')->distinct()->get();
        // dd('sssssssssssss');

        $userData = TransactionHistory::select(
            DB::raw('YEAR(max_date) as year'),
            DB::raw('MONTH(max_date) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->from(DB::raw("(SELECT MAX(created_at) as max_date, YEAR(created_at) as year, MONTH(created_at) as month, customer_id 
                        FROM transaction_histories 
                        GROUP BY year, month, customer_id) as temp_table"))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        // dd($userData)    ;


        $labels = [];
        $data = [];

        foreach ($userData as $user) {
            $month = date('F', mktime(0, 0, 0, $user->month, 1)); // Convert month number to month name
            $labels[] = "$month $user->year";
            $data[] = $user->count;
        }

        $userData2 = TransactionHistory::select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(point) as count'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels2 = [];
        $data2 = [];

        foreach ($userData2 as $user) {
            $month = date('F', mktime(0, 0, 0, $user->month, 1)); // Convert month number to month name
            $labels2[] = "$month $user->year";
            $data2[] = $user->count;
        }

        $userData3 = Redemption::select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(redeem_amount) as count'))
            ->where('status', '!=', '2')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels3 = [];
        $data3 = [];

        foreach ($userData3 as $user) {
            $month = date('F', mktime(0, 0, 0, $user->month, 1)); // Convert month number to month name
            $labels3[] = "$month $user->year";
            $data3[] = $user->count;
        }

        $total_qty = PrimarySales::sum('quantity');
        $total_sale = PrimarySales::sum('net_amount');

        $dealer_poster_setting = DealerPortalSettings::first();

        return view('dashboard.index', compact('users', 'branches', 'divisions', 'years', 'sales_persons', 'retailers', 'dealers_and_distibutors', 'products', 'uniqueProductsNewGroup', 'ps_branches', 'ps_divisions', 'ps_months', 'ps_dealers', 'ps_product_models', 'ps_new_group_names', 'ps_sales_persons', 'labels', 'data', 'labels2', 'data2', 'labels3', 'data3', 'total_qty', 'total_sale', 'dealer_poster_setting'));
    }

    public function dashboardData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);

        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }
        /*============= Query Customers ====================*/
        $totalcustomers = Customers::where(function ($query) use ($users) {
            $query->whereIn('created_by', $users);
        })
            ->select('customertype')->get();
        /*============ Query CheckIn ===============*/
        $datacheckin = CheckIn::with('customers')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('checkin_date', '>=', $query_start_date);
            $query->where('checkin_date', '<=', $query_end_date);
            $query->whereIn('user_id', $users);
        })
            ->select('customer_id', 'checkin_date', 'user_id')->get();
        /*============= Query Orders ====================*/
        $dataorders = Order::with('buyers', 'orderdetails')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('order_date', '>=', $query_start_date);
            $query->where('order_date', '<=', $query_end_date);
            $query->whereIn('created_by', $users);
        })
            ->select('id', 'grand_total', 'order_date', 'buyer_id', 'seller_id', 'created_by')
            ->get();
        $datatours = TourProgramme::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('date', '>=', $query_start_date);
            $query->where('date', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->whereIn('type', ['Tour', 'Central Market', 'Suburban'])
            ->select('userid', 'date')->get();
        /*========= KPIs Counts ==============*/
        $tours = $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate);
        $uniquetours = $tours->unique('date')->all();
        $customercount = 0;
        foreach ($uniquetours as $key => $tourdate) {
            $tourcount = $tours->where('date', '=', $tourdate['date'])->count();
            if ($tourcount >= 1) {
                $customercount = $customercount + ($tourcount * $this->DAILY_VISIT_TARGET);
            }
        }
        $data['visittarget'] = $customercount;
        // Visited Counts
        $uniquecheckin = $datacheckin->where('checkin_date', '>=', $fromdate)->where('checkin_date', '<=', $todate);
        $getuniquecheckin = $uniquecheckin->unique('customer_id', 'checkin_date')->values()->all();
        $data['visitedcounter'] = count($getuniquecheckin);
        // Beat Adherance Percentage
        $data['beatadherance'] = ($data['visittarget'] >= 1) ? number_format((float)(($data['visitedcounter'] * 100) / $data['visittarget']), 2, '.', '') : 0;
        // Active Buyer Count
        $data['activebuyercount'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->unique('buyer_id')
            ->count();
        // Beat Productivity 
        $data['beatproductivity'] = ($data['activebuyercount'] >= 1) ? number_format((float)($data['activebuyercount'] / $data['visitedcounter']), 2, '.', '') : 0;
        //Active Dealers Count 
        $data['activedealerscount'] = $dataorders->where('order_date', '>=', $activeStartDate)
            ->where('buyers.customertype', '=', 2)
            ->unique('buyer_id')
            ->count();
        // Active Stockist Count
        $data['activeStockistcount'] = $dataorders->where('order_date', '>=', $activeStartDate)
            ->where('buyers.customertype', '=', 3)
            ->unique('buyer_id')
            ->count();
        //Active Fleet Owner Count
        $data['activeFleetOwnercount'] = $dataorders->where('order_date', '>=', $activeStartDate)
            ->where('buyers.customertype', '=', 6)
            ->unique('buyer_id')
            ->count();
        //Active Mechanics Count
        $data['activeMechanicscount'] = Wallet::whereDate('transaction_at', '>=', $activeStartDate)
            ->whereHas('customers', function ($query) {
                $query->where('customertype', '=', 6);
            })
            ->groupBy('customer_id')
            ->count();

        $data['totaldealerscount'] = $totalcustomers->where('customertype', '=', 2)->count();
        // Active Stockist Count
        $data['totalStockistcount'] = $totalcustomers->where('customertype', '=', 3)->count();
        //Active Fleet Owner Count
        $data['totalFleetOwnercount'] = $totalcustomers->where('customertype', '=', 6)->count();
        //Total Mechanics
        $data['totalMechanicscount'] = $totalcustomers->where('customertype', '=', 4)->count();
        /*============== Monthly Beat Adherence Data ==============*/
        $data['monthbeatAdherence'] = $month_calendar->map(function ($item, $key) use ($datacheckin, $dataorders, $datatours) {
            $tourcount = $datatours->where('date', '=', $item['date'])->count();
            $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;
            $item['counter_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->unique('customer_id', 'checkin_date')
                ->count();
            $item['productive_counter'] = $dataorders->where('order_date', '=', $item['date'])->unique('buyer_id')->count();
            $item['date'] = date('d', strtotime($item['date']));
            unset($item['beatcustomers']);
            return $item;
        });
        /*============== Yearly Beat Adherence Data ==============*/
        $data['yearbeatAdherence'] = $year_calendar->map(function ($item, $key) use ($datacheckin, $dataorders, $datatours) {
            $month_start_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '31'));
            $tourcount = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->count();
            $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;
            $monthscheckins = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date);

            $getuniquecheckin = $monthscheckins->unique('customer_id', 'checkin_date')->values()->all();
            $item['counter_visited'] = count($getuniquecheckin);
            $item['productive_counter'] = $dataorders->where('order_date', '>=', $month_start_date)
                ->where('order_date', '<=', $month_end_date)
                ->unique('buyer_id', 'order_date')
                ->count();
            return $item;
        });

        return response()->json($data);
    }

    public function travelSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-01")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $year_calendar = collect([]);
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }

        /*========== Query Tour =======================*/
        $datatours =  TourProgramme::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('date', '>=', $query_start_date);
            $query->where('date', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->select('id', 'userid', 'date', 'type')
            ->orderBy('date', 'asc')->get();

        $datatourdetails = TourDetail::with('tourinfo')->whereHas('tourinfo', function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('date', '>=', $query_start_date);
            $query->where('date', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->select('tourid', 'visited_cityid')->get();
        // Days Toured Count
        $data['daystouredcount'] = $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type', '=', 'Tour')->count();
        // Days Office Work Count 
        $data['daysOfficeWorkCount'] = $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type', '=', 'Office Work')->count();
        // Days Central Market Count 
        $data['daysCentralMarketcount'] =  $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type', '=', 'Central Market')->count();
        // Days Suburban Count
        $data['daysSuburbancount'] =  $datatours->where('date', '>=', $fromdate)->where('date', '<=', $todate)->where('type', '=', 'Suburban')->count();
        // Cities Covered Count
        $data['citiescoveredcount'] =  $datatourdetails->unique('visited_cityid')->count();
        /*========= Tour Plan ================*/

        $data['yeartours'] = $year_calendar->map(function ($item, $key) use ($datatours) {
            $month_start_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '31'));
            // Days Toured Count
            $item['daystouredcount'] = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type', '=', 'Tour')->count();
            // Days Office Work Count 
            $item['daysOfficeWorkCount'] = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type', '=', 'Office Work')->count();
            // Days Central Market Count 
            $item['daysCentralMarketcount'] =  $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type', '=', 'Central Market')->count();
            // Days Suburban Count
            $item['daysSuburbancount'] =  $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->where('type', '=', 'Suburban')->count();
            return $item;
        });
        return response()->json($data);
    }
    public function visitSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);
        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }
        /*============ Query BeatShedule ===============*/
        $databeatschedule = BeatSchedule::with('beatcustomers', 'beatcustomers.customers')
            ->where(function ($query) use ($query_start_date, $query_end_date, $users) {
                $query->where('beat_date', '>=', $query_start_date);
                $query->where('beat_date', '<=', $query_end_date);
                $query->whereIn('user_id', $users);
            })
            ->select('id', 'beat_id', 'beat_date')->get();
        /*============= Query Customers ====================*/
        $datacustomers = Customers::with('beatdetails')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('created_at', '>=', $query_start_date);
            $query->where('created_at', '<=', $query_end_date);
            $query->whereIn('created_by', $users);
        })
            ->select('id', 'customertype', 'created_at')->get();
        /*============ Query CheckIn ===============*/
        $datacheckin = CheckIn::with('customers')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('checkin_date', '>=', $query_start_date);
            $query->where('checkin_date', '<=', $query_end_date);
            $query->whereIn('user_id', $users);
        })
            ->select('customer_id', 'checkin_date', 'user_id')->get();
        /*============= Query Orders ====================*/
        $dataorders = Order::with('buyers', 'orderdetails')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('order_date', '>=', $query_start_date);
            $query->where('order_date', '<=', $query_end_date);
            $query->whereIn('created_by', $users);
        })
            ->select('id', 'grand_total', 'order_date', 'buyer_id', 'seller_id', 'created_by')
            ->get();
        $datatours = TourProgramme::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('date', '>=', $query_start_date);
            $query->where('date', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->where('type', '=', 'Tour')
            ->select('userid', 'date')->get();
        // Customer Registerd Count                 
        $data['newSTUsregisteredcount'] =  Customers::whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)
            ->whereIn('created_by', $users)
            ->where('customertype', '=', '5')
            ->count();
        $data['newFleetOwnercount'] =  Customers::whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)
            ->whereIn('created_by', $users)
            ->where('customertype', '=', '6')
            ->count();
        $data['newMechanicregisteredcount'] =  Customers::whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)
            ->whereIn('created_by', $users)
            ->where('customertype', '=', 4)
            ->count();
        $data['newDealerRegisteredcount'] =  Customers::whereDate('created_at', '>=', $fromdate)
            ->whereDate('created_at', '<=', $todate)
            ->whereIn('created_by', $users)
            ->where('customertype', '=', '2')
            ->count();
        // Customer Visited Count  
        $data['visitedstuscount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
            ->where('checkin_date', '<=', $todate)
            ->where('customers.customertype', '=', 5)
            ->unique('customer_id', 'checkin_date')
            ->count();
        $data['visitedFleetOwnercount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
            ->where('checkin_date', '<=', $todate)
            ->where('customers.customertype', '=', 6)
            ->unique('customer_id', 'checkin_date')
            ->count();
        $data['visitedMechanicCount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
            ->where('checkin_date', '<=', $todate)
            ->where('customers.customertype', '=', 4)
            ->unique('customer_id', 'checkin_date')
            ->count();
        $data['visitedDealerCount'] = $datacheckin->where('checkin_date', '>=', $fromdate)
            ->where('checkin_date', '<=', $todate)
            ->where('customers.customertype', '=', 2)
            ->unique('customer_id', 'checkin_date')
            ->count();

        /*========= Counter Data ================*/
        $data['month_created_data'] = $month_calendar->map(function ($item, $key) use ($datacustomers, $users) {
            $item['dealer_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '2')
                ->count();
            $item['stus_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '5')
                ->count();
            $item['mechanic_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '4')
                ->count();
            $item['fleet_owner_registered'] = Customers::whereDate('created_at', '=', $item['date'])
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '6')
                ->count();
            $item['label'] = date('d', strtotime($item['date']));
            return $item;
        });
        $data['year_created_data'] = $year_calendar->map(function ($item, $key) use ($datacustomers, $users) {
            $month_start_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '31'));

            $item['dealer_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                ->whereDate('created_at', '<=', $month_end_date)
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '2')
                ->count();
            $item['stus_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                ->whereDate('created_at', '<=', $month_end_date)
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '5')
                ->count();
            $item['mechanic_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                ->whereDate('created_at', '<=', $month_end_date)
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '4')
                ->count();
            $item['fleet_owner_registered'] = Customers::whereDate('created_at', '>=', $month_start_date)
                ->whereDate('created_at', '<=', $month_end_date)
                ->whereIn('created_by', $users)
                ->where('customertype', '=', '6')
                ->count();
            return $item;
        });
        /*============== Beat Adherence Data ==============*/
        $data['monthbeatAdherence'] = $month_calendar->map(function ($item, $key) use ($databeatschedule, $datacheckin, $dataorders, $datacustomers, $datatours) {
            $tourcount = $datatours->where('date', '=', $item['date'])->count();
            $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;
            $item['counter_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->unique('customer_id', 'checkin_date')->count();
            $item['dealer_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->where('customers.customertype', '=', 2)
                ->unique('customer_id', 'checkin_date')
                ->count();
            $item['stus_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->where('customers.customertype', '=', 5)
                ->unique('customer_id', 'checkin_date')
                ->count();
            $item['mechanic_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->where('customers.customertype', '=', 4)
                ->unique('customer_id', 'checkin_date')
                ->count();
            $item['fleet_owner_visited'] = $datacheckin->where('checkin_date', '=', $item['date'])
                ->where('customers.customertype', '=', 6)
                ->unique('customer_id', 'checkin_date')
                ->count();
            $item['productive_counter'] = $dataorders->where('order_date', '=', $item['date'])
                ->unique('buyer_id', 'order_date')->count();
            $item['date'] = date('d', strtotime($item['date']));
            unset($item['beatcustomers']);
            return $item;
        });

        $data['yearbeatAdherence'] = $year_calendar->map(function ($item, $key) use ($databeatschedule, $datacheckin, $dataorders, $datacustomers, $datatours) {
            $month_start_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '31'));
            $tourcount = $datatours->where('date', '>=', $month_start_date)->where('date', '<=', $month_end_date)->count();
            $item['visit_target'] = $tourcount * $this->DAILY_VISIT_TARGET;

            $item['counter_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date)
                ->unique('checkin_date', 'customer_id')
                ->count();
            $item['productive_counter'] = $dataorders->where('order_date', '>=', $month_start_date)
                ->where('order_date', '<=', $month_end_date)
                ->unique('order_date', 'buyer_id')
                ->count();
            $item['dealer_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date)
                ->where('customers.customertype', '=', 2)
                ->unique('checkin_date', 'customer_id')
                ->count();
            $item['stus_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date)
                ->where('customers.customertype', '=', 5)
                ->unique('checkin_date', 'customer_id')
                ->count();
            $item['mechanic_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date)
                ->where('customers.customertype', '=', 4)
                ->unique('checkin_date', 'customer_id')
                ->count();
            $item['fleet_owner_visited'] = $datacheckin->where('checkin_date', '>=', $month_start_date)
                ->where('checkin_date', '<=', $month_end_date)
                ->where('customers.customertype', '=', 6)
                ->unique('checkin_date', 'customer_id')
                ->count();
            return $item;
        });
        return response()->json($data);
    }
    public function couponSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        /*=========== Query Wallet ======================*/
        $datawallets = Wallet::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('transaction_at', '>=', $query_start_date);
            $query->where('transaction_at', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->select('point_type', 'points', 'quantity', 'transaction_at', 'transaction_type')->get();
        /*=========== Points Counts ======================*/
        $data['couponsCollectedValue'] =  $datawallets->where('point_type', '=', 'coupon')
            ->where('transaction_at', '>=', $fromdate)
            ->where('transaction_at', '<=', $todate)
            ->sum('quantity');
        $data['couponsCollectedPoints'] =  $datawallets->where('point_type', '=', 'coupon')
            ->where('transaction_at', '>=', $fromdate)
            ->where('transaction_at', '<=', $todate)
            ->sum('points');
        $data['mrpCollectedValue'] =  $datawallets->where('point_type', '=', 'mrp')
            ->where('transaction_at', '>=', $fromdate)
            ->where('transaction_at', '<=', $todate)
            ->sum('quantity');
        $data['mrpCollectedPoints'] =  $datawallets->where('point_type', '=', 'mrp')
            ->where('transaction_at', '>=', $fromdate)
            ->where('transaction_at', '<=', $todate)
            ->sum('points');
        /*========= Coupons Summary ==========*/
        $monthsumpoints = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->sum('points');
        $monthsumquantity = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->sum('quantity');
        $yearsumpoints = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->sum('points');
        $yearsumquantity = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->sum('quantity');
        $month_coupon_point = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'coupon')
            ->sum('points');
        $month_mrp_point = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'mrp')
            ->sum('points');
        $month_coupon_quantity = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'coupon')
            ->sum('quantity');
        $month_mrp_quantity = $datawallets->where('transaction_at', '>=', $monthStartDate)
            ->where('transaction_at', '<=', $monthEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'mrp')
            ->sum('points');
        $year_coupon_point = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'coupon')
            ->sum('points');
        $year_mrp_point = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'mrp')
            ->sum('points');
        $year_coupon_quantity = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'coupon')
            ->sum('quantity');
        $year_mrp_quantity = $datawallets->where('transaction_at', '>=', $yearStartDate)
            ->where('transaction_at', '<=', $yearEndDate)
            ->where('transaction_type', '=', 'Cr')
            ->where('point_type', '=', 'mrp')
            ->sum('quantity');
        $data['coupon_summary_chart'] = collect([
            'month_coupon_point' => ($monthsumpoints >= 1 && $month_coupon_point >= 1) ? round(($month_coupon_point * 100) / $monthsumpoints) : 0,
            'month_mrp_point' => ($monthsumpoints >= 1 && $month_mrp_point >= 1) ? round(($month_mrp_point * 100) / $monthsumpoints) : 0,
            'month_coupon_quantity' => ($monthsumquantity >= 1 && $month_coupon_quantity >= 1) ? round(($month_coupon_quantity * 100) / $monthsumquantity) : 0,
            'month_mrp_quantity' => ($monthsumquantity >= 1 && $month_mrp_quantity >= 1) ? round(($month_mrp_quantity * 100) / $monthsumquantity) : 0,
            'year_coupon_point' => ($yearsumpoints >= 1 && $year_coupon_point >= 1) ? round(($year_coupon_point * 100) / $yearsumpoints) : 0,
            'year_mrp_point' => ($yearsumpoints >= 1 && $year_mrp_point >= 1) ? round(($year_mrp_point * 100) / $yearsumpoints) : 0,
            'year_coupon_quantity' => ($yearsumquantity >= 1 && $year_coupon_quantity >= 1) ? round(($year_coupon_quantity * 100) / $yearsumquantity) : 0,
            'year_mrp_quantity' => ($yearsumquantity >= 1 && $year_mrp_quantity >= 1) ? round(($year_mrp_quantity * 100) / $yearsumquantity) : 0,
        ]);
        return response()->json($data);
    }
    public function orderSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);
        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }
        /*============= Query Orders ====================*/
        $dataorders = Order::with('buyers', 'orderdetails')->where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('order_date', '>=', $query_start_date);
            $query->where('order_date', '<=', $query_end_date);
            $query->whereIn('created_by', $users);
        })
            ->select('id', 'grand_total', 'order_date', 'buyer_id', 'seller_id', 'created_by')->get();
        $datatargets = SalesTarget::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('startdate', '>=', $query_start_date);
            $query->where('enddate', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->select('startdate', 'enddate', 'amount')->get();
        /*============= Query Sales ====================*/
        $datasales = Sales::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('invoice_date', '>=', $query_start_date);
            $query->where('invoice_date', '<=', $query_end_date);
            $query->whereIn('created_by', $users);
        })
            ->select('id', 'grand_total', 'invoice_date', 'buyer_id', 'seller_id', 'created_by')->get();
        /*============= Orders Count ====================*/
        $data['orderCollectedCount'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->count();
        $data['orderCollectedSum'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->sum('grand_total');
        $ggplids = Product::where('category_id', '=', '1')->pluck('id')->toArray();
        $gpdids = Product::where('category_id', '=', '2')->pluck('id')->toArray();
        $gdplids = Product::where('category_id', '=', '3')->pluck('id')->toArray();
        $data['GGPLorderCollectedCount'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->whereIn('orderdetails.product_id', $ggplids)
            ->count();
        $data['GGPLorderCollectedSum'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->whereIn('orderdetails.product_id', $ggplids)
            ->sum('grand_total');
        $data['GPDorderCollectedCount'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->whereIn('orderdetails.product_id', $gpdids)
            ->count();
        $data['GPDorderCollectedSum'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->whereIn('orderdetails.product_id', $gpdids)
            ->sum('grand_total');
        $data['GDGLorderCollectedCount'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->whereIn('orderdetails.product_id', $gdplids)
            ->count();
        $data['GDGLorderCollectedSum'] = $dataorders->where('order_date', '>=', $fromdate)
            ->where('order_date', '<=', $todate)
            ->whereIn('orderdetails.product_id', $gdplids)
            ->sum('grand_total');
        /*========= Order Summary =============*/
        $data['top_products'] = OrderDetails::with('products')
            ->whereHas('orders', function ($query) {
                $query->whereYear('order_date', '=', date('Y'));
            })
            ->select(['product_id', DB::raw("SUM(price) as total_price"), DB::raw("SUM(quantity) as total_quantity")])
            ->groupBy('product_id')
            ->orderBy('total_price')
            ->limit(10)
            ->get();
        $data['yeartargetachievement'] = $year_calendar->map(function ($item, $key) use ($dataorders, $datasales, $datatargets) {
            $month_start_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '01'));
            $month_end_date = date('Y-m-d', strtotime($item['year'] . '-' . $item['month'] . '-' . '31'));
            $item['achievement'] = $dataorders->where('order_date', '>=', $month_start_date)
                ->where('order_date', '<=', $month_end_date)
                ->sum('grand_total');
            $item['salesachievement'] = $datasales->where('invoice_date', '>=', $month_start_date)
                ->where('invoice_date', '<=', $month_end_date)
                ->sum('grand_total');
            $item['target'] = $datatargets->where('startdate', '>=', $month_start_date)
                ->where('startdate', '<=', $month_end_date)
                ->where('enddate', '>=', $month_start_date)
                ->where('enddate', '<=', $month_end_date)
                ->sum('amount');
            return $item;
        });
        $data['top_representatives'] = Order::with('createdbyname')
            ->whereYear('order_date', '=', date('Y'))
            ->select(['created_by', DB::raw("SUM(grand_total) as total_amount"), DB::raw("SUM(total_qty) as total_quantity")])
            ->groupBy('created_by')
            ->orderBy('total_amount')
            ->limit(10)
            ->get();
        $userscities = UserCityAssign::with('userinfo')->select('userid')->groupBy('userid')->get();
        $data['orders_by_zone'] = $userscities->map(function ($item, $key) {
            $item['zone_name'] =  $item['userinfo']['location'];
            $citiesids = UserCityAssign::where('userid', '=', $item['userid'])->pluck('city_id')->toArray();

            $item['order_amount'] = Order::whereHas('customeraddress', function ($query) use ($citiesids) {
                $query->whereYear('order_date', '=', date('Y'));
                $query->whereIn('city_id', $citiesids);
            })->sum('grand_total');
            $item['sales_amount'] = Sales::whereHas('customeraddress', function ($query) use ($citiesids) {
                $query->whereYear('invoice_date', '=', date('Y'));
                $query->whereIn('city_id', $citiesids);
            })->sum('grand_total');
            return $item;
        });
        $statesname = Address::with('statename')->whereNotNull('customer_id')->whereNotNull('state_id')->select('state_id')->groupBy('state_id')->get();
        $data['orders_by_state'] = $statesname->map(function ($item, $key) {
            $item['state_name'] =  $item['statename']['state_name'];
            $item['order_amount'] = Order::whereHas('customeraddress', function ($query) use ($item) {
                $query->whereYear('order_date', '=', date('Y'));
                $query->where('state_id', $item['state_id']);
            })->sum('grand_total');
            $item['sales_amount'] = Sales::whereHas('customeraddress', function ($query) use ($item) {
                $query->whereYear('invoice_date', '=', date('Y'));
                $query->where('state_id', $item['state_id']);
            })->sum('grand_total');
            unset($item['statename']);
            return $item;
        });
        return response()->json($data);
    }
    public function salesSummaryData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $monthStartDate = date('Y-m-d', strtotime(date("Y-m-01")));
        $monthEndDate = date('Y-m-d', strtotime(date("Y-m-d")));
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);
        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }
        /*=========== Query SalesTarget ======================*/
        $datatargets = SalesTarget::where(function ($query) use ($query_start_date, $query_end_date, $users) {
            $query->where('startdate', '>=', $query_start_date);
            $query->where('enddate', '<=', $query_end_date);
            $query->whereIn('userid', $users);
        })
            ->select('startdate', 'enddate', 'amount');
        $data['top_sales_representatives'] = Sales::with('createdbyname')
            ->whereYear('invoice_date', '=', date('Y'))
            ->select(['created_by', DB::raw("SUM(grand_total) as total_amount"), DB::raw("SUM(total_qty) as total_quantity")])
            ->groupBy('created_by')
            ->orderBy('total_amount')
            ->limit(10)
            ->get();
        return response()->json($data);
    }
    public function activityDashboardCount(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $users = getUsersReportingToAuth($userid);
        /*========= User Activities ==============*/
        $data['activities'] = UserActivity::with('customers', 'users')
            ->where(function ($query) use ($fromdate, $todate, $users) {
                if (!empty($fromdate)) {
                    $query->whereDate('time', '>=', date('Y-m-d', strtotime($fromdate)));
                }
                if (!empty($todate)) {
                    $query->whereDate('time', '<=', date('Y-m-d', strtotime($todate)));
                }
                $query->whereIn('userid', $users);
            })
            ->select('id', 'userid', 'customerid', 'address', 'description', 'type', 'time')
            ->latest()
            ->get();

        return response()->json($data);
    }

    public function secondary_dashboard_sales(Request $request)
    {
        $retailers = Customers::where('customertype', '2')->get();
        $dealers_and_distibutors = Customers::where('customertype', [3, 4])->get();
        $sales_persons = User::latest()->get();
        $products = Product::latest()->get();;
        $users = User::latest()->get();
        $branches = Branch::latest()->get();
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);

        $orders = Order::with(['buyers', 'orderdetails', 'getuserdetails', 'getsalesdetail'])->get();

        abort_if(Gate::denies('dashboard_secondary_sales_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('secondary_dashboard.index', compact('branches', 'years', 'sales_persons', 'divisions', 'retailers', 'dealers_and_distibutors', 'products'));
    }

    public function secondary_dashboard_sales_list(Request $request)
    {

        $query = OrderDetails::with(['orders', 'orders.sellers', 'orders.buyers', 'orders.createdbyname', 'orders.getuserdetails.getdivision', 'orders.buyers.customeraddress.cityname', 'orders.buyers.customeraddress.statename', 'products', 'products.productpriceinfo', 'orders.getuserdetails.getbranch', 'orders.sellers.customeraddress.cityname', 'orders.createdbyname.getbranch', 'orders.createdbyname.getbranch'])->where(function ($query) use ($request) {
            $query->whereHas('orders', function ($q) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $userids = getUsersReportingToAuth();
                    $customer_ids = Customers::whereIn('executive_id', $userids)->orWhereIn('created_by', $userids)->pluck('id');
                    $q->whereIn('buyer_id', $customer_ids)
                        ->orWhereIn('seller_id', $customer_ids);
                }
            });

            if ($request->month && $request->month != '' && $request->month != null && $request->financial_year && $request->financial_year != '' && $request->financial_year != null) {

                $f_year_array = explode('-', $request->financial_year);

                if ($request->month == 'Jan' || $request->month == 'Feb' || $request->month == 'Mar') {
                    $currentYear = $f_year_array[1];
                    $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$request->month")->startOfMonth();
                    $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$request->month")->endOfMonth();
                    $startDateFormatted = $startDate->toDateString();
                    $endDateFormatted = $endDate->toDateString();
                } else {
                    $currentYear = $f_year_array[0];
                    $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$request->month")->startOfMonth();
                    $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$request->month")->endOfMonth();
                    $startDateFormatted = $startDate->toDateString();
                    $endDateFormatted = $endDate->toDateString();
                }

                $query->whereHas('orders', function ($q) use ($startDateFormatted, $endDateFormatted) {
                    $q->whereDate('order_date', '>=', $startDateFormatted)
                        ->whereDate('order_date', '<=', $endDateFormatted);
                });
            }


            if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                $branchIds = User::where('branch_id', $request->branch_id)->pluck('id');

                $query->whereHas('orders', function ($q) use ($branchIds) {
                    $q->whereIn('orders.created_by', $branchIds);
                });
            }

            if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
                $dealerIds = Customers::where('id', $request->dealer_id)->pluck('id');

                $query->whereHas('orders', function ($q) use ($dealerIds) {
                    $q->whereIn('orders.seller_id', $dealerIds);
                });
            }

            // if($request->user_id && $request->user_id != '' && $request->user_id != null){
            //     $userIds = User::where('id', $request->user_id)->pluck('id');
            //     $query->whereIn('se',$userIds);
            // }

            if ($request->executive_id && $request->executive_id != '' && $request->executive_id != null) {
                $executiveIds = User::where('id', $request->executive_id)->pluck('id');

                $query->whereHas('orders', function ($q) use ($executiveIds) {
                    $q->whereIn('orders.created_by', $executiveIds);
                });
            }

            if ($request->retailer_id && $request->retailer_id != '' && $request->retailer_id != null) {
                $buyerIds = User::where('id', $request->retailer_id)->pluck('id');

                $query->whereHas('orders', function ($q) use ($buyerIds) {
                    $q->whereIn('orders.buyer_id', $buyerIds);
                });
                // $query->whereIn('buyer_id',$buyerIds);
            }

            if ($request->division_id && $request->division_id != '' && $request->division_id != null) {
                $userIds = User::where('division_id', $request->division_id)->pluck('id')->toArray();

                $query->whereHas('orders', function ($q) use ($userIds) {
                    $q->whereIn('orders.created_by', $userIds);
                });
            }

            if ($request->product_model && $request->product_model != '' && $request->product_model != null) {
                $productIds = Product::where('id', $request->product_model)->pluck('id')->toArray();
                $query->whereIn('product_id', $productIds);
            }

            if ($request->new_group && $request->new_group != '') {
                $productIds = Product::where('id', $request->new_group)->pluck('id')->toArray();
                $query->whereIn('product_id', $productIds);
            }

            if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {

                $f_year_array = explode('-', $request->financial_year);
                $financial_year_start = $f_year_array[0] . '-04-01';
                $financial_year_end = $f_year_array[1] . '-03-31';

                $query->whereHas('orders', function ($q) use ($financial_year_start, $financial_year_end) {
                    $q->where('order_date', '>=', $financial_year_start)
                        ->where('order_date', '<=', $financial_year_end);
                });
            }

            if ($request->min_range && $request->min_range != '' && $request->min_range != null && $request->max_range && $request->max_range != '' && $request->max_range != null) {
                $query->whereBetween('points', [$request->min_range, $request->max_range]);
            }
        })->orderBy('id', 'asc');

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('order_date', function ($query) {
                return date('d/m/Y', strtotime($query->orders->order_date)) ?? '';
            })
            ->addColumn('month', function ($query) {
                return date('M Y', strtotime($query->orders->order_date)) ?? '';
            })
            ->addColumn('total_qty', function ($query) {
                return $query->sum('quantity') ?? '';
            })
            ->addColumn('total', function ($query) {
                return $query->products->productpriceinfo->gst ?? '';
            })
            ->addColumn('gst_amount', function ($query) {
                $tax = $query->products->productpriceinfo->gst;
                $new_amount = $query->line_total;

                $gst = number_format((float)(($query->line_total * ($tax / 100))), 2, '.', '');
                // $gst = ($query->line_total* ($tax/100));

                return $gst;
            })
            ->addColumn('total_amount', function ($query) {
                $net_amount = $query->line_total;
                $gst_amount = $query->line_total * ($query->products->productpriceinfo->gst / 100);

                $total_amount = number_format((float)(($net_amount + $gst_amount)), 2, '.', '');

                return $total_amount;
            })
            ->rawColumns(['order_date', 'month', 'total', 'gst_amount', 'total_amount'])
            ->make(true);
    }

    public function secondarySalesKpiData(Request $request)
    {
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);

        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }

        // $orderDetails = OrderDetails::with(['orders','products','productdetails','orders.buyers'])->get();

        $orderDetails = OrderDetails::with(['orders', 'orders.sellers', 'orders.buyers', 'orders.createdbyname', 'orders.getuserdetails.getdivision', 'orders.buyers.customeraddress.cityname', 'orders.buyers.customeraddress.statename', 'products', 'products.productpriceinfo', 'orders.getuserdetails.getbranch', 'orders.sellers.customeraddress.cityname', 'orders.createdbyname.getbranch']);

        if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
            $usersIds = User::where('id', $request->user_id)->where('sales_type', 'Secondary')->pluck('id');
        } else {
            $usersIds = User::with('attendance_details')->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
            $usersIds = User::where('branch_id', $request->branch_id)->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->division_id && $request->division_id != '' && $request->division_id != null) {
            $usersIds = User::where('division_id', $request->division_id)->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $orderDetails->whereHas('orders', function ($query) use ($financial_year_end) {
                $query->where('order_date', '>=', $financial_year_start)
                    ->where('order_date', '<=', $financial_year_end);
            });
        }


        if ($request->month && $request->month != '' && $request->month != null) {
            $orderDetails->where('month', $request->month);
        }

        // user with secondary sales type
        $employeeIds = EmployeeDetail::whereIn('user_id', $usersIds)->pluck('customer_id');
        $customerIds = Customers::whereIn('id', $employeeIds)->pluck('id');

        $registeredRetailerCount = $customerIds->count();

        $activeRetailerCount = $orderDetails->whereHas('orders', function ($query) use ($customerIds) {
            $query->whereIn('buyer_id', $customerIds);
        })->count();

        $activeRetailerPercent = 0;
        if ($registeredRetailerCount > 0) {
            $activeRetailerPercent = number_format((float)(($activeRetailerCount / $registeredRetailerCount) * 100), 2, '.', '');
        }

        $transactionHistory = TransactionHistory::with('customer')->get();

        $nosOfRetailerRegistredSaarthi = $transactionHistory->whereIn('customer_id', $customerIds)->groupBy('customer_id')->count();

        $nosOfRetailerRegistredSaarthiPercent = 0;
        if ($registeredRetailerCount > 0) {
            $nosOfRetailerRegistredSaarthiPercent =  number_format((float)(($nosOfRetailerRegistredSaarthi / $registeredRetailerCount) * 100), 2, '.', '');
        }

        $orderTarget = SalesTargetUsers::where('type', 'secondary')->whereIn('user_id', $customerIds)->sum('target');

        $orderAchievement = $orderDetails->where(function ($query) use ($customerIds) {
            $query->whereHas('orders', function ($subquery) use ($customerIds) {
                $subquery->whereIn('buyer_id', $customerIds);
            });
        })->sum('line_total');

        $achievementPercent = 0;
        if ($orderTarget > 0) {
            $achievementPercent = number_format((float)(($orderAchievement / $orderTarget) * 100), 2, '.', '');
        }

        $attendanceCount = Attendance::whereIn('user_id', $usersIds)->groupBy('user_id')->count();
        $totalUsers = $usersIds->count();

        $avgWorkingDays = 0;
        if ($totalUsers > 0) {
            $avgWorkingDays = number_format((float)($attendanceCount / $totalUsers));
        }

        $totalOrders = $orderDetails->where(function ($query) use ($usersIds) {
            $query->whereHas('orders', function ($subquery) use ($usersIds) {
                $subquery->whereIn('buyer_id', $usersIds);
            });
        })->count();

        $averageVisits = VisitReport::whereIn('user_id', $usersIds)->count();


        $perDayAverageSales = ($avgWorkingDays > 0) ? number_format((float)($totalOrders / $avgWorkingDays)) : '0';
        $perDayAverageVisit = ($averageVisits > 0) ?  number_format((float)($totalOrders / $averageVisits)) : '0';
        $data['registeredRetailerCount'] = $registeredRetailerCount;
        $data['activeRetailerCount'] = $activeRetailerCount;
        $data['activeRetailerPercent'] = $activeRetailerPercent;
        $data['nosOfRetailerRegistredSaarthi'] = $nosOfRetailerRegistredSaarthi;
        $data['nosOfRetailerRegistredSaarthiPercent'] = $nosOfRetailerRegistredSaarthiPercent;

        $data['orderTarget'] = $orderTarget;
        $data['orderAchievement'] = $orderAchievement;
        $data['achievementPercent'] = $achievementPercent;
        $data['perDayAverageSales'] = $perDayAverageSales;
        $data['perDayAverageVisit'] = $perDayAverageVisit;

        return response()->json($data);
    }

    public function primary_sales_template(Request $request)
    {
        abort_if(Gate::denies('primary_sales_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PrimarySalesTemplate, 'primary_sales_template.xlsx');
    }

    public function primary_sales_download(Request $request)
    {
        abort_if(Gate::denies('primary_sales_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PrimarySalesExport($request), 'primary_sales.xlsx');
    }

    public function primary_sales_upload(Request $request)
    {
        abort_if(Gate::denies('primary_sales_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new PrimarySalesImport, request()->file('import_file'));

        return back()->with('success', 'Primary Sales Import successfully !!');
    }

    public function primary_dashboard_sales_list(Request $request)
    {

        $query = PrimarySales::query();

        if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
            $usersIds = User::where('id', $request->user_id)->where('sales_type', 'Secondary')->pluck('id');
        } else {
            $usersIds = User::with('attendance_details')->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
            $query->where('final_branch', $request->branch_id);
        }

        if ($request->division_id && $request->division_id != '' && count($request->division_id) > 0) {
            $query->whereIn('division', $request->division_id);
        }
        $role = Role::find(29);
        if ($role && auth()->user()->hasRole($role->name)) {
            $child_customer = ParentDetail::where('parent_id', auth()->user()->customerid)
                ->pluck('customer_id')
                ->push(auth()->user()->customerid);
            $query->whereIn('customer_id', $child_customer);
        }
        if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
            $query->where('dealer', 'like', '%' . $request->dealer_id . '%');
        }

        if ($request->product_model && $request->product_model != '' && $request->product_model != null) {
            $query->where('product_name', $request->product_model);
        }

        if ($request->new_group && $request->new_group != '' && $request->new_group != null) {
            $query->where('new_group', $request->new_group);
        }

        if ($request->executive_id && $request->executive_id != '' && $request->executive_id != null) {
            $query->where('sales_person', $request->executive_id);
        }

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->where(function ($q) use ($f_year_array, $financial_year_start, $financial_year_end) {
                $q->where('invoice_date', '>=', $financial_year_start)
                    ->where('invoice_date', '<=', $financial_year_end);;
            });
        }

        if ($request->month && $request->month != '' && count($request->month) > 0 && $request->financial_year && $request->financial_year != '' && $request->financial_year != null) {

            $f_year_array = explode('-', $request->financial_year);

            if (in_array('Jan', $request->month) || in_array('Feb', $request->month) || in_array('Mar', $request->month)) {
                $currentYear = $f_year_array[1];
                $monthNumbers = array_map(function ($month) {
                    return Carbon::parse($month)->month;
                }, $request->month);

                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);

                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $startDateFormatted = $firstDate->toDateString();
                $endDateFormatted = $lastDate->toDateString();
            } else {
                $currentYear = $f_year_array[0];
                $monthNumbers = array_map(function ($month) {
                    return Carbon::parse($month)->month;
                }, $request->month);

                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);

                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $startDateFormatted = $firstDate->toDateString();
                $endDateFormatted = $lastDate->toDateString();
            }

            $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->where('invoice_date', '>=', $startDateFormatted)
                    ->where('invoice_date', '<=', $endDateFormatted);;
            });
        }

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('invoice_date', function ($query) {
                return date('d/M/Y', strtotime($query->invoice_date));
            })
            ->rawColumns(['invoice_date'])
            ->make(true);
    }

    /*
    Secondary sales total order value and quantity
    */

    public function total_order_value(Request $request)
    {
        $orderDetails = OrderDetails::with(['orders', 'orders.sellers', 'orders.buyers', 'orders.createdbyname', 'orders.getuserdetails.getdivision', 'orders.buyers.customeraddress.cityname', 'orders.buyers.customeraddress.statename', 'products', 'products.productpriceinfo', 'orders.getuserdetails.getbranch', 'orders.sellers.customeraddress.cityname', 'orders.createdbyname.getbranch']);

        $totalOrderValue = $orderDetails->get()->SUM('line_total');
        $totalOrderQty = $orderDetails->get()->SUM('quantity');
        $totalOrders = $orderDetails->groupBy('order_id')->count();

        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
            $branchIds = User::where('branch_id', $request->branch_id)->pluck('id');

            $totalOrderValue = $orderDetails->whereHas('orders', function ($q) use ($branchIds) {
                $q->whereIn('created_by', $branchIds);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($q) use ($branchIds) {
                $q->whereIn('created_by', $branchIds);
            })->get()->SUM('quantity');
            $totalOrders = $orderDetails->whereHas('orders', function ($q) use ($branchIds) {
                $q->whereIn('created_by', $branchIds);
            })->get()->count();
        }

        if ($request->division_id && $request->division_id != '' && $request->division_id != null) {

            $divisionIds = User::where('division_id', $request->division_id)->pluck('id');

            $totalOrderValue = $orderDetails->whereHas('orders', function ($q) use ($divisionIds) {
                $q->whereIn('created_by', $divisionIds);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($q) use ($divisionIds) {
                $q->whereIn('created_by', $divisionIds);
            })->get()->SUM('quantity');

            $totalOrders = $orderDetails->whereHas('orders', function ($q) use ($divisionIds) {
                $q->whereIn('created_by', $divisionIds);
            })->pluck('id')->count();
        }

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {

            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $totalOrderValue = $orderDetails->whereHas('orders', function ($query) use ($financial_year_start, $financial_year_end) {
                $query->where('order_date', '>=', $financial_year_start)
                    ->where('order_date', '<=', $financial_year_end);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($query) use ($financial_year_start, $financial_year_end) {
                $query->where('order_date', '>=', $financial_year_start)
                    ->where('order_date', '<=', $financial_year_end);
            })->get()->SUM('quantity');
        }

        if ($request->month && $request->month != '' && $request->month != null) {
            $month = $request->month;
            $totalOrderValue = $orderDetails->whereHas('orders', function ($query) use ($month) {
                $query->whereMonth('order_date', '=', $month);
            })->get();
            $totalOrderQty = $orderDetails->whereHas('orders', function ($query) use ($month) {
                $query->whereMonth('order_date', '=', $month);
            })->get();
        }

        if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
            $dealerIds = Customers::where('id', $request->dealer_id)->pluck('id');

            $totalOrderValue = $orderDetails->whereHas('orders', function ($q) use ($dealerIds) {
                $q->whereIn('orders.seller_id', $dealerIds);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($q) use ($dealerIds) {
                $q->whereIn('orders.seller_id', $dealerIds);
            })->get()->SUM('quantity');
        }

        if ($request->executive_id && $request->executive_id != '' && $request->executive_id != null) {
            $executiveIds = User::where('id', $request->executive_id)->pluck('id');

            $totalOrderValue = $orderDetails->whereHas('orders', function ($q) use ($executiveIds) {
                $q->whereIn('orders.created_by', $executiveIds);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($q) use ($executiveIds) {
                $q->whereIn('orders.created_by', $executiveIds);
            })->get()->SUM('quantity');
        }

        // if($request->user_id && $request->user_id != '' && $request->user_id != null){
        //     $userIds = User::where('id', $request->user_id)->pluck('id');
        //     $query->whereIn('se',$userIds);
        // }

        if ($request->retailer_id && $request->retailer_id != '' && $request->retailer_id != null) {
            $buyerIds = User::where('id', $request->retailer_id)->pluck('id');

            $totalOrderValue = $orderDetails->whereHas('orders', function ($q) use ($buyerIds) {
                $q->whereIn('orders.buyer_id', $buyerIds);
            })->get()->SUM('line_total');

            $totalOrderQty = $orderDetails->whereHas('orders', function ($q) use ($buyerIds) {
                $q->whereIn('orders.buyer_id', $buyerIds);
            })->get()->SUM('quantity');
        }

        if ($request->product_model && $request->product_model != '' && $request->product_model != null) {
            $productIds = Product::where('id', $request->product_model)->pluck('id')->toArray();
            $totalOrderValue = $orderDetails->whereIn('product_id', $productIds)->get()->SUM('line_total');
            $totalOrderQty = $orderDetails->whereIn('product_id', $productIds)->get()->SUM('quantity');
        }

        if ($request->new_group && $request->new_group != '') {
            $productIds = Product::where('id', $request->new_group)->pluck('id')->toArray();
            $totalOrderValue = $orderDetails->whereIn('product_id', $productIds)->get()->SUM('line_total');
            $totalOrderQty = $orderDetails->whereIn('product_id', $productIds)->get()->SUM('quantity');
        }

        $data['total_order_value'] = $totalOrderValue;
        $data['total_order_qty'] = $totalOrderQty;
        $data['total_order'] = $totalOrders;

        return response()->json($data);
    }

    /*
    Primary Sales KPI
    */

    public function primarySalesKpiData(Request $request)
    {

        dd($request->all());
        $data = collect([]);
        $fromdate = isset($request->fromdate) ? date('Y-m-d', strtotime($request->fromdate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $todate = isset($request->todate) ? date('Y-m-d', strtotime($request->todate)) : date('Y-m-d', strtotime(date("Y-m-d")));
        $userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        $yearStartDate = date('Y-m-d', strtotime(date("Y-01-01")));
        $yearEndDate = date('Y-m-d', strtotime(date("Y-12-31")));
        $activeStartDate = date("Y-m-d", strtotime('-90 days'));
        $users = getUsersReportingToAuth($userid);
        $query_start_date = ($fromdate < $yearStartDate) ? $fromdate : $yearStartDate;
        $query_end_date = ($todate > $yearEndDate) ? $todate : $yearEndDate;
        $month_calendar = collect([]);
        $year_calendar = collect([]);

        for ($i = 1; $i <=  date('t'); $i++) {
            $month_calendar->push(['date' => date('Y') . "-" . date('m') . "-" . str_pad($i, 2, '0', STR_PAD_LEFT)]);
        }
        for ($i = 1; $i <= 12; $i++) {
            $year_calendar->push(['year' => date('Y'), 'month' => ($i < 10) ? '0' . $i : $i]);
        }

        // $orderDetails = OrderDetails::with(['orders','products','productdetails','orders.buyers'])->get();

        $orderDetails = OrderDetails::with(['orders', 'orders.sellers', 'orders.buyers', 'orders.createdbyname', 'orders.getuserdetails.getdivision', 'orders.buyers.customeraddress.cityname', 'orders.buyers.customeraddress.statename', 'products', 'products.productpriceinfo', 'orders.getuserdetails.getbranch', 'orders.sellers.customeraddress.cityname', 'orders.createdbyname.getbranch']);

        if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
            $usersIds = User::where('id', $request->user_id)->where('sales_type', 'Secondary')->pluck('id');
        } else {
            $usersIds = User::with('attendance_details')->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
            $usersIds = User::where('branch_id', $request->branch_id)->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->division_id && $request->division_id != '' && $request->division_id != null) {
            $usersIds = User::where('division_id', $request->division_id)->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $orderDetails->whereHas('orders', function ($query) use ($financial_year_end) {
                $query->where('order_date', '>=', $financial_year_start)
                    ->where('order_date', '<=', $financial_year_end);
            });
        }


        if ($request->month && $request->month != '' && $request->month != null) {
            $query->where('month', $request->month);
        }

        // user with secondary sales type
        $employeeIds = EmployeeDetail::whereIn('user_id', $usersIds)->pluck('customer_id');
        $customerIds = Customers::whereIn('id', $employeeIds)->pluck('id');

        $registeredRetailerCount = $customerIds->count();

        $activeRetailerCount = $orderDetails->whereHas('orders', function ($query) use ($customerIds) {
            $query->whereIn('buyer_id', $customerIds);
        })->count();

        $activeRetailerPercent = 0;
        if ($registeredRetailerCount > 0) {
            $activeRetailerPercent = number_format((float)(($activeRetailerCount / $registeredRetailerCount) * 100), 2, '.', '');
        }

        $transactionHistory = TransactionHistory::with('customer')->get();

        $nosOfRetailerRegistredSaarthi = $transactionHistory->whereIn('customer_id', $customerIds)->count();

        $nosOfRetailerRegistredSaarthiPercent = 0;
        if ($registeredRetailerCount > 0) {
            $nosOfRetailerRegistredSaarthiPercent =  number_format((float)(($nosOfRetailerRegistredSaarthi / $registeredRetailerCount) * 100), 2, '.', '');
        }

        $orderTarget = SalesTargetUsers::where('type', 'secondary')->whereIn('user_id', $customerIds)->sum('target');

        $orderAchievement = $orderDetails->where(function ($query) use ($customerIds) {
            $query->whereHas('orders', function ($subquery) use ($customerIds) {
                $subquery->whereIn('buyer_id', $customerIds);
            });
        })->sum('line_total');

        $achievementPercent = 0;
        if ($orderTarget > 0) {
            $achievementPercent = number_format((float)(($orderAchievement / $orderTarget) * 100), 2, '.', '');
        }

        $attendanceCount = Attendance::whereIn('user_id', $usersIds)->groupBy('user_id')->count();
        $totalUsers = $usersIds->count();

        $avgWorkingDays = 0;
        if ($totalUsers > 0) {
            $avgWorkingDays = number_format((float)($attendanceCount / $totalUsers));
        }

        $totalOrders = $orderDetails->where(function ($query) use ($usersIds) {
            $query->whereHas('orders', function ($subquery) use ($usersIds) {
                $subquery->whereIn('buyer_id', $usersIds);
            });
        })->count();

        $averageVisits = VisitReport::whereIn('user_id', $usersIds)->count();


        $perDayAverageSales = ($avgWorkingDays > 0) ? number_format((float)($totalOrders / $avgWorkingDays)) : '0';
        $perDayAverageVisit = ($averageVisits > 0) ?  number_format((float)($totalOrders / $averageVisits)) : '0';
        $data['registeredRetailerCount'] = $registeredRetailerCount;
        $data['activeRetailerCount'] = $activeRetailerCount;
        $data['activeRetailerPercent'] = $activeRetailerPercent;
        $data['nosOfRetailerRegistredSaarthi'] = $nosOfRetailerRegistredSaarthi;
        $data['nosOfRetailerRegistredSaarthiPercent'] = $nosOfRetailerRegistredSaarthiPercent;

        $data['orderTarget'] = $orderTarget;
        $data['orderAchievement'] = $orderAchievement;
        $data['achievementPercent'] = $achievementPercent;
        $data['perDayAverageSales'] = $perDayAverageSales;
        $data['perDayAverageVisit'] = $perDayAverageVisit;

        return response()->json($data);
    }


    public function visitors(VisitorDataTable $dataTable)
    {
        abort_if(Gate::denies('visitor_log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('visitor.index');
    }

    public function dealer_dashboard(Request $request)
    {
        abort_if(Gate::denies('dealer_dashboard'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $dealer_poster_setting = DealerPortalSettings::first();
        $all_customer_ids = ParentDetail::where('parent_id', Auth::user()->customerid)->pluck('customer_id')->toArray();
        $all_customer_ids[] = Auth::user()->customerid;
        $today = now();
        $startOfQuarter = $today->copy()->startOfQuarter();
        $endOfQuarter = $today->copy()->endOfQuarter();
        $startOfFinancialYear = $today->month >= 4
            ? Carbon::create($today->year, 4, 1)
            : Carbon::create($today->year - 1, 4, 1);

        $salesSummary = [
            'month' => DB::table('primary_sales')
                ->whereMonth('invoice_date', $today->month)
                ->whereYear('invoice_date', $today->year)
                ->whereIn('customer_id', $all_customer_ids)
                ->sum('net_amount') / 100000,

            'quarter' => DB::table('primary_sales')
                ->whereBetween('invoice_date', [$startOfQuarter, $endOfQuarter])
                ->whereIn('customer_id', $all_customer_ids)
                ->sum('net_amount') / 100000,

            'financial_year' => DB::table('primary_sales')
                ->whereBetween('invoice_date', [$startOfFinancialYear, $today])
                ->whereIn('customer_id', $all_customer_ids)
                ->sum('net_amount') / 100000,
        ];

        return view('dashboard.dealer_dashboard', compact('dealer_poster_setting', 'salesSummary'));
    }
}
