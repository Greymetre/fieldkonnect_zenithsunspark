<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\SalesTargetUsersRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SalesTargetUsers;
use App\Models\SalesTargetCustomers;
use App\Exports\SalesTargetUsersTemplate;
use App\Exports\SalesTargetDealersTemplate;
use App\Exports\SalesAchievementTemplate;
use App\Exports\SalesDealersAchievementTemplate;
use App\Exports\BranchAchievementTemplate;
use App\Exports\SalesTargetUsersExport;
use App\Exports\SalesTargetBranchExport;
use App\Exports\CurrentLastYearSalesGrowthExport;
use App\Exports\SalesDealersTargetBranchExport;
use App\Exports\SalesTargetDealersExport;
use App\Exports\CurrentLastYearDealersSalesGrowthExport;
use App\Exports\CurrentLastYearBranchTargetExport;
use App\Exports\BranchTargetExport;
use App\Imports\SalesTargetUsersImport;
use App\Imports\SalesAchievementImport;
use App\Imports\SalesTargetDealersImport;
use App\Imports\SalesDealersAchievementImport;
use App\Imports\BranchAchievementImport;
use App\Imports\BranchTargetImport;
use App\Exports\BranchTargetTemplate;
use App\Models\BranchWiseTarget;
use App\Models\Branch;
use App\Models\Division;
use App\Models\User;
use App\Models\Customers;
use App\Models\Order;
use App\Models\PrimarySales;
use Carbon\Carbon;
use DataTables;
use Validator;
use Gate;
use Excel;

class SalesTargetUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $all_targets = SalesTargetUsers::all();
        foreach ($all_targets as $key => $value) {
            if ($value->type == 'secondary') {
                $monthNumber = Carbon::parse("1 $value->month")->month;
                $firstDay = Carbon::create($value->year, $monthNumber, 1);
                $lastDay = $firstDay->copy()->endOfMonth();
                $firstDayFormatted = $firstDay->format('Y-m-d');
                $lastDayFormatted = $lastDay->format('Y-m-d');
                $orders_total = Order::whereBetween('order_date', [$firstDayFormatted, $lastDayFormatted])->where('created_by', $value->user_id)->sum('sub_total');
                if ($orders_total > 1) {
                    $achiv = ($orders_total - (($orders_total * 35) / 100)) / 100000;
                    $achiv_per = (100 * $achiv) / $value->target;
                } else {
                    $achiv = 0.00;
                    $achiv_per = 0.00;
                }
                SalesTargetUsers::where('id', $value->id)->update(['achievement' => $achiv, 'achievement_percent' => $achiv_per]);
            }
        }
        $this->middleware('auth');
        $this->subcategories = new SalesTargetUsers();
        $this->path = 'salestargetusers';
    }

    public function sales_target_users_list(Request $request)
    {
        $userid = auth()->user()->id;
        $all_users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->get();
        if(!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Sub_Admin') && !auth()->user()->hasRole('HR_Admin') && !auth()->user()->hasRole('HO_Account')  && !auth()->user()->hasRole('Sub_Support') && !auth()->user()->hasRole('Accounts Order') && !auth()->user()->hasRole('Service Admin') && !auth()->user()->hasRole('All Customers') && !auth()->user()->hasRole('Sub billing') && !auth()->user()->hasRole('Sales Admin'))
        {
            $all_ids_array = array($userid);
            $test = getAllChild(array($userid), $all_users);
            while(count($test) > 0){
                $all_ids_array = array_merge($all_ids_array, $test);
                $test = getAllChild($test, $all_users);
            }
        }elseif(auth()->user()->hasRole('Accounts Order')){
            $all_ids_array = User::where('active' , 'Y')->whereIn('branch_id', explode(',', auth()->user()->branch_show))->pluck('id')->toArray();
            $test = getAllChild(array($userid), $all_users);
            while(count($test) > 0){
                $all_ids_array = array_merge($all_ids_array, $test);
                $test = getAllChild($test, $all_users);
            }
        }else{
            $all_ids_array = User::pluck('id')->toArray();
        }
        $query = SalesTargetUsers::with(['user', 'branch', 'user.getdesignation'])->whereIn('user_id', $all_ids_array)->where(function ($query) use ($request) {

            if ($request->month && $request->month != '' && $request->month != null) {
                $query->where('month', $request->month);
            }

            if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                $userIds = User::where('branch_id', $request->branch_id)->pluck('id');
                $query->whereIn('user_id', $userIds);
            }

            if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
                $userIds = User::where('id', $request->user_id)->pluck('id');
                $query->whereIn('user_id', $userIds);
            }

            if ($request->division && $request->division != '' && $request->division != null) {
                $divisionIds = User::where('division_id', $request->division)->pluck('id');
                $query->whereIn('user_id', $divisionIds);
            }

            if ($request->type && $request->type != '' && $request->type != null) {
                $query->where('type', $request->type);
            }

            if ($request->year && $request->year != '' && $request->year != null) {

                $f_year_array = explode('-', $request->year);
                $months_order = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];

                $query->where(function ($query) use ($f_year_array, $months_order) {
                    $query->where('year', '=', $f_year_array[0])
                          ->whereIn('month', array_slice($months_order, 0, 9)); // Apr to Dec
                })->orWhere(function ($query) use ($f_year_array, $months_order) {
                    $query->where('year', '=', $f_year_array[1])
                          ->whereIn('month', array_slice($months_order, 9)); // Jan to Mar
                });
            }

            if ($request->min_range && $request->min_range != '' && $request->min_range != null && $request->max_range && $request->max_range != '' && $request->max_range != null) {
                $query->whereBetween('points', [$request->min_range, $request->max_range]);
            }
        })->orderBy('id', 'asc');


        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('achievement', function ($data) {
                // dd($data);
                if ($data->user->sales_type == 'Primary') {
                    $monthNumber = Carbon::parse("1 $data->month")->month;
                    $firstDate = Carbon::createFromDate($data->year, $monthNumber, 1)->startOfMonth()->toDateString();
                    $lastDate = Carbon::createFromDate($data->year, $monthNumber, 1)->endOfMonth()->toDateString();

                    $data->achievement = number_format(($data->user->primarySales->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');
                }
                return $data->achievement;
            })
            ->addColumn('achievement_percent', function ($data) {
                if (isset($data['achievement']) && isset($data['target']) && !empty($data['achievement']) && !empty($data['target'])) {
                    $achievementPercent = ($data['target'] == 0) ? 0 : ($data['achievement'] * 100 / $data['target']);
                    return number_format(($achievementPercent), 2) . '%';
                } else {
                    return '';
                }
            })
            ->addColumn('action', function ($data) {
                $btn = '';
                $activebtn = '';

                if (auth()->user()->can(['target_users_access_edit'])) {
                    $btn = $btn . '<a href"javascript:void(0)" class="btn btn-info btn-just-icon btn-sm edit" id="' . encrypt($data->id) . '" title="' . trans('panel.global.edit') . ' ' . trans('panel.sales_target_user.title_singular') . '">
                        <i class="material-icons">edit</i>
                      </a>';
                }

                if (auth()->user()->can(['target_users_access_delete'])) {
                    $btn = $btn . ' <a href="#" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $data->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.sales_target_user.title_singular') . '">
                              <i class="material-icons">clear</i>
                            </a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                              ' . $btn . '
                          </div>';
            })
            ->rawColumns(['action', 'achievement_percent', 'achievement'])
            ->make(true);
    }

    public function sales_target_users(Request $request)
    {
        $sales_target_users = SalesTargetUsers::latest()->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->get();
        $branches = Branch::latest()->get();
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);

        abort_if(Gate::denies('target_users_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('sales_target_users.index', compact('sales_target_users', 'branches', 'years', 'users', 'divisions'));
    }


    public function target_users_upload(Request $request)
    {

        abort_if(Gate::denies('sales_target_users_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        // Excel::import(new SalesTargetUsersImport, request()->file('import_file'));
        Excel::import(new SalesTargetUsersImport, $request->file('import_file')->store('temp'));

        return back()->with('success', 'Sales Target User Import successfully !!');
    }

    public function target_users_download(Request $request)
    {

        if ($request->export_branch) {
            $validator = Validator::make($request->all(), [
                'financial_year' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            abort_if(Gate::denies('sales_branch_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();

            return Excel::download(new SalesTargetBranchExport($request), 'sales_target_branch.xlsx');
        } elseif ($request->export_user) {
            $validator = Validator::make($request->all(), [
                'financial_year' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            abort_if(Gate::denies('sales_target_users_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();

            return Excel::download(new SalesTargetUsersExport($request), 'sales_target_users.xlsx');
        } elseif ($request->cy_ly_sales_report) {
            $validator = Validator::make($request->all(), [
                'financial_year' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            abort_if(Gate::denies('cy_ly_sales_target_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();

            return Excel::download(new CurrentLastYearSalesGrowthExport($request), 'LY_CY_Sales.xlsx');
        }
    }

    public function sales_target_users_delete(Request $request)
    {
        SalesTargetUsers::where('id', $request->id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Sales Target User Deleted successfully']);
    }

    public function template(Request $request)
    {
        abort_if(Gate::denies('sales_target_users_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SalesTargetUsersTemplate, 'sales_target_users.xlsx');
    }

    public function update_target_user_modal(Request $request, $id)
    {
        $id = decrypt($id);
        $sales_target_user = SalesTargetUsers::find($id);
        return response()->json($sales_target_user);
    }

    public function update_target_user_updte(Request $request)
    {

        $data = $request->all();
        SalesTargetUsers::where('id', $data['id'])->update([
            'user_id' => $data['user_id'],
            'month' => $data['month'],
            'year' => $data['year'],
            'target' => $data['target'],
        ]);

        return back()->with('success', 'Sales Target User Updated successfully !!');
    }

    public function achievement_template(Request $request)
    {
        abort_if(Gate::denies('sales_achievement_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SalesAchievementTemplate, 'sales_achievements.xlsx');
    }

    public function achievement_upload(Request $request)
    {
        abort_if(Gate::denies('sales_achievement_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new SalesAchievementImport, request()->file('import_file'));
        // Excel::import(new SalesAchievementImport, $request->file('import_file')->store('temp'));

        return back()->with('success', 'Sales Achievement Import successfully !!');
    }

    public function sales_target_dealers(Request $request)
    {
        $sales_target_users = SalesTargetCustomers::latest()->get();
        $users = Customers::where('customertype', [3, 4])->get();
        $branches = Branch::latest()->get();
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);

        abort_if(Gate::denies('sales_target_dealers_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('sales_target_dealers.index', compact('sales_target_users', 'branches', 'years', 'users', 'divisions'));
    }

    public function sales_dealers_target_achievement(Request $request)
    {

        $query = SalesTargetCustomers::with(['customer', 'customer.createdbyname'])->where(function ($query) use ($request) {

            if (auth()->user()->hasRole('Customer Dealer')) {
                $query->where('customer_id', auth()->user()->customerid);
            }

            if ($request->month && $request->month != '' && $request->month != null) {
                $query->where('month', $request->month);
            }

            if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                $userIds = User::where('branch_id', $request->branch_id)->pluck('id');
                $query->whereIn('customer_id', $userIds);
            }

            if ($request->customer_id && $request->customer_id != '' && $request->customer_id != null) {
                $userIds = Customers::where('id', $request->customer_id)->pluck('id');
                $query->whereIn('customer_id', $userIds);
            }

            if ($request->division && $request->division != '' && $request->division != null) {
                $divisionIds = User::where('division_id', $request->division)->pluck('id');
                $query->whereIn('customer_id', $divisionIds);
            }

            if ($request->type && $request->type != '' && $request->type != null) {
                $query->where('type', $request->type);
            }

            if ($request->year && $request->year != '' && $request->year != null) {

                $f_year_array = explode('-', $request->year);
                $month = $request->month;

                if ($request->month && $request->month != '' && $request->month != null && $request->year && $request->year != '' && $request->year != null) {

                    if ($request->month == 'Jan' || $request->month == 'Feb' || $request->month == 'Mar') {
                        $query->where(function ($query) use ($f_year_array, $month) {
                            $query->where('year', '=', $f_year_array[1])
                                ->where('month', '=', $month);
                        });
                    } else {
                        $query->where(function ($query) use ($f_year_array, $month) {
                            $query->where('year', '=', $f_year_array[0])
                                ->where('month', '=', $month);
                        });
                    }
                } else {
                    $query->where(function ($query) use ($f_year_array) {
                        $query->where('year', '=', $f_year_array[0])
                            ->where('month', '>=', 'Apr');
                    })->orWhere(function ($query) use ($f_year_array) {
                        $query->where('year', '=', $f_year_array[1])
                            ->where('month', '<=', 'Mar');
                    });
                }
            }

            if ($request->min_range && $request->min_range != '' && $request->min_range != null && $request->max_range && $request->max_range != '' && $request->max_range != null) {
                $query->whereBetween('points', [$request->min_range, $request->max_range]);
            }
        })->orderBy('id', 'asc');

        // $data = SalesTargetUsers::with(['user','user.getbranch'])->get();

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('employee_name', function ($data) {

                $monthNumber = Carbon::parse("1 $data->month")->month;
                $firstDate = Carbon::createFromDate($data->year, $monthNumber, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($data->year, $monthNumber, 1)->endOfMonth()->toDateString();

                $employee_name = PrimarySales::where('customer_id', $data->customer_id)->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->orderBy('id', 'desc')->first();

                return $employee_name?$employee_name->sales_person:'-';
            })
            ->addColumn('achievement', function ($data) {

                $monthNumber = Carbon::parse("1 $data->month")->month;
                $firstDate = Carbon::createFromDate($data->year, $monthNumber, 1)->startOfMonth()->toDateString();
                $lastDate = Carbon::createFromDate($data->year, $monthNumber, 1)->endOfMonth()->toDateString();

                $data->achievement = number_format((PrimarySales::where('customer_id', $data->customer_id)->where('invoice_date', '>=', $firstDate)->where('invoice_date', '<=', $lastDate)->sum('net_amount')) / 100000, 2, '.', '');

                return $data->achievement;
            })
            ->addColumn('achievement_percent', function ($data) {
                if (isset($data['achievement']) && isset($data['target']) && !empty($data['achievement']) && !empty($data['target'])) {
                    $achievementPercent = ($data['target'] == 0) ? 0 : ($data['achievement'] * 100 / $data['target']);
                    return number_format($achievementPercent, 2);
                } else {
                    return '';
                }
            })
            ->addColumn('customer_name', function ($data) {

                $first_name = !empty($data['customer']['first_name']) ? $data['customer']['first_name'] : '';
                $last_name = !empty($data['customer']['last_name']) ? $data['customer']['last_name'] : '';

                return $first_name . ' ' . $last_name;
            })
            ->addColumn('city_name', function ($data) {

                $city_name = !empty($data['customer']['customeraddress']['cityname']['city_name']) ? $data['customer']['customeraddress']['cityname']['city_name'] : '';
                return $city_name;
            })
            ->addColumn('branch_name', function ($data) {
                $branch_name = !empty($data['customer']['userdetails']['getbranch']['branch_name']) ? $data['customer']['userdetails']['getbranch']['branch_name'] : '';
                return $branch_name;
            })
            ->addColumn('firm_name', function ($data) {
                $branch_name = !empty($data['customer']['userdetails']['getbranch']['branch_name']) ? $data['customer']['userdetails']['getbranch']['branch_name'] : '';
                return $branch_name;
            })
            ->addColumn('action', function ($data) {
                $btn = '';
                $activebtn = '';

                if (auth()->user()->can(['target_users_access_edit'])) {
                    $btn = $btn . '<a href"javascript:void(0)" class="btn btn-info btn-just-icon btn-sm edit" id="' . encrypt($data->id) . '" title="' . trans('panel.global.edit') . ' ' . trans('panel.sales_target_user.title_singular') . '">
           <i class="material-icons">edit</i>
           </a>';
                }

                if (auth()->user()->can(['target_users_access_delete'])) {
                    $btn = $btn . ' <a href="#" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $data->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.sales_target_user.title_singular') . '">
         <i class="material-icons">clear</i>
         </a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
     ' . $btn . '
     </div>';
            })
            ->rawColumns(['action', 'achievement_percent', 'customer_name', 'city_name', 'branch_name', 'employee_name'])
            ->make(true);
    }


    public function sales_target_dealers_download(Request $request)
    {
        if ($request->export_branch) {
            $validator = Validator::make($request->all(), [
                'financial_year' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            abort_if(Gate::denies('sales_dealers_branch_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();

            return Excel::download(new SalesDealersTargetBranchExport($request), 'sales_target_branch.xlsx');
        } elseif ($request->export_dealer_target) {
            $validator = Validator::make($request->all(), [
                'financial_year' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            abort_if(Gate::denies('sales_target_dealers_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();

            return Excel::download(new SalesTargetDealersExport($request), 'sales_target_dealers.xlsx');
        } elseif ($request->cy_ly_sales_report) {
            $validator = Validator::make($request->all(), [
                'financial_year' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            abort_if(Gate::denies('cy_ly_sales_dealers_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();

            return Excel::download(new CurrentLastYearDealersSalesGrowthExport($request), 'LY_CY_Sales.xlsx');
        }
    }

    public function sales_dealers_achievement_template(Request $request)
    {
        abort_if(Gate::denies('sales_dealers_achievement_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SalesDealersAchievementTemplate, 'sales_dealers_achievements.xlsx');
    }

    public function sales_dealers_target_template(Request $request)
    {
        abort_if(Gate::denies('sales_target_dealers_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SalesTargetDealersTemplate, 'sales_target_dealers.xlsx');
    }

    public function sales_target_dealer_delete(Request $request)
    {
        SalesTargetCustomers::where('id', $request->id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Sales Target Dealer Deleted successfully']);
    }

    public function update_target_dealer_modal(Request $request, $id)
    {
        $id = decrypt($id);
        $sales_target_user = SalesTargetCustomers::find($id);
        // dd($sales_target_user->customer_id);

        $dealer_firm_name = Customers::where('id', $sales_target_user->customer_id)->first();
        $sales_target_user['firm_name'] = $dealer_firm_name->name;
        // dd($dealer_firm_name);
        return response()->json($sales_target_user);
    }

    public function update_target_dealer_update(Request $request)
    {

        $data = $request->all();
        SalesTargetCustomers::where('id', $data['id'])->update([
            'customer_id' => $data['customer_id'],
            'month' => $data['month'],
            'year' => $data['year'],
            'target' => $data['target'],
        ]);

        return back()->with('success', 'Sales Target Dealer Updated successfully !!');
    }

    public function sales_target_dealers_upload(Request $request)
    {
        abort_if(Gate::denies('sales_target_dealers_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new SalesTargetDealersImport, $request->file('import_file')->store('temp'));

        return back()->with('success', 'Sales Target Dealers Import successfully !!');
    }

    public function sales_dealers_achievement_upload(Request $request)
    {
        abort_if(Gate::denies('sales_dealers_achievement_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new SalesDealersAchievementImport, request()->file('import_file'));

        return back()->with('success', 'Sales Dealers Achievement Import successfully !!');
    }

    public function branches_sales_target(Request $request)
    {
        $sales_target_users = SalesTargetUsers::latest()->get();
        $userIds = BranchWiseTarget::select('user_id')->distinct()->get();
        $users = User::whereIn('id', $userIds)->get();
        $branches =  BranchWiseTarget::select('branch_name')->distinct()->get();
        $divisions = BranchWiseTarget::select('division_name')->distinct()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);

        abort_if(Gate::denies('branch_wise_sales_target_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('branchwisetarget.index', compact('sales_target_users', 'branches', 'years', 'users', 'divisions'));
    }

    public function branch_target_list(Request $request)
    {
        $query = BranchWiseTarget::with(['user', 'user.getbranch', 'user.getdesignation', 'user.getdivision'])->where(function ($query) use ($request) {

            if ($request->month && $request->month != '' && $request->month != null) {
                $query->where('month', $request->month);
            }

            if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
                // $userIds = User::where('branch_id', $request->branch_id)->pluck('id');
                $query->where('branch_name', $request->branch_id);
            }

            if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
                $userIds = User::where('id', $request->user_id)->pluck('id');
                $query->whereIn('user_id', $userIds);
            }

            if ($request->division && $request->division != '' && $request->division != null) {
                // $divisionIds = User::where('division_id', $request->division)->pluck('id');
                $query->where('division_name', $request->division);
            }

            if ($request->type && $request->type != '' && $request->type != null) {
                $query->where('type', $request->type);
            }

            if ($request->year && $request->year != '' && $request->year != null) {

                $f_year_array = explode('-', $request->year);
                $month = $request->month;

                if ($request->month && $request->month != '' && $request->month != null) {

                    if ($request->month == 'Jan' || $request->month == 'Feb' || $request->month == 'Mar') {
                        $query->where(function ($query) use ($f_year_array, $month) {
                            $query->where('year', '=', $f_year_array[1])
                                ->where('month', '=', $month);
                        });
                    } else {
                        $query->where(function ($query) use ($f_year_array, $month) {
                            $query->where('year', '=', $f_year_array[0])
                                ->where('month', '=', $month);
                        });
                    }
                } else {
                    $query->where(function ($query) use ($f_year_array) {
                        $query->where('year', '=', $f_year_array[0])
                            ->where('month', '>=', 'Apr');
                    })->orWhere(function ($query) use ($f_year_array) {
                        $query->where('year', '=', $f_year_array[1])
                            ->where('month', '<=', 'Mar');
                    });
                }
            }

            if ($request->min_range && $request->min_range != '' && $request->min_range != null && $request->max_range && $request->max_range != '' && $request->max_range != null) {
                $query->whereBetween('points', [$request->min_range, $request->max_range]);
            }
        })->orderBy('id', 'asc');

        // $data = SalesTargetUsers::with(['user','user.getbranch'])->get();

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('achievement_percent', function ($data) {
                if (isset($data['achievement']) && isset($data['target']) && !empty($data['achievement']) && !empty($data['target'])) {
                    $achievementPercent = ($data['target'] == 0) ? 0 : ($data['achievement'] * 100 / $data['target']);
                    return number_format($achievementPercent, 2);
                } else {
                    return '';
                }
            })
            ->addColumn('action', function ($data) {
                $btn = '';
                $activebtn = '';

                if (auth()->user()->can(['target_users_access_edit'])) {
                    $btn = $btn . '<a href"javascript:void(0)" class="btn btn-info btn-just-icon btn-sm edit" id="' . encrypt($data->id) . '" title="' . trans('panel.global.edit') . ' ' . trans('panel.sales_target_user.title_singular') . '">
                        <i class="material-icons">edit</i>
                      </a>';
                }

                if (auth()->user()->can(['target_users_access_delete'])) {
                    $btn = $btn . ' <a href="#" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $data->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.sales_target_user.title_singular') . '">
                              <i class="material-icons">clear</i>
                            </a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                              ' . $btn . '
                          </div>';
            })
            ->rawColumns(['action', 'achievement_percent'])
            ->make(true);
    }

    public function branch_target_template(Request $request)
    {
        abort_if(Gate::denies('branch_wise_target_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new BranchTargetTemplate, 'branch_target.xlsx');
    }

    public function branch_target_upload(Request $request)
    {

        abort_if(Gate::denies('branch_wise_target_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        // Excel::import(new SalesTargetUsersImport, request()->file('import_file'));
        Excel::import(new BranchTargetImport, $request->file('import_file')->store('temp'));

        return back()->with('success', 'Branch Wise Target Import successfully !!');
    }

    public function branch_target_delete(Request $request)
    {
        BranchWiseTarget::where('id', $request->id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Branch Target Deleted successfully']);
    }

    /*Branch target report download
    */

    public function branch_target_download(Request $request)
    {

        if ($request->export_branch_target) {
            $validator = Validator::make($request->all(), [
                'financial_year' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            abort_if(Gate::denies('branch_wise_target_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();

            return Excel::download(new BranchTargetExport($request), 'branch_target.xlsx');
        } elseif ($request->cy_ly_branch_target) {
            $validator = Validator::make($request->all(), [
                'financial_year' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            abort_if(Gate::denies('cy_ly_branch_target_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();

            return Excel::download(new CurrentLastYearBranchTargetExport($request), 'LY_CY_Branch_Target.xlsx');
        }
    }

    /*
    Branch achievement
    */
    public function branch_achievement_template(Request $request)
    {

        abort_if(Gate::denies('branch_target_achievement_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new BranchAchievementTemplate, 'branch_achievements.xlsx');
    }

    public function branch_achievement_upload(Request $request)
    {

        abort_if(Gate::denies('branch_target_achievement_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new BranchAchievementImport, request()->file('import_file'));
        // Excel::import(new SalesAchievementImport, $request->file('import_file')->store('temp'));

        return back()->with('success', 'Branch Achievement Import successfully !!');
    }
}
