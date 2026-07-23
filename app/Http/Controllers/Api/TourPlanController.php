<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\City;
use App\Models\TourDetail;
use App\Models\TourProgramme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class   TourPlanController extends Controller
{
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], 400);
        }

        $user_id = $request->input('user_id');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $tour_plan = TourProgramme::where('userid', $user_id)->orderBy('date', 'desc');

        if ($start_date && $start_date != '' && $start_date != null) {
            $start_date = date('Y-m-d', strtotime($start_date));
            $end_date = date('Y-m-d', strtotime($end_date));
            $tour_plan = $tour_plan->whereBetween('date', [$start_date, $end_date]);
        }


        $tour_plan = $tour_plan->latest()->take(30)->get();

        foreach ($tour_plan as $key => $val) {
            $tour_plan[$key]->date = date('d-m-Y', strtotime($val->date));
            if ($val->status == '0') {
                $tour_plan[$key]->status = 'Pending';
            } elseif ($val->status == '1') {
                $tour_plan[$key]->status = 'Approved';
            } else {
                $tour_plan[$key]->status = 'Rejected';
            }
        }

        if (count($tour_plan) > 0) {
            foreach ($tour_plan as $key => $value) {
                if ($value->userid == auth()->user()->id) {
                    $tour_plan[$key]->self = "true";
                } else {
                    $tour_plan[$key]->self = "false";
                }
            }
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $tour_plan], 200);
        } else {
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $tour_plan], 400);
        }
    }

    public function user_list(Request $request)
    {
        $user = $request->user();
        $user_id = $user->id;
        $pageSize = $request->input('pageSize');
        $search_name = $request->input('search_name');
        $search_branches = $request->input('search_branches');


        $all_reporting_user_ids = getUsersReportingToAuth($user_id);


        $all_user_branches = User::with('getbranch')->whereIn('id', getUsersReportingToAuth($user_id))->orderBy('branch_id')->get();
        $branches = array();
        $all_branch = array();
        $bkey = 0;
        foreach ($all_user_branches as $k => $val) {
            $branch_ids = explode(',', $val->branch_id);

            foreach ($branch_ids as $key => $value) {
                if (!in_array($value, $all_branch)) {
                    $all_branch[] = $value; 
                    $branch_name = Branch::where('id', $value)->first();
                    $branches[$bkey] = [
                        'id' => $value,
                        'name' => $branch_name ? $branch_name->branch_name : 'Unknown'
                    ];

                    $bkey++;
                }
            }
        }

        if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
            $all_reporting_user_ids = User::whereIn('id', $all_reporting_user_ids)->where(function ($query) use ($search_branches) {
                foreach ($search_branches as $branch) {
                    $query->orWhereRaw("FIND_IN_SET(?, branch_id)", [$branch]);
                }
            })->pluck('id')->toArray();
        }

        $all_user_details = User::with('getbranch')->whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $all_users = array();
        foreach ($all_user_details as $k => $val) {
            $all_users[$k]['id'] = $val->id;
            $all_users[$k]['name'] = $val->name;
        }
        if ($search_name && $search_name != '') {
            $all_reporting_user_ids = array();
            $all_reporting_user_ids[] = $search_name;
        }
        $date_checkIn = User::select('name', 'id')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('id', 29);
            })
            ->whereIn('id', $all_reporting_user_ids);
        $date_checkIn->orderBy('id', 'desc');

        $date_checkIn = (!empty($pageSize)) ? $date_checkIn->paginate($pageSize) : $date_checkIn->paginate(100);

        $data = array();
        if (count($date_checkIn) > 0) {
            foreach ($date_checkIn as $key => $checkIn) {
                $data[$key]['user_id'] = $checkIn->id;
                $data[$key]['name'] = $checkIn->name;
            }
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'users' => $all_users, 'branches' => $branches, 'page_count' => $date_checkIn->lastPage(), 'data' => $data], 200);
        } else {
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'users' => $all_users, 'branches' => $branches, 'page_count' => $date_checkIn->lastPage(), 'data' => []], 200);
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required|array',
            'town' => 'required|array',
            'objectives' => 'required|array',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], 400);
        }
        $created_by = auth()->user()->id;
        $user_id = $request->input('user_id');
        $all_date = $request->input('date');
        $all_town = $request->input('town');
        $all_objectives = $request->input('objectives');

        if ($created_by) {
            foreach ($all_date as $k => $date) {
                $tours = TourProgramme::updateOrCreate(
                    [
                        'date' => date('Y-m-d', strtotime($date)),
                        'userid' => $user_id,
                    ],
                    [
                        'date' => date('Y-m-d', strtotime($date)),
                        'userid' => $user_id,
                        'town' => $all_town[$k],
                        'objectives' => $all_objectives[$k],
                        'created_by' => $created_by,
                    ]
                );
                $towns = $all_town;
                foreach ($towns as $key => $town) {
                    $cityid = City::where('city_name', '=', $town)->pluck('id')->first();

                    $visited = TourDetail::whereHas('tourinfo', function ($query) use ($user_id) {
                        $query->where('userid', '=', $user_id);
                    })
                        ->where('visited_cityid', '=', $cityid)
                        ->whereNotNull('visited_date')
                        ->select('visited_date')
                        ->latest()
                        ->first();
                    $lastvisited = (isset($cityid) && !empty($visited)) ? $visited['visited_date'] : null;
                    TourDetail::create([
                        'tourid' => isset($tours->id) ? $tours->id : null,
                        'city_id' => isset($cityid) ? $cityid : null,
                        'last_visited' => isset($lastvisited) ? $lastvisited : null,
                    ]);
                }
            }
            return response()->json(['status' => 'success', 'message' => 'Data added successfully.'], 200);
        } else {
            return response(['status' => 'error', 'message' => 'Something went wrong.'], 400);
        }
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required|array',
            'user_id' => 'required',
            'date' => 'required|array',
            'town' => 'required|array',
            'objectives' => 'required|array',
            'status' => 'required|array',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' =>  $validator->errors()], 400);
        }
        $tour_id = $request->input('tour_id');
        $user_id = $request->input('user_id');
        $date = $request->input('date');
        $town = $request->input('town');
        $objectives = $request->input('objectives');
        $status = $request->input('status');


        foreach ($tour_id as $k => $val) {
            $tour_plan = TourProgramme::find($val);

            if ($tour_plan) {
                $tour_plan->date = date('Y-m-d', strtotime($date[$k]));
                $tour_plan->userid = $user_id;
                $tour_plan->town = $town[$k];
                $tour_plan->objectives = $objectives[$k];
                $tour_plan->status = $status[$k];
                $tour_plan->save();
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Data updated successfully.'], 200);
    }
}
