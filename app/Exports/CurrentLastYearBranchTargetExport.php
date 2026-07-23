<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Division;
use App\Models\BranchWiseTarget;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesTargetUsers;
use App\Models\User;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CurrentLastYearBranchTargetExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping,WithStyles
{

    private $rowIndex = 3;
    public function __construct($request)
    {    
        $this->user_id = $request->input('user_id');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
        $this->target = $request->input('target');  
        $this->branch_id = $request->input('branch_id');  
        $this->user = $request->input('user');  
        $this->division = $request->input('division');  
        $this->type = $request->input('type');  
      
    }

	public function collection()
	{
		$f_year_array = explode('-', $this->financial_year);
        $division = $this->division;
        $branch = $this->branch_id;
        $user = $this->user;
        $type = $this->type;
        $month = $this->month;


        // $data = BranchWiseTarget::join('users', 'branchwise_targets.user_id', '=', 'users.id')->select([
        //  DB::raw('SUM(achievement) as achievements'),
        //  DB::raw('GROUP_CONCAT(month) as months'),  
        //  DB::raw('GROUP_CONCAT(user_id) as user_ids'),
        //  DB::raw('GROUP_CONCAT(year) as years'),
        //  'users.branch_id',
        //  'users.division_id',
        //  'users.designation_id',
        // ]);


        $data = BranchWiseTarget::with('user')->select([
         DB::raw('GROUP_CONCAT(month) as months'),  
         DB::raw('GROUP_CONCAT(user_id) as user_ids'),
         DB::raw('GROUP_CONCAT(year) as years'),
         DB::raw('branch_name'),
         DB::raw('division_name'),
         'branch_id',
         'div_id',
         'user_id',
         'target',
         'achievement',
        ]);

        if($this->month == '' && empty($this->month)){

            if($this->division != '' && !empty($this->division)){
                $data->where(function ($query) use($f_year_array, $division) {
                    $query->where('year', '=', $f_year_array[0])
                    ->where('month', '>=', 'Apr')
                    ->where('division_name',$division);
                })->orWhere(function ($query) use($f_year_array, $division) {
                    $query->where('year', '=', $f_year_array[1])
                    ->where('month', '<=', 'Mar')
                    ->where('division_name',$division);
                });
            }elseif($this->branch_id != '' && !empty($this->branch_id)){

                $data->where(function ($query) use($f_year_array, $branch) {
                    $query->where('year', '=', $f_year_array[0])
                    ->where('month', '>=', 'Apr')
                    ->where('branch_name',$branch);
                })->orWhere(function ($query) use($f_year_array, $branch) {
                    $query->where('year', '=', $f_year_array[1])
                    ->where('month', '<=', 'Mar')
                    ->where('branch_name',$branch);
                });
            }elseif($this->user != '' && !empty($this->user)){

                $data->where(function ($query) use($f_year_array, $user) {
                    $query->where('year', '=', $f_year_array[0])
                    ->where('month', '>=', 'Apr')
                    ->where('user_id',$user);
                })->orWhere(function ($query) use($f_year_array, $user) {
                    $query->where('year', '=', $f_year_array[1])
                    ->where('month', '<=', 'Mar')
                    ->where('user_id',$user);
                });
            }elseif($this->type != '' && !empty($this->type)){

                $data->where(function ($query) use($f_year_array, $type) {
                    $query->where('year', '=', $f_year_array[0])
                    ->where('month', '>=', 'Apr')
                    ->where('type',$type);
                })->orWhere(function ($query) use($f_year_array, $type) {
                    $query->where('year', '=', $f_year_array[1])
                    ->where('month', '<=', 'Mar')
                    ->where('type',$type);
                });
            }else{
                $data->where(function ($query) use($f_year_array) {
                    $query->where('year', '=', $f_year_array[0])
                          ->where('month', '>=', 'Apr');
                })->orWhere(function ($query) use($f_year_array) {
                    $query->where('year', '=', $f_year_array[1])
                          ->where('month', '<=', 'Mar');
                });
            }
            
        }else {
            if($this->division != '' && !empty($this->division)){
                $data->where(function ($query) use($f_year_array,$division) {
                     $query->where('year', '=', $f_year_array[0])
                           ->where('month', '>=', $this->month)
                           ->where('division_name', $division);
                 })->orWhere(function ($query) use($f_year_array,$division) {
                     $query->where('year', '=', $f_year_array[1])
                           ->where('month', '<=', $this->month)
                           ->where('division_name', $division);
                 }); 
            }elseif($this->branch_id != '' && !empty($this->branch_id)){
                $data->where(function ($query) use($f_year_array,$branch) {
                     $query->where('year', '=', $f_year_array[0])
                           ->where('month', '>=', $this->month)
                           ->where('branch_name', $branch);
                 })->orWhere(function ($query) use($f_year_array,$branch) {
                     $query->where('year', '=', $f_year_array[1])
                           ->where('month', '<=', $this->month)
                           ->where('branch_name', $branch);
                 }); 
            }elseif($this->user != '' && !empty($this->user)){
                $data->where(function ($query) use($f_year_array,$user) {
                     $query->where('year', '=', $f_year_array[0])
                           ->where('month', '>=', $this->month)
                           ->where('user_id', $user);
                 })->orWhere(function ($query) use($f_year_array,$user) {
                     $query->where('year', '=', $f_year_array[1])
                           ->where('month', '<=', $this->month)
                           ->where('user_id', $user);
                 });
            }elseif($this->type != '' && !empty($this->type)){
                $data->where(function ($query) use($f_year_array,$type) {
                     $query->where('year', '=', $f_year_array[0])
                           ->where('month', '>=', $this->month)
                           ->where('type', $type);
                 })->orWhere(function ($query) use($f_year_array,$type) {
                     $query->where('year', '=', $f_year_array[1])
                           ->where('month', '<=', $this->month)
                           ->where('type', $type);
                 });
            }else{
                if($this->month != '' && !empty($this->month) && $this->month != null ){
                    if($this->month == 'Jan' || $this->month == 'Feb' || $this->month == 'Mar') {
                         $data->where(function ($query) use($f_year_array,$month) {
                            $query->where('year', '=', $f_year_array[1])
                            ->where('month', '=', $month);
                        });
                     }else{
                         $data->where(function ($query) use($f_year_array,$month) {
                            $query->where('year', '=', $f_year_array[0])
                            ->where('month', '=', $month);
                        });
                     }

                }else{
                    $data->where(function ($query) use($f_year_array) {
                         $query->where('year', '=', $f_year_array[0])
                               ->where('month', '>=', $this->month);
                     })->orWhere(function ($query) use($f_year_array) {
                         $query->where('year', '=', $f_year_array[1])
                               ->where('month', '<=', $this->month);
                     });
                }
            }
        }
        
        $data = $data->groupBy('branch_id','div_id','user_id')->orderBy('month')->get();

		return $data;
	}


   //  public function headings(): array
   //  {
   //   $f_year_array = explode('-', $this->financial_year);

   //   $startYear = $f_year_array[0];

   //   $endYear = $f_year_array[1];

   //   $headings = ['EMP Code', 'Emp Name','Branch','Division'];

   //   $quarterNames = ['Q1', 'Q2', 'Q3', 'Q4'];

   //   $quarterIndex = 0;

   //   for ($year = $startYear; $year <= $endYear; $year++) {
   //       $startMonth = ($year == $startYear) ? 4 : 1;
   //       $endMonth = ($year == $endYear) ? 3 : 12;


   //       for ($month = $startMonth; $month <= $endMonth; $month++) {

   //             $formattedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
   //             $headings[] = "$formattedMonth/$year";
   //             $headings[] = "";
   //             $headings[] = "";

   //             if($month == '06' || $month == '09' || $month == '12' || $month == '03' ) {
   //                 $headings[] = $quarterNames[$quarterIndex];
   //                 $quarterIndex++;
   //                 $headings[] = "";
   //                 $headings[] = "";
   //             }

   //         }
   //     }

   //     $headings[] = 'Total';

   //     $sub_headings = ['','','','','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%'];

   //     $final_heading = [$headings, $sub_headings];

   //     return $final_heading;
   // }

   // new heading
   public function headings(): array
   {
       $f_year_array = explode('-', $this->financial_year);
       $headings = ['EMP Code', 'Emp Name', 'Branch', 'Division'];
       $quarterNames = ['Q1', 'Q2', 'Q3', 'Q4'];
       $sub_headings = ['','','',''];
       $quarterIndex = 0;

        // If no month is selected, include all months for the financial year
        if (empty($this->month)) {
            $startYear = $f_year_array[0];
            $endYear = $f_year_array[1];
            $allMonths = ['EMP Code', 'Emp Name', 'Branch','Division','Apr', '','','May', '','', 'Jun', '','','Q1',  '','','Jul', '','', 'Aug', '','', 'Sep', '','','Q2', '','', 'Oct', '','', 'Nov', '','', 'Dec', '','','Q3', '','', 'Jan', '','', 'Feb', '','', 'Mar', '','','Q4', '','','Total'];
            // $this->month = $allMonths;
            $quarterIndex %= count($quarterNames);
            $sub_headings = ['','','','','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%'];
            $final_heading = [$allMonths, $sub_headings];
        }


        if (!empty($this->month) && count($this->month)>0) {
           foreach ($this->month as $selectedMonth) {
               array_push($sub_headings, 'LY', 'CY', 'Growth%');
               $selectedMonth = trim($selectedMonth);
               if (in_array($selectedMonth, ['Jan', 'Feb', 'Mar'])) {
                   $startYear = $f_year_array[1];
                   $endYear = $f_year_array[1];
               } else {
                   // $startYear = $f_year_array[0];
                   // $endYear = $f_year_array[0];

                $startYear = $f_year_array[0];
                $endYear = $f_year_array[0];
               }

               for ($year = $startYear; $year <= $endYear; $year++) {
                   $startMonth = date('m', strtotime("$selectedMonth 1, $year"));
                   $endMonth = date('m', strtotime("$selectedMonth 1, $year"));

                   for ($month = $startMonth; $month <= $endMonth; $month++) {        
                       if (!in_array(date('M', strtotime("$year-$month-01")), $this->month)) {
                           continue;
                       }

                       $formattedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
                       $headings[] = "$formattedMonth/$year";
                       $headings[] = "";
                       $headings[] = "";
                   }
                }
                $final_heading = [$headings, $sub_headings];
           }
        }

        // dd($final_heading);

        return $final_heading;
   }


   public function map($data): array
   {
    $branch = Branch::where('id',$data['branch_id'])->first();
    $division = Division::where('id',$data['division_id'])->first();
    $userIds = explode(',', $data['user_ids']);
    $users = User::with('getdivision','getbranch')->whereIn('id', $userIds)->first();
    $response = array();
    $response[0] = $users['employee_codes'] ?? '';
    $response[1] = $users['name'] ?? '';
    $response[2] = $data['branch_name'] ?? '';
    $response[3] = $data['division_name'] ?? '';
    $f_year_array = explode('-', $this->financial_year);
    $data['months'] = explode(',', $data['months']);
    $status = true;

    if($data['months'] == null) {
        $data['months'] =  ['Apr','May','Jun','Jul', 'Aug', 'Sep','Oct','Nov','Dec','Jan','Feb','Mar'];
        $status = false;
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'Apr' && $f_year_array[0] && in_array('Apr',$data['months'])) {

            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('branchwise_targets')
                ->where('month', 'Apr')
                ->where('year', $currentYear)
                ->where('user_id', $data['user_id'])
                ->where('branch_id', $data['branch_id'])
                ->where('div_id', $data['div_id'])
                ->select(DB::raw('SUM(achievement) as achievements_current_year'))
                ->first();


            $sales_data_last_year = DB::table('branchwise_targets')
                ->where('month', 'Apr')
                ->where('year', $previousYear)
                ->where('user_id', $data['user_id'])
                ->where('branch_id', $data['branch_id'])
                ->where('div_id', $data['div_id'])
                ->select(DB::raw('SUM(achievement) as achievements_last_year'))
                ->first();


            $response[4] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[5] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                if ($lastYearAchievements != null) {
                    // $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;

                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                    $growthPercent = ROUND($growthPercent, 2);
                }else{
                    if ($lastYearAchievements == null || $lastYearAchievements == 0) {
                        if (($currentYearAchievements == null || $currentYearAchievements == 0) && ($lastYearAchievements==null || $lastYearAchievements==0)) {
                            $growthPercent = 0;
                        }elseif(($lastYearAchievements==null || $lastYearAchievements==0 ) && isset($currentYearAchievements) && ($currentYearAchievements != null && $currentYearAchievements > 0)) {
                            $growthPercent = 100;
                        }
                    }
                }
            }

            $response[6] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[4])) {
               $response[4] = '0';
            }
            if(!isset($response[5])) {
               $response[5] = '0';
            }
            if(!isset($response[6])) {
               $response[6] = '0';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'May' && $f_year_array[0] && in_array('May',$data['months'])) {

            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('branchwise_targets')
            ->where('month', 'May')
            ->where('year', $currentYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('branchwise_targets')
            ->where('month', 'May')
            ->where('year', $previousYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first();    

            $response[7] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[8] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                if ($lastYearAchievements != 0) {
                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                    $growthPercent = number_format($growthPercent, 2);
                }else{
                    if ($lastYearAchievements == 0 ) {
                        if ($currentYearAchievements == null && $lastYearAchievements==null) {
                            $growthPercent = 0;
                        }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                            $growthPercent = 100;
                        }
                    }
                }
            }

            $response[9] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[7])) {
              $response[7] = '0';
          }
          if(!isset($response[8])) {
              $response[8] = '0';
          }
          if(!isset($response[9])) {
              $response[9] = '0';
          }

      }
  }

      foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'Jun' && $f_year_array[0] && in_array('Jun',$data['months'])) {
            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = ''; 
            $sales_data_current_year = DB::table('branchwise_targets')
            ->where('month', 'Jun')
            ->where('year', $currentYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('branchwise_targets')
            ->where('month', 'Jun')
            ->where('year', $previousYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first();    

            $response[10] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[11] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                if ($lastYearAchievements != 0) {
                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                    $growthPercent = number_format($growthPercent, 2);
                }else{
                    if ($lastYearAchievements == 0 ) {
                        if ($currentYearAchievements == null && $lastYearAchievements==null) {
                            $growthPercent = 0;
                        }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                            $growthPercent = 100;
                        }
                    }
                }
            } 
            $response[12] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[10])) {
              $response[10] = '0';
          }
          if(!isset($response[11])) {
              $response[11] = '0';
          }
          if(!isset($response[12])) {
              $response[12] = '0';
          }

      }
    }

//     EFG
// HIJ
// KLM
    if($status == false) {
    $response[13] = '=E'.$this->rowIndex.' + H'.$this->rowIndex.' + G'.$this->rowIndex;
    $response[14] = '=F'.$this->rowIndex.' + I'.$this->rowIndex.' + L'.$this->rowIndex;
    // $response[13] = '=((L'.$this->rowIndex.' - M'.$this->rowIndex.') / K'.$this->rowIndex.') * 100';

    $response[15] = '=IF(AND(O'.$this->rowIndex.'=0, N'.$this->rowIndex.'=0), "0", IF(O'.$this->rowIndex.'=0, 100, ((O'.$this->rowIndex.' - N'.$this->rowIndex.') / O'.$this->rowIndex.') * 100))';
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'Jul' && $f_year_array[0] && in_array('Jul',$data['months'])) {
            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = ''; 
            $sales_data_current_year = DB::table('branchwise_targets')
            ->where('month', 'Jul')
            ->where('year', $currentYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('branchwise_targets')
            ->where('month', 'Jul')
            ->where('year', $previousYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first();    

            $response[16] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[17] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';
            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                if ($lastYearAchievements != 0) {
                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                    $growthPercent = number_format($growthPercent, 2);
                }else{
                    if ($lastYearAchievements == 0 ) {
                        if ($currentYearAchievements == null && $lastYearAchievements==null) {
                            $growthPercent = 0;
                        }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                            $growthPercent = 100;
                        }
                    }
                }
            }   
            $response[18] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[16])) {
              $response[16] = '0';
          }
          if(!isset($response[17])) {
              $response[17] = '0';
          }
          if(!isset($response[18])) {
              $response[18] = '0';
          }

      }
  }

      foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'Aug' && $f_year_array[0] && in_array('Aug',$data['months'])) {
            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = ''; 
            $sales_data_current_year = DB::table('branchwise_targets')
            ->where('month', 'Aug')
            ->where('year', $currentYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('branchwise_targets')
            ->where('month', 'Aug')
            ->where('year', $previousYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first();    

            $response[19] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[20] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';
            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                if ($lastYearAchievements != 0) {
                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                    $growthPercent = number_format($growthPercent, 2);
                }else{
                    if ($lastYearAchievements == 0 ) {
                        if ($currentYearAchievements == null && $lastYearAchievements==null) {
                            $growthPercent = 0;
                        }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                            $growthPercent = 100;
                        }
                    }
                }
            }   
            $response[21] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[19])) {
              $response[19] = '0';
          }
          if(!isset($response[20])) {
              $response[20] = '0';
          }
          if(!isset($response[21])) {
              $response[21] = '0';
          }

      }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'Sep' && $f_year_array[0] && in_array('Sep',$data['months'])) {
            $currentYear = $f_year_array[0];
                       $previousYear = $f_year_array[0] - 1;
                       $growthPercent = ''; 
                       $sales_data_current_year = DB::table('branchwise_targets')
                       ->where('month', 'Sep')
                       ->where('year', $currentYear)
                       ->where('user_id', $data['user_id'])
                       ->where('branch_id', $data['branch_id'])
                       ->where('div_id', $data['div_id'])
                       ->select(DB::raw('SUM(achievement) as achievements_current_year'))
                       ->first();

                       $sales_data_last_year = DB::table('branchwise_targets')
                       ->where('month', 'Sep')
                       ->where('year', $previousYear)
                       ->where('user_id', $data['user_id'])
                       ->where('branch_id', $data['branch_id'])
                       ->where('div_id', $data['div_id'])
                       ->select(DB::raw('SUM(achievement) as achievements_last_year'))
                       ->first();    

                       $response[22] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
                       $response[23] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';
                       if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                           $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                           $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                           if ($lastYearAchievements != 0) {
                               $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                               $growthPercent = number_format($growthPercent, 2);
                           }else{
                               if ($lastYearAchievements == 0 ) {
                                   if ($currentYearAchievements == null && $lastYearAchievements==null) {
                                       $growthPercent = 0;
                                   }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                                       $growthPercent = 100;
                                   }
                               }
                           }
                       }   
                       $response[24] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[21])) {
              $response[21] = '0';
          }
          if(!isset($response[22])) {
              $response[22] = '0';
          }
          if(!isset($response[23])) {
              $response[23] = '0';
          }

      }

    if($status == false) {

    $response[24] = '=Q'.$this->rowIndex.' + T'.$this->rowIndex.' + W'.$this->rowIndex;
    $response[25] = '=R'.$this->rowIndex.' + U'.$this->rowIndex.' + X'.$this->rowIndex;
    // $response[25] = '=(Q'.$this->rowIndex.' + T'.$this->rowIndex.' + W'.$this->rowIndex.') / 3';
    $response[26] = '=IF(AND(AA'.$this->rowIndex.'=0, Z'.$this->rowIndex.'=0), "0", IF(Z'.$this->rowIndex.'=0, 100, ((AA'.$this->rowIndex.' - Z'.$this->rowIndex.') / AA'.$this->rowIndex.') * 100))';
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
                
                if($month == 'Oct' && $f_year_array[0] && in_array('Oct',$data['months'])) {
                    $currentYear = $f_year_array[0];
                    $previousYear = $f_year_array[0] - 1;
                    $growthPercent = ''; 
                    $sales_data_current_year = DB::table('branchwise_targets')
                    ->where('month', 'Oct')
                    ->where('year', $currentYear)
                   ->where('user_id', $data['user_id'])
                   ->where('branch_id', $data['branch_id'])
                   ->where('div_id', $data['div_id'])
                    ->select(DB::raw('SUM(achievement) as achievements_current_year'))
                    ->first();

                    $sales_data_last_year = DB::table('branchwise_targets')
                    ->where('month', 'Oct')
                    ->where('year', $previousYear)
                    ->where('user_id', $data['user_id'])
                    ->where('branch_id', $data['branch_id'])
                    ->where('div_id', $data['div_id'])
                    ->select(DB::raw('SUM(achievement) as achievements_last_year'))
                    ->first();    

                    $response[27] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
                    $response[28] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';
                    if (isset($sales_data_last_year->achievements_last_year) && isset($sales_data_current_year->achievements_current_year)) {
                        $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                        $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                        if ($lastYearAchievements != 0) {
                            $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                            $growthPercent = number_format($growthPercent, 2);
                        } else {
                            $growthPercent = 0;
                        }
                    }   
                    $response[29] = (string)$growthPercent;
                }elseif($status == false){
                    if(!isset($response[27])) {
                      $response[27] = '0';
                    }
                    if(!isset($response[28])) {
                      $response[28] = '0';
                    }
                    if(!isset($response[29])) {
                      $response[29] = '0';
                    }

                }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Nov' && $f_year_array[0] && in_array('Nov',$data['months'])) {
            $currentYear = $f_year_array[0];
                      $previousYear = $f_year_array[0] - 1;
                      $growthPercent = '';

                      $sales_data_current_year = DB::table('branchwise_targets')
                          ->where('month', 'Nov')
                          ->where('year', $currentYear)
                          ->where('user_id', $data['user_id'])
                          ->where('branch_id', $data['branch_id'])
                          ->where('div_id', $data['div_id'])
                          ->select(DB::raw('SUM(achievement) as achievements_current_year'))
                          ->first();

                      $sales_data_last_year = DB::table('branchwise_targets')
                          ->where('month', 'Nov')
                          ->where('year', $previousYear)
                          ->where('user_id', $data['user_id'])
                          ->where('branch_id', $data['branch_id'])
                          ->where('div_id', $data['div_id'])
                          ->select(DB::raw('SUM(achievement) as achievements_last_year'))
                          ->first(); 


                      $response[30] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
                      $response[31] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

                      if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                          $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                          $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                          if ($lastYearAchievements != 0) {
                              $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                              $growthPercent = number_format($growthPercent, 2);
                          }else{
                              if ($lastYearAchievements == 0 ) {
                                  if ($currentYearAchievements == null && $lastYearAchievements==null) {
                                      $growthPercent = 0;
                                  }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                                      $growthPercent = 100;
                                  }
                              }
                          }
                      }

                      $response[32] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[30])) {
               $response[30] = '0';
            }
            if(!isset($response[31])) {
               $response[31] = '0';
            }
            if(!isset($response[32])) {
               $response[32] = '0';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Dec' && $f_year_array[0] && in_array('Dec',$data['months'])) {
            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('branchwise_targets')
            ->where('month', 'Dec')
            ->where('year', $currentYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('branchwise_targets')
            ->where('month', 'Dec')
            ->where('year', $previousYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first(); 


            $response[33] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[34] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
              $lastYearAchievements = $sales_data_last_year->achievements_last_year;
              $currentYearAchievements = $sales_data_current_year->achievements_current_year;

              if ($lastYearAchievements != 0) {
                  $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                  $growthPercent = number_format($growthPercent, 2);
              }else{
                  if ($lastYearAchievements == 0 ) {
                      if ($currentYearAchievements == null && $lastYearAchievements==null) {
                          $growthPercent = 0;
                      }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                          $growthPercent = 100;
                      }
                  }
              }
          }

          $response[35] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[33])) {
              $response[33] = '0';
            }
            if(!isset($response[34])) {
              $response[34] = '0';
            }
            if(!isset($response[35])) {
              $response[35] = '0';
            }
        }
    }

    if($status == false) {

    $response[36] = '=AC'.$this->rowIndex.' + AF'.$this->rowIndex.' + AI'.$this->rowIndex;
    $response[37] = '=AF'.$this->rowIndex.' + AG'.$this->rowIndex.' + AJ'.$this->rowIndex;
    // $response[37] = '=(AC'.$this->rowIndex.' + AF'.$this->rowIndex.' + AI'.$this->rowIndex.') / 3';
    $response[38] = '=IF(AND(AL'.$this->rowIndex.'=0, AM'.$this->rowIndex.'=0), "0", IF(AM'.$this->rowIndex.'=0, 100, ((AM'.$this->rowIndex.' - AL'.$this->rowIndex.') / AM'.$this->rowIndex.') * 100))';
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Jan' && $f_year_array[1] && in_array('Jan',$data['months'])) {
            $currentYear = $f_year_array[1];
            $previousYear = $f_year_array[1] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('branchwise_targets')
            ->where('month', 'Jan')
            ->where('year', $currentYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('branchwise_targets')
            ->where('month', 'Jan')
            ->where('year', $previousYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first(); 


            $response[39] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[40] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
              $lastYearAchievements = $sales_data_last_year->achievements_last_year;
              $currentYearAchievements = $sales_data_current_year->achievements_current_year;

              if ($lastYearAchievements != 0) {
                  $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                  $growthPercent = number_format($growthPercent, 2);
              }else{
                  if ($lastYearAchievements == 0 ) {
                      if ($currentYearAchievements == null && $lastYearAchievements==null) {
                          $growthPercent = 0;
                      }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                          $growthPercent = 100;
                      }
                  }
              }
          }

          $response[41] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[39])) {
              $response[39] = '0';
            }
            if(!isset($response[40])) {
              $response[40] = '0';
            }
            if(!isset($response[41])) {
              $response[41] = '0';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Feb' && $f_year_array[1] && in_array('Feb',$data['months'])) {
            $currentYear = $f_year_array[1];
            $previousYear = $f_year_array[1] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('branchwise_targets')
            ->where('month', 'Feb')
            ->where('year', $currentYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('branchwise_targets')
            ->where('month', 'Feb')
            ->where('year', $previousYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first(); 


            $response[42] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[43] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
              $lastYearAchievements = $sales_data_last_year->achievements_last_year;
              $currentYearAchievements = $sales_data_current_year->achievements_current_year;

              if ($lastYearAchievements != 0) {
                  $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                  $growthPercent = number_format($growthPercent, 2);
              }else{
                  if ($lastYearAchievements == 0 ) {
                      if ($currentYearAchievements == null && $lastYearAchievements==null) {
                          $growthPercent = 0;
                      }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                          $growthPercent = 100;
                      }
                  }
              }
          }

          $response[44] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[42])) {
              $response[42] = '0';
            }
            if(!isset($response[43])) {
              $response[43] = '0';
            }
            if(!isset($response[44])) {
              $response[44] = '0';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Mar' && $f_year_array[1] && in_array('Mar',$data['months'])) {
            $currentYear = $f_year_array[1];
            $previousYear = $f_year_array[1] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('branchwise_targets')
            ->where('month', 'Mar')
            ->where('year', $currentYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('branchwise_targets')
            ->where('month', 'Mar')
            ->where('year', $previousYear)
            ->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first(); 


            $response[45] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[46] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
              $lastYearAchievements = $sales_data_last_year->achievements_last_year;
              $currentYearAchievements = $sales_data_current_year->achievements_current_year;

              if ($lastYearAchievements != 0) {
                  $growthPercent = (($currentYearAchievements - $lastYearAchievements) / abs($lastYearAchievements)) * 100;
                  $growthPercent = number_format($growthPercent, 2);
              }else{
                  if ($lastYearAchievements == 0 ) {
                      if ($currentYearAchievements == null && $lastYearAchievements==null) {
                          $growthPercent = 0;
                      }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                          $growthPercent = 100;
                      }
                  }
              }
          }

          $response[47] = (string)$growthPercent;
        }elseif($status == false){
            if(!isset($response[45])) {
              $response[45] = '0';
            }
            if(!isset($response[46])) {
              $response[46] = '0';
            }
            if(!isset($response[47])) {
              $response[47] = '0';
            }

        }
    }

    if($status == false) {
    $response[48] = '=AO'.$this->rowIndex.' + AR'.$this->rowIndex.' + AU'.$this->rowIndex;
    $response[49] = '=AP'.$this->rowIndex.' + AS'.$this->rowIndex.' + AT'.$this->rowIndex;
    // $response[49] = '=(AQ'.$this->rowIndex.' + AR'.$this->rowIndex.' + AU'.$this->rowIndex.') / 3';
    $response[50] = '=IF(AND(AY'.$this->rowIndex.'=0, AW'.$this->rowIndex.'=0), "0", IF(AW'.$this->rowIndex.'=0, 100, ((AY'.$this->rowIndex.' - AX'.$this->rowIndex.') / AY'.$this->rowIndex.') * 100))';

    $response[51] = '=N'.$this->rowIndex.' + Z'.$this->rowIndex.' + AL'.$this->rowIndex.' + AX'.$this->rowIndex;
    $response[52] = '='.$this->rowIndex.' + O'.$this->rowIndex.' + AA'.$this->rowIndex.' + AM'.$this->rowIndex;
    $response[53] = '=(P'.$this->rowIndex.' + AB'.$this->rowIndex.' + AN'.$this->rowIndex.' + AZ'.$this->rowIndex.') / 4';

    $response[54] = '=IF(AND(AZ'.$this->rowIndex.'=0, AY'.$this->rowIndex.'=0), "0", IF(AY'.$this->rowIndex.'=0, 100, ((AZ'.$this->rowIndex.' - AY'.$this->rowIndex.') / AW'.$this->rowIndex.') * 100))';
    }

    $this->rowIndex++;

    return $response;

    
    }
}


    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('D1:D2');
        $sheet->mergeCells('E1:G1');
        $sheet->mergeCells('H1:J1');
        $sheet->mergeCells('K1:M1');
        $sheet->mergeCells('N1:P1');
        $sheet->mergeCells('Q1:S1');
        $sheet->mergeCells('T1:V1');
        $sheet->mergeCells('W1:Y1');
        $sheet->mergeCells('Z1:AB1');
        $sheet->mergeCells('AC1:AE1');
        $sheet->mergeCells('AF1:AH1');
        $sheet->mergeCells('AI1:AK1');
        $sheet->mergeCells('AL1:AN1');
        $sheet->mergeCells('AO1:AQ1');
        $sheet->mergeCells('AR1:AT1');
        $sheet->mergeCells('AU1:AW1');
        $sheet->mergeCells('AX1:AZ1');
        $sheet->mergeCells('BA1:BC1');

        $sheet->getStyle('A1:ZZ1')->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'background' => [
                'color'=> '#000000'
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:ZZ2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);
    }
}
