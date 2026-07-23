<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


use Validator;
use Gate;

use App\Models\Division;
use App\Models\EmployeeDetail;
use App\Models\PrimarySales;
use App\Models\PrimaryScheme;
use App\Models\PrimarySchemeDetail;
use App\Models\User;
use Carbon\Carbon;

class PrimarySchemeReportController extends Controller
{
    public function getPrimarySchemeFilter(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        foreach ($years as $k => $year) {
            $startYear = $year - 1;
            $endYear = $year;
            $data['years'][$k]['key'] = $startYear . '-' . $endYear;
            $data['years'][$k]['value'] = $startYear . '-' . $endYear;
        }


        $data['divisions'] = PrimarySales::select('division')
            ->groupBy('division')
            ->pluck('division')
            ->map(function ($item) {
                // if ($item != 'MOTOR') {
                    return [
                        'key' => $item,
                        'value' => $item,
                    ];
                // }
            })
            ->values();
        $data['quarters'] = [['key' => '1', 'value' => 'Q1(Apr,May,Jun)'], ['key' => '2', 'value' => 'Q2(Jul,Aug,Sep)'], ['key' => '3', 'value' => 'Q3(Oct,Nov,Dec)'], ['key' => '4', 'value' => 'Q4(Jan,Feb,Mar)']];
        $data['types'] = [['key' => 'qualified', 'value' => 'Qualified'], ['key' => 'unqualified', 'value' => 'Unqualified']];

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function getPrimarySchemes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'division' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], 400);
        }
        if (!$request->user()->hasRole('superadmin') && !$request->user()->hasRole('Admin')) {
            $user = $request->user();
            if ($request->division == 'PUMP') {
                $user_ids = getUsersReportingToAuth($user->id);
                $branch_ids = User::whereIn('id', $user_ids)->distinct('branch_id')->pluck('branch_id')->toArray();
                $pSchemes = PrimaryScheme::where('quarter', $request->quarter)
                    ->where('division', $request->division)
                    ->where(function ($query) use ($branch_ids) {
                        $query->where('assign_to', '!=', 'branch') // Unconditionally include schemes not assigned to branches
                            ->orWhere(function ($subQuery) use ($branch_ids) {
                                $subQuery->where('assign_to', 'branch') // Include schemes assigned to branches
                                    ->where(function ($nestedQuery) use ($branch_ids) {
                                        foreach ($branch_ids as $branch_id) {
                                            $nestedQuery->orWhereRaw("FIND_IN_SET(?, branch)", [$branch_id]);
                                        }
                                    });
                            });
                    })
                    ->select('id', 'scheme_name')
                    ->get();
            } else {
                $pSchemes = PrimaryScheme::where('quarter', $request->quarter)->where('division', $request->division)->select('id', 'scheme_name')->get();
            }
        } else {
            $pSchemes = PrimaryScheme::where('quarter', $request->quarter)->where('division', $request->division)->select('id', 'scheme_name')->get();
        }
        return response()->json(['status' => 'success', 'data' => $pSchemes]);
    }

    public function getPrimarySchemeData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'financial_year' => 'required',
            'scheme_id' => 'required',
            'division' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], 400);
        }
        $f_year_array = explode('-', $request->financial_year);
        $pSchemesGroups = PrimarySchemeDetail::where('primary_scheme_id', $request->scheme_id)
            ->select([DB::raw('GROUP_CONCAT(`groups`) as `groups`'), 'group_type'])
            ->groupBy('group_type')
            ->get();
        $pSchemesBranchCol = PrimaryScheme::where('id', $request->scheme_id)->groupBy('branch')->pluck('branch');
        $pSchemes = PrimaryScheme::where('id', $request->scheme_id)->first();
        $pSchemesBranch = $pSchemesBranchCol->flatMap(function ($item) {
            return explode(',', $item);
        })->toArray();
        // dd($pSchemesGroups, $pSchemesBranchCol, $pSchemes, $pSchemesBranch);
        if ($pSchemesGroups[0]->group_type == 'group_2') {
            $data = PrimarySales::with(['user', 'user.getdesignation', 'user.getdivision', 'branch', 'customer'])->select([
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(net_amount) as total_net_amount'),
                DB::raw('final_branch'),
                DB::raw('emp_code'),
                DB::raw('branch_id'),
                DB::raw('customer_id'),
                DB::raw('division'),
                DB::raw('group_2'),
                DB::raw('GROUP_CONCAT(DISTINCT new_group_name) as new_group_name'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("CEILING FAN", new_group_name) THEN quantity ELSE 0 END) as ceiling_fan_quantity'),
            ]);
        } else {
            $data = PrimarySales::with(['user', 'user.getdesignation', 'user.getdivision', 'branch', 'customer'])->select([
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(net_amount) as total_net_amount'),
                DB::raw('final_branch'),
                DB::raw('emp_code'),
                DB::raw('branch_id'),
                DB::raw('customer_id'),
                DB::raw('division'),
                DB::raw('new_group_name'),
                DB::raw('GROUP_CONCAT(DISTINCT group_4) as group_4'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("20 additional", group_4) THEN quantity ELSE 0 END) as group_4_quantity'),
            ]);
        }
        $data->where(function ($query) use ($pSchemesGroups) {
            foreach ($pSchemesGroups as $key => $value) {
                // dd($value->groups);
                $groupsArray = explode(',', $value->groups);
                // Apply OR WHERE IN for each group type
                $query->orWhereIn($value->group_type, $groupsArray);
            }
        });

        if ($pSchemes->assign_to == 'branch') {
            $data->whereIn('branch_id', $pSchemesBranch);
        }
        if ($pSchemes->repetition == '3') {
            $data->where('invoice_date', '>=', $pSchemes->start_date)->where('invoice_date', '<=', $pSchemes->end_date);
        }

        if (!$request->user()->hasRole('superadmin') && !$request->user()->hasRole('Admin')) {
            $user = $request->user();
            $user_ids = getUsersReportingToAuth($user->id);
            $customer_ids_assign = EmployeeDetail::whereIn('user_id', $user_ids)->distinct('customer_id')->pluck('customer_id')->toArray();
            $data->whereIn('customer_id', $customer_ids_assign);
        }

        if ($pSchemes->repetition == '5') {
            if ($request->quarter && !empty($request->quarter)) {
                if ($request->quarter == '1') {
                    $data->where(function ($query) use ($f_year_array) {
                        $query->whereYear('invoice_date', '=', $f_year_array[0])
                            ->whereIn('month', ['Apr', 'May', 'Jun']);
                    });
                } elseif ($request->quarter == '2') {
                    $data->where(function ($query) use ($f_year_array) {
                        $query->whereYear('invoice_date', '=', $f_year_array[0])
                            ->whereIn('month', ['Jul', 'Aug', 'Sep']);
                    });
                } elseif ($request->quarter == '3') {
                    $data->where(function ($query) use ($f_year_array) {
                        $query->whereYear('invoice_date', '=', $f_year_array[0])
                            ->whereIn('month', ['Oct', 'Nov', 'Dec']);
                    });
                } elseif ($request->quarter == '4') {
                    $data->where(function ($query) use ($f_year_array) {
                        $query->whereYear('invoice_date', '=', $f_year_array[1])
                            ->whereIn('month', ['Jan', 'Feb', 'Mar']);
                    });
                }
            }
        }

        if ($request->division && !empty($request->division)) {
            $data->where('division', $request->division);
        }


        // dd($data->groupBy('customer_id', 'emp_code', 'final_branch', 'branch_id', 'division', 'new_group_name')->toSql(), $pSchemes->start_date, $pSchemes->end_date, $pSchemesGroups);
        if ($pSchemesGroups[0]->group_type == 'group_2') {
            $data = $data->groupBy('customer_id', 'emp_code', 'final_branch', 'branch_id', 'division', 'group_2')->orderBy('month')->get();
        } else {
            $data = $data->groupBy('customer_id', 'emp_code', 'final_branch', 'branch_id', 'division', 'new_group_name')->orderBy('month')->get();
        }

        $final_data = array();
        foreach ($data as $key => $value) {

            $pSchemesGroups = PrimarySchemeDetail::where('primary_scheme_id', $request->scheme_id)
                ->select([DB::raw('GROUP_CONCAT(`groups`) as `groups`'), 'group_type'])
                ->groupBy('group_type')
                ->get();
            if ($pSchemesGroups[0]->group_type == 'group_2') {
                if ($pSchemes->id == 20) {
                    $CM = PrimarySchemeDetail::whereIn('groups', explode(',', $value['group_2']))->where('min', '<=', $value['ceiling_fan_quantity'])->where('max', '>=', $value['ceiling_fan_quantity'])->where('primary_scheme_id', $request->scheme_id)->first();
                } else {
                    $CM = PrimarySchemeDetail::whereIn('groups', explode(',', $value['group_2']))->where('min', '<=', $value['total_quantity'])->where('max', '>=', $value['total_quantity'])->where('primary_scheme_id', $request->scheme_id)->first();
                }
            } else {
                $CM = PrimarySchemeDetail::where('groups', $value['new_group_name'])->where('min', '<=', $value['total_quantity'])->where('max', '>=', $value['total_quantity'])->where('primary_scheme_id', $request->scheme_id)->first();
            }

            if ($request->types && !empty($request->types)) {
                if ($request->types == 'qualified') {
                    if (!$CM) {
                        continue;
                    }
                } else if ($request->types == 'unqualified') {
                    if ($CM) {
                        continue;
                    }
                }
            }
            $final_data[$key]['dealer_name'] = $value->customer->name;
            $final_data[$key]['group_name'] = $value->new_group_name;
            $final_data[$key]['sale_qty'] = $value->total_quantity;
            $final_data[$key]['sale_amount'] = $value->total_net_amount;

            if ($request->division == 'FAN') {
                $final_data[$key]['discount_cn'] = $CM ? $CM->points : '0';
            } else {
                if ($pSchemes->scheme_type == 'gift') {
                    if ($CM) {
                        if ($CM->slab_min <= $data['total_net_amount'] / 100000) {
                            $final_data[$key]['discount_cn'] = $CM->gift;
                        } else {
                            $checkOthers = PrimarySchemeDetail::where('primary_scheme_id', $request->scheme_id)->where('slab_min', '<=', $final_data[$key]['discount_cn'])->first();
                            $final_data[$key]['discount_cn'] = $checkOthers ? $checkOthers->gift : '-';
                        }
                    } else {
                        $final_data[$key]['discount_cn'] = '-';
                    }
                } else {
                    if ($pSchemes->id == 38) {
                        if ($data->group_4_quantity > 0 && $data->total_quantity >= 300) {
                            $additional = $data->group_4_quantity * 40;
                            $remain = ($data->total_quantity - $data->group_4_quantity) * 20;
                            $final_data[$key]['discount_cn'] = $additional + $remain;
                        } else {
                            $final_data[$key]['discount_cn'] = $CM ? ($CM->primaryscheme->per_pcs == 1 ? $CM->points * $data['total_quantity'] : $CM->points) : '0';
                        }
                    } else {
                        $final_data[$key]['discount_cn'] = $CM ? ($CM->primaryscheme->per_pcs == 1 ? $CM->points * $data['total_quantity'] : $CM->points) : '0';
                    }
                }
            }
        }
        $final_data = array_values($final_data);
        return response()->json(['status' => 'success', 'data' => $final_data]);
    }
}
