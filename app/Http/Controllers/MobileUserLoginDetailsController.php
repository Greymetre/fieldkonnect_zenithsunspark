<?php

namespace App\Http\Controllers;

use Gate;
use Excel;
use Validator;
use App\Models\Branch;
use App\Models\Services;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Exports\MobileAppLoginUsersExport;
use App\Exports\MobileAppLoginUsersFieldKonnectExport;
use App\Models\Designation;
use App\Models\SchemeDetails;
use App\Models\SchemeHeader;
use Carbon\Carbon;
use App\Models\MobileUserLoginDetails;
use App\Models\SalesTargetUsers;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Division;

class MobileUserLoginDetailsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->mobile_user_login_details = new MobileUserLoginDetails();
        $this->path = 'mobile_user_login_details';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function mobile_user_login(Request $request)
    {
        abort_if(Gate::denies('loyalty_mobile_app_users_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::latest()->get();
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);
        $designations = Designation::where('active', 'Y')->get();

        return view('loyalty_app_mobile_users.index', compact('branches', 'years', 'divisions','designations'));
    }


    public function mobile_user_login_list(Request $request)
    {
        $query = MobileUserLoginDetails::with(['customer', 'customer.customeraddress', 'customer.customeraddress.statename', 'customer.customeraddress.districtname', 'customer.customeraddress.cityname'])->where('app', '1')->where(function ($query) use ($request) {

            if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
                $userIds = Customers::where('id', $request->user_id)->pluck('id');
                $query->whereIn('customer_id', $userIds);
            }

            if ($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != '') {
                $startDate = date('Y-m-d', strtotime($request->start_date));
                $endDate = date('Y-m-d', strtotime($request->end_date));
                $query->whereDate('first_login_date', '>=', $startDate)
                    ->whereDate('first_login_date', '<=', $endDate);
            }
        })->orderBy('last_login_date', 'desc');

        // $data = SalesTargetUsers::with(['user','user.getbranch'])->get();

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('contact_person', function ($data) {

                $first_name = !empty($data['customer']['first_name']) ? $data['customer']['first_name'] : '';
                $last_name = !empty($data['customer']['last_name']) ? $data['customer']['last_name'] : '';

                return $first_name . ' ' . $last_name;
            })
            ->addColumn('login_status1', function ($data) {
                if ($data['login_status'] == '0') {
                    return '<span class="badge badge-danger">Logout</span>';
                } elseif ($data['login_status'] == '1') {
                    return '<span class="badge badge-info">Login</span>';
                }
            })
            ->addColumn('first_login_date', function ($data) {
                return $data->first_login_date ? date('d M y h:i A', strtotime($data->first_login_date)) : '';
            })
            ->addColumn('last_login_date', function ($data) {
                return $data->last_login_date ? date('d M y h:i A', strtotime($data->last_login_date)) : '';
            })
            ->addColumn('branches', function ($data) {
                $branch_arr = array();
                if ($data->customer->getemployeedetail && !empty($data->customer->getemployeedetail) && count($data->customer->getemployeedetail) > 0) {
                    foreach ($data->customer->getemployeedetail as $key_new => $datas) {
                        if(isset($datas->employee_detail->getbranch->branch_name) && !in_array($datas->employee_detail->getbranch->branch_name, $branch_arr)){
                            $branch_arr[] =$datas->employee_detail->getbranch->branch_name;
                        }
                    }
                }
                return implode(',',$branch_arr);
            })

            ->addColumn('action', function ($data) {
            })
            ->rawColumns(['action', 'contact_person', 'login_status1', 'last_login_date', 'first_login_date','branches'])
            ->make(true);
    }


    public function mobile_user_login_download(Request $request)
    {
        abort_if(Gate::denies('mobile_app_login_details_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();

        return Excel::download(new MobileAppLoginUsersExport($request), 'mobile_app_login_.xlsx');
    }

    public function user_app_details(Request $request)
    {
        $branches = Branch::latest()->get();
        $divisions = Division::latest()->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 2, $currentYear + 2);

        abort_if(Gate::denies('user_app_details_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('loyalty_app_mobile_users.index_fieldKonnect', compact('branches', 'years', 'divisions'));
    }


    public function user_app_details_list(Request $request)
    {
        $query = MobileUserLoginDetails::with(['user', 'user.getbranch'])->where('app', '2')->where(function ($query) use ($request) {

            if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
                $userIds = Customers::where('id', $request->user_id)->pluck('id');
                $query->whereIn('customer_id', $userIds);
            }

            if ($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != '') {
                $startDate = date('Y-m-d', strtotime($request->start_date));
                $endDate = date('Y-m-d', strtotime($request->end_date));
                $query->whereDate('first_login_date', '>=', $startDate)
                    ->whereDate('first_login_date', '<=', $endDate);
            }
        })->orderBy('last_login_date', 'desc');

        // $data = SalesTargetUsers::with(['user','user.getbranch'])->get();

        return Datatables::of($query)
            ->addIndexColumn()
            
            ->addColumn('login_status1', function ($data) {
                if ($data['login_status'] == '0') {
                    return '<span class="badge badge-danger">Logout</span>';
                } elseif ($data['login_status'] == '1') {
                    return '<span class="badge badge-info">Login</span>';
                }
            })
            ->addColumn('multi_login', function ($data) {
                if(!auth()->user()->hasRole('superadmin')){
                    return '<span class="badge badge-danger" data-id="' . $data['user_id'] . '" data-multi="' . $data['multi_login'] . '">Remove UUID</span>';
                }
                if ($data['unique_id'] == NULL) {
                    return 'No UUID';
                } else{
                    return '<span class="badge badge-danger multi_login_class" data-id="' . $data['user_id'] . '" data-multi="' . $data['multi_login'] . '">Remove UUID</span>';
                }
            })
            ->addColumn('first_login_date', function ($data) {
                return $data->first_login_date ? date('d M y h:i A', strtotime($data->first_login_date)) : '';
            })
            ->addColumn('last_login_date', function ($data) {
                return $data->last_login_date ? date('d M y h:i A', strtotime($data->last_login_date)) : '';
            })
           
            ->rawColumns(['action', 'contact_person', 'login_status1', 'last_login_date', 'first_login_date','branches', 'multi_login'])
            ->make(true);
    }


    public function user_app_details_download(Request $request)
    {
        abort_if(Gate::denies('mobile_app_login_details_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();

        return Excel::download(new MobileAppLoginUsersFieldKonnectExport($request), 'mobile_app_login_userd.xlsx');
    }

    public function user_app_details_multi_login(Request $request)
    {
        $user_id = $request->user_id;
        $user = MobileUserLoginDetails::where('user_id', $user_id)->first();
        $user->unique_id = NULL;
        $user->save();
        return response()->json(['status' => 'success', 'message' => 'Removed UUID Successfully.']);
    }
}
