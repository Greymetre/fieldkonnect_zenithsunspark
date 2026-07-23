<?php

namespace App\Http\Controllers;

use App\Models\Beat;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\Models\{BeatCustomer, BeatSchedule, Customers, District, BeatUser, State, City};
use App\Models\User;
use App\DataTables\SchedulesDataTable;
use Excel;
use App\Imports\BeatImport;
use App\Exports\BeatExport;
use App\Exports\BeatTemplate;
use App\Http\Requests\BeatRequest;

class BeatController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->beats = new Beat();
  }

  public function index(SchedulesDataTable $dataTable)
  {
    ////abort_if(Gate::denies('beat_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    return $dataTable->render('beats.index');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    ////abort_if(Gate::denies('beat_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    $users = User::whereDoesntHave('roles', function ($query) {
      $query->whereIn('id', config('constants.customer_roles'));
    })->select('id', 'name', 'mobile')->get();

    $states = State::where('active', '=', 'Y')->select('id', 'state_name')->get();
    $cities = [];
    $districts = [];
    $customers = Customers::where('active', '=', 'Y')->select('id', 'name', 'mobile')->get();
    return view('beats.create', compact('users', 'customers', 'states', 'cities', 'districts'))->with('beats', $this->beats);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(BeatRequest $request)
  {
    // try
    // { 
    ////abort_if(Gate::denies('beat_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    $request['active'] = 'Y';
    $request['created_by'] = Auth::user()->id;
    if (is_array($request->district_id)) {
      $request['district_id'] = implode(',', $request->district_id);
    }
    if (is_array($request->city_id)) {
      $request['city_id'] = implode(',', $request->city_id);
    }
    $response = $this->beats->create([
      'active' => 'Y',
      'beat_name' => isset($request['beat_name']) ? ucfirst($request['beat_name']) : '',
      'description' => isset($request['description']) ? $request['description'] : '',
      'country_id' => isset($request['country_id']) ? $request['country_id'] : null,
      'state_id' => isset($request['state_id']) ? $request['state_id'] : null,
      'district_id' => isset($request['district_id']) ? $request['district_id'] : null,
      'city_id' => isset($request['city_id']) ? $request['city_id'] : null,
      'region_id' => isset($request['region_id']) ? $request['region_id'] : null,
      'created_by' => isset($request['created_by']) ? $request['created_by'] : null,
      'created_at' => getcurentDateTime(),
      'updated_at' => getcurentDateTime()
    ]);
    if (!empty($response)) {
      $collection = collect([]);
      if ($request['customers']) {
        foreach ($request['customers'] as $key => $value) {
          if (!empty($value)) {
            BeatCustomer::updateOrCreate(['customer_id' => $value], [
              'active' => $request['active'],
              'beat_id' => $response['id'],
              'customer_id' => $value,
              'created_at' => getcurentDateTime(),
              'updated_at' => getcurentDateTime(),
            ]);
            // $collection->push([
            //   'active' => $request['active'],
            //   'beat_id' => $response['id'],
            //   'customer_id' => $value,
            //   'created_at' => getcurentDateTime(),
            //   'updated_at' => getcurentDateTime(),
            // ]);
          }
        }
      }
      $beatusers = collect([]);
      if ($request['users']) {
        foreach ($request['users'] as $key => $value) {
          if (!empty($value)) {
            $beatusers->push([
              'active' => $request['active'],
              'beat_id' => $response['id'],
              'user_id' => $value,
              'created_at' => getcurentDateTime(),
              'updated_at' => getcurentDateTime(),
            ]);
          }
        }
      }
      $schedules = collect([]);
      if (!empty($request['beatdetail'])) {
        foreach ($request['beatdetail'] as $key => $rows) {
          if (isset($rows['user_id']) && isset($rows['beat_date'])) {
            $schedules->push([
              'active' => $request['active'],
              'beat_id' => $response['id'],
              'user_id' => $rows['user_id'],
              'beat_date' => $rows['beat_date'],
              'created_at' => getcurentDateTime(),
              'updated_at' => getcurentDateTime(),
            ]);
          }
        }
      }
      // if($collection->isNotEmpty())
      // {
      //   BeatCustomer::insert($collection->toArray());
      // }
      if ($beatusers->isNotEmpty()) {
        BeatUser::insert($beatusers->toArray());
      }
      if ($schedules->isNotEmpty()) {
        BeatSchedule::insert($schedules->toArray());
      }
      return Redirect::to('beats')->with('message_success', 'beats Store Successfully');
    }
    return redirect()->back()->with('message_danger', 'Error in beats Store')->withInput();
    // }         
    // catch(\Exception $e)
    // {
    //   return redirect()->back()->withErrors($e->getMessage())->withInput();
    // }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\beats  $beats
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    ////abort_if(Gate::denies('beat_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    $id = decrypt($id);
    $beats = Beat::with('beatusers')->find($id);
    $city_names = City::whereIn('id', explode(',', $beats->city_id))->pluck('city_name')->toArray();
    $beats['city_name'] = implode(',', $city_names);
    $district_names = District::whereIn('id', explode(',', $beats->district_id))->pluck('district_name')->toArray();
    $beats['district_name'] = implode(',', $district_names);
    $schedules = BeatSchedule::where('beat_id', $id)->get();
    $customers = BeatCustomer::where('beat_id', $id)->get();
    return view('beats.show', compact('schedules', 'customers'))->with('beats', $beats);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\beats  $beats
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    ////abort_if(Gate::denies('beat_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    try {
      $id = decrypt($id);
      $beats = Beat::with('beatcustomers.customers', 'beatusers.users')->find($id);
      $users = User::whereDoesntHave('roles', function ($query) {
        $query->whereIn('id', config('constants.customer_roles'));
      })->select('id', 'name', 'mobile')->get();
      $customers = Customers::where('active', '=', 'Y')->select('id', 'name', 'mobile')->get();
      $states = State::where('active', '=', 'Y')->select('id', 'state_name')->get();
      $districts = District::where('active', '=', 'Y')->where('state_id', $beats['state_id'])->select('district_name', 'id')->get();
      $cities = City::where('active', '=', 'Y')->where('district_id', $beats['district_id'])->select('city_name', 'id')->get();
      return view('beats.create', compact('users', 'customers', 'states', 'districts', 'cities'))->with('beats', $beats);
    } catch (\Exception $e) {
      return redirect()->back()->withErrors($e->getMessage())->withInput();
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\beats  $beats
   * @return \Illuminate\Http\Response
   */
  public function update(BeatRequest $request, $id)
  {
    try {
      ////abort_if(Gate::denies('beat_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
      $request['beat_id'] = decrypt($id);
      $response = $this->beats->update_data($request);
      if ($response['status'] == 'success') {
        if ($request['customers']) {
          foreach ($request['customers'] as $key => $value) {
            BeatCustomer::updateOrCreate(['customer_id' => $value], [
              'active' => 'Y',
              'beat_id' => $request['beat_id'],
              'customer_id' => $value,
              'created_at' => getcurentDateTime(),
              'updated_at' => getcurentDateTime(),
            ]);
          }
        }
        if ($request['beatdetail']) {
          foreach ($request['beatdetail'] as $key => $rows) {
            BeatSchedule::updateOrCreate(['beat_id' => $request['beat_id'], 'user_id' => $rows['user_id'], 'beat_date' => $rows['beat_date']], [
              'active' => 'Y',
              'beat_id' => $request['beat_id'],
              'user_id' => $rows['user_id'],
              'beat_date' => $rows['beat_date'],
              'created_at' => getcurentDateTime(),
              'updated_at' => getcurentDateTime(),
            ]);

            BeatUser::updateOrCreate(['beat_id' => $request['beat_id'], 'user_id' => $rows['user_id']], [
              'active' => 'Y',
              'beat_id' => $request['beat_id'],
              'user_id' => $rows['user_id'],
              'created_at' => getcurentDateTime(),
              'updated_at' => getcurentDateTime(),
            ]);
          }
        }
        $beatusers = collect([]);
        if ($request['users']) {
          foreach ($request['users'] as $key => $index) {
            BeatUser::updateOrCreate(['beat_id' => $request['beat_id'], 'user_id' => $index], [
              'active' => 'Y',
              'beat_id' => $request['beat_id'],
              'user_id' => $index,
              'created_at' => getcurentDateTime(),
              'updated_at' => getcurentDateTime(),
            ]);
          }
        }
        return Redirect::to('beats')->with('message_success', 'beats Update Successfully');
      }
      return redirect()->back()->with('message_danger', 'Error in beats Update')->withInput();
    } catch (\Exception $e) {
      return redirect()->back()->withErrors($e->getMessage())->withInput();
    }
  }

  public function beatScheduleUpdate(Request $request)
  {
    try {
      if ($request['beatdetail']) {
        foreach ($request['beatdetail'] as $key => $rows) {

          BeatSchedule::updateOrCreate(['beat_id' => $request['beat_id'], 'user_id' => $rows['user_id'], 'beat_date' => $rows['beat_date']], [
            'active' => 'Y',
            'beat_id' => $request['beat_id'],
            'user_id' => $rows['user_id'],
            'beat_date' => $rows['beat_date'],
            'created_at' => getcurentDateTime(),
            'updated_at' => getcurentDateTime(),
          ]);
        }
        return redirect()->back()->with('message_success', 'Schedule Update Successfully');
      }
      return redirect()->back()->with('message_danger', 'Error in beats Schedule')->withInput();
    } catch (\Exception $e) {
      return redirect()->back()->withErrors($e->getMessage())->withInput();
    }
  }

  public function addBeatUsers(Request $request)
  {
    try {
      if ($request['users']) {
        foreach ($request['users'] as $key => $value) {
          if (!empty($value)) {
            BeatUser::updateOrCreate(['beat_id' => $request['beat_id'], 'user_id' => $value], [
              'active' => 'Y',
              'beat_id' => $request['beat_id'],
              'user_id' => $value,
              'created_at' => getcurentDateTime(),
              'updated_at' => getcurentDateTime(),
            ]);
          }
        }
        return redirect()->back()->with('message_success', 'User Update Successfully');
      }
      return redirect()->back()->with('message_danger', 'Error in beats User')->withInput();
    } catch (\Exception $e) {
      return redirect()->back()->withErrors($e->getMessage())->withInput();
    }
  }

  public function addBeatCustomer(Request $request)
  {
    try {
      if ($request['customers']) {
        foreach ($request['customers'] as $key => $value) {
          if (!empty($value)) {
            BeatCustomer::updateOrCreate(['customer_id' => $value], [
              'active' => 'Y',
              'beat_id' => $request['beat_id'],
              'customer_id' => $value,
              'created_at' => getcurentDateTime(),
              'updated_at' => getcurentDateTime(),
            ]);
          }
        }
        return redirect()->back()->with('message_success', 'Customer Update Successfully');
      }
      return redirect()->back()->with('message_danger', 'Error in beats User')->withInput();
    } catch (\Exception $e) {
      return redirect()->back()->withErrors($e->getMessage())->withInput();
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\beats  $beats
   * @return \Illuminate\Http\Response
   */
  public function destroy(beats $beats)
  {
    ////abort_if(Gate::denies('beat_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
  }

  public function beatScheduleDelete($id)
  {
    return json_encode(BeatSchedule::find($id)->delete());
  }

  public function beatCustomerDelete($id)
  {
    return json_encode(BeatCustomer::find($id)->delete());
  }
  public function beatUserDelete($id)
  {
    return json_encode(BeatUser::find($id)->delete());
  }

  public function beatdetail(Request $request)
  {
    ////abort_if(Gate::denies('beatdetail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    if ($request->ajax()) {
      $data = BeatSchedule::with('beats', 'users', 'beatcustomers.customers', 'beats.createdbyname')->latest();
      return Datatables::of($data)
        ->addIndexColumn()
        ->editColumn('created_at', function ($data) {
          return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
        })
        ->addColumn('customers', function ($query) {
          $customers = array();
          foreach ($query['beatcustomers'] as $key => $customer) {
            array_push($customers, $customer['customers']['name']);
          }
          return !empty($customers) ? implode(', ', $customers) : '';
        })
        ->addColumn('action', function ($query) {
          return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group"><a href="' . url("beats/" . encrypt($query->beat_id) . '/edit') . '" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' ' . trans('panel.beat.title_singular') . '">
                                    <i class="material-icons">edit</i>
                                </a></div>';
        })
        ->rawColumns(['action', 'customers'])
        ->make(true);
    }
    return view('beats.beatdetail');
  }

  public function upload(Request $request)
  {
    ////abort_if(Gate::denies('beat_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    if (ob_get_contents()) ob_end_clean();
    ob_start();
    Excel::import(new BeatImport, request()->file('import_file'));
    return back();
  }
  public function download()
  {
    ////abort_if(Gate::denies('beat_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    if (ob_get_contents()) ob_end_clean();
    ob_start();
    return Excel::download(new BeatExport, 'beats.xlsx');
  }
  public function template()
  {
    ////abort_if(Gate::denies('beat_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    if (ob_get_contents()) ob_end_clean();
    ob_start();
    return Excel::download(new BeatTemplate, 'beats.xlsx');
  }
  public function beatsSchedule($id)
  {
    ////abort_if(Gate::denies('beat_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    $id = decrypt($id);
    $beats = Beat::find($id);
    return view('beats.schedule')->with('beats', $beats);
  }

  // public function livelocation(Request $request)
  // {
  //   $search_branches = $request->input('search_branches');
  //   $user_id = auth()->user()->id;
  //   $all_reporting_user_ids = getUsersReportingToAuth($user_id);
  //   $all_user_branches = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
  //   $all_user_divisions = User::with('getdivision')->orderBy('division_id')->get();
  //   $all_user_departments = User::with('getdepartment')->orderBy('department_id')->get();
  //     $branches= array();
  //     $all_branch= array();
  //     $bkey = 0;
  //     foreach ($all_user_branches as $k => $val) {
  //       if($val->getbranch){
  //         if(!in_array($val->getbranch->id, $all_branch)){
  //             array_push($all_branch, $val->getbranch->id);
  //             $branches[$bkey]['id'] = $val->getbranch->id;
  //             $branches[$bkey]['name'] = $val->getbranch->branch_name;
  //             $bkey++;
  //         }
  //       }
  //     }

  //     $divisions = array();
  //     $all_division = array();
  //     $dkey = 0;

  //     foreach ($all_user_divisions as $k => $val) {

  //       if($val->getdivision){
  //         if(!in_array($val->getdivision->id, $all_division)){
  //             array_push($all_division, $val->getdivision->id);
  //             $divisions[$dkey]['id'] = $val->getdivision->id;
  //             $divisions[$dkey]['name'] = $val->getdivision->division_name;
  //             $dkey++;
  //         }
  //       }
  //     }


  //     $departments = array();
  //     $all_department = array();
  //     $dp_key = 0;

  //     foreach($all_user_departments as $k=>$val) {
  //       if($val->getdepartment){
  //         if(!in_array($val->getdepartment->id, $all_department)){
  //             array_push($all_department, $val->getdepartment->id);
  //             $departments[$dp_key]['id'] = $val->getdepartment->id;
  //             $departments[$dp_key]['name'] = $val->getdepartment->name;
  //             $dp_key++;
  //         }
  //       }
  //     }

  //     if($search_branches && count($search_branches) > 0 && $search_branches[0] != null){
  //       $all_reporting_user_ids = User::whereIn('id', $all_reporting_user_ids)->whereIn('branch_id', $search_branches)->pluck('id')->toArray();
  //     }

  //     if(!empty($search_divisions) && count($search_divisions) > 0 && $search_divisions[0] != null){
  //       $all_reporting_user_ids = User::whereIn('id', $all_reporting_user_ids)->whereIn('division_id', $search_divisions)->pluck('id')->toArray();
  //     }

  //     if(!empty($search_departments) && count($search_departments) > 0 && $search_departments[0] != null){
  //       $all_reporting_user_ids = User::whereIn('id', $all_reporting_user_ids)->whereIn('department_id', $search_departments)->pluck('id')->toArray();
  //     }

  //     $all_user_details = User::with('getbranch','getdivision','getdepartment')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
  //     $all_users= array();
  //     foreach ($all_user_details as $k => $val) {
  //         $users[$k]['id'] = $val->id;
  //         $users[$k]['name'] = $val->name;
  //     }
  //     if($request->ajax()){
  //         $response = ["users"=>$users, "status"=>true];
  //         return response()->json($response);
  //     }
  //   return view('beats.livelocation',compact('users', 'branches','divisions','departments'));
  // }

  public function livelocation(Request $request)
  {
    $search_branches = $request->input('search_branches');
    $search_divisions = $request->input('search_divisions');
    $search_departments = $request->input('search_departments');
    $user_id = auth()->user()->id;
    $all_reporting_user_ids = getUsersReportingToAuth($user_id);
    $all_user_divisions = User::whereDoesntHave('roles', function ($query) {
      $query->whereIn('id', config('constants.customer_roles'));
    })->with('getdivision')->orderBy('division_id')->get();
    $all_user_departments = User::whereDoesntHave('roles', function ($query) {
      $query->whereIn('id', config('constants.customer_roles'));
    })->with('getdepartment')->orderBy('department_id')->get();

    $all_user_branches = User::whereDoesntHave('roles', function ($query) {
      $query->whereIn('id', config('constants.customer_roles'));
    })->with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
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

    $divisions = array();
    $all_division = array();
    $dkey = 0;

    foreach ($all_user_divisions as $k => $val) {

      if ($val->getdivision) {
        if (!in_array($val->getdivision->id, $all_division)) {
          array_push($all_division, $val->getdivision->id);
          $divisions[$dkey]['id'] = $val->getdivision->id;
          $divisions[$dkey]['name'] = $val->getdivision->division_name;
          $dkey++;
        }
      }
    }


    $departments = array();
    $all_department = array();
    $dp_key = 0;

    foreach ($all_user_departments as $k => $val) {
      if ($val->getdepartment) {
        if (!in_array($val->getdepartment->id, $all_department)) {
          array_push($all_department, $val->getdepartment->id);
          $departments[$dp_key]['id'] = $val->getdepartment->id;
          $departments[$dp_key]['name'] = $val->getdepartment->name;
          $dp_key++;
        }
      }
    }

    if ($search_branches && count($search_branches) > 0 && $search_branches[0] != null) {
      $all_reporting_user_ids = User::whereDoesntHave('roles', function ($query) {
        $query->whereIn('id', config('constants.customer_roles'));
      })->whereIn('id', $all_reporting_user_ids)->whereIn('branch_id', $search_branches)->pluck('id')->toArray();
    }

    if (!empty($search_divisions) && count($search_divisions) > 0 && $search_divisions[0] != null) {
      $all_reporting_user_ids = User::whereDoesntHave('roles', function ($query) {
        $query->whereIn('id', config('constants.customer_roles'));
      })->whereIn('id', $all_reporting_user_ids)->whereIn('division_id', $search_divisions)->pluck('id')->toArray();
    }

    if (!empty($search_departments) && count($search_departments) > 0 && $search_departments[0] != null) {
      $all_reporting_user_ids = User::whereDoesntHave('roles', function ($query) {
        $query->whereIn('id', config('constants.customer_roles'));
      })->whereIn('id', $all_reporting_user_ids)->whereIn('department_id', $search_departments)->pluck('id')->toArray();
    }

    $all_user_details = User::whereDoesntHave('roles', function ($query) {
      $query->whereIn('id', config('constants.customer_roles'));
    })->with('getbranch', 'getdivision', 'getdepartment')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
    $all_users = array();
    foreach ($all_user_details as $k => $val) {
      $users[$k]['id'] = $val->id;
      $users[$k]['name'] = $val->name;
    }
    if ($request->ajax()) {
      $response = ["users" => $users, "status" => true];
      return response()->json($response);
    }

    if ($request->user_id) {
      $user_id = $request->user_id;
    } else {
      $user_id = NULL;
    }

    if ($request->date) {
      $date = $request->date;
    } else {
      $date = NULL;
    }

    return view('beats.livelocation', compact('users', 'branches', 'divisions', 'departments', 'date', 'user_id'));
  }
}
