<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PrimarySales;
use App\Models\SalesTargetCustomers;
use Validator;
use DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }

    public function primarySales(Request $request)
    {
        try {
            $user = $request->user();
            $user_id = $user->id;
            $pageSize = $request->per_page ??  16;
            $user_employee_codes = $user->employee_codes ?? '';

            $user_ids = getUsersReportingToAuth($user->id);

            $all_employee_code = User::whereIn('id', $user_ids)->pluck('employee_codes');

            // Get unique dealers and branches
            if (isset($user_employee_codes) && $user_employee_codes != "Greymetre Test") {
                $all_users = PrimarySales::select('dealer', 'id')->whereIn('emp_code', $all_employee_code)->latest()->get()->unique('dealer');
            } else {
                $all_users = PrimarySales::select('dealer', 'id')->latest()->get()->unique('dealer');
            }

            $all_branches = PrimarySales::select('final_branch', 'id')->latest()->get()->unique('final_branch');
            $users = $all_users->values()->toArray();
            $branches = $all_branches->values()->toArray();
            $currentYear = Carbon::now()->year;
            $years = range($currentYear, $currentYear + 1);
            $year_range = collect([]);
            foreach ($years as $key => $year) {
                $year_range->push([
                    'range' => ($year - 1) . '-' . $year,
                ]);
            }
            // Determine the current year and current date based on the financial year from the request
            DB::statement("SET SESSION group_concat_max_len = 10000000");
            $query = PrimarySales::select(
                'dealer',
                'final_branch',
                'city',
                DB::raw('SUM(net_amount) as total_net_amounts'),
                DB::raw('0 as last_year_net_amounts')
            );

            $currentMonth = Carbon::now()->month;
            $last_monts = [1,2,3];
            
            // Determine the financial year date range
           if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
                $f_year_array = explode('-', $request->financial_year);

                $financial_year_start = $f_year_array[0] . '-04-01';
                $financial_year_end = $f_year_array[1] . '-03-31';
            } else {
                $currentYear = Carbon::now()->year;
                if(in_array($currentMonth, $last_monts)){
                    $request->financial_year = ($currentYear-1).'-'.$currentYear;
                }else{
                    $request->financial_year = $currentYear.'-'.$currentYear+1;
                }
                $f_year_array = explode('-', $request->financial_year);
                $financial_year_start = $f_year_array[0] . '-04-01';
                $financial_year_end = $f_year_array[1] . '-03-31';                
            }

            // Adjust financial_year_end if it is greater than today
            $today = Carbon::today();
            if (Carbon::parse($financial_year_end)->greaterThan($today)) {
                $financial_year_end = $today->format('Y-m-d');
            }

            if(in_array($currentMonth, $last_monts)){
                $cyfirstDate = Carbon::create($f_year_array[1], $currentMonth, 1)->startOfMonth()->toDateString();
                $cylastDate = Carbon::create($f_year_array[1], $currentMonth, 1)->endOfMonth()->toDateString();
                $lyfirstDate = Carbon::create($f_year_array[0], $currentMonth, 1)->startOfMonth()->toDateString();
                $lylastDate = Carbon::create($f_year_array[0], $currentMonth, 1)->endOfMonth()->toDateString();
            }else{
                $cyfirstDate = Carbon::create($f_year_array[0], $currentMonth, 1)->startOfMonth()->toDateString();
                $cylastDate = Carbon::create($f_year_array[0], $currentMonth, 1)->endOfMonth()->toDateString();
                $lyfirstDate = Carbon::create($f_year_array[0]-1, $currentMonth, 1)->startOfMonth()->toDateString();
                $lylastDate = Carbon::create($f_year_array[0]-1, $currentMonth, 1)->endOfMonth()->toDateString();
            }

            // Calculate last year start and end dates after potentially adjusting financial_year_end
            $last_year_start = Carbon::parse($financial_year_start)->subYear()->format('Y-m-d');
            $last_year_end = Carbon::parse($financial_year_end)->subYear()->format('Y-m-d');

            // Filter by financial year
            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);

            // Additional filters
            if ($request->division_id && $request->division_id != '' && $request->division_id != NULL) {
                $query->where('division', $request->division_id);
            }

            if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                $query->where('final_branch', $request->branch_id);
            }

            if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
                $query->where('dealer', 'like', '%' . $request->dealer_id . '%');
            }

            if ($request->product_model && $request->product_model != '' && $request->product_model != null) {
                $query->where('model_name', $request->product_model);
            }

            if ($request->new_group && $request->new_group != '' && $request->new_group != null) {
                $query->where('new_group', $request->new_group);
            }

            if ($request->executive_id && $request->executive_id != '' && $request->executive_id != null) {
                $query->where('sales_person', $request->executive_id);
            }
            $query->whereIn('emp_code', $all_employee_code);
            // Grouping and ordering
            $query->groupBy('dealer', 'final_branch', 'city')->orderBy('total_net_amounts', 'desc');

            // Execute the primary query
            $results = (!empty($pageSize)) ? $query->paginate($pageSize*4) : $query->get();

            // Calculate the last year's net amounts
            $lastYearAmounts = PrimarySales::select(
                'dealer',
                'final_branch',
                'city',
                DB::raw('SUM(net_amount) as last_year_net_amounts')
            )
                ->whereBetween('invoice_date', [$last_year_start, $last_year_end])
                ->groupBy('dealer', 'final_branch', 'city')
                ->get();

            // Merge the results
            $results = $results->map(function ($item) use ($lastYearAmounts) {
                $lastYearAmount = $lastYearAmounts->firstWhere(function ($value) use ($item) {
                    return $value->dealer == $item->dealer &&
                        $value->final_branch == $item->final_branch &&
                        $value->city == $item->city;
                });

                $item->last_year_net_amounts = $lastYearAmount ? $lastYearAmount->last_year_net_amounts : 0;
                return $item;
            });
           
            foreach ($results as $dealer) {
                $cytm = PrimarySales::select(
                    'dealer',
                    DB::raw('SUM(net_amount) as cytm_total_net_amounts')
                )->where('dealer', 'like', '%' . $dealer->dealer . '%')
                ->where('invoice_date', '>=', $cyfirstDate)
                ->where('invoice_date', '<=', $cylastDate)
                ->groupBy('dealer')->first();
                
                $lytm = PrimarySales::select(
                    'dealer',
                    DB::raw('SUM(net_amount) as lytm_total_net_amounts')
                )->where('dealer', 'like', '%' . $dealer->dealer . '%')              
                ->where('invoice_date', '>=', $lyfirstDate)
                ->where('invoice_date', '<=', $lylastDate)
                ->groupBy('dealer')->first();

                $salesData[] = [
                    'dealer' => $dealer->dealer,
                    'total_net_amount_last_year' => $dealer->last_year_net_amounts>0?number_format(($dealer->last_year_net_amounts/100000),2,'.',''):"0.00",
                    'total_net_amount_current_year' =>  $dealer->total_net_amounts>0?number_format(($dealer->total_net_amounts/100000),2,'.',''):"0.00",
                    'total_net_amount_last_month' => $lytm?number_format(($lytm->lytm_total_net_amounts/100000),2,'.',''):"0.00",
                    'total_net_amount_current_month' => $cytm?number_format(($cytm->cytm_total_net_amounts/100000),2,'.',''):"0.00",
                ];
            }

          
            $ps_divisions = PrimarySales::distinct()->pluck('division');
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $salesData, 'users' => $users, 'branches' => $branches,  'year_rang' => $year_range, 'currentYear' => $currentYear, 'ps_divisions' => $ps_divisions], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function monthlySales(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'financial_year'  => 'required',
            'dealer_id'  => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->messages()->all()], $this->badrequest);
        }
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $query = PrimarySales::select(
            'dealer',
            'final_branch',
            'city',
            'customer_id',
            DB::raw('SUM(net_amount) as total_net_amounts'),
            DB::raw('GROUP_CONCAT(net_amount) as net_amounts'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(invoice_date) as invoice_dates'),
        );

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);
            $months = [];

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $startDate = Carbon::createFromFormat('Y-m-d', $financial_year_start);
            $endDate = Carbon::createFromFormat('Y-m-d', $financial_year_end);
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $monthName = $currentDate->format('F');
                if (!in_array($monthName, $months)) {
                    $months[] = $monthName;
                }
                $currentDate->addMonth()->startOfMonth();
            }
            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        }

        $db_data = $query->where('dealer', 'like', '%' . $request->dealer_id . '%')->groupBy('dealer', 'final_branch', 'city', 'customer_id')->first();

        $response = array();
        $invoice_dates = explode(',', $db_data->invoice_dates);
        $net_amounts = explode(',', $db_data->net_amounts);

        foreach ($months as $k => $val) {
            $tsale = 0;
            foreach ($invoice_dates as $key => $value) {
                $invDate = Carbon::createFromFormat('Y-m-d', $value);
                $currentDate = $invDate->copy();
                $monthName = $currentDate->format('F');
                if ($monthName == $val) {
                    $tsale += $net_amounts[$key];
                }
            }
            if($val == 'January' || $val == 'February' || $val == 'March'){
                $date = Carbon::parse($financial_year_end);
                $year = $date->year;
                $date = Carbon::createFromFormat('F', $val);
                $shortMonthName = $date->format('M');

                $target = SalesTargetCustomers::where(['customer_id'=>$db_data->customer_id,'month'=>$shortMonthName,'year'=>$year])->first();
                // dd($target->target);
            }else{
                $date = Carbon::parse($financial_year_start);
                $year = $date->year;
                $date = Carbon::createFromFormat('F', $val);
                $shortMonthName = $date->format('M');
                $target = SalesTargetCustomers::where(['customer_id'=>$db_data->customer_id,'month'=>$shortMonthName,'year'=>$year])->first();
            }
            if ($tsale > 0) {
                if($target){
                    $achievementPercent = ($target->target == 0) ? 0 : (($tsale / 100000) * 100 / $target->target);
                }else{
                    $achievementPercent = 100;
                }
                $response[$val]['achiv'] = number_format(($tsale / 100000), 2, '.', '');
                $response[$val]['terg'] = $target?$target->target:'0.00';
                $response[$val]['achivper'] =  number_format($achievementPercent, 2);
            } else {
                $response[$val]['achiv'] =  "0.0";
                $response[$val]['terg'] = $target?$target->target:'0.00';
                $response[$val]['achivper'] =  "0.0";
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $response], $this->successStatus);
    }

    public function getDealerGrowth(Request $request)
    {
        try {
            $user = $request->user();
            $pageSize = $request->input('pageSize');
            $user_ids = getUsersReportingToAuth($user->id);
            
            $all_employee_code = User::whereIn('id', $user_ids)->pluck('employee_codes');
            
            // Get unique dealers and branches
            if (isset($user_employee_codes) && $user_employee_codes != "Greymetre Test") {
                $all_users = PrimarySales::select('dealer', 'id')->whereIn('emp_code', $all_employee_code)->latest()->get()->unique('dealer');
            } else {
                $all_users = PrimarySales::select('dealer', 'id')->latest()->get()->unique('dealer');
            }
            
            $all_branches = PrimarySales::select('final_branch', 'id')->latest()->get()->unique('final_branch');
            $users = $all_users->values()->toArray();
            $branches = $all_branches->values()->toArray();
            $currentYear = Carbon::now()->year;
            $years = range($currentYear, $currentYear + 1);
            $year_range = collect([]);
            foreach ($years as $key => $year) {
                $year_range->push([
                    'range' => ($year - 1) . '-' . $year,
                ]);
            }
            
            DB::statement("SET SESSION group_concat_max_len = 10000000");
            $query = PrimarySales::select(
                'dealer',
                'final_branch',
                'city',
                DB::raw('SUM(net_amount) as total_net_amounts'),
                DB::raw('0 as last_year_net_amounts')
            );
            
            // Determine the financial year date range
            if ($request->month && is_array($request->month) && count($request->month) > 0 && $request->financial_year && !empty($request->financial_year)) {
                $f_year_array = explode('-', $request->financial_year);

                // Determine if months are in Jan-Mar and set the correct year
                $isJanToMar = in_array('Jan', $request->month) || in_array('Feb', $request->month) || in_array('Mar', $request->month);
                $currentYear = $isJanToMar ? $f_year_array[1] : $f_year_array[0];

                // Get the first and last months from the array
                $firstMonth = $request->month[0];
                $lastMonth = $request->month[count($request->month) - 1];

                // Format the month and create start and end dates
                $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$firstMonth")->startOfMonth();
                $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$lastMonth")->endOfMonth();

                // Convert to date strings
                $financial_year_start = $startDate->toDateString();
                $financial_year_end = $endDate->toDateString();
            } elseif ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
                $f_year_array = explode('-', $request->financial_year);

                $financial_year_start = $f_year_array[0] . '-04-01';
                $financial_year_end = $f_year_array[1] . '-03-31';
            } else {
                $currentDate = Carbon::now();

                $currentYear = $currentDate->year;
                $financialYearStart = Carbon::create($currentYear, 4, 1);
                $financialYearEnd = Carbon::create($currentYear + 1, 3, 31);

                if ($currentDate->lt($financialYearStart)) {
                    $financialYearStart = Carbon::create($currentYear - 1, 4, 1);
                    $financialYearEnd = Carbon::create($currentYear, 3, 31);
                }

                $financial_year_start = $financialYearStart->format('Y-m-d');
                $financial_year_end = $financialYearEnd->format('Y-m-d');
            }

            // Adjust financial_year_end if it is greater than today
            $today = Carbon::today();
            if (Carbon::parse($financial_year_end)->greaterThan($today)) {
                $financial_year_end = $today->format('Y-m-d');
            }

            // Calculate last year start and end dates after potentially adjusting financial_year_end
            $last_year_start = Carbon::parse($financial_year_start)->subYear()->format('Y-m-d');
            $last_year_end = Carbon::parse($financial_year_end)->subYear()->format('Y-m-d');

            // Filter by financial year
            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);

            // Additional filters
            if ($request->division_id && $request->division_id != '' && $request->division_id != NULL) {
                $query->where('division', $request->division_id);
            }

            if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                $query->where('final_branch', $request->branch_id);
            }

            if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
                $query->where('dealer', 'like', '%' . $request->dealer_id . '%');
            }

            if ($request->product_model && $request->product_model != '' && $request->product_model != null) {
                $query->where('model_name', $request->product_model);
            }

            if ($request->new_group && $request->new_group != '' && $request->new_group != null) {
                $query->where('new_group', $request->new_group);
            }

            if ($request->executive_id && $request->executive_id != '' && $request->executive_id != null) {
                $query->where('sales_person', $request->executive_id);
            }
            $query->whereIn('emp_code', $all_employee_code);
            // Grouping and ordering
            $query->groupBy('dealer', 'final_branch', 'city')->orderBy('total_net_amounts', 'desc');

            // Execute the primary query
            $results = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();

            // Calculate the last year's net amounts
            $lastYearAmounts = PrimarySales::select(
                'dealer',
                'final_branch',
                'city',
                DB::raw('SUM(net_amount) as last_year_net_amounts')
            )
                ->whereBetween('invoice_date', [$last_year_start, $last_year_end])
                ->groupBy('dealer', 'final_branch', 'city')
                ->get();

            // Merge the results
            $results = $results->map(function ($item) use ($lastYearAmounts) {
                $lastYearAmount = $lastYearAmounts->firstWhere(function ($value) use ($item) {
                    return $value->dealer == $item->dealer &&
                        $value->final_branch == $item->final_branch &&
                        $value->city == $item->city;
                });
    
                $item->last_year_net_amounts = $lastYearAmount ? $lastYearAmount->last_year_net_amounts : 0;
    
                $currentYearAchievements = $item->total_net_amounts;
                $lastYearAchievements = $item->last_year_net_amounts;
    
                $growthPercent = 0;
                if ($lastYearAchievements != null) {
                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                    $growthPercent = ROUND($growthPercent, 2);
                } else {
                    if ($lastYearAchievements == null || $lastYearAchievements == 0) {
                        if (($currentYearAchievements == null || $currentYearAchievements == 0) && ($lastYearAchievements == null || $lastYearAchievements == 0)) {
                            $growthPercent = 0;
                        } elseif (($lastYearAchievements == null || $lastYearAchievements == 0) && isset($currentYearAchievements) && ($currentYearAchievements != null && $currentYearAchievements > 0)) {
                            $growthPercent = 0;
                        }
                    }
                }
    
                $item->growthPercent = $growthPercent;
                return $item;
            });
            $remarks = [['id'=>'1','title'=>'INACTIVE DEALER'],['id'=>'2','title'=>'LY -NO SALE'],['id'=>'3','title'=>'DE-GROWTH'],['id'=>'4','title'=>'GROWTH DEALER']];
            if ($request->remark && $request->remark != '' && $request->remark != null) {
                if ($request->remark == '1') {
                    // INACTIVE DEALER
                    $results = $results->filter(function ($item) {
                        return $item->total_net_amounts == 0;
                    });
                } elseif ($request->remark == '2') {
                    // LY -NO SALE
                    $results = $results->filter(function ($item) {
                        return $item->last_year_net_amounts == 0;
                    });
                } elseif ($request->remark == '3') {
                    // DE-GROWTH
                    $results = $results->filter(function ($item) {
                        return $item->growthPercent < 0;
                    });
                } elseif ($request->remark == '4') {
                    // GROWTH DEALER
                    $results = $results->filter(function ($item) {
                        return $item->growthPercent > 0;
                    });
                }
            }
    
            $results = $results->sortByDesc('growthPercent');

            $final_result = array();
            
            foreach ($results as $key => $value) {
                $results[$key]['total_net_amounts'] = number_format(($value->total_net_amounts/100000),2,'.','');
                $results[$key]['last_year_net_amounts'] = number_format(($value->last_year_net_amounts/100000),2,'.','');

                $results[$key]['goly'] = (string)$value->growthPercent;

                if($value->total_net_amounts <= 0){
                    $results[$key]['remark'] = 'INACTIVE DEALER';
                }elseif($value->last_year_net_amounts <= 0){
                    $results[$key]['remark'] =  'LY -NO SALE';
                }elseif ($value->growthPercent <= 0) {
                    $results[$key]['remark'] =  'DE-GROWTH';
                } elseif ($value->growthPercent > 0) {
                    $results[$key]['remark'] =  'GROWTH DEALER';
                }
                array_push($final_result, $results[$key]);
            }
            $ps_divisions = PrimarySales::distinct()->pluck('division');
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $final_result, 'users' => $users, 'branches' => $branches,  'year_rang' => $year_range, 'currentYear' => $currentYear, 'remarks' => $remarks, 'ps_divisions' => $ps_divisions], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }
}
