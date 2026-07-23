<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeCostingDataTable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\Models\CheckIn;
use App\Models\Customers;
use App\Models\Attendance;
use App\Models\BeatSchedule;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Division;
use App\Models\Department;
use App\Models\Branch;
use App\Models\{Address, Attachment, BranchStock, CustomerOutstanting, DealerAppointment, EmployeeDetail, Media, TransactionHistory, MobileUserLoginDetails, Redemption, ParentDetail, PrimarySales, Product, SalesTargetUsers, State};
use Excel;
use App\Exports\CounterVisitReportExport;
use App\Exports\AdherenceDetailReportExport;
use App\Models\User;
use App\Models\City;
use App\Models\Beat;
use App\Models\Wallet;
use App\Models\TourProgramme;
use App\Models\UserCityAssign;
use App\Models\SalesDetails;
use App\Models\SalesTarget;
use App\Models\TourDetail;
use App\Exports\FieldActivityExport;
use App\Exports\TourProgrammeReportExport;
use App\Exports\MovementReportExport;
use App\Exports\PointCollectionsExport;
use App\Exports\TerritoryCoverageExport;
use App\Exports\PerformanceParameterExport;
use App\Exports\MechanicsPointsExport;
use App\Exports\TargetAchievementExport;
use App\Exports\SurveyAnalysisExport;
use App\Exports\LoyaltySummaryReportExport;
use App\Exports\LoyaltyDealerSummaryReportExport;
use App\Models\DealIn;
use App\DataTables\GamificationDataTable;
use App\Exports\BranchCostingExport;
use App\Exports\BranchOnlySalesCostingExport;
use App\Exports\CustomerAnalysisExport;
use App\Exports\CutomerOutstantingExport;
use App\Exports\CutomerOutstantingTemplate;
use App\Exports\DealerGrowthExport;
use App\Exports\GroupWiseAnalysisExport;
use App\Exports\LoyaltyRetialerSummaryReportExport;
use App\Exports\NewDealerSaleExport;
use App\Exports\NewDealerSaleLastYearExport;
use App\Exports\PerEmployeeCostingExport;
use App\Exports\ProductAnalysisBranchExport;
use App\Exports\ProductAnalysisQtyExport;
use App\Exports\ProductAnalysisValueExport;
use App\Exports\TopDealerExport;
use App\Exports\UserIncentiveExport;
use App\Imports\CutomerOutstantingImport;
use App\Jobs\GenerateBalanceConfirmationJob;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class ReportController extends Controller
{
    public function __construct() {}
    public function beatadherence(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        if ($request->ajax()) {
            $data = BeatSchedule::with('users:id,name', 'beats', 'beatcustomers', 'beatcheckininfo', 'beatscheduleorders')
                ->where(function ($query) use ($start_date, $end_date, $userids) {
                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                        $query->whereHas('users', function ($query) use ($userids) {
                            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                                $query->whereIn('id', $userids);
                            }
                        });
                    }
                    if ($start_date) {
                        $query->where('beat_date', '>=', date('Y-m-d', strtotime($start_date)));
                    }
                    if ($end_date) {
                        $query->where('beat_date', '<=', date('Y-m-d', strtotime($end_date)));
                    }
                })
                ->select('id', 'beat_date', 'user_id', 'beat_id')
                ->latest();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('beatCounters', function ($query) {
                    return !empty($query['beatcustomers']) ? $query['beatcustomers']->count() : 0;
                })
                ->addColumn('visitedcounter', function ($query) {
                    return !empty($query['beatcheckininfo']) ? $query['beatcheckininfo']->unique('customer_id', 'checkin_date')->count() : 0;
                })
                ->addColumn('beatadherence', function ($query) {
                    $totalcustomer = !empty($query['beatcustomers']) ? $query['beatcustomers']->count() : 0;
                    $visitedcounter = !empty($query['beatcheckininfo']) ? $query['beatcheckininfo']->unique('customer_id', 'checkin_date')->count() : 0;
                    //   return ($visitedcounter * 100) / $totalcustomer;
                    return ($totalcustomer === 0) ? 0 : number_format((float)($visitedcounter * 100) / $totalcustomer, 1, '.', '') . ' %';
                })
                ->addColumn('totalorder', function ($query) {
                    return !empty($query['beatscheduleorders']) ? $query['beatscheduleorders']->count() : 0;
                })
                ->addColumn('beatproductivity', function ($query) {
                    $visitedcounter = !empty($query['beatcheckininfo']) ? $query['beatcheckininfo']->unique('customer_id', 'checkin_date')->count() : 0;
                    $totalorder = !empty($query['beatscheduleorders']) ? $query['beatscheduleorders']->count() : 0;
                    //   return ($totalorder * 100) / $totalcustomer;
                    return ($visitedcounter === 0) ? 0 :  number_format((float)($totalorder * 100) / $visitedcounter, 1, '.', '') . ' %';
                })
                ->addColumn('newcounter', function ($query) {
                    return !empty($query['beatschedulecustomer']) ? $query['beatschedulecustomer']->count() : 0;
                })
                ->addColumn('orderqty', function ($query) {
                    return !empty($query['beatscheduleorders']) ? $query['beatscheduleorders']->sum('total_qty') : 0;
                })
                ->addColumn('uniqueskucount', function ($query) {
                    return !empty($query['beatscheduleorders']['orderdetails']) ? $query['beatscheduleorders']['orderdetails']->sum('total_qty') : 0;
                })
                ->addColumn('ordervalue', function ($query) {
                    return !empty($query['beatscheduleorders']) ? $query['beatscheduleorders']->sum('grand_total') : 0;
                })
                ->addColumn('dailyAvarageSales', function ($query) use ($data) {
                    return '';
                })
                ->rawColumns(['beatCounters', 'visitedcounter', 'beatadherence', 'totalorder', 'beatproductivity', 'newcounter', 'orderqty', 'uniqueskucount', 'ordervalue', 'dailyAvarageSales'])
                ->make(true);
        }
        return view('reports.beatadherence');
    }

    public function adherencesummary(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        if ($request->ajax()) {
            $data = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->where(function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('id', $userids);
                }
            })->whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['superadmin', 'Admin']);
            })->select('id', 'name', 'mobile', 'location')->latest();
            $users = $data->pluck('id')->toArray();
            $schedules = BeatSchedule::with('beatcustomers', 'beatcheckininfo', 'beatscheduleorders')
                ->where(function ($query) use ($start_date, $end_date, $userids) {
                    if ($start_date) {
                        $query->whereDate('beat_date', '>=', date('Y-m-d', strtotime($start_date)));
                    }
                    if ($end_date) {
                        $query->whereDate('beat_date', '<=', date('Y-m-d', strtotime($end_date)));
                    }
                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                        $query->whereIn('user_id', $userids);
                    }
                })
                ->select('id', 'beat_date', 'user_id', 'beat_id')
                ->latest()
                ->get();
            $orders = Order::with('orderdetails')
                ->where(function ($query) use ($start_date, $end_date, $userids) {
                    if ($start_date) {
                        $query->whereDate('order_date', '>=', date('Y-m-d', strtotime($start_date)));
                    }
                    if ($end_date) {
                        $query->whereDate('order_date', '<=', date('Y-m-d', strtotime($end_date)));
                    }
                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                        $query->whereIn('created_by', $userids);
                    }
                })
                ->select('id', 'grand_total', 'created_by', 'total_qty', 'buyer_id')
                ->get();

            $orderdetaildata = OrderDetails::with('orders')->whereHas('orders', function ($query) use ($start_date, $end_date, $userids) {
                if ($start_date) {
                    $query->whereDate('order_date', '>=', date('Y-m-d', strtotime($start_date)));
                }
                if ($end_date) {
                    $query->whereDate('order_date', '<=', date('Y-m-d', strtotime($end_date)));
                }
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('created_by', $userids);
                }
            })
                ->select('id', 'order_id', 'product_id', 'quantity', 'line_total')
                ->get();
            $customers = Customers::where(function ($query) use ($start_date, $end_date, $userids) {
                if ($start_date) {
                    $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)));
                }
                if ($end_date) {
                    $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)));
                }
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('executive_id', $userids);
                }
            })
                ->select('id', 'created_by', 'executive_id')
                ->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('total_beat_counter', function ($query) use ($schedules) {
                    $userbeats = $schedules->where('user_id', '=', $query->id);
                    $total_beat_counter = 0;
                    if (!empty($userbeats)) {
                        foreach ($userbeats as $key => $counter) {
                            $total_beat_counter += count($counter['beatcustomers']);
                        }
                    }
                    return $total_beat_counter;
                })
                ->addColumn('total_visited_counter', function ($query) use ($schedules) {
                    $userbeats = $schedules->where('user_id', '=', $query->id);
                    $total_visited_counter = 0;
                    if (!empty($userbeats)) {
                        foreach ($userbeats as $key => $counter) {
                            $total_visited_counter += $counter['beatcheckininfo']->unique('customer_id', 'checkin_date')->count();
                        }
                    }
                    return $total_visited_counter;
                })
                ->addColumn('beat_adherence', function ($query) use ($schedules) {
                    $userbeats = $schedules->where('user_id', '=', $query->id);
                    $total_visited_counter = 0;
                    $total_beat_counter = 0;
                    if (!empty($userbeats)) {
                        foreach ($userbeats as $key => $counter) {
                            $total_visited_counter += $counter['beatcheckininfo']->unique('customer_id', 'checkin_date')->count();
                            $total_beat_counter += count($counter['beatcustomers']);
                        }
                    }
                    return ($total_beat_counter >= 1) ? number_format((float)($total_visited_counter * 100) / $total_beat_counter, 1, '.', '') . ' %'  : '';
                })
                ->addColumn('total_order_counter', function ($query) use ($orders) {
                    $userorders = $orders->where('created_by', '=', $query->id);
                    return !empty($userorders) ? $userorders->unique('buyer_id')->count() : 0;
                })
                ->addColumn('beat_productivity', function ($query) use ($schedules, $orders) {
                    $userbeats = $schedules->where('user_id', '=', $query->id);
                    $ordercount = $orders->where('created_by', '=', $query->id)->count();
                    $total_visited_counter = 0;
                    if (!empty($userbeats)) {
                        foreach ($userbeats as $key => $counter) {
                            $total_visited_counter += $counter['beatcheckininfo']->unique('customer_id', 'checkin_date')->count();
                        }
                    }
                    return ($total_visited_counter >= 1) ? number_format((float)($ordercount * 100) / $total_visited_counter, 1, '.', '') . ' %'  : '';
                })
                ->addColumn('new_counter_added', function ($query) use ($customers) {
                    return $customers->where('created_by', '=', $query->id)->count();
                })
                ->addColumn('total_order_qty', function ($query) use ($orderdetaildata) {
                    return $orderdetaildata->where('orders.created_by', '=', $query->id)->sum('quantity');
                })
                ->addColumn('total_order_value', function ($query) use ($orders) {
                    return $orders->where('created_by', '=', $query->id)->sum('grand_total');
                })
                ->addColumn('total_assign_counter', function ($query) {
                    return Customers::where('executive_id', '=', $query->id)->count();
                })
                ->addColumn('unique_sku_count', function ($query) use ($orderdetaildata) {
                    return $orderdetaildata->where('orders.created_by', '=', $query->id)->unique('product_id')->count();
                })
                ->addColumn('active_counter', function ($query) use ($orders) {
                    return $orders->where('created_by', '=', $query->id)->unique('buyer_id')->count();
                })
                ->addColumn('inactive_counter', function ($query) use ($orders) {
                    return Customers::where('executive_id', '=', $query->id)->count() - $orders->where('created_by', '=', $query->id)->unique('buyer_id')->count();
                })
                ->make(true);
        }
        return view('reports.adherencesummary');
    }

    public function customervisit(Request $request)
    {
        $userids = getUsersReportingToAuth();
        if ($request->ajax()) {
            $data = CheckIn::with('users:id,name', 'customers:id,name,mobile', 'customers.customeraddress', 'beatschedules.beats', 'visitreports', 'orders_sum')
                ->whereHas('users', function ($query) use ($userids, $request) {
                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                        $query->whereIn('id', $userids);
                    }
                    if ($request->user_id && $request->user_id != null && $request->user_id != '') {
                        $query->where('user_id', $request->user_id);
                    }
                    if ($request->division_id && $request->division_id != null && $request->division_id != '') {
                        $query->where('division_id', $request->division_id);
                    }
                    if ($request->branch_id && $request->branch_id != null && $request->branch_id != '') {
                        $query->where('branch_id', $request->branch_id);
                    }
                    if ($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != '') {
                        $startDate = date('Y-m-d', strtotime($request->start_date));
                        $endDate = date('Y-m-d', strtotime($request->end_date));
                        $query->whereDate('checkin_date', '>=', $startDate)
                            ->whereDate('checkin_date', '<=', $endDate);
                    }
                })
                ->select('id', 'checkin_date', 'checkin_time', 'user_id', 'customer_id', 'checkout_time', 'beatscheduleid')
                ->latest();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('visit_time', function ($query) {
                    if (!empty($query->checkout_time) && !empty($query->checkin_time)) {
                        $parsedTime1 = Carbon::createFromFormat('H:i:s', $query->checkout_time);
                        $parsedTime2 = Carbon::createFromFormat('H:i:s', $query->checkin_time);

                        $difference = $parsedTime1->diff($parsedTime2);
                        $interval = $difference->format('%H:%I:%S');
                        return $interval;
                    } else {
                        return '-';
                    }
                })
                ->addColumn('beat_name', function ($query) {
                    return isset($query['beatschedules']['beats']['beat_name']) ? $query['beatschedules']['beats']['beat_name'] : '';
                })
                ->addColumn('district_name', function ($query) {
                    return isset($query['customers']['customeraddress']['districtname']['district_name']) ? $query['customers']['customeraddress']['districtname']['district_name'] : '';
                })
                ->addColumn('city_name', function ($query) {
                    return  isset($query['customers']['customeraddress']['cityname']['city_name']) ? $query['customers']['customeraddress']['cityname']['city_name'] : '';
                })
                ->addColumn('pincode', function ($query) {
                    return isset($query['customers']['customeraddress']['zipcode']) ? $query['customers']['customeraddress']['zipcode'] : '';
                })
                ->addColumn('address', function ($query) {
                    return isset($query['customers']['customeraddress']['address1']) ? $query['customers']['customeraddress']['address1'] : '';
                })
                ->addColumn('ordersum', function ($query) {
                    //return $query['orders']->sum('grand_total');
                    $sum_qty = 0;
                    if (!empty($query->orders_sum)) {
                        foreach ($query->orders_sum as $key_new => $datas) {
                            $order_id = $datas->id;
                            $sum_qty += OrderDetails::where('order_id', $order_id)->sum('quantity') ?? 0;
                        }
                    }
                    return $sum_qty;
                })
                ->addColumn('uniquesku', function ($query) {
                    //return $query['orders']->sum('total_qty');
                    return $query->orders_sum->sum('grand_total') ?? 0;
                })
                ->addColumn('uniqueorder', function ($query) {
                    return $query['orders']->count();
                })
                ->addColumn('remarks', function ($query) {
                    return isset($query['visitreports']['description']) ? $query['visitreports']['description'] : '';
                })
                ->rawColumns(['visit_time', 'beat_name', 'district_name', 'city_name', 'pincode', 'address', 'ordersum', 'uniquesku', 'uniqueorder', 'remarks'])
                ->make(true);
        }
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->whereIn('id', $userids)->select('id', 'name')->orderBy('name', 'asc')->get();
        $divisions = Division::where('active', 'Y')->get();
        $branches = Branch::where('active', 'Y')->get();
        return view('reports.customervisit', compact('users', 'divisions', 'branches'));
    }

    public function attendancereport(Request $request)
    {
        abort_if(Gate::denies('attendance_report'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $search_branches = $request->input('search_branches');
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_branches = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('name', 'asc')->get();
        $branches = array();
        $all_branch = array();
        $bkey = 0;
        foreach ($all_user_branches as $k => $val) {
            if ($val->getbranch) {
                if (!in_array($val->getbranch->id, $all_branch)) {
                    array_push($all_branch, $val->getbranch->id);
                    $branches[$bkey]['id'] = $val->getbranch->id;
                    $branches[$bkey]['name'] = $val->getbranch->branch_name;
                    $bkey++;
                }
            }
        }
        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            $all_reporting_user_ids = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->whereIn('id', $all_reporting_user_ids)->whereIn('branch_id', $search_branches)->orderBy('name', 'asc')->pluck('id')->toArray();
        }
        $all_user_details = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('name', 'asc')->get();
        $all_users = array();
        foreach ($all_user_details as $k => $val) {
            $users[$k]['id'] = $val->id;
            $users[$k]['name'] = $val->name;
        }
        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            if ($request->ajax()) {
                $response = ["users" => $users, "status" => true];
                return response()->json($response);
            }
        }
        if ($request->ajax()) {
            $data = Attendance::with('users:id,name')
                ->where(function ($query) use ($request, $all_reporting_user_ids) {
                    if (!empty($request['executive_id'])) {
                        $query->where('user_id', $request['executive_id']);
                    }

                    if (!empty($request['division_id']) && $request['division_id'] != null && $request['division_id'] != "") {
                        $division_id = $request['division_id'];
                        $query->whereHas('users', function ($query) use ($division_id) {
                            $query->where('division_id', $division_id);
                        });
                    }
                    if (!empty($request['active']) && $request['active'] != null && $request['active'] != "") {
                        $active = $request['active'];
                        $query->whereHas('users', function ($query) use ($active) {
                            $query->where('active', $active);
                        });
                    }

                    if (!empty($request['department_id']) && $request['department_id'] != null && $request['department_id'] != "") {
                        $department_id = $request['department_id'];
                        $query->whereHas('users', function ($query) use ($department_id) {
                            $query->where('department_id', $department_id);
                        });
                    }

                    if ($request['status'] != null && $request['status'] != "") {
                        $query->where('attendance_status', $request['status']);
                    }

                    if (!empty($request['start_date']) && !empty($request['end_date'])) {
                        $query->whereBetween('punchin_date', [$request['start_date'], $request['end_date']]);
                    }

                    // if(!empty($request['search']) && is_array($request['search']) == false){
                    //     $search = $request['search'] ;
                    //     $query->where(function($query) use($search) {
                    //         $query->where('punchin_date', 'like', "%{$search}%")
                    //         ->Orwhere('punchin_time', 'like', "%{$search}%")
                    //         ->Orwhere('working_type', 'like', "%{$search}%");
                    //     });
                    // }

                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                        $query->whereIn('user_id', $all_reporting_user_ids);
                    }
                })
                ->select('id', 'user_id', 'punchin_date', 'punchin_time', 'punchin_longitude', 'punchin_latitude', 'punchin_address', 'punchin_image', 'punchout_date', 'punchout_time', 'punchout_latitude', 'punchout_longitude', 'punchout_address', 'punchout_image', 'worked_time', 'punchin_summary', 'punchout_summary', 'working_type', 'attendance_status', 'remark_status', 'punchin_from')
                ->latest();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($data) {
                    if ($data->user_id == Auth::user()->id) {
                        return '';
                    }
                    return '<input type="checkbox" class="row-checkbox" value="' . $data->id . '">';
                })
                ->editColumn('punchin_date', function ($data) {
                    return isset($data->punchin_date) ? stringtodate($data->punchin_date) : '';
                })
                ->editColumn('punchin_from', function ($data) {
                    if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('Admin	')) {
                        return $data->punchin_from;
                    }
                    return '';
                })

                ->addColumn('current_status', function ($query) {
                    $status = '';
                    if ($query->attendance_status == '0') {
                        $status = 'pending';
                    } elseif ($query->attendance_status == '1') {
                        $status = 'approved';
                    } else {
                        $status = 'rejected';
                    }
                    return $status;
                })

                ->addColumn('punchout', function ($query) {
                    $punchout_image = !empty($query->punchout_image) ? env('IMAGE_UPLOADS') . $query->punchout_image : asset('assets/img/placeholder.jpg');
                    return '<img src="' . $punchout_image . '" border="0" width="70" class="img-rounded imageDisplayModel" align="center" />';
                })
                ->addColumn('action', function ($query) {
                    $btn = '';


                    if (auth()->user()->can(['attendance_delete'])) {
                        $btn = $btn . '<a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm deleteAttendance" value="' . $query->id . '" title="Delete Attendance">
                                        <i class="material-icons">clear</i>
                                      </a>';
                    }

                    if (auth()->user()->can(['attendance_punchout'])) {

                        $btn = $btn . '<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm removePunchout" value="' . $query->id . '" title="Remove Puncout">
                                    <i class="material-icons">schedule</i>
                                  </a>';
                    }



                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
                })

                ->addColumn('action_status', function ($query) {
                    $btn = '';
                    // if(auth()->user()->can(['attendance_delete'])  && $query->punchin_date == date('Y-m-d'))
                    // {

                    if ($query->attendance_status == 0) {

                        $btn = '<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm approve_status" value="' . $query->id . '" title="Approve Status">
                                        <i class="material-icons">approval</i>
                                      </a>
                                      <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm reject_status" value="' . $query->id . '" title="Reject Status">
                                    <i class="material-icons">cancel</i>
                                  </a>
                                  <a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm punchoutnow" value="' . $query->id . '" title="Punch Out Now">
                                    <i class="material-icons">pending</i>
                                  </a>
                                  ';
                    }
                    if ($query->attendance_status == 1) {

                        $btn = '<a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm reject_status" value="' . $query->id . '" title="Reject Status">
                                    <i class="material-icons">cancel</i>
                                  </a>';
                    }
                    if ($query->attendance_status == 2) {

                        $btn = '<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm approve_status" value="' . $query->id . '" title="Approve Status">
                                        <i class="material-icons">approval</i>
                                      </a>';
                    }


                    // }
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
                })




                ->rawColumns(['punchin', 'punchout', 'action', 'action_status', 'current_status', 'punchin_from', 'checkbox'])
                ->make(true);
        }
        $divisions = Division::latest()->get();
        return view('reports.attendancereport', compact('users', 'branches', 'divisions'));
    }


    // new summary attendance report start


    public function attendancereportSummary(Request $request)
    {
        abort_if(Gate::denies('attendance_summary_report'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $search_branches = $request->input('search_branches');
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_branches = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('name', 'asc')->get();
        $divisions = Division::latest()->get();
        $all_user_divisions = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->with('getdivision')->whereIn('id', $all_reporting_user_ids)->orderBy('name', 'asc')->get();
        $all_user_departments = Department::latest()->get();
        $branches = array();
        $all_branch = array();
        $bkey = 0;

        $divisions = Division::latest()->get();
        $all_division = array();
        $dkey = 0;

        foreach ($all_user_branches as $k => $val) {
            if ($val->getbranch) {
                if (!in_array($val->getbranch->id, $all_branch)) {
                    array_push($all_branch, $val->getbranch->id);
                    $branches[$bkey]['id'] = $val->getbranch->id;
                    $branches[$bkey]['name'] = $val->getbranch->branch_name;
                    $bkey++;
                }
            }
        }

        foreach ($all_user_divisions as $k => $val) {
            if ($val->getdivision) {
                if (!in_array($val->getdivision->id, $all_division)) {
                    array_push($all_branch, $val->getdivision->id);
                    $divisions[$dkey]['id'] = $val->getdivision->id;
                    $divisions[$dkey]['name'] = $val->getdivision->division_name;
                    $bkey++;
                }
            }
        }

        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            $all_reporting_user_ids = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->whereIn('id', $all_reporting_user_ids)->whereIn('branch_id', $search_branches)->orderBy('name', 'asc')->pluck('id')->toArray();
        }
        $all_user_details = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('name', 'asc')->get();
        $all_users = array();
        foreach ($all_user_details as $k => $val) {
            $users[$k]['id'] = $val->id;
            $users[$k]['name'] = $val->name;
        }
        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            if ($request->ajax()) {
                $response = ["users" => $users, "status" => true];
                return response()->json($response);
            }
        }
        if ($request->ajax()) {
            $data = Attendance::with('users:id,name')
                ->where(function ($query) use ($request, $all_reporting_user_ids) {
                    if (!empty($request['executive_id'])) {
                        $query->where('user_id', $request['executive_id']);
                    }

                    if (!empty($request['start_date']) && !empty($request['end_date'])) {
                        $start_date = Carbon::parse($request->start_date)->startOfDay();
                        $end_date = Carbon::parse($request->end_date)->endOfDay();
                        $query->whereBetween('punchin_date', [$start_date, $end_date]);
                    }

                    if ($request['status'] != null && $request['status'] != "") {
                        $query->where('attendance_status', $request['status']);
                    }

                    // if(!empty($request['search']) && is_array($request['search']) == false){
                    //     $search = $request['search'] ;
                    //     $query->where(function($query) use($search) {
                    //         $query->where('punchin_date', 'like', "%{$search}%")
                    //         ->Orwhere('punchin_time', 'like', "%{$search}%")
                    //         ->Orwhere('working_type', 'like', "%{$search}%");
                    //     });
                    // }

                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                        $query->whereIn('user_id', $all_reporting_user_ids);
                    }
                })
                ->select('id', 'user_id', 'punchin_date', 'punchin_time', 'punchin_longitude', 'punchin_latitude', 'punchin_address', 'punchin_image', 'punchout_date', 'punchout_time', 'punchout_latitude', 'punchout_longitude', 'punchout_address', 'punchout_image', 'worked_time', 'punchin_summary', 'punchout_summary', 'working_type', 'attendance_status', 'remark_status')
                ->latest();
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('punchin_date', function ($data) {
                    return isset($data->punchin_date) ? stringtodate($data->punchin_date) : '';
                })

                ->addColumn('current_status', function ($query) {
                    $status = '';
                    if ($query->attendance_status == '0') {
                        $status = 'pending';
                    } elseif ($query->attendance_status == '1') {
                        $status = 'approved';
                    } else {
                        $status = 'rejected';
                    }
                    return $status;
                })


                // ->editColumn('punchout_date', function($data)
                // {
                //     return isset($data->punchout_date) ? stringtodate($data->punchout_date) : stringtodate($data->punchin_date);
                // })

                // ->addColumn('punchin', function ($query) {
                //     $punchin_image = !empty($query->punchin_image) ? env('IMAGE_UPLOADS').$query->punchin_image : asset('assets/img/placeholder.jpg') ;
                //         return '<img src="'.$punchin_image.'" border="0" width="70" class="img-rounded imageDisplayModel" align="center" />';
                //     })
                ->addColumn('punchout', function ($query) {
                    $punchout_image = !empty($query->punchout_image) ? env('IMAGE_UPLOADS') . $query->punchout_image : asset('assets/img/placeholder.jpg');
                    return '<img src="' . $punchout_image . '" border="0" width="70" class="img-rounded imageDisplayModel" align="center" />';
                })
                ->addColumn('action', function ($query) {
                    $btn = '';
                    // if(auth()->user()->can(['attendance_delete'])  && $query->punchin_date == date('Y-m-d'))
                    // {
                    // $btn = '<a href="" class="btn btn-danger btn-just-icon btn-sm deleteAttendance" value="' . $query->id . '" title="Delete Attendance">
                    //                     <i class="material-icons">clear</i>
                    //                   </a>
                    //                   <a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm removePunchout" value="' . $query->id . '" title="Remove Puncout">
                    //                 <i class="material-icons">schedule</i>
                    //               </a>';


                    // }

                    if (auth()->user()->can(['attendance_delete'])) {
                        $btn = $btn . '<a href="" class="btn btn-danger btn-just-icon btn-sm deleteAttendance" value="' . $query->id . '" title="Delete Attendance">
                                        <i class="material-icons">clear</i>
                                      </a>';
                    }

                    if (auth()->user()->can(['attendance_punchout'])) {

                        $btn = $btn . '<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm removePunchout" value="' . $query->id . '" title="Remove Puncout">
                                    <i class="material-icons">schedule</i>
                                  </a>';
                    }



                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
                })

                ->addColumn('action_status', function ($query) {
                    $btn = '';
                    // if(auth()->user()->can(['attendance_delete'])  && $query->punchin_date == date('Y-m-d'))
                    // {

                    if ($query->attendance_status == 0) {

                        $btn = '<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm approve_status" value="' . $query->id . '" title="Approve Status">
                                        <i class="material-icons">approval</i>
                                      </a>
                                      <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm reject_status" value="' . $query->id . '" title="Reject Status">
                                    <i class="material-icons">cancel</i>
                                  </a>
                                  <a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm pending" value="' . $query->id . '" title="Pending">
                                    <i class="material-icons">pending</i>
                                  </a>
                                  ';
                    }
                    if ($query->attendance_status == 1) {

                        $btn = '<a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm reject_status" value="' . $query->id . '" title="Reject Status">
                                    <i class="material-icons">cancel</i>
                                  </a>';
                    }
                    if ($query->attendance_status == 2) {

                        $btn = '<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm approve_status" value="' . $query->id . '" title="Approve Status">
                                        <i class="material-icons">approval</i>
                                      </a>';
                    }


                    // }
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
                })



                ->rawColumns(['punchin', 'punchout', 'action', 'action_status', 'current_status'])
                ->make(true);
        }


        return view('reports.attendancereport_summary', compact('users', 'branches', 'divisions', 'all_user_departments'));
    }


    // new summary attendance report end








    public function counterVisitReportDownload(Request $request)
    {
        ////abort_if(Gate::denies('visitreport_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CounterVisitReportExport($request), 'counter_visit_report.xlsx');
    }
    public function beatAdherenceDetailDownload(Request $request)
    {
        ////abort_if(Gate::denies('visitreport_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new AdherenceDetailReportExport($request), 'adherence_detail_report.xlsx');
    }
    public function customersReport(Request $request)
    {
        $userids = getUsersReportingToAuth();
        if ($request->ajax()) {
            $data = Customers::with('customertypes', 'firmtypes', 'createdbyname')
                ->where(function ($query) use ($userids) {
                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                        $query->whereIn('executive_id', $userids);
                    }
                })
                ->latest();
            return Datatables::of($item)
                ->addIndexColumn()
                ->editColumn('created_at', function ($data) {
                    return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                })

                ->addColumn('image', function ($query) {
                    return '<img src="' . asset(!empty($query->profile_image) ? $query->profile_image : 'public/assets/img/placeholder.jpg') . '" border="0" width="70" class="rounded-circle imageDisplayModel" align="center" />';
                })
                ->rawColumns(['image'])
                ->make(true);
        }
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->get();
        return view('reports.customersreport', compact('users'));
    }

    public function loyaltyDealerWiseSummaryReport(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $branches = Branch::latest()->get();
        $dealers = Customers::where('customertype', ['1', '3'])->get();

        if ($request->ajax()) {
            $role = Role::find(29);
            $data = Customers::with('customertypes', 'firmtypes', 'createdbyname', 'getretailers', 'customeraddress.cityname', 'customeraddress.statename', 'getretailers.redemption', 'getretailers.transactions')->where('customertype', ['1', '3'])
                ->where(function ($query) use ($request, $userids, $role) {
                    if ($role && auth()->user()->hasRole($role->name)) {
                        $child_customer = ParentDetail::where('parent_id', auth()->user()->customerid)
                            ->pluck('customer_id')
                            ->push(auth()->user()->customerid);
                        $query->whereIn('id', $child_customer);
                    } else {
                        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                            $userIdsss = user::whereDoesntHave('roles', function ($query) {
                                $query->whereIn('id', config('constants.customer_roles'));
                            })->where('branch_id', $request->branch_id)->whereIn('id', $userids)->pluck('id');
                            $query->whereIn('executive_id', $userIdsss);
                        } else {
                            $query->whereIn('executive_id', $userids);
                        }
                    }


                    if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
                        $query->where('id', $request->dealer_id);
                    }
                })->orderBy('id', 'asc');
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($data) {
                    return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                })
                ->editColumn('branch', function ($data) {
                    return $data->createdbyname ? $data->createdbyname->getbranch->branch_name : '';
                })
                ->addColumn('total_registered_retailers', function ($data) {
                    $registeredRetailerCount = $data->getretailers->count();

                    return isset($registeredRetailerCount) ? $registeredRetailerCount : '';
                })
                ->addColumn('total_registered_retailers_under_saarthi', function ($data) {

                    $customerIds = $data->getretailers->pluck('customer_id');
                    $nosOfRetailerRegistredSaarthi = TransactionHistory::whereIn('customer_id', $customerIds)->groupBy('customer_id')->count();

                    return isset($nosOfRetailerRegistredSaarthi) ? $nosOfRetailerRegistredSaarthi : '';
                })
                ->addColumn('coupon_scan_nos', function ($data) {

                    $customerIds = $data->getretailers->pluck('customer_id');
                    $coupon_scan_nos = TransactionHistory::whereIn('customer_id', $customerIds)->count();

                    return isset($coupon_scan_nos) ? $coupon_scan_nos : '';
                })
                ->addColumn('mobile_app_downloads', function ($data) {

                    $customerIds = $data->getretailers->pluck('customer_id');
                    $mobile_app_downloads = MobileUserLoginDetails::whereIn('customer_id', $customerIds)->count();

                    return isset($mobile_app_downloads) ? $mobile_app_downloads : '';
                })
                ->addColumn('provision_point', function ($data) {

                    $customerIds = $data->getretailers->pluck('customer_id');
                    if (count($customerIds) > 0) {
                        $provision_point = TransactionHistory::whereIn('customer_id', $customerIds)->where('status', '0')->sum('provision_point');
                    } else {
                        $provision_point = 0;
                    }

                    return isset($provision_point) ? $provision_point : '';
                })
                ->addColumn('active_point', function ($data) {

                    $customerIds = $data->getretailers->pluck('customer_id');
                    if (count($customerIds) > 0) {
                        $active_point = TransactionHistory::whereIn('customer_id', $customerIds)->where('status', '1')->sum('point');
                        $active_point += TransactionHistory::whereIn('customer_id', $customerIds)->where('status', '0')->sum('active_point');
                    } else {
                        $active_point = 0;
                    }

                    return isset($active_point) ? $active_point : '';
                })
                ->addColumn('total_point', function ($data) {
                    $customerIds = $data->getretailers->pluck('customer_id');

                    if (count($customerIds) > 0) {
                        $total_point = TransactionHistory::whereIn('customer_id', $customerIds)->sum('point');
                    } else {
                        $total_point = 0;
                    }
                    return isset($total_point) ? $total_point : '';
                })
                ->addColumn('redeem_gift', function ($data) {
                    $customerIds = $data->getretailers->pluck('customer_id');
                    $redeem_gift = Redemption::with('customer')->where('status', '!=', '2')->whereIn('customer_id', $customerIds)->where('redeem_mode', '1')->sum('redeem_amount');

                    return isset($redeem_gift) ? $redeem_gift : '';
                })
                ->addColumn('redeem_neft', function ($data) {
                    $customerIds = $data->getretailers->pluck('customer_id');
                    $redeem_neft = Redemption::with('customer')->where('status', '!=', '2')->whereIn('customer_id', $customerIds)->where('redeem_mode', '2')->sum('redeem_amount');

                    return isset($redeem_neft) ? $redeem_neft : '';
                })
                ->addColumn('total_redeem', function ($data) {
                    $customerIds = $data->getretailers->pluck('customer_id');
                    $redeem_neft = Redemption::with('customer')->where('status', '!=', '2')->whereIn('customer_id', $customerIds)->where('redeem_mode', '2')->sum('redeem_amount');
                    $redeem_gift = Redemption::with('customer')->where('status', '!=', '2')->whereIn('customer_id', $customerIds)->where('redeem_mode', '1')->sum('redeem_amount');
                    $total_redeem = $redeem_gift + $redeem_neft;
                    return isset($total_redeem) ? $total_redeem : '';
                })
                ->addColumn('balance_active_point', function ($data) {
                    $customerIds = $data->getretailers->pluck('customer_id');
                    $total_redeem = Redemption::with('customer')->where('status', '!=', '2')->whereIn('customer_id', $customerIds)->sum('redeem_amount');
                    $active_point = TransactionHistory::whereIn('customer_id', $customerIds)->where('status', '1')->sum('point');
                    $active_point += TransactionHistory::whereIn('customer_id', $customerIds)->where('status', '0')->sum('active_point');

                    $balance_active_point = $active_point - $total_redeem;

                    return isset($balance_active_point) ? $balance_active_point : '';
                })

                ->rawColumns(['total_registered_retailers', 'total_registered_retailers_under_saarthi', 'coupon_scan_nos', 'mobile_app_downloads', 'provision_point', 'active_point', 'total_point', 'redeem_gift', 'redeem_neft', 'balance_active_point', 'created_at'])
                ->make(true);
        }
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('name', 'asc')->get();
        return view('reports.loyaltydealerwisesummaryreport', compact('branches', 'dealers'));
    }

    public function loyaltySummaryReport(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $states = State::latest()->get();
        if ($request->ajax()) {
            $userid = !empty($userid) ? $userid : Auth::user()->id;
            $userinfo = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->where('id', '=', $userid)->first();
            if ($request->state_id && !empty($request->state_id)) {
                $data = State::where('id', $request->state_id);
            } else if (!$userinfo->hasRole('superadmin') && !$userinfo->hasRole('Admin') && !$userinfo->hasRole('Sub_Admin') && !$userinfo->hasRole('HR_Admin') && !$userinfo->hasRole('HO_Account')  && !$userinfo->hasRole('Sub_Support') && !$userinfo->hasRole('Accounts Order') && !$userinfo->hasRole('Service Admin') && !$userinfo->hasRole('All Customers')) {
                $state_ids = City::whereIn('id', auth()->user()->cities->pluck('city_id'))->pluck('state_id');
                $data = State::whereIn('id', $state_ids)->orderBy('id', 'asc');
            } else {
                $data = State::orderBy('id', 'asc');
            }
            $retail_ids = Customers::with('getemployeedetail.employee_detail')->whereHas('getemployeedetail.employee_detail', function ($q) {
                $q->where('division_id', '10');
            })->where(['customertype' => '2', 'active' => 'Y'])->pluck('id');
            $customerIdsByState = Address::whereIn('customer_id', $retail_ids)
                ->get()
                ->groupBy('state_id')
                ->map(function ($addresses) {
                    return $addresses->pluck('customer_id');
                });
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('total_registered_retailers', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    $ttttsss = array();
                    foreach ($customerIds as $key => $value) {
                        if (!in_array($value, $ttttsss)) {
                            array_push($ttttsss, $value);
                        }
                    }
                    return count($ttttsss);
                })
                ->addColumn('total_registered_retailers_under_saarthi', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    return TransactionHistory::whereIn('customer_id', $customerIds)
                        ->select('customer_id')
                        ->groupBy('customer_id')
                        ->havingRaw('COUNT(*) > 1 OR SUM(CASE WHEN scheme_id IS NOT NULL THEN 1 ELSE 0 END) > 0')
                        ->count();
                })
                ->addColumn('coupon_scan_nos', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    return TransactionHistory::whereIn('customer_id', $customerIds)
                        ->select('customer_id')
                        ->whereNotNull('scheme_id')
                        ->count();
                })
                ->addColumn('mobile_app_downloads', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    return MobileUserLoginDetails::whereIn('customer_id', $customerIds)
                        ->count();
                })
                ->addColumn('provision_point', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    $provision_point = 0;
                    $thistorys = TransactionHistory::whereIn('customer_id', $customerIds)->get();
                    foreach ($thistorys as $thistory) {
                        if ($thistory->status != '1') {
                            $provision_point += $thistory->provision_point;
                        }
                    }
                    return $provision_point;
                })
                ->addColumn('active_point', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    $active_point = 0;
                    $thistorys = TransactionHistory::whereIn('customer_id', $customerIds)->whereNotNull('scheme_id')->get();
                    foreach ($thistorys as $thistory) {
                        if ($thistory->status == '1') {
                            $active_point += $thistory->point;
                        } else {
                            $active_point += $thistory->active_point;
                        }
                    }
                    return $active_point;
                })
                ->addColumn('total_point', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    return TransactionHistory::whereIn('customer_id', $customerIds)
                        ->whereNot('status', '2')
                        ->sum('point');
                })
                ->addColumn('redeem_gift', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    return Redemption::with('customer')
                        ->where('status', '!=', '2')
                        ->whereIn('customer_id', $customerIds)
                        ->where('redeem_mode', '1')
                        ->sum('redeem_amount');
                })
                ->addColumn('redeem_neft', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    return Redemption::with('customer')
                        ->where('status', '!=', '2')
                        ->whereIn('customer_id', $customerIds)
                        ->where('redeem_mode', '2')
                        ->sum('redeem_amount');
                })
                ->addColumn('total_redeem', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    $redeem_neft = Redemption::with('customer')
                        ->where('status', '!=', '2')
                        ->whereIn('customer_id', $customerIds)
                        ->where('redeem_mode', '2')
                        ->sum('redeem_amount');
                    $redeem_gift = Redemption::with('customer')
                        ->where('status', '!=', '2')
                        ->whereIn('customer_id', $customerIds)
                        ->where('redeem_mode', '1')
                        ->sum('redeem_amount');
                    return $redeem_gift + $redeem_neft;
                })
                ->addColumn('balance_active_point', function ($data) use ($customerIdsByState) {
                    $customerIds = $customerIdsByState->get($data->id, collect());
                    $redeem_neft = Redemption::with('customer')
                        ->where('status', '!=', '2')
                        ->whereIn('customer_id', $customerIds)
                        ->where('redeem_mode', '2')
                        ->sum('redeem_amount');
                    $redeem_gift = Redemption::with('customer')
                        ->where('status', '!=', '2')
                        ->whereIn('customer_id', $customerIds)
                        ->where('redeem_mode', '1')
                        ->sum('redeem_amount');
                    $total_redeem = $redeem_gift + $redeem_neft;

                    $active_point = 0;
                    $provision_point = 0;
                    $thistorys = TransactionHistory::whereIn('customer_id', $customerIds)->get();
                    foreach ($thistorys as $thistory) {
                        if ($thistory->status == '1') {
                            $active_point += $thistory->point;
                        } else {
                            $active_point += $thistory->active_point;
                            $provision_point += $thistory->provision_point;
                        }
                    }
                    $total_point = $provision_point + $active_point;

                    return $total_point - $total_redeem;
                })
                ->rawColumns(['total_registered_retailers', 'total_registered_retailers_under_saarthi', 'coupon_scan_nos', 'mobile_app_downloads', 'provision_point', 'active_point', 'total_point', 'redeem_gift', 'redeem_neft', 'balance_active_point', 'created_at'])
                ->make(true);
        }
        // $users = user::whereDoesntHave('roles', function ($query) {
        //     $query->whereIn('id', config('constants.customer_roles'));
        // })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
        //     if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
        //         $query->whereIn('id', $userids);
        //     }
        // })->select('id', 'name')->get();

        return view('reports.loyaltysummaryreport', compact('states'));
    }

    /*
    Loyalty summary report export
    */

    public function loyaltySummaryReportDownload(Request $request)
    {
        abort_if(Gate::denies('loyalty_summary_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new LoyaltySummaryReportExport($request), 'loyalty_summary.xlsx');
    }

    public function loyaltyDealerSummaryReportDownload(Request $request)
    {
        abort_if(Gate::denies('loyalty_summary_dealer_wise_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new LoyaltyDealerSummaryReportExport($request), 'loyalty_dealer_wise_summary.xlsx');
    }

    public function perDayCounterVisitReport(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $start_date = !empty($request->input('start_date')) ? $request->input('start_date') : date("Y-m-01");
        $end_date = !empty($request->input('end_date')) ? $request->input('end_date') : date("Y-m-t");
        if ($request->ajax()) {
            $data = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->where(function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('id', $userids);
                }
            })->whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['superadmin', 'Admin']);
            })->select('id', 'name', 'mobile', 'location')->orderBy('name', 'asc')->latest();
            $users = $data->pluck('id')->toArray();
            $workings = Attendance::whereIn('user_id', $users)->select('id', 'punchin_date', 'worked_time', 'working_type', 'user_id')->get();
            $attendances = $workings->map(function ($item) use ($start_date, $end_date) {
                $days = 0;
                switch ($item->worked_time) {
                    case (date('H', strtotime($item->worked_time))  >= 4 && date('H', strtotime($item->worked_time)) < 7):
                        $days = 0.5;
                        break;
                    case (date('H', strtotime($item->worked_time)) >= 7):
                        $days = 1;
                        break;
                    default:
                        break;
                }
                $item['field_working_days'] = ($item->working_type == 'Tour' || $item->working_type == 'Central Market' || $item->working_type == 'Suburban') ? $days : 0;
                $item['working_days'] = ($item->working_type != 'Tour' && $item->working_type != 'Central Market' && $item->working_type != 'Suburban') ? $days : 0;
                $item['range_field_working_days'] = ((date('Y-m-d', strtotime($item->punchin_date)) >= date('Y-m-d', strtotime($start_date))) && (date('Y-m-d', strtotime($item->punchin_date)) <= date('Y-m-d', strtotime($end_date))) && $item->working_type == 'Tour' || $item->working_type == 'Central Market' || $item->working_type == 'Suburban') ? $days : 0;
                $item['range_working_days'] = (date('Y-m-d', strtotime($item->punchin_date)) >= date('Y-m-d', strtotime($start_date))) && (date('Y-m-d', strtotime($item->punchin_date)) <= date('Y-m-d', strtotime($end_date)) && $item->working_type != 'Tour' && $item->working_type != 'Central Market' && $item->working_type != 'Suburban') ? $days : 0;
                return $item;
            });

            $counters = CheckIn::with('customers:id,created_at')->whereIn('user_id', $users)->select('customer_id', 'checkin_date', 'user_id')->get();
            $revisited = $counters->map(function ($item) use ($start_date, $end_date) {
                $item['revisited_counters'] = (date("Y-m-d", strtotime($item['customers']['created_at'])) != date("Y-m-d", strtotime($item['checkin_date']))) ? 1 : 0;
                $item['range_revisited_counters'] = (date('Y-m-d', strtotime($item->checkin_date)) >= date('Y-m-d', strtotime($start_date))) && (date('Y-m-d', strtotime($item->checkin_date)) <= date('Y-m-d', strtotime($end_date)) && (date("Y-m-d", strtotime($item['customers']['created_at'])) != date("Y-m-d", strtotime($item['checkin_date'])))) ? 1 : 0;
                return $item;
            });
            $customers = Customers::whereIn('created_by', $users)->select('created_at', 'created_by');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('non_field_working_days', function ($query) use ($attendances) {
                    return $attendances->where('user_id', $query->id)->sum('working_days');
                })
                ->addColumn('field_working_days', function ($query) use ($attendances) {
                    return $attendances->where('user_id', $query->id)->sum('field_working_days');
                })
                ->addColumn('new_visit_counters', function ($query) use ($customers) {
                    return $customers->where('created_by', $query->id)->count();
                })
                ->addColumn('revisited_counters', function ($query) use ($revisited) {
                    return $revisited->where('user_id', $query->id)->sum('revisited_counters');
                })
                ->addColumn('visits_per_day', function ($query) use ($revisited, $attendances, $customers) {
                    $visit_counters = $customers->where('created_by', $query->id)->count() + $revisited->where('user_id', $query->id)->sum('revisited_counters');
                    $working_days = $attendances->where('user_id', $query->id)->sum('field_working_days');
                    return !empty($visit_counters) ? floor($visit_counters / $working_days) : '';
                })
                ->addColumn('between_non_field_working_days', function ($query) use ($attendances, $start_date, $end_date) {
                    return $attendances->where('user_id', $query->id)->where('punchin_date', '>=', date('Y-m-d', strtotime($start_date)))->where('punchin_date', '<=', date('Y-m-d', strtotime($end_date)))->sum('working_days');
                })
                ->addColumn('between_field_working_days', function ($query) use ($attendances, $start_date, $end_date) {
                    return $attendances->where('user_id', $query->id)->where('punchin_date', '>=', date('Y-m-d', strtotime($start_date)))->where('punchin_date', '<=', date('Y-m-d', strtotime($end_date)))->sum('field_working_days');
                })
                ->addColumn('between_new_visit_counters', function ($query) use ($customers, $start_date, $end_date) {
                    return $customers->where('created_by', $query->id)->where('created_at', '>=', date('Y-m-d', strtotime($start_date)))->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)))->count();
                })
                ->addColumn('between_revisited_counters', function ($query) use ($revisited, $start_date, $end_date) {
                    return $revisited->where('user_id', $query->id)->where('checkin_date', '>=', date('Y-m-d', strtotime($start_date)))->where('checkin_date', '<=', date('Y-m-d', strtotime($end_date)))->sum('revisited_counters');
                })
                ->addColumn('between_visits_per_day', function ($query) use ($revisited, $attendances, $customers, $start_date, $end_date) {
                    $visit_counters = $customers->where('created_by', $query->id)->whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)))->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)))->count() + $revisited->where('user_id', $query->id)->where('checkin_date', '>=', date('Y-m-d', strtotime($start_date)))->where('checkin_date', '<=', date('Y-m-d', strtotime($end_date)))->sum('revisited_counters');
                    $working_days = $attendances->where('user_id', $query->id)->where('punchin_date', '>=', date('Y-m-d', strtotime($start_date)))->where('punchin_date', '<=', date('Y-m-d', strtotime($end_date)))->sum('field_working_days');
                    return !empty($visit_counters) ? floor($visit_counters / $working_days) : '';
                })
                ->rawColumns(['non_field_working_days', 'field_working_days', 'new_visit_counters', 'revisited_counters', 'visits_per_day', 'between_non_field_working_days', 'between_field_working_days', 'between_new_visit_counters', 'between_revisited_counters', 'between_visits_per_day'])
                ->make(true);
        }
        return view('reports.countervisitreport');
    }

    public function fieldActivity(Request $request)
    {
        return view('reports.fieldactivity');
    }

    public function tourProgramme(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['superadmin', 'Admin']);
        })->select('id', 'name', 'mobile')->orderBy('name', 'asc')->get();
        return view('reports.tourprogramme', compact('users'));
    }

    public function monthlyMovement(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['superadmin', 'Admin']);
        })->select('id', 'name', 'mobile')->orderBy('name', 'asc')->get();
        return view('reports.monthlymovement', compact('users'));
    }

    public function pointCollections(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['superadmin', 'Admin']);
        })->select('id', 'name', 'mobile')->orderBy('name', 'asc')->get();
        return view('reports.pointcollections', compact('users'));
    }
    public function territoryCoverage(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['superadmin', 'Admin']);
        })->select('id', 'name', 'mobile')->orderBy('name', 'asc')->get();
        return view('reports.territorycoverage', compact('users'));
    }
    public function performanceParameter(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['superadmin', 'Admin']);
        })->select('id', 'name', 'mobile')->orderBy('name', 'asc')->get();
        return view('reports.performanceparameter', compact('users'));
    }
    public function asmWiseMechanicsPoints(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['superadmin', 'Admin']);
        })->select('id', 'name', 'mobile')->orderBy('name', 'asc')->get();
        return view('reports.asmwisemechanicspoints', compact('users'));
    }
    public function targetVsSales(Request $request)
    {
        return view('reports.targetvssales');
    }

    public function fieldActivityReportData(Request $request)
    {
        try {
            $userids = getUsersReportingToAuth();
            $threemonth = date("Y-m-d", strtotime("-3 Months"));
            $sixmonth = date("Y-m-d", strtotime("-6 Months"));
            $fromdate = date("Y-m-01");
            $todate = date("Y-m-t");
            $users = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->with('cities', 'roles')->where(function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('id', $userids);
                }
            })
                ->whereHas('roles', function ($query) {
                    $query->whereNotIn('name', ['superadmin', 'Admin']);
                })->where('active', '=', 'Y')->select('id', 'name', 'location')->orderBy('name', 'asc')->get();
            $data = $users->map(function ($item, $key) use ($fromdate, $todate, $threemonth, $sixmonth) {
                $visited = collect([
                    'new_dealer_visited' => 0,
                    'existing_dealer_visited' => 0,
                    'total_dealer_visited' => 0,
                    'new_mechanic_visited' => 0,
                    'existing_mechanic_visited' => 0,
                    'total_mechanic_visited' => 0,
                ]);
                $cities = UserCityAssign::with('cityname')->where('userid', '=', $item['id'])->select('city_id')->get();
                $tourdetails = TourDetail::with('visitedcities', 'tourinfo')->whereHas('tourinfo', function ($query) use ($sixmonth, $todate, $item) {
                    if ($sixmonth) {
                        $query->whereDate('date', '>=', date('Y-m-d', strtotime($sixmonth)));
                    }
                    if ($todate) {
                        $query->whereDate('date', '<=', date('Y-m-d', strtotime($todate)));
                    }
                    $query->where('userid', '=', $item['id']);
                })
                    ->whereNotNull('visited_cityid')
                    ->select('visited_cityid', 'tourid')
                    ->get();
                $checkins =  CheckIn::with('customers')->where(function ($query) use ($fromdate, $todate, $item) {
                    if ($fromdate) {
                        $query->where('checkin_date', '>=', date('Y-m-d', strtotime($fromdate)));
                    }
                    if ($todate) {
                        $query->where('checkin_date', '<=', date('Y-m-d', strtotime($todate)));
                    }
                    $query->where('user_id', '=', $item['id']);
                })
                    ->select('customer_id', 'checkin_date')->get();

                $checkins->map(function ($item2) use ($visited) {
                    if (date("Y-m-d", strtotime($item2['customers']['created_at'])) != date("Y-m-d", strtotime($item2['checkin_date']))) {
                        if ($item2['customers']['customertype'] == 2) {
                            $visited['existing_dealer_visited'] = $visited['existing_dealer_visited'] + 1;
                            $visited['total_dealer_visited'] = $visited['total_dealer_visited'] + 1;
                        }
                        if ($item2['customers']['customertype'] == 4) {
                            $visited['existing_mechanic_visited'] = $visited['existing_mechanic_visited'] + 1;
                            $visited['total_mechanic_visited'] = $visited['total_mechanic_visited'] + 1;
                        }
                    } else {
                        if ($item2['customers']['customertype'] == 2) {
                            $visited['new_dealer_visited'] = $visited['new_dealer_visited'] + 1;
                            $visited['total_dealer_visited'] = $visited['total_dealer_visited'] + 1;
                        }
                        if ($item2['customers']['customertype'] == 4) {
                            $visited['new_mechanic_visited'] = $visited['new_mechanic_visited'] + 1;
                            $visited['total_mechanic_visited'] = $visited['total_mechanic_visited'] + 1;
                        }
                    }
                });

                $points = Wallet::with('customers')->where(function ($query) use ($fromdate, $todate, $item) {
                    if ($fromdate) {
                        $query->where('transaction_at', '>=', date('Y-m-d', strtotime($fromdate)));
                    }
                    if ($todate) {
                        $query->where('transaction_at', '<=', date('Y-m-d', strtotime($todate)));
                    }
                    $query->where('transaction_type', '=', 'Cr');
                    $query->where('userid', '=', $item['id']);
                })
                    ->select('points', 'customer_id')->get();
                $item['month'] = date('M');
                $item['stations_in_territory_a'] = $cities->where('cityname.grade', '=', 'A')->count();
                $item['stations_in_territory_b'] = $cities->where('cityname.grade', '=', 'B')->count();
                $item['stations_in_territory_c'] = $cities->where('cityname.grade', '=', 'C')->count();
                $item['stations_in_territory_total'] = $cities->count();
                $item['stations_visited_month_a'] = $tourdetails->where('tourinfo.date', '>=', date('Y-m-d', strtotime($fromdate)))->where('visitedcities.grade', '=', 'A')->unique('visited_cityid')->count();
                $item['stations_visited_month_b'] = $tourdetails->where('tourinfo.date', '>=', date('Y-m-d', strtotime($fromdate)))->where('visitedcities.grade', '=', 'B')->unique('visited_cityid')->count();
                $item['stations_visited_month_c'] = $tourdetails->where('tourinfo.date', '>=', date('Y-m-d', strtotime($fromdate)))->where('visitedcities.grade', '=', 'C')->unique('visited_cityid')->count();
                $item['stations_visited_month_total'] = $tourdetails->where('tourinfo.date', '>=', date('Y-m-d', strtotime($fromdate)))->unique('visited_cityid')->count();
                $item['new_dealer_visited'] = $visited['new_dealer_visited'];
                $item['existing_dealer_visited'] = $visited['existing_dealer_visited'];
                $item['total_dealer_visited'] = $visited['total_dealer_visited'];
                $item['new_mechanic_visited'] = $visited['new_mechanic_visited'];
                $item['existing_mechanic_visited'] = $visited['existing_mechanic_visited'];
                $item['total_mechanic_visited'] = $visited['total_mechanic_visited'];
                $item['points_collected_garage'] = $points->where('customers.customertype', '=', 4)->sum('points');
                $item['points_collected_dealer'] = $points->where('customers.customertype', '=', 2)->sum('points');
                $item['stations_visited_last_three_months'] = $tourdetails->where('tourinfo.date', '>=', date('Y-m-d', strtotime($threemonth)))->unique('visited_cityid')->count();
                $item['stations_visited_last_three_months_abc'] = $tourdetails->where('tourinfo.date', '>=', date('Y-m-d', strtotime($threemonth)))->whereIn('visitedcities.grade', ['A', 'B', 'C'])->unique('visited_cityid')->count();
                $item['stations_visited_last_six_months'] = $tourdetails->unique('visited_cityid')->count();
                $item['stations_visited_last_six_months_abc'] = $tourdetails->whereIn('visitedcities.grade', ['A', 'B', 'C'])->unique('visited_cityid')->count();
                $item['stations_activity_name'] = isset($item['location']) ? $item['location'] : '';
                $item['sales_blitz_activity_date'] = '';
                $item['nukkad_activity_date'] = '';
                $item['road_show_activity_date'] = '';
                $item['van_campaign_activity_date'] = '';
                $item['activity_expenses'] = '';
                $item['remarks'] = '';
                return $item;
            });
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function tourProgrammeReportData(Request $request)
    {
        try {
            $userid = !empty($request->input('user_id')) ? $request->input('user_id') : Auth::user()->id;
            $fromdate = date("Y-m-01");
            $todate = date("Y-m-t");
            $tours = TourProgramme::with('tourdetails')->where('userid', '=', $userid)
                ->where('date', '>=', date('Y-m-d', strtotime($fromdate)))
                ->where('date', '<=', date('Y-m-d', strtotime($todate)))
                ->select('id', 'date', 'objectives', 'town', 'type', 'status')
                ->get();
            $finaldata = $tours->map(function ($item, $key) {
                $category = collect([]);
                $visited_date = collect([]);
                foreach ($item['tourdetails'] as $key => $detail) {
                    if (!empty($detail['cityname'])) {
                        $category->push($detail['cityname']['grade']);
                    }
                    if (!empty($detail['visited_date'])) {
                        $visited_date->push($detail['visited_date']);
                    }
                }
                $unique_category = $category->unique()->toArray();
                $unique_visited = $visited_date->unique()->toArray();
                $item['category'] = implode(',', $unique_category);
                $item['last_visit_date'] = implode(',', $unique_visited);
                return $item;
            });
            $collections['tours'] = $finaldata;
            $collections['users'] = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->where('id', $userid)->select('name', 'location')->first();
            return response()->json($collections);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function monthlyMovementReportData(Request $request)
    {
        try {
            $userid = !empty($request->input('user_id')) ? $request->input('user_id') : Auth::user()->id;
            $fromdate = date("Y-m-01");
            $todate = date("Y-m-t");
            $tours = TourProgramme::with('tourdetails')->where('userid', '=', $userid)
                ->where('date', '>=', date('Y-m-d', strtotime($fromdate)))
                ->where('date', '<=', date('Y-m-d', strtotime($todate)))
                ->select('id', 'userid', 'date', 'objectives', 'type', 'town')
                ->get();
            $finaldata = $tours->map(function ($item, $key) {
                switch ($item['type']) {
                    case 'Tour':
                        $item['type'] = 'T';
                        break;
                    case 'Office Work':
                        $item['type'] = 'O';
                        break;
                    case 'Suburban':
                        $item['type'] = 'S';
                        break;
                    case 'Central Market':
                        $item['type'] = 'C';
                        break;
                    case 'Holiday':
                        $item['type'] = 'H';
                        break;
                    case 'Leave':
                        $item['type'] = 'L';
                        break;
                    default:
                        $item['type'] = '';
                        break;
                }
                $grade = collect([]);
                $cityname = collect([]);
                if (!empty($item['tourdetails'])) {
                    foreach ($item['tourdetails'] as $key => $detail) {
                        if (!empty($detail['visited_cityid'])) {
                            $grade->push($detail['visitedcities']['grade']);
                            $cityname->push($detail['visitedcities']['city_name']);
                        }
                    }
                }
                $item['actual_visited'] = implode(',', $cityname->unique()->toArray());
                $item['grade'] = implode(',', $grade->unique()->toArray());
                $item['dealer_visited'] = CheckIn::whereHas('customers', function ($query) {
                    $query->where('customertype', '=', 2);
                })
                    ->whereDate('checkin_date', '=', date('Y-m-d', strtotime($item['date'])))
                    ->where('user_id', '=', $item['userid'])
                    ->count();
                $item['mechanic_visited'] = CheckIn::whereHas('customers', function ($query) {
                    $query->where('customertype', '=', 4);
                })
                    ->whereDate('checkin_date', '=', date('Y-m-d', strtotime($item['date'])))
                    ->where('user_id', '=', $item['userid'])
                    ->count();
                $item['stu_visited'] = CheckIn::whereHas('customers', function ($query) {
                    $query->where('customertype', '=', 5);
                })
                    ->whereDate('checkin_date', '=', date('Y-m-d', strtotime($item['date'])))
                    ->where('user_id', '=', $item['userid'])
                    ->count();
                $item['fleet_owner_visited'] = CheckIn::whereHas('customers', function ($query) {
                    $query->where('customertype', '=', 6);
                })
                    ->whereDate('checkin_date', '=', date('Y-m-d', strtotime($item['date'])))
                    ->where('user_id', '=', $item['userid'])
                    ->count();
                $item['total_visited'] = CheckIn::whereDate('checkin_date', '=', date('Y-m-d', strtotime($item['date'])))->where('user_id', '=', $item['userid'])->count();
                $points = Wallet::whereDate('transaction_at', '=', date('Y-m-d', strtotime($item['date'])))
                    ->where('userid', '=', $item['userid'])
                    ->select('points', 'quantity', 'point_type')
                    ->get();
                $item['no_of_coupons'] = $points->where('point_type', '=', 'coupons')->sum('quantity');
                $item['total_points'] = $points->where('point_type', '=', 'coupons')->sum('points');
                $item['no_of_gifts'] = $points->where('point_type', '=', 'gifts')->sum('quantity');
                $item['gift_value'] = $points->where('point_type', '=', 'gifts')->sum('points');
                return $item;
            });
            $data['users'] = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->where('id', $userid)->select('name', 'location')->first();
            $data['tours'] = $finaldata;
            $data['total'] = collect([
                'dealer_visited' => $finaldata->sum('dealer_visited'),
                'mechanic_visited' => $finaldata->sum('mechanic_visited'),
                'stu_visited' => $finaldata->sum('stu_visited'),
                'fleet_owner_visited' => $finaldata->sum('fleet_owner_visited'),
                'total_visited' => $finaldata->sum('total_visited'),
                'no_of_coupons' => $finaldata->sum('no_of_coupons'),
                'total_points' => $finaldata->sum('total_points'),
                'no_of_gifts' => $finaldata->sum('no_of_gifts'),
                'gift_value' => $finaldata->sum('gift_value'),
            ]);
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function pointCollectionReportData(Request $request)
    {
        try {
            $userid = !empty($request->input('user_id')) ? $request->input('user_id') : Auth::user()->id;
            $fromdate = date("Y-m-01");
            $todate = date("Y-m-t");
            $points = Wallet::with('customers', 'customers.customeraddress.cityname')->where('userid', '=', $userid)
                ->where('transaction_at', '>=', date('Y-m-d', strtotime($fromdate)))
                ->where('transaction_at', '<=', date('Y-m-d', strtotime($todate)))
                ->where('transaction_type', '=', 'Cr')
                ->select(['customer_id', DB::raw("SUM(quantity) as total_quantity"), DB::raw("SUM(points) as total_points")])
                ->groupBy('customer_id')
                ->get();
            $finaldata = $points->map(function ($item, $key) {
                $item['city_name'] = isset($item['customers']['customeraddress']['cityname']['city_name']) ? $item['customers']['customeraddress']['cityname']['city_name'] : '';
                return $item;
            });
            $data['users'] = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->where('id', $userid)->select('name', 'location')->first();
            $data['points'] = $finaldata;
            $data['total'] = collect([
                'total_quantity' => $points->sum('total_quantity'),
                'total_points' => $points->sum('total_points'),
            ]);
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function territoryCoverageReportData(Request $request)
    {
        try {
            $userid = !empty($request->input('user_id')) ? $request->input('user_id') : Auth::user()->id;
            $fromdate = date("Y-m-01");
            $todate = date("Y-m-t");
            $cities = UserCityAssign::with('cityname')->where('userid', $userid)->select('city_id')->get();
            $finaldata = $cities->map(function ($item, $key) use ($fromdate, $todate, $userid) {
                $item['city_name'] = isset($item['cityname']['city_name']) ? $item['cityname']['city_name'] : '';
                $item['grade'] = isset($item['cityname']['grade']) ? $item['cityname']['grade'] : '';
                $item['total_dealer'] = Customers::where('customertype', '=', '2')
                    ->whereHas('customeraddress', function ($query) use ($item) {
                        $query->where('city_id', '=', $item['city_id']);
                    })->count();
                $item['total_mechanic'] = Customers::where('customertype', '=', '4')
                    ->whereHas('customeraddress', function ($query) use ($item) {
                        $query->where('city_id', '=', $item['city_id']);
                    })->count();
                $item['dealer_visited'] = CheckIn::whereHas('customers', function ($query) use ($item) {
                    $query->where('customertype', '=', 2);
                    $query->whereHas('customeraddress', function ($query) use ($item) {
                        $query->where('city_id', '=', $item['city_id']);
                    });
                })
                    ->whereDate('checkin_date', '>=', date('Y-m-d', strtotime($fromdate)))
                    ->whereDate('checkin_date', '<=', date('Y-m-d', strtotime($todate)))
                    ->where('user_id', '=', $item['userid'])
                    ->count();
                $item['mechanic_visited'] = CheckIn::whereHas('customers', function ($query) use ($item) {
                    $query->where('customertype', '=', 4);
                    $query->whereHas('customeraddress', function ($query) use ($item) {
                        $query->where('city_id', '=', $item['city_id']);
                    });
                })
                    ->whereDate('checkin_date', '>=', date('Y-m-d', strtotime($fromdate)))
                    ->whereDate('checkin_date', '<=', date('Y-m-d', strtotime($todate)))
                    ->where('user_id', '=', $item['userid'])
                    ->count();
                $item['gift_coupons'] = Wallet::whereHas('customers', function ($query) use ($item) {
                    $query->whereHas('customeraddress', function ($query) use ($item) {
                        $query->where('city_id', '=', $item['city_id']);
                    });
                })
                    ->where('userid', '=', $userid)
                    ->where('transaction_at', '>=', date('Y-m-d', strtotime($fromdate)))
                    ->where('transaction_at', '<=', date('Y-m-d', strtotime($todate)))
                    ->where('point_type', '=', 'coupon')
                    ->where('transaction_type', '=', 'Cr')
                    ->sum('points');
                $item['mrp_label'] = Wallet::whereHas('customers', function ($query) use ($item) {
                    $query->whereHas('customeraddress', function ($query) use ($item) {
                        $query->where('city_id', '=', $item['city_id']);
                    });
                })
                    ->where('userid', '=', $userid)
                    ->where('transaction_at', '>=', date('Y-m-d', strtotime($fromdate)))
                    ->where('transaction_at', '<=', date('Y-m-d', strtotime($todate)))
                    ->where('point_type', '=', 'mrp')
                    ->where('transaction_type', '=', 'Cr')
                    ->sum('points');
                $item['total_dealer_visited'] = CheckIn::whereHas('customers', function ($query) use ($item) {
                    $query->where('customertype', '=', 2);
                    $query->whereHas('customeraddress', function ($query) use ($item) {
                        $query->where('city_id', '=', $item['city_id']);
                    });
                })
                    ->where('user_id', '=', $item['userid'])
                    ->count();
                $item['total_mechanic_visited'] = CheckIn::whereHas('customers', function ($query) use ($item) {
                    $query->where('customertype', '=', 4);
                    $query->whereHas('customeraddress', function ($query) use ($item) {
                        $query->where('city_id', '=', $item['city_id']);
                    });
                })
                    ->where('user_id', '=', $item['userid'])
                    ->count();
                $item['total_gift_coupons'] = Wallet::whereHas('customers', function ($query) use ($item) {
                    $query->whereHas('customeraddress', function ($query) use ($item) {
                        $query->where('city_id', '=', $item['city_id']);
                    });
                })
                    ->where('userid', '=', $userid)
                    ->where('point_type', '=', 'coupon')
                    ->where('transaction_type', '=', 'Cr')
                    ->sum('points');
                $item['total_mrp_label'] = Wallet::whereHas('customers', function ($query) use ($item) {
                    $query->whereHas('customeraddress', function ($query) use ($item) {
                        $query->where('city_id', '=', $item['city_id']);
                    });
                })
                    ->where('userid', '=', $userid)
                    ->where('point_type', '=', 'mrp')
                    ->where('transaction_type', '=', 'Cr')
                    ->sum('points');
                return $item;
            });

            $data['users'] = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->where('id', $userid)->select('name', 'location')->first();
            $data['cities'] = $finaldata;
            $data['total'] = collect([
                'total_dealer' => $finaldata->sum('total_dealer'),
                'total_mechanic' => $finaldata->sum('total_mechanic'),
                'dealer_visited' => $finaldata->sum('dealer_visited'),
                'mechanic_visited' => $finaldata->sum('mechanic_visited'),
                'gift_coupons' => $finaldata->sum('gift_coupons'),
                'mrp_label' => $finaldata->sum('mrp_label'),
                'total_dealer_visited' => $finaldata->sum('total_dealer_visited'),
                'total_mechanic_visited' => $finaldata->sum('total_mechanic_visited'),
                'total_gift_coupons' => $finaldata->sum('total_gift_coupons'),
                'total_mrp_label' => $finaldata->sum('total_mrp_label'),
            ]);
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function performanceParameterReportData(Request $request)
    {
        try {
            $userid = !empty($request->input('user_id')) ? $request->input('user_id') : Auth::user()->id;
            $perameters = collect([
                collect(["name" => "days_toured", "parameter" => "NO. OF DAYS TOURED"]),
                collect(["name" => "cities_covered", "parameter" => "NO. OF CITIES COVERED"]),
                collect(["name" => "mechanic_visited", "parameter" => "NO. OF MECHANIC VISTED"]),
                collect(["name" => "dealer_visited", "parameter" => "NO. OF DEALER VISITED"]),
                collect(["name" => "gift_collected", "parameter" => "MECHANIC GIFT POINT COLLECTED"]),
                collect(["name" => "mechanic_registered", "parameter" => "NO. OF MECHANIC REGISTERED"]),
                collect(["name" => "gift_settled", "parameter" => "NOS. OF GIFT SETTLED"]),
                collect(["name" => "mechanic_points", "parameter" => "NO. OF MECHANIC POINTS GIVEN"]),
                collect(["name" => "order_collected", "parameter" => "ORDER COLLECTED(VALUE IN LAC)"])
            ]);
            $tours = TourDetail::with('beatschedules')->whereHas('tourinfo', function ($query) use ($userid) {
                $query->where('userid', '=', $userid);
                $query->whereYear('date', '=', date('Y'));
            })
                ->whereNotNull('visited_cityid')
                ->select('visited_date', 'visited_cityid');

            $dealervisited = CheckIn::whereHas('customers', function ($query) {
                $query->where('customertype', '=', 2);
            })
                ->where('user_id', '=', $userid)
                ->whereYear('checkin_date', '=', date('Y'))
                ->select('checkin_date');
            $mechanicvisited = CheckIn::whereHas('customers', function ($query) {
                $query->where('customertype', '=', 4);
            })
                ->where('user_id', '=', $userid)
                ->whereYear('checkin_date', '=', date('Y'))
                ->select('checkin_date', 'customer_id');
            $points = Wallet::with('customers')->where('userid', '=', $userid)
                ->whereYear('transaction_at', '=', date('Y'))
                ->where('transaction_type', '=', 'Cr')
                ->select('transaction_at', 'points', 'point_type', 'quantity', 'transaction_type');
            $customers = Customers::where('created_by', '=', $userid)
                ->whereYear('created_at', '=', date('Y'))
                ->where('customertype', '=', 4)
                ->select('created_at');
            $orders = Order::where('created_by', '=', $userid)
                ->whereYear('order_date', '=', date('Y'))
                ->select('order_date', 'grand_total');

            $finaldata = $perameters->map(function ($item, $key) use ($userid, $tours, $dealervisited, $mechanicvisited, $points, $customers, $orders) {
                switch ($item['name']) {
                    case 'days_toured':
                        $item['jan'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-01-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-01-31"))))->count();
                        $item['feb'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-02-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-02-29"))))->count();
                        $item['mar'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-03-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-03-31"))))->count();
                        $item['apr'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-04-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-04-30"))))->count();
                        $item['may'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-05-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-05-31"))))->count();
                        $item['jun'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-06-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-06-30"))))->count();
                        $item['jul'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-07-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-07-31"))))->count();
                        $item['aug'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-08-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-08-31"))))->count();
                        $item['sep'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-09-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-09-30"))))->count();
                        $item['oct'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-10-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-10-31"))))->count();
                        $item['nov'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-11-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-11-30"))))->count();
                        $item['dec'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-12-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-12-31"))))->count();
                        $item['total'] = $tours->count();
                        $item['apm'] =  $tours->count();
                        break;
                    case 'cities_covered':
                        $item['jan'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-01-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-01-31"))))->distinct('visited_cityid')->count();
                        $item['feb'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-02-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-02-29"))))->distinct('visited_cityid')->count();
                        $item['mar'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-03-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-03-31"))))->distinct('visited_cityid')->count();
                        $item['apr'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-04-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-04-30"))))->distinct('visited_cityid')->count();
                        $item['may'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-05-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-05-31"))))->distinct('visited_cityid')->count();
                        $item['jun'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-06-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-06-30"))))->distinct('visited_cityid')->count();
                        $item['jul'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-07-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-07-31"))))->distinct('visited_cityid')->count();
                        $item['aug'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-08-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-08-31"))))->distinct('visited_cityid')->count();
                        $item['sep'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-09-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-09-30"))))->distinct('visited_cityid')->count();
                        $item['oct'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-10-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-10-31"))))->distinct('visited_cityid')->count();
                        $item['nov'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-11-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-11-30"))))->distinct('visited_cityid')->count();
                        $item['dec'] = $tours->where('visited_date', '>=', date('Y-m-d', strtotime(date("Y-12-01"))))->where('visited_date', '<=', date('Y-m-d', strtotime(date("Y-12-31"))))->distinct('visited_cityid')->count();
                        $item['total'] = $tours->distinct('visited_cityid')->count();
                        $item['apm'] =  $tours->distinct('visited_cityid')->count();
                        break;
                    case 'mechanic_visited':
                        $item['jan'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-01-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-01-31"))))->distinct('customer_id')->count();
                        $item['feb'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-02-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-02-29"))))->distinct('customer_id')->count();
                        $item['mar'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-03-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-03-31"))))->distinct('customer_id')->count();
                        $item['apr'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-04-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-04-30"))))->distinct('customer_id')->count();
                        $item['may'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-05-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-05-31"))))->distinct('customer_id')->count();
                        $item['jun'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-06-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-06-30"))))->distinct('customer_id')->count();
                        $item['jul'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-07-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-07-31"))))->distinct('customer_id')->count();
                        $item['aug'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-08-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-08-31"))))->distinct('customer_id')->count();
                        $item['sep'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-09-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-09-30"))))->distinct('customer_id')->count();
                        $item['oct'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-10-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-10-31"))))->distinct('customer_id')->count();
                        $item['nov'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-11-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-11-30"))))->distinct('customer_id')->count();
                        $item['dec'] = $mechanicvisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-12-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-12-31"))))->distinct('customer_id')->count();
                        $item['total'] = $mechanicvisited->distinct('customer_id')->count();
                        $item['apm'] =  $mechanicvisited->distinct('customer_id')->count();
                        break;
                    case 'dealer_visited':
                        $item['jan'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-01-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-01-31"))))->distinct('customer_id')->count();
                        $item['feb'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-02-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-02-29"))))->distinct('customer_id')->count();
                        $item['mar'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-03-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-03-31"))))->distinct('customer_id')->count();
                        $item['apr'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-04-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-04-30"))))->distinct('customer_id')->count();
                        $item['may'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-05-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-05-31"))))->distinct('customer_id')->count();
                        $item['jun'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-06-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-06-30"))))->distinct('customer_id')->count();
                        $item['jul'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-07-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-07-31"))))->distinct('customer_id')->count();
                        $item['aug'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-08-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-08-31"))))->distinct('customer_id')->count();
                        $item['sep'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-09-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-09-30"))))->distinct('customer_id')->count();
                        $item['oct'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-10-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-10-31"))))->distinct('customer_id')->count();
                        $item['nov'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-11-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-11-30"))))->distinct('customer_id')->count();
                        $item['dec'] = $dealervisited->where('checkin_date', '>=', date('Y-m-d', strtotime(date("Y-12-01"))))->where('checkin_date', '<=', date('Y-m-d', strtotime(date("Y-12-31"))))->distinct('customer_id')->count();
                        $item['total'] = $dealervisited->distinct('customer_id')->count();
                        $item['apm'] =  $dealervisited->distinct('customer_id')->count();
                        break;
                    case 'gift_collected':
                        $item['jan'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-01-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-01-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['feb'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-02-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-02-29"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['mar'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-03-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-03-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['apr'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-04-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-04-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['may'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-05-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-05-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['jun'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-06-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-06-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['jul'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-07-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-07-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['aug'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-08-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-08-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['sep'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-09-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-09-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['oct'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-10-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-10-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['nov'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-11-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-11-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['dec'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-12-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-12-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['total'] = $points->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        $item['apm'] =  $points->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('quantity');
                        break;
                    case 'mechanic_registered':
                        $item['jan'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-01-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-01-31"))))->count();
                        $item['feb'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-02-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-02-29"))))->count();
                        $item['mar'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-03-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-03-31"))))->count();
                        $item['apr'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-04-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-04-30"))))->count();
                        $item['may'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-05-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-05-31"))))->count();
                        $item['jun'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-06-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-06-30"))))->count();
                        $item['jul'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-07-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-07-31"))))->count();
                        $item['aug'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-08-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-08-31"))))->count();
                        $item['sep'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-09-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-09-30"))))->count();
                        $item['oct'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-10-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-10-31"))))->count();
                        $item['nov'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-11-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-11-30"))))->count();
                        $item['dec'] = $customers->where('created_at', '>=', date('Y-m-d', strtotime(date("Y-12-01"))))->where('created_at', '<=', date('Y-m-d', strtotime(date("Y-12-31"))))->count();
                        $item['total'] = $customers->count();
                        $item['apm'] =  $customers->count();
                        break;
                    case 'gift_settled':
                        $item['jan'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-01-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-01-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['feb'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-02-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-02-29"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['mar'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-03-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-03-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['apr'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-04-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-04-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['may'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-05-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-05-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['jun'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-06-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-06-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['jul'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-07-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-07-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['aug'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-08-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-08-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['sep'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-09-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-09-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['oct'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-10-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-10-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['nov'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-11-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-11-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['dec'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-12-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-12-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['total'] = $points->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['apm'] =  $points->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');
                        break;
                    case 'mechanic_points':
                        $item['jan'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-01-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-01-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['feb'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-02-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-02-29"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['mar'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-03-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-03-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['apr'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-04-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-04-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['may'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-05-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-05-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['jun'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-06-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-06-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['jul'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-07-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-07-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['aug'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-08-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-08-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['sep'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-09-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-09-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['oct'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-10-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-10-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['nov'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-11-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-11-30"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['dec'] = $points->where('transaction_at', '>=', date('Y-m-d', strtotime(date("Y-12-01"))))->where('transaction_at', '<=', date('Y-m-d', strtotime(date("Y-12-31"))))->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['total'] = $points->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');

                        $item['apm'] =  $points->whereHas('customers', function ($query) {
                            $query->where('customertype', '=', '4');
                        })->sum('points');
                        break;
                    case 'order_collected':
                        $item['jan'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-01-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-01-31"))))->sum('grand_total'));

                        $item['feb'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-02-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-02-29"))))->sum('grand_total'));

                        $item['mar'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-03-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-03-31"))))->sum('grand_total'));

                        $item['apr'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-04-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-04-30"))))->sum('grand_total'));
                        $item['may'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-05-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-05-31"))))->sum('grand_total'));

                        $item['jun'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-06-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-06-30"))))->sum('grand_total'));

                        $item['jul'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-07-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-07-31"))))->sum('grand_total'));

                        $item['aug'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-08-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-08-31"))))->sum('grand_total'));

                        $item['sep'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-09-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-09-30"))))->sum('grand_total'));

                        $item['oct'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-10-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-10-31"))))->sum('grand_total'));

                        $item['nov'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-11-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-11-30"))))->sum('grand_total'));

                        $item['dec'] = amountConversion($orders->where('order_date', '>=', date('Y-m-d', strtotime(date("Y-12-01"))))->where('order_date', '<=', date('Y-m-d', strtotime(date("Y-12-31"))))->sum('grand_total'));

                        $item['total'] = amountConversion($orders->sum('grand_total'));

                        $item['apm'] =  amountConversion($orders->sum('grand_total'));
                        break;
                    default:
                        $item['jan'] = 0;
                        $item['feb'] = 0;
                        $item['mar'] = 0;
                        $item['apr'] = 0;
                        $item['may'] = 0;
                        $item['jun'] = 0;
                        $item['jul'] = 0;
                        $item['aug'] = 0;
                        $item['sep'] = 0;
                        $item['oct'] = 0;
                        $item['nov'] = 0;
                        $item['dec'] = 0;
                        $item['total'] = 0;
                        $item['apm'] = 0;
                        break;
                }
                return $item;
            });
            $data['users'] = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->where('id', $userid)->select('name', 'location')->first();
            $data['perams'] = $finaldata;
            $data['total'] = collect([
                'jan' => $finaldata->sum('jan'),
                'feb' => $finaldata->sum('feb'),
                'mar' => $finaldata->sum('mar'),
                'apr' => $finaldata->sum('apr'),
                'may' => $finaldata->sum('may'),
                'jun' => $finaldata->sum('jun'),
                'jul' => $finaldata->sum('jul'),
                'aug' => $finaldata->sum('aug'),
                'sep' => $finaldata->sum('sep'),
                'oct' => $finaldata->sum('oct'),
                'nov' => $finaldata->sum('nov'),
                'dec' => $finaldata->sum('dec'),
            ]);
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function asmWiseMechanicsPointsReportData(Request $request)
    {
        try {
            $userid = !empty($request->input('user_id')) ? $request->input('user_id') : Auth::user()->id;
            $data['users'] = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->with('reportinginfo')->where('id', $userid)->select('name', 'location', 'reportingid')->first();
            $perameters = collect([
                collect(["month" => date("Y-01"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-02"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-03"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-04"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-05"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-06"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-07"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-08"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-09"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-10"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-11"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
                collect(["month" => date("Y-12"), "user_name" => isset($data['users']['name']) ? $data['users']['name'] : '', "location" => isset($data['users']['location']) ? $data['users']['location'] : '', "state" => isset($data['users']['location']) ? $data['users']['location'] : '', "reporting" => isset($data['users']['reportinginfo']['name']) ? $data['users']['reportinginfo']['name'] : '']),
            ]);
            $customers = Customers::where('created_by', '=', $userid)
                ->whereYear('created_at', '=', date('Y'))
                ->where('customertype', '=', 4)
                ->select('created_at');
            $points = Wallet::with('customers')->where('userid', '=', $userid)
                ->whereYear('transaction_at', '=', date('Y'))
                ->select('transaction_at', 'points', 'point_type', 'quantity', 'transaction_type');
            $sales = SalesDetails::with('products')->whereHas('sales', function ($query) use ($userid) {
                $query->where('created_by', '=', $userid);
                $query->whereYear('invoice_date', '=', date('Y'));
            })->select('product_id', 'price', 'quantity', 'line_total');

            $finaldata = $perameters->map(function ($item, $key) use ($customers, $points, $sales) {
                $item['mech_territory'] = $customers->count();
                $item['under_coupon_scheme'] = $points->whereHas('customers', function ($query) {
                    $query->where('customertype', '=', '4');
                })->where('transaction_type', '=', 'Dr')->where('point_type', '=', 'coupon')->distinct('customer_id')->count();
                $item['under_mrp_scheme'] = $points->whereHas('customers', function ($query) {
                    $query->where('customertype', '=', '4');
                })->where('transaction_type', '=', 'Dr')->where('point_type', '=', 'mrp')->distinct('customer_id')->count();
                $item['total_coupon_value'] = $points->whereHas('customers', function ($query) {
                    $query->where('customertype', '=', '4');
                })->where('transaction_type', '=', 'Dr')->where('point_type', '=', 'coupon')->sum('quantity');
                $item['total_mrp_value'] = $points->whereHas('customers', function ($query) {
                    $query->where('customertype', '=', '4');
                })->where('transaction_type', '=', 'Dr')->where('point_type', '=', 'mrp')->sum('quantity');
                $item['collection_coupons_value'] = $points->whereHas('customers', function ($query) {
                    $query->where('customertype', '=', '4');
                })->where('transaction_type', '=', 'Dr')->where('point_type', '=', 'coupon')->where('transaction_at', '=', date('m', strtotime($item['month'])))->sum('quantity');
                $item['collection_mrp_value'] = $points->whereHas('customers', function ($query) {
                    $query->where('customertype', '=', '4');
                })->where('transaction_type', '=', 'Dr')->where('point_type', '=', 'mrp')->where('transaction_at', '=', date('m', strtotime($item['month'])))->sum('quantity');
                $item['secondary_sales_ggl'] = $sales->whereHas('products', function ($query) {
                    $query->where('category_id', '=', '3');
                })->sum('line_total');
                $item['secondary_sales_gpd'] = $sales->whereHas('products', function ($query) {
                    $query->where('category_id', '=', '2');
                })->sum('line_total');
                $item['diff'] = $sales->whereHas('products', function ($query) {
                    $query->where('category_id', '=', '1');
                })->sum('line_total');
                $item['total'] = $sales->sum('line_total');
                return $item;
            });
            $data['perams'] = $finaldata;
            $data['total'] = collect([
                'mech_territory' => $finaldata->sum('mech_territory'),
                'under_coupon_scheme' => $finaldata->sum('under_coupon_scheme'),
                'under_mrp_scheme' => $finaldata->sum('under_mrp_scheme'),
                'total_coupon_value' => $finaldata->sum('total_coupon_value'),
                'total_mrp_value' => $finaldata->sum('total_mrp_value'),
                'collection_coupons_value' => $finaldata->sum('collection_coupons_value'),
                'collection_mrp_value' => $finaldata->sum('collection_mrp_value'),
                'secondary_sales_ggl' => $finaldata->sum('secondary_sales_ggl'),
                'secondary_sales_gpd' => $finaldata->sum('secondary_sales_gpd'),
                'diff' => $finaldata->sum('diff'),
                'total' => $finaldata->sum('total'),
            ]);
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function targetvsSaleReportData(Request $request)
    {
        try {
            $userid = !empty($request->input('user_id')) ? $request->input('user_id') : Auth::user()->id;
            $userids = getUsersReportingToAuth();
            $users = user::whereDoesntHave('roles', function ($query) {
                $query->whereIn('id', config('constants.customer_roles'));
            })->with('reportinginfo')->whereIn('id', $userids)->select('name', 'location', 'reportingid')->orderBy('name', 'asc')->get();
            $sales = SalesDetails::with('products', 'sales')
                ->whereHas('sales', function ($query) use ($userid) {
                    $query->where('created_by', '=', $userid);
                    $query->whereMonth('invoice_date', '=', date('m'));
                    $query->whereYear('invoice_date', '=', date('Y'));
                })
                ->select('product_id', 'price', 'quantity', 'line_total')
                ->get();
            $data = $users->map(function ($item, $key) use ($sales) {
                $targets = SalesTarget::where('userid', '=', $item['id'])
                    ->whereMonth('startdate', '=', date('m'))
                    ->whereYear('startdate', '=', date('Y'))->sum('amount');
                $item['zsm_name'] = isset($item['reportinginfo']['name']) ? $item['reportinginfo']['name'] : '';
                $item['ggl_targets'] = $targets;
                $item['ggl_achievement_10th'] = $sales->where('products.category_id', '=', '3')->where('sales.invoice_date', '>=', date("Y-m-01"))->where('sales.invoice_date', '<=', date("Y-m-10"))->sum('line_total');
                $item['ggl_achievement_20th'] = $sales->where('products.category_id', '=', '3')->where('sales.invoice_date', '>=', date("Y-m-11"))->where('sales.invoice_date', '<=', date("Y-m-20"))->sum('line_total');
                $item['ggl_achievement_30th'] = $sales->where('products.category_id', '=', '3')->where('sales.invoice_date', '>=', date("Y-m-21"))->where('sales.invoice_date', '<=', date("Y-m-t"))->sum('line_total');
                $item['gpd_targets'] = $targets;
                $item['gpd_achievement_10th'] = $sales->where('products.category_id', '=', '2')->where('sales.invoice_date', '>=', date("Y-m-01"))->where('sales.invoice_date', '<=', date("Y-m-10"))->sum('line_total');
                $item['gpd_achievement_20th'] = $sales->where('products.category_id', '=', '2')->where('sales.invoice_date', '>=', date("Y-m-11"))->where('sales.invoice_date', '<=', date("Y-m-20"))->sum('line_total');
                $item['gpd_achievement_30th'] = $sales->where('products.category_id', '=', '2')->where('sales.invoice_date', '>=', date("Y-m-21"))->where('sales.invoice_date', '<=', date("Y-m-t"))->sum('line_total');
                $item['diff_targets'] = $targets;
                $item['diff_achievement_10th'] = $sales->where('products.category_id', '=', '1')->where('sales.invoice_date', '>=', date("Y-m-01"))->where('sales.invoice_date', '<=', date("Y-m-10"))->sum('line_total');
                $item['diff_achievement_20th'] = $sales->where('products.category_id', '=', '1')->where('sales.invoice_date', '>=', date("Y-m-11"))->where('sales.invoice_date', '<=', date("Y-m-20"))->sum('line_total');
                $item['diff_achievement_30th'] = $sales->where('products.category_id', '=', '1')->where('sales.invoice_date', '>=', date("Y-m-21"))->where('sales.invoice_date', '<=', date("Y-m-t"))->sum('line_total');
                $item['targets'] = $targets;
                $item['achievement_10th'] = $sales->where('sales.invoice_date', '>=', date("Y-m-01"))->where('sales.invoice_date', '<=', date("Y-m-10"))->sum('line_total');
                $item['achievement_20th'] = $sales->where('sales.invoice_date', '>=', date("Y-m-11"))->where('sales.invoice_date', '<=', date("Y-m-20"))->sum('line_total');
                $item['achievement_30th'] = $sales->where('sales.invoice_date', '>=', date("Y-m-21"))->where('sales.invoice_date', '<=', date("Y-m-t"))->sum('line_total');
                return $item;
            });
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function fieldActivityDownload()
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new FieldActivityExport, 'fieldactivity.xlsx');
    }
    public function tourProgrammeDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TourProgrammeReportExport($request), 'tourprogramme.xlsx');
    }
    public function monthlyMovementDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new MovementReportExport($request), 'monthlymovement.xlsx');
    }
    public function pointCollectionDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PointCollectionsExport($request), 'pointcollection.xlsx');
    }
    public function territoryCoverageDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TerritoryCoverageExport($request), 'territorycoverage.xlsx');
    }

    public function performanceParameterDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PerformanceParameterExport($request), 'performances.xlsx');
    }

    public function mechanicsPointsDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new MechanicsPointsExport($request), 'points.xlsx');
    }

    public function targetAchievementDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TargetAchievementExport($request), 'achievement.xlsx');
    }
    public function surveyAnalysis(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->whereHas('roles', function ($query) {
            $query->whereNotIn('name', ['superadmin', 'Admin']);
        })->select('id', 'name', 'mobile')->orderBy('name', 'asc')->get();

        return view('reports.surveyanalysis', compact('users'));
    }
    public function surveyAnalysisReportData(Request $request)
    {
        try {
            $userid = !empty($request->input('user_id')) ? $request->input('user_id') : Auth::user()->id;
            $userids = getUsersReportingToAuth();
            $data = DealIn::select('types', DB::raw('count(*) as total'), DB::raw('SUM(hcv) as total_hcv'), DB::raw('SUM(mav) as total_mav'), DB::raw('SUM(lmv) as total_lmv'), DB::raw('SUM(lcv) as total_lcv'), DB::raw('SUM(other) as total_other'), DB::raw('SUM(tractor) as total_tractor'))->groupBy('types')->get();
            // $data = $dealins->map(function ($item, $key) {
            //     $segments = DealIn::where('types','=',$item['types'])->where('segments','<>','')->pluck('segments');
            //     $ansArray = [];
            //     foreach ($segments as $key => $ans) {
            //         $ansRowArray = explode(',', $ans);
            //         array_push($ansArray, ...$ansRowArray);
            //     }
            //     $reports = array();
            //     $counts = array_count_values($ansArray);
            //     $uniquesegments = array_unique($ansArray);
            //     foreach ($uniquesegments as $key => $value) {
            //         $reports[$value] = $counts[ $value ];
            //     }
            //     $item['reports'] = $reports;
            //     return $item;
            // });
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function surveyAnalysisDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SurveyAnalysisExport($request), 'surveyanalysis.xlsx');
    }

    public function gamification(GamificationDataTable $dataTable)
    {
        return $dataTable->render('reports.gamification');
    }

    public function customerAnalysisDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CustomerAnalysisExport($request), 'customeranalysis.xlsx');
    }

    public function primary_sales(Request $request)
    {
        // Fetch distinct values directly from DB instead of fetching all rows
        $ps_branches = PrimarySales::select('final_branch')->distinct()->pluck('final_branch');
        $ps_divisions = PrimarySales::select('division')->distinct()->pluck('division');
        $ps_months = PrimarySales::select('month')->distinct()->pluck('month');
        $ps_dealers = PrimarySales::select('dealer')->distinct()->pluck('dealer');
        $ps_product_models = PrimarySales::select('product_name')->distinct()->pluck('product_name');
        $ps_new_group_names = PrimarySales::select('new_group')->distinct()->pluck('new_group');
        $ps_sales_persons = PrimarySales::select('sales_person')->distinct()->pluck('sales_person');

        // Get active users excluding role ID 29
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })
            ->where('active', 'Y')
            ->orderBy('name', 'asc')
            ->get(['id', 'name']); // Fetch only required columns

        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);

        // Optimize role check and filtering
        $total_query = PrimarySales::query();

        $role = Role::find(29);
        if ($role && auth()->user()->hasRole($role->name)) {
            $child_customer = ParentDetail::where('parent_id', auth()->user()->customerid)
                ->pluck('customer_id')
                ->push(auth()->user()->customerid)
                ->toArray(); // Convert to array for better query performance
            $total_query->whereIn('customer_id', $child_customer);
        }

        // Optimize sum calculations
        $total_qty = $total_query->sum('quantity');
        $total_sale = $total_query->sum('net_amount');

        return view('reports.primary_sales', compact(
            'years',
            'users',
            'ps_branches',
            'ps_divisions',
            'ps_months',
            'ps_dealers',
            'ps_product_models',
            'ps_new_group_names',
            'ps_sales_persons',
            'total_qty',
            'total_sale'
        ));
    }


    public function secondary_sales(Request $request)
    {
        $retailers = Customers::where('customertype', '2')->get();
        $dealers_and_distibutors = Customers::where('customertype', [3, 4])->get();
        $sales_persons = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->orderBy('name', 'asc')->get();
        $products = Product::latest()->get();;
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->orderBy('name', 'asc')->get();
        $branches = Branch::latest()->get();
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);

        $orders = Order::with(['buyers', 'orderdetails', 'getuserdetails', 'getsalesdetail'])->get();

        abort_if(Gate::denies('dashboard_secondary_sales_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('reports.secondary_sales', compact('branches', 'years', 'sales_persons', 'divisions', 'retailers', 'dealers_and_distibutors', 'products'));
    }

    public function product_analysis_branch(Request $request)
    {
        $ps_branches = PrimarySales::latest()->get()->unique('final_branch');
        $ps_divisions = PrimarySales::latest()->get()->unique('division');
        $ps_months = PrimarySales::latest()->get()->unique('month');
        $ps_dealers = PrimarySales::latest()->get()->unique('dealer');
        $ps_product_models = PrimarySales::latest()->get()->unique('model_name');
        $ps_new_group_names = PrimarySales::latest()->get()->unique('new_group');
        $ps_sales_persons = PrimarySales::latest()->get()->unique('sales_person');
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', 'Y')->orderBy('name', 'asc')->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $total_qty = PrimarySales::sum('quantity');
        $total_sale = PrimarySales::sum('net_amount');
        $currentDate = Carbon::now();
        $months = [
            $currentDate->copy()->subMonthsNoOverflow(3)->format('M'), // Three months ago
            $currentDate->copy()->subMonthsNoOverflow(2)->format('M'), // Two months ago
            $currentDate->copy()->subMonthsNoOverflow(1)->format('M'), // Last month
        ];
        return view('reports.product_analysis_branch', compact('years', 'users', 'ps_branches', 'ps_divisions', 'ps_months', 'ps_dealers', 'ps_product_models', 'ps_new_group_names', 'ps_sales_persons', 'total_qty', 'total_sale', 'months'));
    }

    public function product_analysis_branch_list(Request $request)
    {
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $query = PrimarySales::select(
            'final_branch',
            'model_name',
            DB::raw('GROUP_CONCAT(quantity) as quantitys'),
            DB::raw('SUM(quantity) as total_quantitys'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(invoice_date) as invoice_dates'),
            DB::raw('GROUP_CONCAT(net_amount) as net_amounts'),
            DB::raw('SUM(net_amount) as total_net_amounts'),
        );

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
            $startDateFormatted = $startDate->toDateString();
            $endDateFormatted = $endDate->toDateString();

            // Apply the date range to the query
            $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->where('invoice_date', '>=', $startDateFormatted)
                    ->where('invoice_date', '<=', $endDateFormatted);
            });
        } elseif ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        } else {
            $currentDate = Carbon::now();
            $startDatethree = $currentDate->copy()->subMonthsNoOverflow(3)->firstOfMonth()->format('Y-m-d');
            $endDatethree = $currentDate->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d');
            $query->whereBetween('invoice_date', [$startDatethree, $endDatethree]);
        }

        if ($request->division_id && $request->division_id != '' && $request->division_id != null && count($request->division_id) > 0) {
            $query->whereIn('division', $request->division_id);
        }
        $data = $query->get();

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

        $query = $query->groupBy('final_branch', 'model_name')->orderBy('final_branch');

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('month1_qty', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(3)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $quantitys = explode(',', $query->quantitys);
                $tqty = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tqty += $quantitys[$key];
                    }
                }

                return $tqty;
            })
            ->addColumn('month1_sale', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(3)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $net_amounts = explode(',', $query->net_amounts);
                $tsale = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tsale += $net_amounts[$key];
                    }
                }
                if ($tsale > 0) {
                    return number_format(($tsale / 100000), 2, '.', '');
                } else {
                    return $tsale;
                }
            })
            ->addColumn('month2_qty', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(2)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $quantitys = explode(',', $query->quantitys);
                $tqty = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tqty += $quantitys[$key];
                    }
                }

                return $tqty;
            })
            ->addColumn('month2_sale', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(2)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $net_amounts = explode(',', $query->net_amounts);
                $tsale = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tsale += $net_amounts[$key];
                    }
                }
                if ($tsale > 0) {
                    return number_format(($tsale / 100000), 2, '.', '');
                } else {
                    return $tsale;
                }
            })
            ->addColumn('month3_qty', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(1)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $quantitys = explode(',', $query->quantitys);
                $tqty = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tqty += $quantitys[$key];
                    }
                }

                return $tqty;
            })
            ->addColumn('month3_sale', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(1)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $net_amounts = explode(',', $query->net_amounts);
                $tsale = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tsale += $net_amounts[$key];
                    }
                }
                if ($tsale > 0) {
                    return number_format(($tsale / 100000), 2, '.', '');
                } else {
                    return $tsale;
                }
            })
            ->addColumn('total_net_amounts', function ($query) {

                if ($query->total_net_amounts > 0) {
                    return number_format(($query->total_net_amounts / 100000), 2, '.', '');
                } else {
                    return $query->total_net_amounts;
                }
            })
            ->addColumn('qty_wise', function ($query) use ($data) {
                if ($query->total_quantitys > 0) {
                    return number_format((($query->total_quantitys / $data[0]->total_quantitys) * 100), 2, '.', '') . "%";
                } else {
                    return $query->total_quantitys;
                }
            })
            ->addColumn('sale_wise', function ($query) use ($data) {
                if ($query->total_net_amounts > 0) {
                    return number_format((($query->total_net_amounts / $data[0]->total_net_amounts) * 100), 2, '.', '') . "%";
                } else {
                    return $query->total_net_amounts;
                }
            })
            ->rawColumns(['month1_qty', 'month1_sale', 'month2_qty', 'month2_sale', 'month3_qty', 'month3_sale', 'total_net_amounts', 'qty_wise'])
            ->make(true);
    }

    public function product_analysis_branch_download(Request $request)
    {
        abort_if(Gate::denies('product_analysis_branch_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if ($request->financial_year && !empty($request->financial_year)) {
            $fileName = 'product_analysis_branch_wise_' . $request->financial_year . '.xlsx';
        } else {
            $fileName = 'product_analysis_branch_wise.xlsx';
        }
        return Excel::download(new ProductAnalysisBranchExport($request), $fileName);
    }
    public function product_analysis_qty(Request $request)
    {
        $ps_branches = PrimarySales::latest()->get()->unique('final_branch');
        $ps_divisions = PrimarySales::latest()->get()->unique('division');
        $ps_months = PrimarySales::latest()->get()->unique('month');
        $ps_dealers = PrimarySales::latest()->get()->unique('dealer');
        $ps_product_models = PrimarySales::latest()->get()->unique('model_name');
        $ps_new_group_names = PrimarySales::latest()->get()->unique('new_group');
        $ps_sales_persons = PrimarySales::latest()->get()->unique('sales_person');
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', 'Y')->orderBy('name', 'asc')->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $total_qty = PrimarySales::sum('quantity');
        $total_sale = PrimarySales::sum('net_amount');
        $currentDate = Carbon::now();
        $months = [
            $currentDate->copy()->subMonthsNoOverflow(3)->format('M'), // Three months ago
            $currentDate->copy()->subMonthsNoOverflow(2)->format('M'), // Two months ago
            $currentDate->copy()->subMonthsNoOverflow(1)->format('M'), // Last month
        ];
        return view('reports.product_analysis_qty', compact('years', 'users', 'ps_branches', 'ps_divisions', 'ps_months', 'ps_dealers', 'ps_product_models', 'ps_new_group_names', 'ps_sales_persons', 'total_qty', 'total_sale', 'months'));
    }

    public function product_analysis_qty_list(Request $request)
    {
        DB::statement("SET SESSION group_concat_max_len = 10000000");

        // Base query with selected fields and total calculations
        $query = PrimarySales::select(
            'model_name',
            DB::raw('GROUP_CONCAT(quantity) as quantitys'),
            DB::raw('SUM(quantity) as total_quantitys'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(invoice_date) as invoice_dates'),
            DB::raw('GROUP_CONCAT(net_amount) as net_amounts'),
            DB::raw('SUM(net_amount) as total_net_amounts')
        );

        // Filter by financial year or last three months
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
            $startDateFormatted = $startDate->toDateString();
            $endDateFormatted = $endDate->toDateString();

            // Apply the date range to the query
            $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->where('invoice_date', '>=', $startDateFormatted)
                    ->where('invoice_date', '<=', $endDateFormatted);
            });
        } elseif ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        } else {
            $currentDate = Carbon::now();
            $startDatethree = $currentDate->copy()->subMonthsNoOverflow(3)->firstOfMonth()->format('Y-m-d');
            $endDatethree = $currentDate->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d');
            $query->whereBetween('invoice_date', [$startDatethree, $endDatethree]);
        }

        if ($request->division_id) {
            $query->whereIn('division', $request->division_id);
        }
        // Get total quantities to calculate percentages
        $totalQuantities = $query->sum('quantity');

        // Apply additional filters
        if ($request->branch_id) {
            $query->where('final_branch', $request->branch_id);
        }


        if ($request->dealer_id) {
            $query->where('dealer', 'like', '%' . $request->dealer_id . '%');
        }

        if ($request->product_model) {
            $query->where('model_name', $request->product_model);
        }

        if ($request->new_group) {
            $query->where('new_group', $request->new_group);
        }

        if ($request->executive_id) {
            $query->where('sales_person', $request->executive_id);
        }

        // Group and order by calculated qty_wise percentage
        $query = $query->groupBy('model_name')
            ->selectRaw('SUM(quantity) as total_quantitys, SUM(quantity) / ? * 100 as qty_wise', [$totalQuantities])
            ->orderBy('qty_wise', 'DESC');

        // Fetch the data
        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('month1_qty', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(3)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $quantitys = explode(',', $query->quantitys);
                $tqty = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tqty += $quantitys[$key];
                    }
                }
                return $tqty;
            })
            ->addColumn('month2_qty', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(2)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $quantitys = explode(',', $query->quantitys);
                $tqty = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tqty += $quantitys[$key];
                    }
                }
                return $tqty;
            })
            ->addColumn('month3_qty', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(1)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $quantitys = explode(',', $query->quantitys);
                $tqty = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tqty += $quantitys[$key];
                    }
                }
                return $tqty;
            })
            ->addColumn('qty_wise', function ($query) {
                return number_format($query->qty_wise, 2, '.', '') . "%";
            })
            ->rawColumns(['month1_qty', 'month2_qty', 'month3_qty', 'qty_wise'])
            ->make(true);
    }


    public function product_analysis_qty_download(Request $request)
    {
        abort_if(Gate::denies('product_analysis_branch_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if ($request->financial_year && !empty($request->financial_year)) {
            $fileName = 'product_analysis_qty_' . $request->financial_year . '.xlsx';
        } else {
            $fileName = 'product_analysis_qty.xlsx';
        }
        return Excel::download(new ProductAnalysisQtyExport($request), $fileName);
    }

    public function product_analysis_value(Request $request)
    {
        $ps_branches = PrimarySales::latest()->get()->unique('final_branch');
        $ps_divisions = PrimarySales::latest()->get()->unique('division');
        $ps_months = PrimarySales::latest()->get()->unique('month');
        $ps_dealers = PrimarySales::latest()->get()->unique('dealer');
        $ps_product_models = PrimarySales::latest()->get()->unique('model_name');
        $ps_new_group_names = PrimarySales::latest()->get()->unique('new_group');
        $ps_sales_persons = PrimarySales::latest()->get()->unique('sales_person');
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', 'Y')->orderBy('name', 'asc')->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $total_qty = PrimarySales::sum('quantity');
        $total_sale = PrimarySales::sum('net_amount');
        $currentDate = Carbon::now();
        $months = [
            $currentDate->copy()->subMonthsNoOverflow(3)->format('M'), // Three months ago
            $currentDate->copy()->subMonthsNoOverflow(2)->format('M'), // Two months ago
            $currentDate->copy()->subMonthsNoOverflow(1)->format('M'), // Last month
        ];
        return view('reports.product_analysis_value', compact('years', 'users', 'ps_branches', 'ps_divisions', 'ps_months', 'ps_dealers', 'ps_product_models', 'ps_new_group_names', 'ps_sales_persons', 'total_qty', 'total_sale', 'months'));
    }

    public function product_analysis_value_list(Request $request)
    {
        DB::statement("SET SESSION group_concat_max_len = 10000000");

        // Base query with selected fields and total calculations
        $query = PrimarySales::select(
            'model_name',
            DB::raw('GROUP_CONCAT(quantity) as quantitys'),
            DB::raw('SUM(quantity) as total_quantitys'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(invoice_date) as invoice_dates'),
            DB::raw('GROUP_CONCAT(net_amount) as net_amounts'),
            DB::raw('SUM(net_amount) as total_net_amounts')
        );

        // Filter by financial year or last three months
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
            $startDateFormatted = $startDate->toDateString();
            $endDateFormatted = $endDate->toDateString();

            // Apply the date range to the query
            $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->where('invoice_date', '>=', $startDateFormatted)
                    ->where('invoice_date', '<=', $endDateFormatted);
            });
        } elseif ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        } else {
            $currentDate = Carbon::now();
            $startDatethree = $currentDate->copy()->subMonthsNoOverflow(3)->firstOfMonth()->format('Y-m-d');
            $endDatethree = $currentDate->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d');
            $query->whereBetween('invoice_date', [$startDatethree, $endDatethree]);
        }

        if ($request->division_id && count($request->division_id) > 0) {
            $query->whereIn('division', $request->division_id);
        }
        // Get total net amounts to calculate percentages
        $totalNetAmounts = $query->sum('net_amount');

        // Apply additional filters
        if ($request->branch_id) {
            $query->where('final_branch', $request->branch_id);
        }


        if ($request->dealer_id) {
            $query->where('dealer', 'like', '%' . $request->dealer_id . '%');
        }

        if ($request->product_model) {
            $query->where('model_name', $request->product_model);
        }

        if ($request->new_group) {
            $query->where('new_group', $request->new_group);
        }

        if ($request->executive_id) {
            $query->where('sales_person', $request->executive_id);
        }

        // Group and order by calculated sale_wise percentage
        $query = $query->groupBy('model_name')
            ->selectRaw('SUM(net_amount) as total_net_amounts, SUM(net_amount) / ? * 100 as sale_wise', [$totalNetAmounts])
            ->orderBy('sale_wise', 'DESC');

        // Fetch the data
        $data = $query->get();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('month1_sale', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(3)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $net_amounts = explode(',', $query->net_amounts);
                $tsale = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tsale += $net_amounts[$key];
                    }
                }
                if ($tsale > 0) {
                    return number_format(($tsale / 100000), 2, '.', '');
                } else {
                    return $tsale;
                }
            })
            ->addColumn('month2_sale', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(2)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $net_amounts = explode(',', $query->net_amounts);
                $tsale = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tsale += $net_amounts[$key];
                    }
                }
                if ($tsale > 0) {
                    return number_format(($tsale / 100000), 2, '.', '');
                } else {
                    return $tsale;
                }
            })
            ->addColumn('month3_sale', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(1)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $net_amounts = explode(',', $query->net_amounts);
                $tsale = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tsale += $net_amounts[$key];
                    }
                }
                if ($tsale > 0) {
                    return number_format(($tsale / 100000), 2, '.', '');
                } else {
                    return $tsale;
                }
            })
            ->addColumn('total_net_amounts', function ($query) {
                if ($query->total_net_amounts > 0) {
                    return number_format(($query->total_net_amounts / 100000), 2, '.', '');
                } else {
                    return $query->total_net_amounts;
                }
            })
            ->addColumn('sale_wise', function ($query) {
                return number_format($query->sale_wise, 2, '.', '') . "%";
            })
            ->rawColumns(['month1_sale', 'month2_sale', 'month3_sale', 'total_net_amounts', 'sale_wise'])
            ->make(true);
    }


    public function product_analysis_value_download(Request $request)
    {
        abort_if(Gate::denies('product_analysis_value_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if ($request->financial_year && !empty($request->financial_year)) {
            $fileName = 'product_analysis_value_' . $request->financial_year . '.xlsx';
        } else {
            $fileName = 'product_analysis_value.xlsx';
        }
        return Excel::download(new ProductAnalysisValueExport($request), $fileName);
    }

    public function group_wise_analysis(Request $request)
    {
        $ps_branches = PrimarySales::latest()->get()->unique('final_branch');
        $ps_divisions = PrimarySales::latest()->get()->unique('division');
        $ps_months = PrimarySales::latest()->get()->unique('month');
        $ps_dealers = PrimarySales::latest()->get()->unique('dealer');
        $ps_product_models = PrimarySales::latest()->get()->unique('model_name');
        $ps_new_group_names = PrimarySales::latest()->get()->unique('new_group');
        $ps_sales_persons = PrimarySales::latest()->get()->unique('sales_person');
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', 'Y')->orderBy('name', 'asc')->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $total_qty = PrimarySales::sum('quantity');
        $total_sale = PrimarySales::sum('net_amount');
        $currentDate = Carbon::now();
        $months = [
            $currentDate->copy()->subMonthsNoOverflow(3)->format('M'), // Three months ago
            $currentDate->copy()->subMonthsNoOverflow(2)->format('M'), // Two months ago
            $currentDate->copy()->subMonthsNoOverflow(1)->format('M'), // Last month
        ];
        return view('reports.group_wise_analysis', compact('years', 'users', 'ps_branches', 'ps_divisions', 'ps_months', 'ps_dealers', 'ps_product_models', 'ps_new_group_names', 'ps_sales_persons', 'total_qty', 'total_sale', 'months'));
    }

    public function group_wise_analysis_list(Request $request)
    {
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $query = PrimarySales::select(
            'new_group',
            'final_branch',
            DB::raw('GROUP_CONCAT(quantity) as quantitys'),
            DB::raw('SUM(quantity) as total_quantitys'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(invoice_date) as invoice_dates'),
            DB::raw('GROUP_CONCAT(net_amount) as net_amounts'),
            DB::raw('SUM(net_amount) as total_net_amounts'),
        );

        // Filter by financial year or last three months
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
            $startDateFormatted = $startDate->toDateString();
            $endDateFormatted = $endDate->toDateString();

            // Apply the date range to the query
            $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->where('invoice_date', '>=', $startDateFormatted)
                    ->where('invoice_date', '<=', $endDateFormatted);
            });
        } elseif ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        } else {
            $currentDate = Carbon::now();
            $startDatethree = $currentDate->copy()->subMonthsNoOverflow(3)->firstOfMonth()->format('Y-m-d');
            $endDatethree = $currentDate->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d');
            $query->whereBetween('invoice_date', [$startDatethree, $endDatethree]);
        }

        if ($request->division_id && $request->division_id != '' && count($request->division_id) > 0) {
            $query->whereIn('division', $request->division_id);
        }

        $data = $query->get();

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

        $query = $query->groupBy('new_group', 'final_branch')->orderBy('new_group');

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('month1_qty', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(3)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $quantitys = explode(',', $query->quantitys);
                $tqty = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tqty += $quantitys[$key];
                    }
                }

                return $tqty;
            })
            ->addColumn('month1_sale', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(3)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $net_amounts = explode(',', $query->net_amounts);
                $tsale = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tsale += $net_amounts[$key];
                    }
                }
                if ($tsale > 0) {
                    return number_format(($tsale / 100000), 2, '.', '');
                } else {
                    return $tsale;
                }
            })
            ->addColumn('month2_qty', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(2)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $quantitys = explode(',', $query->quantitys);
                $tqty = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tqty += $quantitys[$key];
                    }
                }

                return $tqty;
            })
            ->addColumn('month2_sale', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(2)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $net_amounts = explode(',', $query->net_amounts);
                $tsale = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tsale += $net_amounts[$key];
                    }
                }
                if ($tsale > 0) {
                    return number_format(($tsale / 100000), 2, '.', '');
                } else {
                    return $tsale;
                }
            })
            ->addColumn('month3_qty', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(1)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $quantitys = explode(',', $query->quantitys);
                $tqty = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tqty += $quantitys[$key];
                    }
                }

                return $tqty;
            })
            ->addColumn('month3_sale', function ($query) {
                $currentDate = Carbon::now();
                $month_are = $currentDate->copy()->subMonthsNoOverflow(1)->format('M');
                $invoice_dates = explode(',', $query->invoice_dates);
                $net_amounts = explode(',', $query->net_amounts);
                $tsale = 0;
                foreach ($invoice_dates as $key => $value) {
                    $getMonth = date('M', strtotime($value));
                    if ($getMonth == $month_are) {
                        $tsale += $net_amounts[$key];
                    }
                }
                if ($tsale > 0) {
                    return number_format(($tsale / 100000), 2, '.', '');
                } else {
                    return $tsale;
                }
            })
            ->addColumn('total_net_amounts', function ($query) {

                if ($query->total_net_amounts > 0) {
                    return number_format(($query->total_net_amounts / 100000), 2, '.', '');
                } else {
                    return $query->total_net_amounts;
                }
            })
            ->addColumn('qty_wise', function ($query) use ($data) {
                if ($query->total_quantitys > 0) {
                    return number_format((($query->total_quantitys / $data[0]->total_quantitys) * 100), 2, '.', '') . "%";
                } else {
                    return $query->total_quantitys;
                }
            })
            ->addColumn('sale_wise', function ($query) use ($data) {
                if ($query->total_net_amounts > 0) {
                    return number_format((($query->total_net_amounts / $data[0]->total_net_amounts) * 100), 2, '.', '') . "%";
                } else {
                    return $query->total_net_amounts;
                }
            })
            ->rawColumns(['month1_qty', 'month1_sale', 'month2_qty', 'month2_sale', 'month3_qty', 'month3_sale', 'total_net_amounts', 'qty_wise'])
            ->make(true);
    }

    public function group_wise_analysis_download(Request $request)
    {
        abort_if(Gate::denies('product_analysis_branch_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if ($request->financial_year && !empty($request->financial_year)) {
            $fileName = 'group_wise_analysis_' . $request->financial_year . '.xlsx';
        } else {
            $fileName = 'group_wise_analysis.xlsx';
        }
        return Excel::download(new GroupWiseAnalysisExport($request), $fileName);
    }

    public function per_employee_costing(EmployeeCostingDataTable $dataTable)
    {
        $ps_branches = Branch::where('active', 'Y')->select('id', 'branch_name')->get();
        $ps_divisions = Division::where('active', 'Y')->select('id', 'division_name')->get();
        $ps_months = PrimarySales::select('month')->distinct()->pluck('month');
        $ps_dealers = PrimarySales::select('dealer')->distinct()->pluck('dealer');
        $ps_product_models = PrimarySales::select('model_name')->distinct()->pluck('model_name');
        $ps_new_group_names = PrimarySales::select('new_group')->distinct()->pluck('new_group');

        $ps_sales_persons = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', 'Y')->select('id', 'name')->orderBy('name', 'asc')->get();
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', 'Y')->latest()->orderBy('name', 'asc')->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $total_qty = PrimarySales::sum('quantity');
        $total_sale = PrimarySales::sum('net_amount');
        $currentDate = Carbon::now();
        $months = [
            $currentDate->copy()->subMonthsNoOverflow(3)->format('M'), // Three months ago
            $currentDate->copy()->subMonthsNoOverflow(2)->format('M'), // Two months ago
            $currentDate->copy()->subMonthsNoOverflow(1)->format('M'), // Last month
        ];
        return $dataTable->render('reports.per_employee_costing', compact('years', 'users', 'ps_branches', 'ps_divisions', 'ps_months', 'ps_dealers', 'ps_product_models', 'ps_new_group_names', 'ps_sales_persons', 'total_qty', 'total_sale', 'months'));
    }

    public function per_employee_costing_download(Request $request)
    {
        abort_if(Gate::denies('per_employee_costing_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if ($request->branch_wise) {
            if ($request->financial_year && !empty($request->financial_year)) {
                $fileName = 'branch_costing_' . $request->financial_year . '.xlsx';
            } else {
                $fileName = 'branch_costing.xlsx';
            }
            return Excel::download(new BranchCostingExport($request), $fileName);
        } elseif ($request->branch_wise_only_sales) {
            if ($request->financial_year && !empty($request->financial_year)) {
                $fileName = 'branch_costing_only_sales_' . $request->financial_year . '.xlsx';
            } else {
                $fileName = 'branch_costing_only_sales.xlsx';
            }
            return Excel::download(new BranchOnlySalesCostingExport($request), $fileName);
        } else {
            if ($request->financial_year && !empty($request->financial_year)) {
                $fileName = 'per_employee_costing_' . $request->financial_year . '.xlsx';
            } else {
                $fileName = 'per_employee_costing.xlsx';
            }
            return Excel::download(new PerEmployeeCostingExport($request), $fileName);
        }
    }

    public function top_dealer(Request $request)
    {
        $ps_branches = PrimarySales::latest()->get()->unique('final_branch');
        $ps_divisions = PrimarySales::latest()->get()->unique('division');
        $ps_months = PrimarySales::latest()->get()->unique('month');
        $ps_dealers = PrimarySales::latest()->get()->unique('dealer');
        $ps_product_models = PrimarySales::latest()->get()->unique('model_name');
        $ps_new_group_names = PrimarySales::latest()->get()->unique('new_group');
        $ps_sales_persons = PrimarySales::latest()->get()->unique('sales_person');
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', 'Y')->orderBy('name', 'asc')->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $total_qty = PrimarySales::sum('quantity');
        $total_sale = PrimarySales::sum('net_amount');
        $currentDate = Carbon::now();
        $months = [
            $currentDate->copy()->subMonthsNoOverflow(3)->format('M'), // Three months ago
            $currentDate->copy()->subMonthsNoOverflow(2)->format('M'), // Two months ago
            $currentDate->copy()->subMonthsNoOverflow(1)->format('M'), // Last month
        ];
        return view('reports.top_dealer', compact('years', 'users', 'ps_branches', 'ps_divisions', 'ps_months', 'ps_dealers', 'ps_product_models', 'ps_new_group_names', 'ps_sales_persons', 'total_qty', 'total_sale', 'months'));
    }

    public function top_dealer_list(Request $request)
    {
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $query = PrimarySales::select(
            'dealer',
            'customer_id',
            'final_branch',
            'city',
            DB::raw('SUM(net_amount) as total_net_amounts'),
        );

        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $userids = getUsersReportingToAuth();
            $customer_ids = Customers::whereIn('executive_id', $userids)->orWhereIn('created_by', $userids)->pluck('id');
            $query->whereIn('customer_id', $customer_ids);
        }

        // Filter by financial year or last three months
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
            $startDateFormatted = $startDate->toDateString();
            $endDateFormatted = $endDate->toDateString();

            // Apply the date range to the query
            $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->where('invoice_date', '>=', $startDateFormatted)
                    ->where('invoice_date', '<=', $endDateFormatted);
            });
        } elseif ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        } else {
            $currentMonth = Carbon::now()->month;
            $last_monts = [1, 2, 3];
            $currentYear = Carbon::now()->year;
            if (in_array($currentMonth, $last_monts)) {
                $request->financial_year = ($currentYear - 1) . '-' . $currentYear;
            } else {
                $request->financial_year = $currentYear . '-' . $currentYear + 1;
            }
            $f_year_array = explode('-', $request->financial_year);
            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        }

        if ($request->division_id && $request->division_id != '' && count($request->division_id) > 0) {
            $query->whereIn('division', $request->division_id);
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

        $query = $query->groupBy('dealer', 'customer_id', 'final_branch', 'city')->orderBy('total_net_amounts', 'desc');

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('total_net_amounts', function ($query) {
                return number_format(($query->total_net_amounts / 100000), 2, '.', '');
            })
            ->rawColumns(['total_net_amounts'])
            ->make(true);
    }

    public function top_dealer_download(Request $request)
    {
        abort_if(Gate::denies('top_dealer_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if ($request->financial_year && !empty($request->financial_year)) {
            $fileName = 'top_dealer_' . $request->financial_year . '.xlsx';
        } else {
            $fileName = 'top_dealer.xlsx';
        }
        return Excel::download(new TopDealerExport($request), $fileName);
    }
    public function dealer_growth(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $currentDate = Carbon::now();

        // Fetch distinct values in a single query
        $primarySales = PrimarySales::select([
            'final_branch',
            'division',
            'month',
            'dealer',
            'model_name',
            'new_group',
            'sales_person'
        ])
            ->distinct()
            ->get();

        // Extract unique values
        $ps_branches = $primarySales->pluck('final_branch')->unique();
        $ps_divisions = $primarySales->pluck('division')->unique();
        $ps_months = $primarySales->pluck('month')->unique();
        $ps_dealers = $primarySales->pluck('dealer')->unique();
        $ps_product_models = $primarySales->pluck('model_name')->unique();
        $ps_new_group_names = $primarySales->pluck('new_group')->unique();
        $ps_sales_persons = $primarySales->pluck('sales_person')->unique();

        // Optimized sum queries
        $totals = PrimarySales::selectRaw('SUM(quantity) as total_qty, SUM(net_amount) as total_sale')
            ->first();

        $total_qty = $totals->total_qty;
        $total_sale = $totals->total_sale;

        // Fetch users efficiently
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })
            ->where('active', 'Y')
            ->orderBy('name', 'asc')
            ->get();

        // Last three months
        $months = [
            $currentDate->copy()->subMonthsNoOverflow(3)->format('M'),
            $currentDate->copy()->subMonthsNoOverflow(2)->format('M'),
            $currentDate->copy()->subMonthsNoOverflow(1)->format('M'),
        ];

        return view('reports.dealer_growth', compact(
            'years',
            'users',
            'ps_branches',
            'ps_divisions',
            'ps_months',
            'ps_dealers',
            'ps_product_models',
            'ps_new_group_names',
            'ps_sales_persons',
            'total_qty',
            'total_sale',
            'months'
        ));
    }


    public function dealer_growth_list(Request $request)
    {
        DB::statement("SET SESSION group_concat_max_len = 1000000");

        $query = PrimarySales::select(
            'dealer',
            'customer_id',
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
        if ($request->division_id && $request->division_id != '' && count($request->division_id) > 0) {
            $query->whereIn('division', $request->division_id);
        }

        $role = Role::find(29);
        if ($role && auth()->user()->hasRole($role->name)) {
            $child_customer = ParentDetail::where('parent_id', auth()->user()->customerid)
                ->pluck('customer_id')
                ->push(auth()->user()->customerid);
            $query->whereIn('customer_id', $child_customer);
        } else {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $userids = getUsersReportingToAuth();
                $customer_ids = Customers::whereIn('executive_id', $userids)->orWhereIn('created_by', $userids)->pluck('id');
                $query->whereIn('customer_id', $customer_ids);
            }
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

        // Grouping and ordering
        $query->whereIn('division', ['PUMP', 'MOTOR'])->groupBy('dealer', 'customer_id', 'final_branch', 'city');

        // Execute the primary query
        $results = $query->get();

        // Calculate the last year's net amounts
        $lastYearAmounts = PrimarySales::select(
            'dealer',
            'customer_id',
            'final_branch',
            'city',
            DB::raw('SUM(net_amount) as last_year_net_amounts')
        )
            ->whereBetween('invoice_date', [$last_year_start, $last_year_end])
            ->groupBy('dealer', 'final_branch', 'customer_id', 'city')
            ->get();
        // Merge the results
        $results = $results->map(function ($item) use ($lastYearAmounts) {
            $lastYearAmount = $lastYearAmounts->firstWhere(function ($value) use ($item) {
                return $value->customer_id == $item->customer_id && $value->dealer == $item->dealer;
                // return $value->dealer == $item->dealer &&
                //     $value->final_branch == $item->final_branch &&
                //     $value->city == $item->city;
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

        return Datatables::of($results)
            ->addIndexColumn()
            ->addColumn('cy_total_net_amounts', function ($results) {
                return number_format(($results->total_net_amounts / 100000), 2, '.', '');
            })
            ->addColumn('ly_total_net_amounts', function ($results) {
                if ($results->last_year_net_amounts > 0) {
                    return number_format(($results->last_year_net_amounts / 100000), 2, '.', '');
                } else {
                    return "0";
                }
            })
            ->addColumn('growth', function ($results) {
                return $results->growthPercent;
            })
            ->rawColumns(['cy_total_net_amounts', 'ly_total_net_amounts', 'growth'])
            ->make(true);
    }

    public function dealer_growth_download(Request $request)
    {
        abort_if(Gate::denies('dealer_growth_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if ($request->financial_year && !empty($request->financial_year)) {
            $fileName = 'dealer_growth_' . $request->financial_year . '.xlsx';
        } else {
            $fileName = 'dealer_growth.xlsx';
        }
        return Excel::download(new DealerGrowthExport($request), $fileName);
    }

    public function new_dealer_sale(Request $request)
    {
        $ps_branches = PrimarySales::latest()->get()->unique('final_branch');
        $ps_divisions = PrimarySales::latest()->get()->unique('division');
        $ps_months = PrimarySales::latest()->get()->unique('month');
        $ps_dealers = PrimarySales::latest()->get()->unique('dealer');
        $ps_product_models = PrimarySales::latest()->get()->unique('model_name');
        $ps_new_group_names = PrimarySales::latest()->get()->unique('new_group');
        $ps_sales_persons = PrimarySales::latest()->get()->unique('sales_person');
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', 'Y')->orderBy('name', 'asc')->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $total_qty = PrimarySales::sum('quantity');
        $total_sale = PrimarySales::sum('net_amount');
        $currentDate = Carbon::now();
        $months = [
            $currentDate->copy()->subMonthsNoOverflow(3)->format('M'), // Three months ago
            $currentDate->copy()->subMonthsNoOverflow(2)->format('M'), // Two months ago
            $currentDate->copy()->subMonthsNoOverflow(1)->format('M'), // Last month
        ];
        return view('reports.new_dealer_sale', compact('years', 'users', 'ps_branches', 'ps_divisions', 'ps_months', 'ps_dealers', 'ps_product_models', 'ps_new_group_names', 'ps_sales_persons', 'total_qty', 'total_sale', 'months'));
    }

    public function new_dealer_sale_list(Request $request)
    {
        $firstDateOfApril = Carbon::createFromDate(null, 4, 1)->startOfDay()->toDateString();
        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $userids = getUsersReportingToAuth();
            $customer_ids = Customers::whereIn('executive_id', $userids)->orWhereIn('created_by', $userids)->pluck('id');
            $new_dealers = Customers::where('creation_date', '>=', $firstDateOfApril)->whereIn('id', $customer_ids)->pluck('id');
        } else {
            $new_dealers = Customers::where('creation_date', '>=', $firstDateOfApril)->pluck('id');
        }
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $query = PrimarySales::with('customer')->select(
            'dealer',
            'final_branch',
            'city',
            'customer_id',
            'division',
            DB::raw('SUM(net_amount) as total_net_amounts'),
        )->whereIn('customer_id', $new_dealers);

        // Filter by financial year or last three months
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
            $startDateFormatted = $startDate->toDateString();
            $endDateFormatted = $endDate->toDateString();

            // Apply the date range to the query
            $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->where('invoice_date', '>=', $startDateFormatted)
                    ->where('invoice_date', '<=', $endDateFormatted);
            });
        } elseif ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        } else {
            $currentMonth = Carbon::now()->month;
            $last_monts = [1, 2, 3];
            $currentYear = Carbon::now()->year;
            if (in_array($currentMonth, $last_monts)) {
                $request->financial_year = ($currentYear - 1) . '-' . $currentYear;
            } else {
                $request->financial_year = $currentYear . '-' . $currentYear + 1;
            }
            $f_year_array = explode('-', $request->financial_year);
            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        }

        if ($request->division_id && $request->division_id != '' && count($request->division_id) > 0) {
            $query->whereIn('division', $request->division_id);
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

        $query = $query->groupBy('dealer', 'final_branch', 'division', 'city', 'customer_id')->orderBy('total_net_amounts', 'desc');

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('total_net_amounts', function ($query) {
                return number_format(($query->total_net_amounts / 100000), 2, '.', '');
            })
            ->addColumn('customer.creation_date', function ($query) {
                return date('d M Y', strtotime($query->customer->creation_date));
            })
            ->addColumn('customer.customertypes.customertype_name', function ($query) {
                return $query->customer->customertypes->customertype_name;
            })
            ->addColumn('slab', function ($query) {
                $sales = number_format(($query->total_net_amounts / 100000), 2, '.', '');
                if ($sales > 0 && $sales < 2) {
                    return '0L-2L';
                } elseif ($sales >= 2 && $sales < 5) {
                    return '2L-5L';
                } elseif ($sales >= 5 && $sales < 10) {
                    return '5L-10L';
                } elseif ($sales >= 10 && $sales < 15) {
                    return '10L-15L';
                } elseif ($sales >= 15 && $sales < 25) {
                    return '15L-25L';
                } elseif ($sales >= 25 && $sales < 75) {
                    return '25L-75L';
                } elseif ($sales >= 75 && $sales < 100) {
                    return '75L-1Cr';
                } elseif ($sales >= 100) {
                    return '1Cr Plus';
                }
            })
            ->rawColumns(['total_net_amounts', 'customer.creation_date', 'customer.customertypes.customertype_name', 'slab'])
            ->make(true);
    }

    public function new_dealer_sale_download(Request $request)
    {
        abort_if(Gate::denies('new_dealer_sale_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if ($request->last_year) {
            if ($request->financial_year && !empty($request->financial_year)) {
                $fileName = 'new_dealer_sale_last_year_' . $request->financial_year . '.xlsx';
            } else {
                $fileName = 'new_dealer_sale_last_year.xlsx';
            }
            return Excel::download(new NewDealerSaleLastYearExport($request), $fileName);
        } else {
            if ($request->financial_year && !empty($request->financial_year)) {
                $fileName = 'new_dealer_sale_' . $request->financial_year . '.xlsx';
            } else {
                $fileName = 'new_dealer_sale.xlsx';
            }
            return Excel::download(new NewDealerSaleExport($request), $fileName);
        }
    }

    public function loyaltyRetailerWiseSummaryReport(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $branches = Branch::latest()->get();
        $dealers = Customers::where('customertype', ['1', '3'])->get();

        if ($request->ajax()) {
            DB::statement("SET SESSION group_concat_max_len = 100000");
            $retailers_sarthi = TransactionHistory::groupBy('customer_id')->pluck('customer_id');
            $role = Role::find(29);
            if ($role && auth()->user()->hasRole($role->name)) {
                $child_customer = ParentDetail::where('parent_id', auth()->user()->customerid)
                    ->pluck('customer_id')
                    ->push(auth()->user()->customerid);
                $retailers_sarthi = $child_customer->intersect($retailers_sarthi);
            }
            $data = Customers::with([
                'customertypes',
                'firmtypes',
                'createdbyname',
                'customeraddress.cityname',
                'customeraddress.statename',
                'customer_transacation',
                'getparentdetail.parent_detail',
                'transactions' => function ($q) {
                    $q->select('customer_id', 'status', 'point', 'active_point', 'provision_point');
                },
                'redemptions' => function ($q) {
                    $q->select('customer_id', 'status', 'redeem_mode', 'redeem_amount')
                        ->whereNot('status', '2');
                }
            ])->whereIn('id', $retailers_sarthi)
                ->where(function ($query) use ($request, $userids, $role) {
                    if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                        $userIdsss = user::whereDoesntHave('roles', function ($query) {
                            $query->whereIn('id', config('constants.customer_roles'));
                        })->where('branch_id', $request->branch_id)->whereIn('id', $userids)->pluck('id');
                        $query->whereIn('executive_id', $userIdsss)
                            ->orWhereIn('created_by', $userIdsss);
                    } else {
                        if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Sub_Admin') && !auth()->user()->hasRole($role->name)) {
                            $query->whereIn('executive_id', $userids)
                                ->orWhereIn('created_by', $userids);
                        }
                    }

                    if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
                        $query->whereHas('getparentdetail', function ($q) use ($request) {
                            $q->where('parent_id', $request->dealer_id);
                        });
                    }
                })->orderBy('id', 'asc');
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($data) {
                    return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                })
                ->editColumn('branch', function ($data) {
                    return $data->createdbyname ? $data->createdbyname->getbranch->branch_name : '';
                })
                ->addColumn('coupon_scan_nos', function ($data) {
                    return $data->transactions->count();
                })
                ->addColumn('mobile_app_downloads', function ($data) {

                    $mobile_app_downloads = MobileUserLoginDetails::where('customer_id', $data->id)->count();

                    return isset($mobile_app_downloads) ? $mobile_app_downloads : '';
                })
                ->addColumn('provision_point', function ($data) {
                    return $data->transactions->where('status', '0')->sum('provision_point');
                })
                ->addColumn('active_point', function ($data) {
                    $active_points = $data->transactions->where('status', '1')->sum('point') ?? 0;
                    $active_points += $data->transactions->where('status', '0')->sum('active_point') ?? 0;
                    return  $active_points;
                })
                ->addColumn('total_point', function ($data) {
                    $total_points = $data->transactions->sum('point') ?? 0;
                    return $total_points;
                })
                ->addColumn('redeem_gift', function ($data) {
                    $redeem_gift = $data->redemptions->where('redeem_mode', '1')->sum('redeem_amount') ?? '0';
                    return $redeem_gift;
                })
                ->addColumn('redeem_neft', function ($data) {
                    return $redeem_neft = $data->redemptions->where('redeem_mode', '2')->sum('redeem_amount') ?? '0';
                })
                ->addColumn('total_redeem', function ($data) {
                    $total_redemption = Redemption::where('customer_id', $data->id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
                    return $data->redemptions->sum('redeem_amount') ?? '0';
                })
                ->addColumn('balance_active_point', function ($data) {
                    $active_points = $data->transactions->where('status', '1')->sum('point') ?? 0;
                    $active_points += $data->transactions->where('status', '0')->sum('active_point') ?? 0;
                    $redeem_gift = $data->redemptions->where('redeem_mode', '1')->sum('redeem_amount') ?? 0;
                    $redeem_neft = $data->redemptions->where('redeem_mode', '2')->sum('redeem_amount') ?? 0;
                    $total_redemption = $redeem_gift + $redeem_neft;
                    $total_balance = (int)$active_points - (int)$total_redemption;

                    return $total_balance;
                })

                ->rawColumns(['total_registered_retailers', 'total_registered_retailers_under_saarthi', 'coupon_scan_nos', 'mobile_app_downloads', 'provision_point', 'active_point', 'total_point', 'redeem_gift', 'redeem_neft', 'balance_active_point', 'created_at'])
                ->make(true);
        }
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('name', 'asc')->get();
        return view('reports.loyaltyretailerwisesummaryreport', compact('branches', 'dealers'));
    }

    public function loyaltyRetailerSummaryReportDownload(Request $request)
    {
        abort_if(Gate::denies('loyalty_summary_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new LoyaltyRetialerSummaryReportExport($request), 'loyalty_retailer_summary.xlsx');
    }

    public function customer_outstanting(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $customers = Customers::whereIn('id', CustomerOutstanting::pluck('customer_id')->unique())->select('id', 'name')->get();
        $dealers = Customers::where('customertype', ['1', '3'])->get();
        $branchs = Branch::where('active', 'Y')->select('id', 'branch_name')->get();
        $divisions = Division::where('active', 'Y')->select('id', 'division_name')->get();

        if ($request->ajax()) {
            $data = CustomerOutstanting::with('branch', 'customer.customerdocuments')->select(
                'customer_id',
                'branch_id',
                'year',
                'quarter',
                DB::raw('ROUND(SUM(amount), 2) as total_amounts'),
                DB::raw('GROUP_CONCAT(amount) as amounts'),
                DB::raw('GROUP_CONCAT(days) as days'),
                DB::raw('JSON_OBJECTAGG(days, amount) as day_amount_pairs'),
            );

            if ($request->customer_id && !empty($request->customer_id)) {
                $data->where('customer_id', $request->customer_id);
            }
            if (auth()->user()->hasRole('Customer Dealer')) {
                $customer_idss = ParentDetail::where('parent_id', auth()->user()->customerid)->pluck('customer_id')->toArray();
                $customer_idss[] = auth()->user()->customerid;
                $data->whereIn('customer_id', $customer_idss);
            }
            if ($request->branch_id && !empty($request->branch_id)) {
                $data->where('branch_id', $request->branch_id);
            }
            if ($request->division_id && !empty($request->division_id)) {
                $data->where('division_id', $request->division_id);
            }

            $data = $data->groupBy('customer_id', 'branch_id', 'year', 'quarter');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('first_slot', function ($data) {
                    $day_wise_amount_array = json_decode($data->day_amount_pairs, true);
                    return $day_wise_amount_array['0-30'] ?? '0';
                })
                ->addColumn('second_slot', function ($data) {
                    $day_wise_amount_array = json_decode($data->day_amount_pairs, true);
                    return $day_wise_amount_array['31-60'] ?? '0';
                })
                ->addColumn('thired_slot', function ($data) {
                    $day_wise_amount_array = json_decode($data->day_amount_pairs, true);
                    return $day_wise_amount_array['61-90'] ?? '0';
                })
                ->addColumn('fourth_slot', function ($data) {
                    $day_wise_amount_array = json_decode($data->day_amount_pairs, true);
                    return $day_wise_amount_array['91-150'] ?? '0';
                })
                ->addColumn('fifth_slot', function ($data) {
                    $day_wise_amount_array = json_decode($data->day_amount_pairs, true);
                    return $day_wise_amount_array['150'] ?? '0';
                })
                ->addColumn('balance_confirmations', function ($data) {
                    if ($data->customer->customerdocuments->where('document_name', 'balance_confirmations')->first()) {
                        return '<a href="' . $data->customer->customerdocuments->where('document_name', 'balance_confirmations')->first()->file_path . '" target="_blank" title="Balance Confirmations" ><i class="material-icons" style="color: #0a77b1 !important; font-size: 25px;" >picture_as_pdf</i></a>';
                    } else {
                        return '-';
                    }
                })

                ->rawColumns(['first_slot', 'second_slot', 'thired_slot', 'fourth_slot', 'fifth_slot', 'balance_confirmations'])
                ->make(true);
        }

        return view('reports.customer_outstanting', compact('customers', 'dealers', 'branchs', 'divisions'));
    }

    public function customer_outstanting_upload(Request $request)
    {
        abort_if(Gate::denies('customer_outstanting_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new CutomerOutstantingImport, request()->file('import_file'));

        return back()->with('success', 'Customer Outstanding Import successfully !!');
    }

    public function customer_outstanting_template(Request $request)
    {
        abort_if(Gate::denies('customer_outstanting_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CutomerOutstantingTemplate, 'customer_outstanding_template.xlsx');
    }

    public function customer_outstanting_download(Request $request)
    {
        if ($request->download == 'pdf') {

            $logoPath = public_path('assets/img/certificate_logo_fan2.png');
            $logoPath2 = public_path('assets/img/certificate_logo2.png');
            $footerLogoImage = public_path('assets/img/certificate_footer_logo2.png');

            $logoBase64 = "data:image/png;base64," . base64_encode(file_get_contents($logoPath));
            $logoBase642 = "data:image/png;base64," . base64_encode(file_get_contents($logoPath2));
            $footerLogoImage64 = "data:image/png;base64," . base64_encode(file_get_contents($footerLogoImage));

            $bal_date = $request->balance_date
                ? date('d.m.Y', strtotime($request->balance_date))
                : date('d.m.Y');

            $query = CustomerOutstanting::with('branch', 'customer')
                ->select('customer_id', 'branch_id', 'user_id', 'division_id', 'year', 'quarter', DB::raw('SUM(amount) as total_amounts'))
                ->groupBy('customer_id', 'branch_id', 'year', 'quarter');

            if ($request->customer_id && !empty($request->customer_id)) {
                $query->where('customer_id', $request->customer_id);
            }

            $query->chunk(50, function ($batch) use ($logoBase64, $footerLogoImage64, $logoBase642, $bal_date) {
                GenerateBalanceConfirmationJob::dispatch($batch, $logoBase64, $footerLogoImage64, $logoBase642, $bal_date);
            });

            return redirect()->back()->with('message_success', 'PDF generation started in background. You will see files once ready.');



            // $data = CustomerOutstanting::with('branch', 'customer')->select(
            //     'customer_id',
            //     'branch_id',
            //     'user_id',
            //     'division_id',
            //     'year',
            //     'quarter',
            //     DB::raw('SUM(amount) as total_amounts'),
            // );

            // if ($request->customer_id && !empty($request->customer_id)) {
            //     $data->where('customer_id', $request->customer_id);
            // }

            // $data = $data->groupBy('customer_id', 'branch_id', 'year', 'quarter')->get();
            // $logoPath = public_path('assets/img/certificate_logo_fan2.png');
            // $logoPath2 = public_path('assets/img/certificate_logo2.png');
            // $footerLogoImage = public_path('assets/img/certificate_footer_logo2.png');
            // $footerLogoImage64 = "data:image/png;base64," . base64_encode(file_get_contents($footerLogoImage));
            // $logoBase64 = "data:image/png;base64," . base64_encode(file_get_contents($logoPath));
            // $logoBase642 = "data:image/png;base64," . base64_encode(file_get_contents($logoPath2));
            // if ($request->balance_date) {
            //     $bal_date = date('d.m.Y', strtotime($request->balance_date));
            // } else {
            //     $bal_date = date('d.m.Y');
            // }
            // $data->chunk(50)->each(function ($batch) use ($logoBase64, $footerLogoImage64, $logoBase642, $bal_date) {
            //     foreach ($batch as $key => $value) {
            //         $main_data = [
            //             'image' => $logoBase64,
            //             'image2' => $footerLogoImage64,
            //             'image3' => $logoBase642,
            //             'date' => $bal_date,
            //             'data' => $value
            //         ];
            //         $html = view('customers.BalanceConfirmationPDF', $main_data)->render();
            //         $dompdf = new Dompdf();
            //         $dompdf->loadHtml($html);
            //         $dompdf->render();

            //         $filename = 'balance_confirmation_' . $value->customer->id . '.pdf';
            //         $tempPath = storage_path('app/temp/' . $filename);
            //         file_put_contents($tempPath, $dompdf->output());
            //         $s3Path = 'uploads/balance_confirmations/' . $filename;
            //         $uploaded = Storage::disk('s3')->put($s3Path, fopen($tempPath, 'r+'));
            //         unlink($tempPath);

            //         if ($uploaded) {
            //             $filePath = Storage::disk('s3')->url($s3Path);
            //             Attachment::updateOrCreate(
            //                 ['document_name' => 'balance_confirmations', 'customer_id' => $value->customer->id],
            //                 ['file_path' => $filePath, 'active' => 'Y']
            //             );
            //         }
            //     }
            // });
            // return redirect()->back()->with('message_success', 'PDF generation successfully.');
        } else if ($request->download == 'excel') {
            abort_if(Gate::denies('customer_outstanting_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();
            return Excel::download(new CutomerOutstantingExport($request), 'customer_outstanding.xlsx');
        }
    }

    public function user_incentive(Request $request)
    {
        $branches =  Branch::where('active', 'Y')->get();
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', 'Y')->orderBy('name', 'asc')->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $currentDate = Carbon::now();
        return view('reports.user_incentive', compact('years', 'users', 'branches'));
    }

    public function user_incentive_list(Request $request)
    {
        $userIds = getUsersReportingToAuth();
        $data = SalesTargetUsers::with(['user', 'user.userinfo', 'branch'])->whereIn('user_id', $userIds)->select([
            DB::raw('GROUP_CONCAT(target) as targets'),
            DB::raw('SUM(target) as total_target'),
            DB::raw('SUM(achievement) as total_achievement'),
            DB::raw('GROUP_CONCAT(achievement) as achievements'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(year) as years'),
            DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
            DB::raw('user_id'),
            DB::raw('branch_id'),
            DB::raw('type'),
        ]);

        if ($request->financial_year && !empty($request->financial_year)) {
            $f_year_array = explode('-', $request->financial_year);
        } else {
            $currentYear = now()->year;
            $currentMonth = now()->month;
            if (!$request->quarter || empty($request->quarter)) {
                if ($currentMonth == 7 || $currentMonth == 8 || $currentMonth == 9) {
                    $request->quarter = '1';
                } elseif ($currentMonth == 10 || $currentMonth == 11 || $currentMonth == 12) {
                    $request->quarter = '2';
                } elseif ($currentMonth == 1 || $currentMonth == 2 || $currentMonth == 3) {
                    $request->quarter = '3';
                } elseif ($currentMonth == 4 || $currentMonth == 5 || $currentMonth == 6) {
                    $request->quarter = '4';
                }
            }

            if ($currentMonth <= 3) {
                $f_year_array = [$currentYear - 1, $currentYear];
            } else {
                $f_year_array = [$currentYear, $currentYear + 1];
            }
        }
        if ($request->quarter && !empty($request->quarter)) {
            if ($request->quarter == '1') {
                $quarter_name = 'Q1';
                $data->where(function ($query) use ($f_year_array) {
                    $query->where('year', '=', $f_year_array[0])
                        ->whereIn('month', ['Apr', 'May', 'Jun']);
                });
                $months = ['Apr', 'May', 'Jun'];
            } elseif ($request->quarter == '2') {
                $quarter_name = 'Q2';
                $data->where(function ($query) use ($f_year_array) {
                    $query->where('year', '=', $f_year_array[0])
                        ->whereIn('month', ['Jul', 'Aug', 'Sep']);
                });
                $months = ['Jul', 'Aug', 'Sep'];
            } elseif ($request->quarter == '3') {
                $quarter_name = 'Q3';
                $data->where(function ($query) use ($f_year_array) {
                    $query->where('year', '=', $f_year_array[0])
                        ->whereIn('month', ['Oct', 'Nov', 'Dec']);
                });
                $months = ['Oct', 'Nov', 'Dec'];
            } elseif ($request->quarter == '4') {
                $quarter_name = 'Q4';
                $data->where(function ($query) use ($f_year_array) {
                    $query->where('year', '=', $f_year_array[1])
                        ->whereIn('month', ['Jan', 'Feb', 'Mar']);
                });
                $months = ['Oct', 'Nov', 'Dec'];
            }
        }

        $data = $data->groupBy('user_id', 'branch_id')->orderBy('month');

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('achiv', function ($data) use ($request, $months, $f_year_array) {
                $fmonth = $months[0];
                $lmonth = $months[2];
                if ($request->quarter == '4') {
                    $monthNumber = Carbon::parse("1 $fmonth")->month;
                    $monthNumber2 = Carbon::parse("1 $lmonth")->month;
                    $firstDate = Carbon::createFromDate($f_year_array[1], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($f_year_array[1], $monthNumber2, 1)->endOfMonth()->toDateString();
                } else {
                    $monthNumber = Carbon::parse("1 $fmonth")->month;
                    $monthNumber2 = Carbon::parse("1 $lmonth")->month;
                    $firstDate = Carbon::createFromDate($f_year_array[0], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($f_year_array[0], $monthNumber2, 1)->endOfMonth()->toDateString();
                }
                $total_achiv =  number_format(($data->user->primarySales->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                return $data->total_achievement ?? $total_achiv;
            })
            ->addColumn('taper', function ($data) use ($request, $months, $f_year_array) {
                $fmonth = $months[0];
                $lmonth = $months[2];
                if ($request->quarter == '4') {
                    $monthNumber = Carbon::parse("1 $fmonth")->month;
                    $monthNumber2 = Carbon::parse("1 $lmonth")->month;
                    $firstDate = Carbon::createFromDate($f_year_array[1], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($f_year_array[1], $monthNumber2, 1)->endOfMonth()->toDateString();
                } else {
                    $monthNumber = Carbon::parse("1 $fmonth")->month;
                    $monthNumber2 = Carbon::parse("1 $lmonth")->month;
                    $firstDate = Carbon::createFromDate($f_year_array[0], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($f_year_array[0], $monthNumber2, 1)->endOfMonth()->toDateString();
                }
                $total_achiv =  number_format(($data->user->primarySales->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                $total_achievement = $data->total_achievement ?? $total_achiv;
                $total_target = $data->total_target ?? 0;
                return number_format((($total_achievement / $total_target) * 100), 2, '.', '');
            })
            ->addColumn('ovper', function ($data) use ($request, $months, $f_year_array, $quarter_name) {
                if ($request->quarter == '4') {
                    $total_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->where('year', $f_year_array[1])->where('quarter', 'Like', '%' . $quarter_name . '%')->sum('amount');
                    $sixty_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->whereNotIn('days', ['0-30', '31-60'])->where('year', $f_year_array[1])->where('quarter', 'Like', '%' . $quarter_name . '%')->sum('amount');
                } else {
                    $total_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->where('year', $f_year_array[0])->where('quarter', 'Like', '%' . $quarter_name . '%')->sum('amount');
                    $sixty_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->whereNotIn('days', ['0-30', '31-60'])->where('year', $f_year_array[0])->where('quarter', 'Like', '%' . $quarter_name . '%')->sum('amount');
                }
                return $total_outstanding > 0 ? number_format((($sixty_outstanding / $total_outstanding) * 100), 2, '.', '') : '0';
            })
            ->addColumn('svper', function ($data) use ($request, $months, $f_year_array, $quarter_name) {
                if ($request->quarter == '4') {
                    $total_stock = BranchStock::where('branch_id', $data->branch_id)->where('year', $f_year_array[1])->where('quarter', 'Like', '%' . $quarter_name . '%')->sum('amount');
                    $ninty_stock = BranchStock::where('branch_id', $data->branch_id)->whereNotIn('days', ['0-30', '31-60', '61-90'])->where('year', $f_year_array[1])->where('quarter', 'Like', '%' . $quarter_name . '%')->sum('amount');
                } else {
                    $total_stock = BranchStock::where('branch_id', $data->branch_id)->where('year', $f_year_array[0])->where('quarter', 'Like', '%' . $quarter_name . '%')->sum('amount');
                    $ninty_stock = BranchStock::where('branch_id', $data->branch_id)->whereNotIn('days', ['0-30', '31-60', '61-90'])->where('year', $f_year_array[0])->where('quarter', 'Like', '%' . $quarter_name . '%')->sum('amount');
                }
                return $total_stock > 0 ? number_format((($ninty_stock / $total_stock) * 100), 2, '.', '') : '0';
            })
            ->addColumn('total_inc', function ($data) use ($request, $months, $f_year_array) {
                $fmonth = $months[0];
                $lmonth = $months[2];
                if ($request->quarter == '4') {
                    $monthNumber = Carbon::parse("1 $fmonth")->month;
                    $monthNumber2 = Carbon::parse("1 $lmonth")->month;
                    $firstDate = Carbon::createFromDate($f_year_array[1], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($f_year_array[1], $monthNumber2, 1)->endOfMonth()->toDateString();
                } else {
                    $monthNumber = Carbon::parse("1 $fmonth")->month;
                    $monthNumber2 = Carbon::parse("1 $lmonth")->month;
                    $firstDate = Carbon::createFromDate($f_year_array[0], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($f_year_array[0], $monthNumber2, 1)->endOfMonth()->toDateString();
                }
                $total_achiv =  number_format(($data->user->primarySales->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                $total_achievement = $data->total_achievement ?? $total_achiv;
                $total_target = $data->total_target ?? 0;
                $total_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->sum('amount');
                $sixty_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->whereNotIn('days', ['0-30', '31-60'])->sum('amount');
                $total_stock = BranchStock::where('branch_id', $data->branch_id)->sum('amount');
                $ninty_stock = BranchStock::where('branch_id', $data->branch_id)->whereNotIn('days', ['0-30', '31-60', '61-90'])->sum('amount');
                $sixty_outstanding_per = $total_outstanding > 0 ? ($sixty_outstanding / $total_outstanding) * 100 : 0;
                $ninty_stock_per = $total_stock > 0 ? ($ninty_stock / $total_stock) * 100 : 0;
                if (($total_achievement / $total_target) * 100 >= 70 && ($total_achievement / $total_target) * 100 <= 79.99) {
                    $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 50) / 100 : 0;
                    $fincentive = $incentive;
                    $wincentive = $incentive;
                    if ($sixty_outstanding_per > 10) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                    if ($ninty_stock_per > 20) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                } elseif (($total_achievement / $total_target) * 100 >= 80 && ($total_achievement / $total_target) * 100 <= 89.99) {
                    $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 100) / 100 : '0';
                    $fincentive = $incentive;
                    $wincentive = $incentive;
                    if ($sixty_outstanding_per > 10) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                    if ($ninty_stock_per > 20) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                } elseif (($total_achievement / $total_target) * 100 >= 90 && ($total_achievement / $total_target) * 100 <= 99.99) {
                    $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 150) / 100 : '0';
                    $fincentive = $incentive;
                    $wincentive = $incentive;
                    if ($sixty_outstanding_per > 10) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                    if ($ninty_stock_per > 20) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                } elseif (($total_achievement / $total_target) * 100 >= 100) {
                    $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 200) / 100 : '0';
                    $fincentive = $incentive;
                    $wincentive = $incentive;
                    if ($sixty_outstanding_per > 10) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                    if ($ninty_stock_per > 20) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                } else {
                    $fincentive = '0';
                    $wincentive =  '0';
                }
                return number_format($fincentive, 2, '.', '');;
            })
            ->addColumn('total_inc_w', function ($data) use ($request, $months, $f_year_array) {
                $fmonth = $months[0];
                $lmonth = $months[2];
                if ($request->quarter == '4') {
                    $monthNumber = Carbon::parse("1 $fmonth")->month;
                    $monthNumber2 = Carbon::parse("1 $lmonth")->month;
                    $firstDate = Carbon::createFromDate($f_year_array[1], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($f_year_array[1], $monthNumber2, 1)->endOfMonth()->toDateString();
                } else {
                    $monthNumber = Carbon::parse("1 $fmonth")->month;
                    $monthNumber2 = Carbon::parse("1 $lmonth")->month;
                    $firstDate = Carbon::createFromDate($f_year_array[0], $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($f_year_array[0], $monthNumber2, 1)->endOfMonth()->toDateString();
                }
                $total_achiv =  number_format(($data->user->primarySales->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                $total_achievement = $data->total_achievement ?? $total_achiv;
                $total_target = $data->total_target ?? 0;
                $total_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->sum('amount');
                $sixty_outstanding = CustomerOutstanting::where('user_id', $data->user_id)->whereNotIn('days', ['0-30', '31-60'])->sum('amount');
                $total_stock = BranchStock::where('branch_id', $data->branch_id)->sum('amount');
                $ninty_stock = BranchStock::where('branch_id', $data->branch_id)->whereNotIn('days', ['0-30', '31-60', '61-90'])->sum('amount');
                $sixty_outstanding_per = $total_outstanding > 0 ? ($sixty_outstanding / $total_outstanding) * 100 : 0;
                $ninty_stock_per = $total_stock > 0 ? ($ninty_stock / $total_stock) * 100 : 0;
                if (($total_achievement / $total_target) * 100 >= 70 && ($total_achievement / $total_target) * 100 <= 79.99) {
                    $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 50) / 100 : 0;
                    $fincentive = $incentive;
                    $wincentive = $incentive;
                    if ($sixty_outstanding_per > 10) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                    if ($ninty_stock_per > 20) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                } elseif (($total_achievement / $total_target) * 100 >= 80 && ($total_achievement / $total_target) * 100 <= 89.99) {
                    $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 100) / 100 : '0';
                    $fincentive = $incentive;
                    $wincentive = $incentive;
                    if ($sixty_outstanding_per > 10) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                    if ($ninty_stock_per > 20) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                } elseif (($total_achievement / $total_target) * 100 >= 90 && ($total_achievement / $total_target) * 100 <= 99.99) {
                    $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 150) / 100 : '0';
                    $fincentive = $incentive;
                    $wincentive = $incentive;
                    if ($sixty_outstanding_per > 10) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                    if ($ninty_stock_per > 20) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                } elseif (($total_achievement / $total_target) * 100 >= 100) {
                    $incentive = $data['user']['userinfo'] ? ($data['user']['userinfo']['gross_salary_monthly'] * 200) / 100 : '0';
                    $fincentive = $incentive;
                    $wincentive = $incentive;
                    if ($sixty_outstanding_per > 10) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                    if ($ninty_stock_per > 20) {
                        $fincentive = '0';
                        $wincentive = $wincentive > 0 ? $wincentive - (($incentive * 20) / 100) : '0';
                    }
                } else {
                    $fincentive = '0';
                    $wincentive =  '0';
                }
                return number_format($wincentive, 2, '.', '');
            })

            ->rawColumns(['achiv', 'taper', 'ovper', 'svper', 'total_inc', 'total_inc_w'])
            ->make(true);
    }

    public function user_incentive_download(Request $request)
    {
        abort_if(Gate::denies('user_incentive_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if ($request->financial_year && !empty($request->financial_year)) {
            $fileName = 'user_incentive_' . $request->financial_year . '_' . $request->quarter . '.xlsx';
        } else {
            $fileName = 'user_incentive.xlsx';
        }
        return Excel::download(new UserIncentiveExport($request), $fileName);
    }
}
