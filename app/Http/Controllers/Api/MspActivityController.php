<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MarketingActivity;
use App\Models\MspActivity;
use Carbon\Carbon;
use App\Models\MspActivityCity;
use App\Models\MspActivityCustomer;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Validator;
use Dompdf\Dompdf;
use Dompdf\Options;

class MspActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->marketingActivity = new MarketingActivity();


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

    public function index(Request $request)
    {
        try {
            $user = $request->user();
            if($user->hasRole('superadmin') || $user->hasRole('Admin')) {
                $activities = $this->marketingActivity->select('id', 'type')->get();
            }else{
                $activities = $this->marketingActivity->where('activity_division', $user->division_id)->select('id', 'type')->get();
            }

            return response()->json([
                'status' => true,
                'message' => 'Marketing activities retrieved successfully',
                'data' => $activities
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], $this->internalError);
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'activity_date'  => 'required|date',
                'activity_type'  => 'required|integer',
                'msp_count'      => 'required|integer',
                'customers'      => 'required|array',
                'cities'         => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()
                ], $this->noContent);
            }

            // Format activity date
            $activityDate = Carbon::parse($request->activity_date);
            $month = $activityDate->format('M');
            $year  = $activityDate->format('Y');

            // Determine financial year
            $startYear = ($activityDate->month >= 4) ? $year : $year - 1;
            $endYear = substr($startYear + 1, -2); // Get last two digits of next year

            $formattedYear = "$startYear-$endYear";


            // Create Marketing Activity
            $activity = MspActivity::create([
                'emp_code'      => $request->user()->employee_codes ?? '',
                'fyear'         => $formattedYear,
                'activity_date'         => Carbon::parse($request->activity_date)->toDateString(),
                'month'         => $month,
                'msp_count'     => $request->msp_count,
                'activity_type' => $request->activity_type,
            ]);

            // Insert cities in bulk
            if (!empty($request->cities)) {
                $citiesData = collect($request->cities)->map(function ($city) use ($activity) {
                    return ['msp_activity_id' => $activity->id, 'city_id' => $city];
                })->toArray();

                MspActivityCity::insert($citiesData);
            }

            // Insert customers in bulk
            if (!empty($request->customers)) {
                $customersData = collect($request->customers)->map(function ($customer) use ($activity) {
                    return ['msp_activity_id' => $activity->id, 'customer_id' => $customer];
                })->toArray();

                MspActivityCustomer::insert($customersData);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Marketing activity added successfully',
                'data'    => $activity
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong!',
                'error'   => $e->getMessage()
            ], $this->internalError);
        }
    }

    public function getMspActivityFilter(Request $request)
    {
        try {
            $users          = User::whereIn('id', getUsersReportingToAuth($request->user()->id))->select('id', 'name', 'employee_codes', 'branch_id')->get();
            $financial_year = $this->getFinancialYearList();
            $branchIds = $users->pluck('branch_id')->unique()->filter();
            // Get only those branches
            $branches = Branch::whereIn('id', $branchIds)
                ->select('id', 'branch_name')
                ->get();
            return response()->json([
                'status'  => true,
                'message' => 'Marketing activity added successfully',
                'users'    => $users,
                'financial_year' => $financial_year,
                'branches'      => $branches
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], $this->internalError);
        }
    }

    public function getMspActivityCount(Request $request)
    {
        try {
            // Get financial year
            $activities = $this->marketingActivity->select('id', 'type')->get();

            $financial_year = $request->financial_year ?? $this->getFinancialYear();

            // Optional filters
            $employee_codes = $request->emp_code ?? null;
            $branch_id      = $request->branch_id ?? null;


            // Extract start and end year from financial year (e.g., "2024-25")
            $startYear = substr($financial_year, 0, 4);
            $endYear = substr($financial_year, -2); // Last two digits of next year

            // Mapping numeric month to three-letter format
            $monthMapping = [
                "01" => "Jan",
                "02" => "Feb",
                "03" => "Mar",
                "04" => "Apr",
                "05" => "May",
                "06" => "Jun",
                "07" => "Jul",
                "08" => "Aug",
                "09" => "Sep",
                "10" => "Oct",
                "11" => "Nov",
                "12" => "Dec"
            ];

            // Generate 12-month period from April (startYear) to March (endYear)
            $months = [];
            for ($i = 4; $i <= 12; $i++) { // April to December of startYear
                $months[sprintf('%02d/%s', $i, substr($startYear, -2))] = $monthMapping[sprintf('%02d', $i)];
            }
            for ($i = 1; $i <= 3; $i++) { // January to March of endYear
                $months[sprintf('%02d/%s', $i, $endYear)] = $monthMapping[sprintf('%02d', $i)];
            }

            // Get all activity types
            $activities = $this->marketingActivity->select('id', 'type')->get();

            // Fetch all relevant activity data first
            $query = MspActivity::with('user')->selectRaw('
                        month, 
                        activity_type, 
                        COUNT(*) as total_performed, 
                        SUM(msp_count) as total_participants
                    ')
                ->where('fyear', $financial_year)
                ->groupBy('month', 'activity_type');

            // Apply optional filters

            if (isset($branch_id) && isset($employee_codes)) {
                $query->whereHas('user', function ($query1) use ($request) {
                    $query1->where('branch_id', $request->branch_id);
                });
                $query->where('emp_code', $employee_codes);
            } else if (isset($branch_id)) {
                $query->whereHas('user', function ($query1) use ($request) {
                    $query1->where('branch_id', $request->branch_id);
                });
            } else if (isset($employee_codes)) {
                $query->where('emp_code', $employee_codes);
            } else {
                $query->where('emp_code', $request->user()->employee_codes);
            }


            // Fetch the data

            $activityData = $query->get()->groupBy('month');

            // Prepare the result array
            $activityReport = [];

            foreach ($months as $formattedMonth => $dbMonth) {
                $monthData = $activityData[$dbMonth] ?? collect();
                // $monthArray = [
                //     'month' => $formattedMonth,
                //     'activities' => []
                // ];

                foreach ($activities as $activity) {
                    // Find activity data or default to 0
                    $data = $monthData->where('activity_type', $activity->id)->first();

                    $activityReport[$formattedMonth][] = [
                        'activity_name' => $activity->type,
                        'total_performed' => isset($data->total_performed) ? (int)$data->total_performed : 0, // How many times performed
                        'total_participants' => isset($data->total_participants) ? (int)$data->total_participants : 0, // Total participants (msp_count)
                    ];
                }

                // $activityReport[] = $monthArray;
            }

            $pdfDirectory = public_path('pdf/orders/');
            $files = File::files($pdfDirectory);
            $now = time();

            foreach ($files as $file) {
                if ($now - $file->getMTime() > (3 * 3600)) {
                    File::delete($file->getRealPath());
                }
            }

            $data_pdf = [
                'activities' => $activities,
                'data' => $activityReport
            ];

            $html = view('msp_activity.pdf', $data_pdf)->render();
            File::makeDirectory($pdfDirectory, $mode = 0755, true, true);
            $pdfFilePath = $pdfDirectory . 'MspActivity_' . time() . '.pdf';

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            file_put_contents($pdfFilePath, $dompdf->output());
            $data_main['pdf_url'] = $url = url(str_replace('/var/www/html/', '', $pdfFilePath));

            return response()->json([
                'status'  => true,
                'activities' => $activities,
                'data' => $activityReport,
                'pdf_url' => $data_main
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    private function getFinancialYear()
    {
        $date = now(); // Get current date using Carbon

        $year = $date->year;
        $startYear = ($date->month >= 4) ? $year : $year - 1;
        $endYear = substr($startYear + 1, -2);

        return $startYear . '-' . $endYear;
    }

    private function getFinancialYearList()
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Determine the starting financial year
        $startYear = ($currentMonth >= 4) ? $currentYear : $currentYear - 1;

        // Generate last three, current, and next three financial years
        $financialYears = [];
        for ($i = -3; $i <= 3; $i++) {
            $year = $startYear + $i;
            $nextYear = substr($year + 1, -2); // Get last two digits of next year

            $financialYears[] = [            // Unique ID (can be the start year)
                'year' => "$year-$nextYear"  // Financial year in "YYYY-YY" format
            ];
        }
        // Output the financial years
        return $financialYears;
    }
}
