<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;
use Validator;
use Gate;
use App\Models\TourProgramme;
use App\DataTables\AttendancesDataTable;
use App\Exports\AttendanceExport;
use Carbon\Carbon;
use Excel;
use App\Models\BeatSchedule;
use Illuminate\Support\Facades\Storage;
use File;

use App\Models\Attachment;
use App\Models\User;
use App\Models\Holiday;

use App\Exports\ExcelExport;
use App\Models\Beat;
use App\Models\CompOffLeave;
use App\Models\Leave;
use App\Models\Media;
use App\Models\TourDetail;
use DateTime;
use DatePeriod;
use DateInterval;
use Stevebauman\Location\Drivers\IpInfo;
use Stevebauman\Location\Facades\Location;

class AttendanceController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
    $this->attendance = new Attendance();
  }

  public function index(AttendancesDataTable $dataTable)
  {
    //abort_if(Gate::denies('attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $pinchinimages = Attachment::where('file_path', '<>', '')->pluck('file_path');
    $path = public_path();
    //$files = File::allFiles($path);
    foreach ($pinchinimages as $key => $image) {
      if (File::exists($path . '/' . $image)) {
        File::move($path . '/' . $image, $path . '/finals/' . $image);
      }
    }
    // foreach ($pinchinimages as $key => $value) {
    //     $newpath = str_ireplace('attendances', 'final', $value);
    //     Storage::move($value, $newpath);

    //     // if(File::exists($value)){
    //     //     File::move(public_path($value), public_path($newpath));
    //     // }           
    // }

    return $dataTable->render('attendances.index');
  }

  public function attendancesInfo(Request $request)
  {
    if ($request->ajax()) {
      $data = Attendance::where(function ($query) use ($request) {
        if (!empty($request['user_id'])) {
          $query->where('user_id', $request['user_id']);
        }
      })
        ->latest();
      return Datatables::of($data)
        ->addIndexColumn()
        ->editColumn('punchin_date', function ($data) {
          return isset($data->punchin_date) ? showdateformat($data->punchin_date) : '';
        })
        ->editColumn('worked_time', function ($data) {
          return isset($data->worked_time) ? $data->worked_time : '';
        })
        ->make(true);
    }
  }

  public function download(Request $request)
  {
    ////abort_if(Gate::denies('visitreport_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    if (ob_get_contents()) ob_end_clean();
    ob_start();
    return Excel::download(new AttendanceExport($request), 'attendancereports.xlsx');
  }



  public function attendanceSummaryDownload(Request $request)
  {


    $filename = 'attendance-summary-report.xlsx';
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    $executive_id = $request->executive_id;
    $end_date = date('Y-m-d', strtotime($end_date . "+1 days"));

    $period = new DatePeriod(
      new DateTime($start_date),
      new DateInterval('P1D'),
      new DateTime($end_date)
    );

    $last60Days = Carbon::now()->subDays(60);



    $attendancesummary = User::with(['attendance_details', 'createdbyname', 'getbranch', 'userinfo'])->where('active', 'Y')->whereDoesntHave('roles', function ($query) {
      $query->whereIn('id', config('constants.customer_roles'));
    })->where('show_attandance_report', '1');
    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
      $attendancesummary = $attendancesummary->whereIn('id', getUsersReportingToAuth());
    }

    if ($executive_id) {
      $attendancesummary = $attendancesummary->where('id', $executive_id);
    }

    $attendancesummary = $attendancesummary->limit(4500)->get();

    $date1 = $start_date;
    $date2 = $end_date;


    $label2 = [];

    //new

    foreach ($period as $key => $value) {

      $label2[] =  $value->format('j-M-Y');
      $like_date =  $value->format('j-M-Y');
    }


    $data = $attendancesummary->map(function ($item, $key) use ($label2, $date1, $date2, $period, $last60Days) {


      //neww
      $label_data = [];
      $total_wo = 0;
      $total_a = 0;
      $total_lop = 0;
      $total_mis = 0;
      $total_pw = 0;
      $total_h = 0;
      $total_hd = 0;
      $total_p = 0;
      $total_pn = 0;
      $total_atte = 0;
      $total_al = 0;
      $total_hdal = 0;
      $total_co = 0;
      $total_con = 0;

      foreach ($period as $key => $value) {
        $like_date =  $value->format('j-M-Y');
        $total_atte++;
        $check = $value->format('Y-m-d');

        //last new

        $attendance_details = Attendance::where(['user_id' => $item->id])->where('punchin_date', 'like', $check . '%')->first();


        ///nnn

        $userId = $item->id;
        $branchId = $item->branch_id;
        $holiday_detail = Holiday::where('branch', $branchId)->get();
        $holiday_dates = $holiday_detail->pluck('holiday_date')->toArray(); // Extract holiday_date values
        $check_date_attendance = explode(',', implode(',', $holiday_dates));

        if (in_array($check, $check_date_attendance)) {
          $label_data[] = 'H';
          $total_h++;
        } else {


          ///nnn  

          $dayname = date('l', strtotime($check));

          if (!empty($attendance_details)) {

            if ($attendance_details->working_type == 'Second Half Leave' || $attendance_details->working_type == 'First Half Leave' || $attendance_details->working_type == 'Full Day Leave' || $attendance_details->working_type == 'Leave') {
              $leaveExists = Leave::where('user_id', $attendance_details->user_id)
                ->whereDate('from_date', '<=', $attendance_details->punchin_date)
                ->whereDate('to_date', '>=', $attendance_details->punchin_date)
                ->first();
            }

            if ($attendance_details->attendance_status == '1') {
              if ($attendance_details->working_type == 'Leave' || $attendance_details->working_type == 'Full Day Leave') {
                if (($leaveExists->bal_type ?? null) == 'Comp-off Balance') {
                  $label_data[] =  'Comp Off';
                  $total_co++;
                } else {
                  $label_data[] =  'AL';
                  $total_al++;
                }
              } elseif ($dayname == 'Sunday') {
                $label_data[] =  'PW';
                $total_pw++;
              } elseif ($attendance_details->working_type == 'Second Half Leave' || $attendance_details->working_type == 'First Half Leave') {
                if (($leaveExists->bal_type ?? null) == 'Comp-off Balance') {
                  $label_data[] =  '1/2P+1/2Comp Off';
                  $total_co++;
                } else {
                  $label_data[] =  '1/2P+1/2AL';
                  $total_hdal++;
                }
              } elseif ($attendance_details->working_type == 'Local Market Visit') {
                $label_data[] =  'P';
                $total_p++;
              } elseif ($attendance_details->working_type == 'Office Work') {
                $label_data[] =  'P';
                $total_p++;
              } elseif ($attendance_details->working_type == 'Office Meeting') {
                $label_data[] =  'P';
                $total_p++;
              } elseif ($attendance_details->working_type == 'Scouting for market') {
                $label_data[] =  'P';
                $total_p++;
              } elseif ($attendance_details->working_type == 'Plumber Meet') {
                $label_data[] =  'P';
                $total_p++;
              } elseif ($attendance_details->working_type == 'Retailer Meet') {
                $label_data[] =  'P';
                $total_p++;
              } elseif ($attendance_details->working_type == 'Service Center Visit') {
                $label_data[] =  'P';
                $total_p++;
              } elseif ($attendance_details->working_type == 'Tour') {
                $label_data[] =  'P';
                $total_p++;
              } elseif ($attendance_details->working_type == 'Holiday') {
                $label_data[] = 'H';
                $total_h++;
              }
            } else if ($attendance_details->attendance_status == '2') {
              if ($attendance_details->working_type == 'Full Day Leave' || $attendance_details->working_type == 'Leave') {
                if (($leaveExists->bal_type ?? null) == 'Comp-off Balance') {
                  $label_data[] =  'Comp Off N';
                  $total_con++;
                } else {
                  $label_data[] =  'LOPN';
                  $total_lop++;
                }
              } elseif ($attendance_details->working_type == 'Second Half Leave' || $attendance_details->working_type == 'First Half Leave') {
                if (($leaveExists->bal_type ?? null) == 'Comp-off Balance') {
                  $label_data[] =  '1/2P+1/2Comp Off N';
                  $total_con++;
                } else {
                  $label_data[] =  '1/2P+1/2LOPN';
                  $total_hd++;
                }
              } else {
                $label_data[] = 'A';
                $total_a++;
              }
            } else {
              if ($attendance_details->working_type == 'Full Day Leave' || $attendance_details->working_type == 'Leave') {

                if (($leaveExists->bal_type ?? null) == 'Comp-off Balance') {
                  $label_data[] =  'Comp Off N';
                  $total_con++;
                } else {
                  $label_data[] =  'LOPN';
                  $total_lop++;
                }
              } elseif ($attendance_details->working_type == 'Second Half Leave' || $attendance_details->working_type == 'First Half Leave') {
                if ($leaveExists->bal_type == 'Comp-off Balance') {
                  $label_data[] =  '1/2P+1/2Comp Off N';
                  $total_con++;
                } else {
                  $label_data[] =  '1/2P+1/2LOPN';
                  $total_hd++;
                }
              } else {
                $label_data[] = 'PN';
                $total_pn++;
              }
            }
          } else {
            $date_of_joining_object = new DateTime($item->userinfo->date_of_joining ?? '');

            if ($dayname == 'Sunday') {
              if ($date_of_joining_object <= $value) {
                $label_data[] = 'W/o';
                $total_wo++;
              } else {
                $label_data[] = '-';
              }
            } else {
              if ($date_of_joining_object <= $value) {
                $label_data[] = 'MIS';
                $total_mis++;
              } else {
                $label_data[] = '-';
              }
            }
          }
        }
      }

      //neww

      $sundayPunchinCount = CompOffLeave::where('comp_off_date', '>=', $last60Days)->where('is_used', false)
        ->where('user_id', $item->id)
        ->sum('balance');

      $label_data[] = $item->leave_balance ?? '0';
      $label_data[] = $sundayPunchinCount > 0 ? $sundayPunchinCount : '0';
      $label_data[] = (string)$total_wo;
      $label_data[] = (string)$total_a;
      $label_data[] = (string)$total_lop;
      $label_data[] = (string)$total_al;
      $label_data[] = (string)$total_con;
      $label_data[] = (string)$total_co;
      $label_data[] = (string)$total_mis;
      $label_data[] = (string)$total_pw;
      $label_data[] = (string)$total_h;
      $label_data[] = (string)$total_hd;
      $label_data[] = (string)$total_hdal;
      $label_data[] = (string)$total_p;
      $label_data[] = (string)$total_pn;
      $label_data[] = $total_wo + $total_al + $total_pw + $total_h + $total_hdal + $total_p;
      $label_data[] = (string)$total_atte;

      $return =  [
        $item->id ?? '',
        $item->employee_codes ?? '',
        $item->name ?? '',
        $item->getbranch->branch_name ?? '',
        $item->getdivision->division_name ?? '',
        $item->getdesignation->designation_name ?? '',
        $item->userinfo ? date('d M Y', strtotime($item->userinfo->date_of_joining)) : '-',
      ];

      return  $option_array = array_merge($return, $label_data);
    })->toArray();


    $label1 = [
      'User Id',
      'Employee Code',
      'User Name',
      'Branch',
      'Division',
      'Designation',
      'DOJ',
    ];

    $label3 = [
      'Leave Balance',
      'Comp Leave Balance',
      'Week Of (W/o)',
      'Absent (A)',
      'LOP',
      'AL',
      'Comp Off NP',
      'Comp Off A',
      'MIS Punch (MIS)',
      'Present Week of (PW)',
      'Holiday (H)',
      'Half Day (1/2P+1/2LOP)',
      'Half Day (1/2P+1/AL)',
      'Present (P)',
      'Present Not Approve (PN)',
      'Paid Days',
      'TOTAL Days',
    ];

    $label = array_merge($label1, $label2, $label3);

    $export = new ExcelExport($label, $data);

    return Excel::download($export, $filename);
  }









  public function submitAttendances(Request $request)
  {
    // try {
      $ipAddress = $request->server('HTTP_X_FORWARDED_FOR') 
      ? explode(',', $request->server('HTTP_X_FORWARDED_FOR'))[0] 
      : $request->ip();
    $accessToken = '4060dae74e438c';
    $location = Location::get($ipAddress);
    $addressP = getLatLongToAddress($location->latitude, $location->longitude);
    $user = User::find($request['user_id']);
    $branchIds = explode(',', $user->branch_id);

    $punchinDate = Carbon::parse($request['punchin_date'])->format('Y-m-d');
    $isSunday = Carbon::parse($request['punchin_date'])->isSunday();
    $holidayDates = Holiday::whereIn('branch', $branchIds)
      ->pluck('holiday_date')
      ->map(function ($dateString) {
        return explode(',', $dateString);
      })
      ->collapse()
      ->map('trim')
      ->toArray();
   
    $isHoliday = in_array($punchinDate, $holidayDates);

    if ($isSunday || $isHoliday) {
      $expiryDate = Carbon::parse($request['punchin_date'])->addDays(60);

      CompOffLeave::create([
        'user_id' => $request['user_id'],
        'comp_off_date' => $punchinDate,
        'expiry_date' => $expiryDate,
        'is_used' => false,
      ]);
    }

    if (Attendance::updateOrCreate(['user_id' => $request['user_id'], 'punchin_date' => date('Y-m-d', strtotime($request['punchin_date']))], [
      'user_id' => $request['user_id'],
      'active' => 'Y',
      'punchin_date' => date('Y-m-d', strtotime($request['punchin_date'])),
      'punchin_time' => date('G:i', strtotime($request['punchin_date'])),
      'punchin_summary' => !empty($request['punchin_summary']) ? $request['punchin_summary'] : '',
      'punchin_address' => !empty($addressP) ? $addressP : '',
      'working_type' => !empty($request['working_type']) ? $request['working_type'] : '',
      'punchin_from' => 'Web',
      'flag' => 'true',
      'created_at' => getcurentDateTime(),
      'updated_at' => getcurentDateTime(),
    ])) {
      if (!empty($request['tourid'])) {
        TourProgramme::where('id', '=', $request['tourid'])->update([
          'type' => !empty($request['type']) ? $request['type'] : ''
        ]);

        $cityids = Beat::whereHas('beatschedules', function ($query) use ($request) {
          $query->where('user_id', '=', $request['user_id']);
          $query->whereDate('beat_date', '=', date('Y-m-d', strtotime($request['punchin_date'])));
        })
          ->orderBy('city_id', 'asc')
          ->pluck('city_id');
        $cityids = $cityids->unique();

        //start new

        if (!empty($request['city'])) {

          $city_datas = explode(",", $request['city']);
          foreach ($city_datas as $key => $city) {
            $updatecity = TourDetail::where('tourid', '=', $request['tourid'])->whereNull('visited_cityid')->first();
            if (!empty($updatecity)) {
              $updatecity->update([
                'visited_cityid' => $city,
                'visited_date' => date('Y-m-d'),
              ]);
            } else {
              TourDetail::create([
                'tourid' => $request['tourid'],
                'city_id' => null,
                'visited_cityid' => $city,
                'visited_date' => date('Y-m-d'),
                'last_visited' => date('Y-m-d'),
              ]);
            }
          }
        }
      }
      return Redirect::to('reports/attendancereport')->with('message_success', 'PunchIn Successfully');
    }
    return redirect()->back()->with('message_danger', 'Error in Lead Stages')->withInput();
    // } catch (\Exception $e) {
    //   return redirect()->back()->withErrors($e->getMessage())->withInput();
    // }
  }

  public function removePunchout(Request $request)
  {
    try {
      if (Attendance::where('id', '=', $request['id'])->whereDate('punchin_date', '=', date('Y-m-d'))->update([
        'punchout_date' => null,
        'punchout_time' => null,
        'punchout_latitude' => null,
        'punchout_longitude' => null,
        'punchout_address' => '',
        'punchout_image' => '',
        'punchout_summary' => '',
        'worked_time' => '',
        'updated_at' => getcurentDateTime(),
      ])) {
        return response()->json(['status' => 'success', 'message' => 'Punchout Remeoved Successfully'], 200);
      }
      return response()->json(['status' => 'error', 'message' => 'Error in Punchout Remeoved'], 404);
    } catch (\Exception $e) {
      return redirect()->back()->withErrors($e->getMessage())->withInput();
    }
  }
  public function destroy($id)
  {
    ////abort_if(Gate::denies('customer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    try {
      $attendance_details = Attendance::where('id', '=', $id)->first();
      $attendance = Attendance::whereDate('punchin_date', '=', $attendance_details->punchin_date)->where('id', '=', $id)->first();
      TourProgramme::whereDate('date', '=', $attendance_details->punchin_date)->where('userid', '=', $attendance['user_id'])->update(['type' => '']);
      BeatSchedule::whereDate('beat_date', '=', $attendance_details->punchin_date)->where('user_id', '=', $attendance['user_id'])->delete();
      if ($attendance->delete()) {
        return response()->json(['status' => 'success', 'message' => 'Attendance deleted successfully!']);
      }
      return response()->json(['status' => 'error', 'message' => 'Error in Attendance Delete!']);
    } catch (\Exception $e) {
      return redirect()->back()->withErrors($e->getMessage())->withInput();
    }
  }

  public function approveAttendance(Request $request)
  {
    try {
      $ids = explode(',', $request['id']);
      foreach ($ids as $key => $value) {
        Attendance::where('id', '=', $value)->update([
          'attendance_status' => 1,
          'approve_reject_by' => Auth::user()->id,
          'remark_status' => null
        ]);
      }
      return  response()->json(['status' => 'success', 'message' => 'Attendance Approved Successfully']);
    } catch (\Exception $e) {
      return response()->json(['status' => 'error', 'message' => 'Attendance Not Approved Successfully']);
    }
  }


  public function rejectAttendance(Request $request)
  {
    $remark_status  = $request['remark_status'] ?? null;
    try {
      $id_array = explode(',', $request['attendance_id']);
      foreach ($id_array as $key => $value) {
        Attendance::where('id', '=', $value)->update([
          'attendance_status' => 2,
          'approve_reject_by' => Auth::user()->id,
          'remark_status' => $remark_status ?? null,
        ]);
      }
      return Redirect::to('reports/attendancereport')->with('message_success', 'Attendance Rejected Successfully');
    } catch (\Exception $e) {
      return redirect()->back()->withErrors($e->getMessage())->withInput();
    }
  }

  public function punchoutnow(Request $request)
  {
    try {
      $user = $request->user();
      $validator = Validator::make($request->all(), [
        'id' => 'required|exists:attendances,id',
      ]);
      if ($validator->fails()) {
        return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
      }

      $punchout = Attendance::where('id', $request->id)->first();
      $punchout->punchout_date = getcurentDate();
      $punchout->punchout_time = getcurentTime();
      $punchout->punchout_summary = !empty($request['punchout_summary']) ? $request['punchout_summary'] : '';
      $punchout->worked_time = gmdate("H:i:s", strtotime(getcurentDateTime()) - strtotime($punchout->punchin_date . ' ' . $punchout->punchin_time));
      if ($punchout->save()) {
        // $useractivity = array(
        //         'userid' => $user->id, 
        //         'latitude' => $request['punchout_latitude'], 
        //         'longitude' => $request['punchout_longitude'], 
        //         'type' => 'Punchout',
        //         'description' => 'User Logout',
        //     );
        // submitUserActivity($useractivity);
        // $zsmnotify = collect([
        //     'title' => 'Successfully punched out',
        //     'body' =>  $user->name.' has Punched out'
        // ]);
        // sendNotification($user->reportingid,$zsmnotify);
        // $asmnotify = collect([
        //     'title' => 'Successfully punched out',
        //     'body' =>  'You have successfully Punched out'
        // ]);
        // sendNotification($user->id,$asmnotify);
        return response()->json(['status' => 'success', 'message' => 'Punch Out successfully', 'punchout' => $punchout], 200);
      }
      return response()->json(['status' => 'error', 'message' => 'Error in Punch Out'], 404);
    } catch (\Exception $e) {
      return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
  }
}
