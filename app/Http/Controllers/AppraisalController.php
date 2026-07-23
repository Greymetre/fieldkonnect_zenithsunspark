<?php

namespace App\Http\Controllers;

use App\Exports\AppraisalExport;
use App\Models\Appraisal;
use App\Models\Customers;
use App\Models\salesWeightage;
use App\Models\User;
use App\Models\Designation;
use App\Models\Division;
use Edujugon\PushNotification\PushNotification;
use Edujugon\PushNotification\Messages\PushMessage;
use Edujugon\PushNotification\Channels\ApnChannel;
use Edujugon\PushNotification\Channels\FcmChannel;

use Illuminate\Http\Request;
use DataTables;
use Validator;
use Excel;
use Auth;
use DB;
use Carbon\Carbon;


class AppraisalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->appraisal = new Appraisal();
    }
    public function index(Request $request)
    {
        $search_branches = $request->input('search_branches');
        $all_reporting_user_ids = getUsersReportingToAuth();

        $all_user_divisions = User::with('getdivision')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $divisions = array();
        $all_division = array();
        $dkey = 0;

        $all_user_branches = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
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

        ///

        foreach ($all_user_divisions as $dv => $div_val) {
            if ($div_val->getdivision) {
                if (!in_array($div_val->getdivision->id, $all_division)) {
                    array_push($all_division, $div_val->getdivision->id);
                    $divisions[$dkey]['id'] = $div_val->getdivision->id;
                    $divisions[$dkey]['name'] = $div_val->getdivision->division_name;
                    $dkey++;
                }
            }
        }

        ///


        //fy year
        $sale_weightage_years = ['2023-24','2024-25','2025-26'];
        //fy year

        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            $all_reporting_user_ids = User::whereIn('id', $all_reporting_user_ids)->whereIn('branch_id', $search_branches)->pluck('id')->toArray();
        }
        $all_user_details = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $all_users = array();
        $all_designation_id = array();
        $all_designation = array();
        foreach ($all_user_details as $k => $val) {
            $users[$k]['id'] = $val->id;
            $users[$k]['name'] = $val->name;
            if ($val->getdesignation) {
                if (!in_array($val->getdesignation->id, $all_designation_id)) {
                    array_push($all_designation_id, $val->getdesignation->id);
                    $all_designation[$k]['id'] = $val->getdesignation->id;
                    $all_designation[$k]['designation_name'] = $val->getdesignation->designation_name;
                }
            }
        }
        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            if ($request->ajax()) {
                $response = ["users" => $users, "status" => true];
                return response()->json($response);
            }
        }
        if ($request->user_id && $request->user_id != null && $request->user_id != '') {
            $all_reporting_user_ids = array();
            $all_reporting_user_ids[] = $request->user_id;
        }

        if ($request->ajax()) {
            // $data = Appraisal::select('year', 'user_id', DB::raw('GROUP_CONCAT(created_at) as dates'))->whereIn('user_id', $all_reporting_user_ids)->groupBy('year', 'user_id');

            // $data = Appraisal::with(['users.getdesignation','users.getdivision','users.getbranch'])->select('year', 'user_id',DB::raw('GROUP_CONCAT(grade) as grade'), DB::raw('GROUP_CONCAT(created_at) as dates'))->whereIn('user_id', $all_reporting_user_ids)->groupBy('year', 'user_id');


            // tttttt
            $userids = getUsersReportingToAuth();
            $data = User::with([
                'getpmsdetail' => function ($query) use ($request) {
                    $financialYear = $request['financial_year'] ?? getCurrentFinancialYear();
                    // dd($financialYear);
                    $query->where('year', $financialYear);
                },
                'createdbyname',
                'getbranch',
                'getdivision',
                'getdesignation'
            ])
                ->where(function ($query) use ($request, $userids) {
                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                        $query->whereIn('id', $userids);
                    }

                    if (!empty($request['user_id'])) {
                        $query->where('id', $request['user_id']);
                    }

                    if (!empty($request['division_id'])) {
                        $query->where('division_id', $request['division_id']);
                    }
                })
                ->latest();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($query) {
                    return $query->name ? $query->name : '-';
                })
                ->addColumn('designation_name', function ($query) {
                    return $query->getdesignation->designation_name ?? '';
                })
                ->addColumn('division_name', function ($query) {
                    return $query->getdivision->division_name ?? '';
                })
                ->addColumn('branch_name', function ($query) {
                    return $query->getbranch->branch_name ?? '';
                })

                ->addColumn('financial_year', function ($query) {
                    //return str_replace('_', '-', $query->getpmsdetail->year);
                    return $query->getpmsdetail->year ?? '';
                })
                ->addColumn('grade', function ($query) {
                    $grade_val = '';
                    $grade_sum = Appraisal::where('user_id', $query->id)->where('rating_by', $query->id)->sum('grade');

                    //dd($grade_sum);

                    if (!empty($grade_sum)) {
                        if ($grade_sum < 50) {
                            $grade_val = 'C';
                        } elseif ($grade_sum >= 50 && $grade_sum <= 60) {
                            $grade_val = 'B';
                        } elseif ($grade_sum >= 60 && $grade_sum <= 70) {
                            $grade_val = 'B+';
                        } elseif ($grade_sum >= 70 && $grade_sum <= 80) {
                            $grade_val = 'A';
                        } else {
                            $grade_val = 'A+';
                        }
                    }

                    return $grade_val;
                })

                ->addColumn('date', function ($query) {
                    // $all_dates = explode(',', $query->dates);
                    // return date('d-M-y', strtotime($all_dates[0]));
                    return date('d-M-y', strtotime($query->created_at));
                })


                ->addColumn('action', function ($query) {
                    $btn = '';

                    if (Auth::user()->roles->pluck('name')[0] == 'superadmin') {
                        $user_id = auth()->user()->id;
                        if ($query->getpmsdetail) {
                            $btn = $btn . "<a href='" . url("appraisal/" . encrypt($query->getpmsdetail->user_id) . '/' . $query->getpmsdetail->year . '/edit') . "'><span class='btn btn-primary'>Edit</span></a>";
                        }

                        if ($query->getpmsdetail) {
                            $btn = $btn . "<a href='" . url("appraisal/" . encrypt($query->getpmsdetail->user_id) . '/' . $query->getpmsdetail->year . '/viewappraisal') . "'><span class='btn btn-success'>View</span></a>";
                        }

                        if (empty($query->getpmsdetail)) {
                            $btn = $btn . "<a href='" . url("appraisal/" . $query->id . "/create/") . "'><span class='btn btn-warning'>Create</span></a>";
                        } else {

                            if ($query->getpmsdetail) {
                                $btn = $btn . "<a href='" . url("appraisal/" . encrypt($query->getpmsdetail->user_id) . '/' . $query->getpmsdetail->year . '/appraisalApprove') . "'><span class='btn btn-info'>Approve</span></a>";
                            }
                        }
                    } else {

                        $user_id = auth()->user()->id;
                        if ($user_id == $query->id) {
                            if ($query->getpmsdetail) {
                                $btn = $btn . "<a href='" . url("appraisal/" . encrypt($query->getpmsdetail->user_id) . '/' . $query->getpmsdetail->year . '/edit') . "'><span class='btn btn-primary'>Edit</span></a>";
                            }
                        }

                        if ($query->getpmsdetail) {
                            $btn = $btn . "<a href='" . url("appraisal/" . encrypt($query->getpmsdetail->user_id) . '/' . $query->getpmsdetail->year . '/viewappraisal') . "'><span class='btn btn-success'>View</span></a>";
                        }

                        if ($user_id == $query->id && empty($query->getpmsdetail)) {
                            $btn = $btn . "<a href='" . url("appraisal/" . $query->id . "/create/") . "'><span class='btn btn-warning'>Create</span></a>";
                        }


                        if ($user_id != $query->id && !empty($query->getpmsdetail)) {
                            $btn = $btn . "<a href='" . url("appraisal/" . encrypt($query->getpmsdetail->user_id) . '/' . $query->getpmsdetail->year . '/appraisalApprove') . "'><span class='btn btn-info'>Approve</span></a>";
                        }
                    }


                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
                })

                ->rawColumns(['financial_year', 'date', 'grade', 'action', 'getdesignation.designation_name'])
                ->make(true);
        }
        return view('appraisal.index', compact('branches', 'users', 'all_designation', 'divisions', 'sale_weightage_years'));
    }

    public function create(Request $request)
    {

        $search_branches = $request->input('search_branches');
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_branches = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
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
            $all_reporting_user_ids = User::whereIn('id', $all_reporting_user_ids)->whereIn('branch_id', $search_branches)->pluck('id')->toArray();
        }
        $all_user_details = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
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
        //$sale_weightage = salesWeightage::get();

        $divisions = Division::all();
        $designations = Designation::all();

        $sale_weightage_years = ['2023-24','2024-25','2025-26'];

        //dd($sale_weightage_years);




        $user_id = $request->id;
        $user_details = User::where('id', $user_id)->first();
        $division_id = $user_details->division_id;

        $designation_id = $user_details->designation_id;

        $curruntfinancial_year = getCurrentFinancialYear();

        $sale_weightages = salesWeightage::where('division_id', $division_id)->where('financial_year', $curruntfinancial_year)->whereRaw("FIND_IN_SET('$designation_id', designation_id)")->get();



        return view('appraisal.create', compact('users', 'branches', 'sale_weightages', 'divisions', 'designations', 'user_id', 'sale_weightage_years','curruntfinancial_year'))->with('appraisal', $this->appraisal);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'executive_id' => 'required',
            'f_year' => 'required',
            //'appraisal_type' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user_id = auth()->user()->id;
        $sale_weightage_id = $request->sale_weightage_id;
        // $executive_id = $request->executive_id;
        $f_year = $request->f_year;
        //$appraisal_type = $request->appraisal_type;
        //$appraisal_session = $request->appraisal_session;
        $target = $request->target;
        $achivment = $request->achivment;
        $acual = $request->acual;
        $rating = $request->rating;
        $remark = $request->remark;
        $weightage = $request->weightage;
        $user_id_new = $request->user_id;
        $totalper = 0;

        // foreach ($sale_weightage_id as $key => $value) {
        //     $weightages = salesWeightage::find($value);
        //     //$totalper += ($weightages->weightage * $rating[$key]) / 10;
        //     Appraisal::updateOrCreate(
        //         [
        //             'weightage_id' => $value,
        //             'user_id' => $user_id_new,
        //             'year' => $f_year,
        //             'rating_by' => $user_id,
        //         ],
        //         [
        //             'weightage_id' => $value,
        //             'user_id' => $user_id_new,
        //             'year' => $f_year,
        //             'target' => $target[$key],
        //             'achivment' => $achivment[$key],
        //             'acual' => $acual[$key],
        //             'rating' => $rating[$key],
        //             'rating_by' => $user_id,
        //             //'appraisal_type' => $appraisal_type,
        //             //'appraisal_session' => $appraisal_session,
        //             'remark' => $remark,
        //         ]
        //     );
        // }


        //new


        if (!empty($request['kra_names'])) {
            foreach ($request['kra_names'] as $key => $rows) {

                $totalper = ($weightage[$key] * $rating[$key]) / 10;

                $appraisals = Appraisal::updateOrCreate(
                    [
                        'weightage_id' => $sale_weightage_id[$key],
                        'user_id' => $user_id_new,
                        'year' => $f_year,
                        'rating_by' => $user_id
                    ],
                    [
                        'kra' => $request['kra_names'][$key],
                        'weightage_id' => $sale_weightage_id[$key],
                        'user_id' => $user_id_new,
                        'year' => $f_year,
                        'target' => $target[$key],
                        'achivment' => $achivment[$key],
                        'acual' => $acual[$key],
                        'rating' => $rating[$key],
                        'rating_by' => $user_id,
                        'remark' => $remark,
                        'grade' => $totalper
                    ]
                );
            }
        }


        //new


        return redirect(url('appraisal/index'));
    }

    public function edit(Request $request)
    {

        $user_id = decrypt($request->id);
        $year = $request->year;
        //    $appraisal_details = Appraisal::with('sales_weightage')->where('user_id',$user_id)->where('year',$year)->get();
        $appraisal_details = Appraisal::with('sales_weightage')
            ->select([
                DB::raw('GROUP_CONCAT(acual) as acuals'),
                DB::raw('GROUP_CONCAT(achivment) as achivments'),
                DB::raw('GROUP_CONCAT(target) as targets'),
                DB::raw('GROUP_CONCAT(user_id) as user_ids'),
                DB::raw('GROUP_CONCAT(rating_by) as rating_bys'),
                DB::raw('year'),
                DB::raw('GROUP_CONCAT(rating) as ratings'),
                DB::raw('GROUP_CONCAT(remark) as remarks'),
                DB::raw('weightage_id'),
            ])
            ->where('user_id', $user_id)
            ->where('year', $year)
            ->groupBy('weightage_id', 'year')
            ->orderByRaw('MIN(created_at)')
            ->get();
        // dd($appraisal_details);

        $sale_weightage_years = ['2023-24','2024-25','2025-26'];

        // $designation_id = $user_details->designation_id;   

        //$sale_weightage = salesWeightage::where('division_id',$division_id)->whereRaw("FIND_IN_SET('$designation_id', designation_id)")->get();

        //return view('appraisal.edit', compact('sale_weightage','user_id'))->with('appraisal', $this->appraisal);
        return view('appraisal.edit', compact('appraisal_details', 'user_id', 'sale_weightage_years'));
    }


    public function updateAppraisal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'executive_id' => 'required',
            'f_year' => 'required',
            //'appraisal_type' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $user_id = auth()->user()->id;
        $sale_weightage_id = $request->sale_weightage_id;
        // $executive_id = $request->executive_id;
        $f_year = $request->f_year;
        //$appraisal_type = $request->appraisal_type;
        //$appraisal_session = $request->appraisal_session;
        $target = $request->target;
        $achivment = $request->achivment;
        $acual = $request->acual;
        $rating = $request->rating;
        $remark = $request->remark;
        $user_id_new = $request->user_id;
        $weightage = $request->weightage;
        $totalper = 0;

        // foreach ($sale_weightage_id as $key => $value) {
        //     $weightages = salesWeightage::find($value);
        //     //$totalper += ($weightages->weightage * $rating[$key]) / 10;
        //     Appraisal::updateOrCreate(
        //         [
        //             'weightage_id' => $value,
        //             'user_id' => $user_id_new,
        //             'year' => $f_year,
        //             'rating_by' => $user_id,
        //         ],
        //         [
        //             'weightage_id' => $value,
        //             'user_id' => $user_id_new,
        //             'year' => $f_year,
        //             'target' => $target[$key],
        //             'achivment' => $achivment[$key],
        //             'acual' => $acual[$key],
        //             'rating' => $rating[$key],
        //             'rating_by' => $user_id,
        //             //'appraisal_type' => $appraisal_type,
        //             //'appraisal_session' => $appraisal_session,
        //             'remark' => $remark,
        //         ]
        //     );
        // }

        //new
        $totalper = 0;

        if (!empty($request['appraisal_ids'])) {
            foreach ($request['appraisal_ids'] as $key => $appraisal_id) {

                $totalper = ($weightage[$key] * $rating[$key]) / 10;

                $appraisals = Appraisal::updateOrCreate(
                    [
                        'weightage_id' => $sale_weightage_id[$key],
                        'user_id' => $user_id_new,
                        'year' => $f_year,
                        'rating_by' => $user_id
                    ],
                    [
                        'kra' => $request['kra_names'][$key],
                        'weightage_id' => $sale_weightage_id[$key],
                        'user_id' => $user_id_new,
                        'year' => $f_year,
                        'target' => $target[$key],
                        'achivment' => $achivment[$key],
                        'acual' => $acual[$key],
                        'rating' => $rating[$key],
                        'rating_by' => $user_id,
                        'remark' => $remark,
                        'grade' => $totalper
                    ]
                );
            }
        }

        //new

        return redirect(url('appraisal/index'));
    }


    public function viewappraisal(Request $request)
    {

        $user_id = decrypt($request->id);
        $year = $request->year;

        $appraisal_details = Appraisal::with('sales_weightage')->select([
            DB::raw('GROUP_CONCAT(acual) as acuals'),
            DB::raw('GROUP_CONCAT(achivment) as achivments'),
            DB::raw('GROUP_CONCAT(target) as targets'),
            DB::raw('GROUP_CONCAT(user_id) as user_ids'),
            DB::raw('GROUP_CONCAT(rating_by) as rating_bys'),
            DB::raw('year'),
            DB::raw('GROUP_CONCAT(rating) as ratings'),
            DB::raw('GROUP_CONCAT(remark) as remarks'),
            DB::raw('weightage_id'),
        ])->where('user_id', $user_id)->where('year', $year)->groupBy('weightage_id', 'year')->get();

        $appraisal_grade_details = Appraisal::with('sales_weightage')->select([
            DB::raw('SUM(grade) as grades'),
            DB::raw('GROUP_CONCAT(achivment) as achivments'),
            DB::raw('GROUP_CONCAT(target) as targets'),
            DB::raw('GROUP_CONCAT(user_id) as user_ids'),
            DB::raw('GROUP_CONCAT(rating_by) as rating_bys'),
            DB::raw('year'),
            DB::raw('GROUP_CONCAT(rating) as ratings'),
            DB::raw('GROUP_CONCAT(remark) as remarks'),
        ])->where('user_id', $user_id)->where('year', $year)->groupBy('rating_by', 'year')->get();

        return view('appraisal.view', compact('appraisal_details', 'user_id', 'appraisal_grade_details'));
    }


    public function appraisalApprove(Request $request)
    {

        $user_id = decrypt($request->id);
        $year = $request->year;


        $appraisal_details = Appraisal::with('sales_weightage')->select([
            DB::raw('GROUP_CONCAT(acual) as acuals'),
            DB::raw('GROUP_CONCAT(achivment) as achivments'),
            DB::raw('GROUP_CONCAT(target) as targets'),
            DB::raw('GROUP_CONCAT(user_id) as user_ids'),
            DB::raw('GROUP_CONCAT(rating_by) as rating_bys'),
            DB::raw('year'),
            DB::raw('GROUP_CONCAT(rating) as ratings'),
            DB::raw('GROUP_CONCAT(remark) as remarks'),
            DB::raw('weightage_id'),
        ])->where('user_id', $user_id)->where('year', $year)->groupBy('weightage_id', 'year')->get();

        // dd($appraisal_details);



        //$appraisal_details = Appraisal::with('sales_weightage')->where('user_id',$user_id)->where('year',$year)->get();
        return view('appraisal.approve', compact('appraisal_details', 'user_id'));
    }

    public function updateapproval(Request $request)
    {

        $rating_by = auth()->user()->id;
        $sale_weightage_id = $request->sale_weightage_id;
        // $executive_id = $request->executive_id;
        $f_year = $request->f_year;
        //$appraisal_type = $request->appraisal_type;
        //$appraisal_session = $request->appraisal_session;
        $target = $request->target;
        $achivment = $request->achivment;
        $acual = $request->acual;
        $rating = $request->rating;
        $remark = $request->remark;
        $user_id_new = $request->user_id;
        $totalper = 0;
        $weightage = $request->weightage;




        if (!empty($request['sale_weightage_ids'])) {
            foreach ($request['sale_weightage_ids'] as $key => $sale_weightage_id) {
                $totalper = ($weightage[$key] * $rating[$key]) / 10;
                $appraisals = Appraisal::updateOrCreate(
                    [
                        //'id' => $appraisal_id,
                        'weightage_id' => $sale_weightage_id,
                        'user_id' => $user_id_new,
                        'year' => $f_year,
                        'rating_by' => $rating_by,
                    ],
                    [
                        'kra' => $request['kra_names'][$key],
                        'weightage_id' => $sale_weightage_id,
                        'user_id' => $user_id_new,
                        'year' => $f_year,
                        'target' => $target[$key],
                        'achivment' => $achivment[$key],
                        'acual' => $acual[$key],
                        'rating' => $rating[$key],
                        'rating_by' => $rating_by,
                        'remark' => $remark,
                        'grade' => $totalper
                    ]
                );
            }
        }


        return redirect(url('appraisal/index'));
    }





    public function download(Request $request)
    {
        $all_sales_weight = salesWeightage::get();
        $validator = Validator::make($request->all(), [
            'financial_year' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $executive_id = $request->input('executive_id');
        $f_year = $request->input('financial_year');
        //$f_year = str_replace('_', '-', $f_year);
        if ($executive_id && $executive_id != '' && $executive_id != null) {
            $all_reporting_user_ids = array($executive_id);
        } else {
            $all_reporting_user_ids = getUsersReportingToAuth();
        }
        $data = [];
        $appraisal = Appraisal::select(
            DB::raw('GROUP_CONCAT(IFNULL(target, \'\')) as target'),
            DB::raw('GROUP_CONCAT(weightage_id) as weightage_id'),
            DB::raw('GROUP_CONCAT(year) as year'),
            DB::raw('GROUP_CONCAT(user_id) as user_id'),
            DB::raw('GROUP_CONCAT(achivment) as achivment'),
            DB::raw('GROUP_CONCAT(rating) as rating'),
            DB::raw('GROUP_CONCAT(rating_by) as rating_by'),
        )->whereIn('user_id', $all_reporting_user_ids)->where('year', $f_year)->groupBy('year', 'user_id')->orderBy('year')->get();

        $rportingByArray = Appraisal::select(DB::raw('GROUP_CONCAT(user_id) as user_id'), 'year', 'rating_by')->whereIn('user_id', $all_reporting_user_ids)->groupBy('year', 'rating_by')->where('year', $f_year)->orderBy('year')->get();


        // $all_grades =  array('Grade By Self');

        $all_grades =  array('Self Grade');


        $first_head = [
            'S.No',
            'Emp Code',
            'Emp Name',
            'Designation',
            'Branch',
            'Division',
            'Department',
            'Reporting Head',
            'Email',
            'Personal Number',
            'Mobile Number',
            'Head Quarter',
            'Date Of Birth',
            'Date Of Joining',
            'Date Of Leaving',
            'Education',
            'Age',
            'Company TENURE(in year)',
            'Previous Exp(in year)',
            'Total Exp(in year)',
            'Gross Salary Monthly',
            'CTC Per Month',
            'CTC Annual',
            'Last Yr Gross Increments Value',
            'Last Yr Increments %',
            'Last Yr Increment Value',
        ];


        if (count($appraisal) > 0) {
            $rids = array();
            foreach ($appraisal as $k => $val) {
                $check_same_user = explode(',', $val->user_id);
                $year_array = explode(',', $val->year);
                $allWId = explode(',', $val->weightage_id);
                $final_arr = array();
                foreach ($allWId as $wid) {
                    if (!in_array($wid, $final_arr)) {
                        array_push($final_arr, $wid);
                    }
                }


                //new
                $degree_name = array();
                if (!empty($val->users->geteducation)) {
                    foreach ($val->users->geteducation as $key_new => $datas) {
                        $degree_name[] = isset($datas->degree_name) ? $datas->degree_name : '';
                    }
                }

                //new


                $data[$k][0] = ++$k;
                $data[$k][1] = $val->users->employee_codes ?? '';
                $data[$k][2] = $val->users->name;
                $data[$k][3] = $val->users->getdesignation->designation_name ?? '';
                $data[$k][4] = $val->users->getbranch->branch_name ?? '';
                $data[$k][5] = $val->users->getdivision->division_name ?? '';
                $data[$k][6] = $val->users->getdepartment->name ?? '';
                $data[$k][7] = $val->users->reportinginfo->name;
                $data[$k][8] = $val->users->email;
                $data[$k][9] = $val->users->userinfo->emergency_number ?? '';
                $data[$k][10] = $val->users->mobile;
                $data[$k][11] = $val->users->location;
                $data[$k][12] = $val->users->userinfo ? date('d-m-Y', strtotime($val->users->userinfo->date_of_birth)) : "";
                $data[$k][13] = $val->users->userinfo ? date('d-m-Y', strtotime($val->users->userinfo->date_of_joining)) : "";
                $data[$k][14] = $val->users->userinfo ? date('d-m-Y', strtotime($val->users->userinfo->date_of_leaving)) : "";
                $data[$k][15] = implode(',', $degree_name);
                $data[$k][16] = Carbon::parse($val->users->userinfo->date_of_birth ?? 0)->age;
                $data[$k][17] = $val->users->userinfo->current_company_tenture ?? 0;
                $data[$k][18] = $val->users->userinfo->previous_exp ?? 0;
                $data[$k][19] = $val->users->userinfo->total_exp ?? 0;
                $data[$k][20] = $val->users->userinfo->gross_salary_monthly ?? 0;
                $data[$k][21] = $val->users->userinfo->salary ?? 0;
                $data[$k][22] = $val->users->userinfo->ctc_annual ?? 0;
                $data[$k][23] = $val->users->userinfo->last_year_increments ?? 0;
                $data[$k][24] = $val->users->userinfo->last_year_increment_percent ?? 0;
                $data[$k][25] = $val->users->userinfo->last_year_increment_value ?? 0;
                $all_reporting_by = explode(',', $val->rating_by);
                $all_reporting_by = array_unique($all_reporting_by);
                if (in_array($val->users->id, $all_reporting_by)) {
                    $getRating = Appraisal::where('user_id', $val->users->id)->where('rating_by', $val->users->id)->get();
                    $totalper = 0;
                    if (count($getRating) > 0) {
                        foreach ($getRating as $calcu) {
                            $totalper += ($calcu->sales_weightage->weightage * $calcu->rating) / 10;
                        }
                        if ($totalper < 51) {
                            $data[$k][26] = 'C';
                        } else if ($totalper > 50 && $totalper < 61) {
                            $data[$k][26] = 'B';
                        } else if ($totalper > 60 && $totalper < 71) {
                            $data[$k][26] = 'B+';
                        } else if ($totalper > 70 && $totalper < 81) {
                            $data[$k][26] = 'A';
                        } else if ($totalper > 80) {
                            $data[$k][26] = 'A+';
                        }
                    }
                } else {
                    $data[$k][26] = '-';
                }

                $remark = "-";
                $Increment = "-";
                $asmrat = '-';
                $bmrat = '-';
                $rmrat = '-';
                $shrat = '-';
                $agmrat = '-';
                $chmrat = '-';
                $homrat = '-';
                foreach ($rportingByArray as $k2 => $val2) {
                    $check_same_user2 = explode(',', $val2->user_id);
                    $main_user = User::find($check_same_user[0]);
                    $rp_user = User::find($val2->rating_by);

                    if ($rp_user->hasRole('ASM')) {
                        if (!in_array($val2->rating_by, $check_same_user) && in_array($main_user->id, $check_same_user2) && $rp_user->roles[0]->id != $main_user->roles[0]->id) {
                            $rats = Appraisal::where('year', $val2->year)->where('user_id', $main_user->id)->where('rating_by', $val2->rating_by)->get();
                            if (count($rats) > 0) {
                                $totalper = 0;
                                foreach ($rats as $fn) {
                                    $totalper += ($fn->sales_weightage->weightage * $fn->rating) / 10;
                                    if ($totalper < 51) {
                                        $asmrat = 'C';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = 'NIL';
                                        }
                                    } else if ($totalper > 50 && $totalper < 61) {
                                        $asmrat = 'B';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '8%';
                                        }
                                    } else if ($totalper > 60 && $totalper < 71) {
                                        $asmrat = 'B+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '10%';
                                        }
                                    } else if ($totalper > 70 && $totalper < 81) {
                                        $asmrat = 'A';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '12%';
                                        }
                                    } else if ($totalper > 80) {
                                        $asmrat = 'A+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '14%';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($rp_user->hasRole('Branch Manager')) {
                        if (!in_array($val2->rating_by, $check_same_user) && in_array($main_user->id, $check_same_user2) && $rp_user->roles[0]->id != $main_user->roles[0]->id) {
                            $rats = Appraisal::where('year', $val2->year)->where('user_id', $main_user->id)->where('rating_by', $val2->rating_by)->get();
                            if (count($rats) > 0) {
                                $totalper = 0;
                                foreach ($rats as $fn) {
                                    $totalper += ($fn->sales_weightage->weightage * $fn->rating) / 10;
                                    if ($totalper < 51) {
                                        $bmrat = 'C';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = 'NIL';
                                        }
                                    } else if ($totalper > 50 && $totalper < 61) {
                                        $bmrat = 'B';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '8%';
                                        }
                                    } else if ($totalper > 60 && $totalper < 71) {
                                        $bmrat = 'B+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '10%';
                                        }
                                    } else if ($totalper > 70 && $totalper < 81) {
                                        $bmrat = 'A';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '12%';
                                        }
                                    } else if ($totalper > 80) {
                                        $bmrat = 'A+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '14%';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($rp_user->hasRole('Regional Manager')) {
                        if (!in_array($val2->rating_by, $check_same_user) && in_array($main_user->id, $check_same_user2) && $rp_user->roles[0]->id != $main_user->roles[0]->id) {
                            $rats = Appraisal::where('year', $val2->year)->where('user_id', $main_user->id)->where('rating_by', $val2->rating_by)->get();
                            if (count($rats) > 0) {
                                $totalper = 0;
                                foreach ($rats as $fn) {
                                    $totalper += ($fn->sales_weightage->weightage * $fn->rating) / 10;
                                    if ($totalper < 51) {
                                        $rmrat = 'C';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = 'NIL';
                                        }
                                    } else if ($totalper > 50 && $totalper < 61) {
                                        $rmrat = 'B';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '8%';
                                        }
                                    } else if ($totalper > 60 && $totalper < 71) {
                                        $rmrat = 'B+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '10%';
                                        }
                                    } else if ($totalper > 70 && $totalper < 81) {
                                        $rmrat = 'A';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '12%';
                                        }
                                    } else if ($totalper > 80) {
                                        $rmrat = 'A+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '14%';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($rp_user->hasRole('State Head')) {
                        if (!in_array($val2->rating_by, $check_same_user) && in_array($main_user->id, $check_same_user2) && $rp_user->roles[0]->id != $main_user->roles[0]->id) {
                            $rats = Appraisal::where('year', $val2->year)->where('user_id', $main_user->id)->where('rating_by', $val2->rating_by)->get();
                            if (count($rats) > 0) {
                                $totalper = 0;
                                foreach ($rats as $fn) {
                                    $totalper += ($fn->sales_weightage->weightage * $fn->rating) / 10;
                                    if ($totalper < 51) {
                                        $shrat = 'C';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = 'NIL';
                                        }
                                    } else if ($totalper > 50 && $totalper < 61) {
                                        $shrat = 'B';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '8%';
                                        }
                                    } else if ($totalper > 60 && $totalper < 71) {
                                        $shrat = 'B+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '10%';
                                        }
                                    } else if ($totalper > 70 && $totalper < 81) {
                                        $shrat = 'A';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '12%';
                                        }
                                    } else if ($totalper > 80) {
                                        $shrat = 'A+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '14%';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($rp_user->hasRole('Asst General Manager')) {
                        if (!in_array($val2->rating_by, $check_same_user) && in_array($main_user->id, $check_same_user2) && $rp_user->roles[0]->id != $main_user->roles[0]->id) {
                            $rats = Appraisal::where('year', $val2->year)->where('user_id', $main_user->id)->where('rating_by', $val2->rating_by)->get();
                            if (count($rats) > 0) {
                                $totalper = 0;
                                foreach ($rats as $fn) {
                                    $totalper += ($fn->sales_weightage->weightage * $fn->rating) / 10;
                                    if ($totalper < 51) {
                                        $agmrat = 'C';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = 'NIL';
                                        }
                                    } else if ($totalper > 50 && $totalper < 61) {
                                        $agmrat = 'B';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '8%';
                                        }
                                    } else if ($totalper > 60 && $totalper < 71) {
                                        $agmrat = 'B+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '10%';
                                        }
                                    } else if ($totalper > 70 && $totalper < 81) {
                                        $agmrat = 'A';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '12%';
                                        }
                                    } else if ($totalper > 80) {
                                        $agmrat = 'A+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '14%';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($rp_user->hasRole('Cluster Head')) {
                        if (!in_array($val2->rating_by, $check_same_user) && in_array($main_user->id, $check_same_user2) && $rp_user->roles[0]->id != $main_user->roles[0]->id) {
                            $rats = Appraisal::where('year', $val2->year)->where('user_id', $main_user->id)->where('rating_by', $val2->rating_by)->get();
                            if (count($rats) > 0) {
                                $totalper = 0;
                                foreach ($rats as $fn) {
                                    $totalper += ($fn->sales_weightage->weightage * $fn->rating) / 10;
                                    if ($totalper < 51) {
                                        $chmrat = 'C';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = 'NIL';
                                        }
                                    } else if ($totalper > 50 && $totalper < 61) {
                                        $chmrat = 'B';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '8%';
                                        }
                                    } else if ($totalper > 60 && $totalper < 71) {
                                        $chmrat = 'B+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '10%';
                                        }
                                    } else if ($totalper > 70 && $totalper < 81) {
                                        $chmrat = 'A';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '12%';
                                        }
                                    } else if ($totalper > 80) {
                                        $chmrat = 'A+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '14%';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($rp_user->hasRole('Head office')) {
                        if (!in_array($val2->rating_by, $check_same_user) && in_array($main_user->id, $check_same_user2) && $rp_user->roles[0]->id != $main_user->roles[0]->id) {
                            $rats = Appraisal::where('year', $val2->year)->where('user_id', $main_user->id)->where('rating_by', $val2->rating_by)->get();
                            if (count($rats) > 0) {
                                $totalper = 0;
                                foreach ($rats as $fn) {
                                    $totalper += ($fn->sales_weightage->weightage * $fn->rating) / 10;
                                    if ($totalper < 51) {
                                        $homrat = 'C';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = 'NIL';
                                        }
                                    } else if ($totalper > 50 && $totalper < 61) {
                                        $homrat = 'B';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '8%';
                                        }
                                    } else if ($totalper > 60 && $totalper < 71) {
                                        $homrat = 'B+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '10%';
                                        }
                                    } else if ($totalper > 70 && $totalper < 81) {
                                        $homrat = 'A';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '12%';
                                        }
                                    } else if ($totalper > 80) {
                                        $homrat = 'A+';
                                        if ($rp_user->hasRole('Head office')) {
                                            $remark = $rats[0]->remark;
                                            $Increment = '14%';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $data[$k][27] = $asmrat;
                $data[$k][28] = $bmrat;
                $data[$k][29] = $rmrat;
                $data[$k][30] = $shrat;
                $data[$k][31] = $agmrat;
                $data[$k][32] = $chmrat;
                $data[$k][33] = $homrat;
                $data[$k][34] = $Increment;
                $data[$k][35] = " ";
                $data[$k][36] = " ";
                $data[$k][37] = $remark;
            }
        }
        $last_head = [
            'Self Rating',
            'ASM',
            'Branch Manager',
            'Regional Manager',
            'State Head',
            'Asst General Manager',
            'National Head',
            'Head office',
            'Increment %',
            'Final Amount',
            'Promotion',
            'Remark'
        ];
        $headings = array_merge(
            $first_head,
            //$second_head,
            // $all_grades,
            $last_head,
        );
        $export = new AppraisalExport($data, $headings, count($all_grades));

        return Excel::download($export, 'AppraisalData.xlsx');
    }

    public function getappraisal(Request $request)
    {
        if ($request->appraisal_type == 'quarterly' || $request->appraisal_type == 'half_yearly') {
            $all_reporting_user_ids = getUsersReportingToAuth();
            $appraisal = Appraisal::with('sales_weightage')->where('user_id', $request->executive_id)->where('year', $request->f_year)->where('appraisal_type', $request->appraisal_type)->where('appraisal_session', $request->appraisal_session)->whereIn('rating_by', $all_reporting_user_ids)->get();
        } else {
            $all_reporting_user_ids = getUsersReportingToAuth();
            $appraisal = Appraisal::with('sales_weightage')->where('user_id', $request->executive_id)->where('year', $request->f_year)->where('appraisal_type', $request->appraisal_type)->whereIn('rating_by', $all_reporting_user_ids)->get();
        }
        if (count($appraisal) > 0) {
            foreach ($appraisal as $k => $val) {
                $appraisal[$k]->rating_by_user->getdesignation = $val->rating_by_user->getdesignation;
                $appraisal[$k]->rating_by_user->roles = $val->rating_by_user->roles;
            }
        }

        return response()->json($appraisal);
    }


    public function geSalesWeightages(Request $request)
    {
        $user_details = User::where('id', $request->executive_id)->first();
        $division_id = $user_details->division_id;

        $designation_id = $user_details->designation_id;

        $curruntfinancial_year = $request->f_year;

        $sale_weightages = salesWeightage::where('division_id', $division_id)->where('financial_year', $curruntfinancial_year)->whereRaw("FIND_IN_SET('$designation_id', designation_id)")->get();

        return response()->json(['status' => 'success', 'sale_weightages' => $sale_weightages]);
    }
}
