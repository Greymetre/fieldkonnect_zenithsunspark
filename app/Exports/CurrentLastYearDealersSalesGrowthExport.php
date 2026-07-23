<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Division;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesTargetUsers;
use App\Models\SalesTargetCustomers;
use App\Models\User;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CurrentLastYearDealersSalesGrowthExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping,WithStyles
{

    private $rowIndex = 3;
    public function __construct($request)
    {    
        $this->user_id = $request->input('user_id');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
        $this->target = $request->input('target');  
      
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);

        // $data = SalesTargetUsers::join('users', 'salestargetusers.user_id', '=', 'users.id')->select([
        //  DB::raw('SUM(achievement) as achievements'),
        //  DB::raw('GROUP_CONCAT(month) as months'),  
        //  DB::raw('GROUP_CONCAT(user_id) as user_ids'),
        //  DB::raw('GROUP_CONCAT(year) as years'),
        //  'users.branch_id',
        //  'users.division_id',
        //  'users.designation_id',
        // ]);

        $data = SalesTargetCustomers::join('customers', 'salestargetcustomers.customer_id', '=', 'customers.id')->join('users', 'users.id', '=', 'customers.executive_id' )->

        select([
         // DB::raw('SUM(target) as targets'),
         // DB::raw('SUM(achievement) as achievements'),
         DB::raw('GROUP_CONCAT(month) as months'),  
         DB::raw('GROUP_CONCAT(customer_id) as user_ids'),
         DB::raw('GROUP_CONCAT(year) as years'),
         // DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
         'users.branch_id',
         'users.division_id',
         'users.designation_id',
        ]);


        if($this->month == '' && empty($this->month)){
            $data->where(function ($query) use($f_year_array) {
                $query->where('year', '=', $f_year_array[0])
                      ->where('month', '>=', 'Apr');
            })->orWhere(function ($query) use($f_year_array) {
                $query->where('year', '=', $f_year_array[1])
                      ->where('month', '<=', 'Mar');
            });
        }else {
           $data->where(function ($query) use($f_year_array) {
                $query->where('year', '=', $f_year_array[0])
                      ->where('month', '>=', $this->month);
            })->orWhere(function ($query) use($f_year_array) {
                $query->where('year', '=', $f_year_array[1])
                      ->where('month', '<=', $this->month);
            });
        }
        
        $data = $data->groupBy('users.branch_id', 'users.division_id')->orderBy('month')->get();

        return $data;
    }


    public function headings(): array
    {
     $f_year_array = explode('-', $this->financial_year);

     $startYear = $f_year_array[0];

     $endYear = $f_year_array[1];

     $headings = ['Branch', 'Division'];

     $quarterNames = ['Q1', 'Q2', 'Q3', 'Q4'];

     $quarterIndex = 0;

     for ($year = $startYear; $year <= $endYear; $year++) {
         $startMonth = ($year == $startYear) ? 4 : 1;
         $endMonth = ($year == $endYear) ? 3 : 12;


         for ($month = $startMonth; $month <= $endMonth; $month++) {

               $formattedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
               $headings[] = "$formattedMonth/$year";
               $headings[] = "";
               $headings[] = "";

               if($month == '06' || $month == '09' || $month == '12' || $month == '03' ) {
                   $headings[] = $quarterNames[$quarterIndex];
                   $quarterIndex++;
                   $headings[] = "";
                   $headings[] = "";
               }

           }
       }

       $headings[] = 'Total';

       $sub_headings = ['','','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%','LY','CY','Growth%'];

       $final_heading = [$headings, $sub_headings];

       return $final_heading;
   }


   public function map($data): array
   {
    $branch = Branch::where('id',$data['branch_id'])->first();
    $division = Division::where('id',$data['division_id'])->first();
    $userIds = explode(',', $data['user_ids']);
    $response = array();
    $response[0] =  $branch->branch_name ?? '';
    $response[1] = $division->division_name ?? '';
    $f_year_array = explode('-', $this->financial_year);
    $data['months'] = explode(',', $data['months']);

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'Apr' && $f_year_array[0] == $year[$key]) {

            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('salestargetcustomers')
                ->where('month', 'Apr')
                ->where('year', $currentYear)
                ->whereIn('customer_id', $userIds)
                ->select(DB::raw('SUM(achievement) as achievements_current_year'))
                ->first();

            $sales_data_last_year = DB::table('salestargetcustomers')
                ->where('month', 'Apr')
                ->where('year', $previousYear)
                ->whereIn('customer_id', $userIds)
                ->select(DB::raw('SUM(achievement) as achievements_last_year'))
                ->first(); 


            $response[2] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[3] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                if ($lastYearAchievements != null) {
                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
                    dd($growthPercent);
                }else{
                    if ($lastYearAchievements == null ) {
                        if ($currentYearAchievements == null && $lastYearAchievements==null) {
                            $growthPercent = 0;
                        }elseif($lastYearAchievements==null && isset($currentYearAchievements) && ($currentYearAchievements != null)) {
                            $growthPercent = 100;
                        }
                    }
                }
            }

            $response[4] = (string)$growthPercent;
        }else{
            if(!isset($response[2])) {
               $response[2] = '0';
            }
            if(!isset($response[3])) {
               $response[3] = '0';
            }
            if(!isset($response[4])) {
               $response[4] = '0';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'May' && $f_year_array[0] == $year[$key]) {

            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('salestargetcustomers')
            ->where('month', 'May')
            ->where('year', $currentYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('salestargetcustomers')
            ->where('month', 'May')
            ->where('year', $previousYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first();    

            $response[5] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[6] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                if ($lastYearAchievements != 0) {
                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
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

            $response[7] = (string)$growthPercent;
        }else{
            if(!isset($response[5])) {
              $response[5] = '0';
          }
          if(!isset($response[6])) {
              $response[6] = '0';
          }
          if(!isset($response[7])) {
              $response[7] = '0';
          }

      }
  }

      foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'Jun' && $f_year_array[0] == $year[$key]) {
            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = ''; 
            $sales_data_current_year = DB::table('salestargetcustomers')
            ->where('month', 'Jun')
            ->where('year', $currentYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('salestargetcustomers')
            ->where('month', 'Jun')
            ->where('year', $previousYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first();    

            $response[8] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[9] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                if ($lastYearAchievements != 0) {
                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
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
            $response[10] = (string)$growthPercent;
        }else{
            if(!isset($response[8])) {
              $response[8] = '0';
          }
          if(!isset($response[9])) {
              $response[9] = '0';
          }
          if(!isset($response[10])) {
              $response[10] = '0';
          }

      }
    }

    $response[11] = '=C'.$this->rowIndex.' + F'.$this->rowIndex.' + I'.$this->rowIndex;
    $response[12] = '=D'.$this->rowIndex.' + G'.$this->rowIndex.' + J'.$this->rowIndex;
    // $response[13] = '=((L'.$this->rowIndex.' - M'.$this->rowIndex.') / K'.$this->rowIndex.') * 100';

    $response[13] = '=IF(AND(M'.$this->rowIndex.'=0, L'.$this->rowIndex.'=0), "0", IF(L'.$this->rowIndex.'=0, 100, ((M'.$this->rowIndex.' - L'.$this->rowIndex.') / L'.$this->rowIndex.') * 100))';

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'Jul' && $f_year_array[0] == $year[$key]) {
            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = ''; 
            $sales_data_current_year = DB::table('salestargetcustomers')
            ->where('month', 'Jul')
            ->where('year', $currentYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('salestargetcustomers')
            ->where('month', 'Jul')
            ->where('year', $previousYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first();    

            $response[14] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[15] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';
            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                if ($lastYearAchievements != 0) {
                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
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
            $response[16] = (string)$growthPercent;
        }else{
            if(!isset($response[14])) {
              $response[14] = '0';
          }
          if(!isset($response[15])) {
              $response[15] = '0';
          }
          if(!isset($response[16])) {
              $response[16] = '0';
          }

      }
  }

      foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'Aug' && $f_year_array[0] == $year[$key]) {
            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = ''; 
            $sales_data_current_year = DB::table('salestargetcustomers')
            ->where('month', 'Aug')
            ->where('year', $currentYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('salestargetcustomers')
            ->where('month', 'Aug')
            ->where('year', $previousYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first();    

            $response[17] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[18] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';
            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                if ($lastYearAchievements != 0) {
                    $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
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
            $response[19] = (string)$growthPercent;
        }else{
            if(!isset($response[17])) {
              $response[17] = '0';
          }
          if(!isset($response[18])) {
              $response[18] = '0';
          }
          if(!isset($response[19])) {
              $response[19] = '0';
          }

      }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if($month == 'Sep' && $f_year_array[0] == $year[$key]) {
            $currentYear = $f_year_array[0];
                       $previousYear = $f_year_array[0] - 1;
                       $growthPercent = ''; 
                       $sales_data_current_year = DB::table('salestargetcustomers')
                       ->where('month', 'Sep')
                       ->where('year', $currentYear)
                       ->whereIn('customer_id', $userIds)
                       ->select(DB::raw('SUM(achievement) as achievements_current_year'))
                       ->first();

                       $sales_data_last_year = DB::table('salestargetcustomers')
                       ->where('month', 'Sep')
                       ->where('year', $previousYear)
                       ->whereIn('customer_id', $userIds)
                       ->select(DB::raw('SUM(achievement) as achievements_last_year'))
                       ->first();    

                       $response[20] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
                       $response[21] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';
                       if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                           $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                           $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                           if ($lastYearAchievements != 0) {
                               $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
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
                       $response[22] = (string)$growthPercent;
        }else{
            if(!isset($response[20])) {
              $response[20] = '0';
          }
          if(!isset($response[21])) {
              $response[21] = '0';
          }
          if(!isset($response[22])) {
              $response[22] = '0';
          }

      }


    $response[23] = '=O'.$this->rowIndex.' + R'.$this->rowIndex.' + U'.$this->rowIndex;
    $response[24] = '=P'.$this->rowIndex.' + S'.$this->rowIndex.' + V'.$this->rowIndex;
    // $response[25] = '=(Q'.$this->rowIndex.' + T'.$this->rowIndex.' + W'.$this->rowIndex.') / 3';
    $response[25] = '=IF(AND(Y'.$this->rowIndex.'=0, X'.$this->rowIndex.'=0), "0", IF(X'.$this->rowIndex.'=0, 100, ((Y'.$this->rowIndex.' - X'.$this->rowIndex.') / L'.$this->rowIndex.') * 100))';


    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
                
                if($month == 'Oct' && $f_year_array[0] == $year[$key]) {
                    $currentYear = $f_year_array[0];
                    $previousYear = $f_year_array[0] - 1;
                    $growthPercent = ''; 
                    $sales_data_current_year = DB::table('salestargetcustomers')
                    ->where('month', 'Oct')
                    ->where('year', $currentYear)
                    ->whereIn('customer_id', $userIds)
                    ->select(DB::raw('SUM(achievement) as achievements_current_year'))
                    ->first();

                    $sales_data_last_year = DB::table('salestargetcustomers')
                    ->where('month', 'Oct')
                    ->where('year', $previousYear)
                    ->whereIn('customer_id', $userIds)
                    ->select(DB::raw('SUM(achievement) as achievements_last_year'))
                    ->first();    

                    $response[26] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
                    $response[27] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';
                    if (isset($sales_data_last_year->achievements_last_year) && isset($sales_data_current_year->achievements_current_year)) {
                        $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                        $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                        if ($lastYearAchievements != 0) {
                            $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
                        } else {
                            $growthPercent = 0;
                        }
                    }   
                    $response[28] = (string)$growthPercent;
                }else{
                    if(!isset($response[26])) {
                      $response[26] = '0';
                    }
                    if(!isset($response[27])) {
                      $response[27] = '0';
                    }
                    if(!isset($response[28])) {
                      $response[28] = '0';
                    }

                }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Nov' && $f_year_array[0] == $year[$key]) {
            $currentYear = $f_year_array[0];
                      $previousYear = $f_year_array[0] - 1;
                      $growthPercent = '';

                      $sales_data_current_year = DB::table('salestargetcustomers')
                          ->where('month', 'Nov')
                          ->where('year', $currentYear)
                          ->whereIn('customer_id', $userIds)
                          ->select(DB::raw('SUM(achievement) as achievements_current_year'))
                          ->first();

                      $sales_data_last_year = DB::table('salestargetcustomers')
                          ->where('month', 'Nov')
                          ->where('year', $previousYear)
                          ->whereIn('customer_id', $userIds)
                          ->select(DB::raw('SUM(achievement) as achievements_last_year'))
                          ->first(); 


                      $response[29] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
                      $response[30] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

                      if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
                          $lastYearAchievements = $sales_data_last_year->achievements_last_year;
                          $currentYearAchievements = $sales_data_current_year->achievements_current_year;

                          if ($lastYearAchievements != 0) {
                              $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
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

                      $response[31] = (string)$growthPercent;
        }else{
            if(!isset($response[29])) {
               $response[29] = '0';
            }
            if(!isset($response[30])) {
               $response[30] = '0';
            }
            if(!isset($response[31])) {
               $response[31] = '0';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Dec' && $f_year_array[0] == $year[$key]) {
            $currentYear = $f_year_array[0];
            $previousYear = $f_year_array[0] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('salestargetcustomers')
            ->where('month', 'Dec')
            ->where('year', $currentYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('salestargetcustomers')
            ->where('month', 'Dec')
            ->where('year', $previousYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first(); 


            $response[32] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[33] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
              $lastYearAchievements = $sales_data_last_year->achievements_last_year;
              $currentYearAchievements = $sales_data_current_year->achievements_current_year;

              if ($lastYearAchievements != 0) {
                  $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
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

          $response[34] = (string)$growthPercent;
        }else{
            if(!isset($response[32])) {
              $response[32] = '0';
            }
            if(!isset($response[33])) {
              $response[33] = '0';
            }
            if(!isset($response[34])) {
              $response[34] = '0';
            }
        }
    }

    $response[35] = '=AA'.$this->rowIndex.' + AD'.$this->rowIndex.' + AG'.$this->rowIndex;
    $response[36] = '=AB'.$this->rowIndex.' + AE'.$this->rowIndex.' + AH'.$this->rowIndex;
    // $response[37] = '=(AC'.$this->rowIndex.' + AF'.$this->rowIndex.' + AI'.$this->rowIndex.') / 3';
    $response[37] = '=IF(AND(AK'.$this->rowIndex.'=0, AJ'.$this->rowIndex.'=0), "0", IF(AJ'.$this->rowIndex.'=0, 100, ((AK'.$this->rowIndex.' - AJ'.$this->rowIndex.') / AK'.$this->rowIndex.') * 100))';

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Jan' && $f_year_array[1] == $year[$key]) {
            $currentYear = $f_year_array[1];
            $previousYear = $f_year_array[1] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('salestargetcustomers')
            ->where('month', 'Jan')
            ->where('year', $currentYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('salestargetcustomers')
            ->where('month', 'Jan')
            ->where('year', $previousYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first(); 


            $response[38] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[39] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
              $lastYearAchievements = $sales_data_last_year->achievements_last_year;
              $currentYearAchievements = $sales_data_current_year->achievements_current_year;

              if ($lastYearAchievements != 0) {
                  $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
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

          $response[40] = (string)$growthPercent;
        }else{
            if(!isset($response[38])) {
              $response[38] = '0';
            }
            if(!isset($response[39])) {
              $response[39] = '0';
            }
            if(!isset($response[40])) {
              $response[40] = '0';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Feb' && $f_year_array[1] == $year[$key]) {
            $currentYear = $f_year_array[1];
            $previousYear = $f_year_array[1] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('salestargetcustomers')
            ->where('month', 'Feb')
            ->where('year', $currentYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('salestargetcustomers')
            ->where('month', 'Feb')
            ->where('year', $previousYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first(); 


            $response[41] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[42] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
              $lastYearAchievements = $sales_data_last_year->achievements_last_year;
              $currentYearAchievements = $sales_data_current_year->achievements_current_year;

              if ($lastYearAchievements != 0) {
                  $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
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

          $response[43] = (string)$growthPercent;
        }else{
            if(!isset($response[41])) {
              $response[41] = '0';
            }
            if(!isset($response[42])) {
              $response[42] = '0';
            }
            if(!isset($response[43])) {
              $response[43] = '0';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Mar' && $f_year_array[1] == $year[$key]) {
            $currentYear = $f_year_array[1];
            $previousYear = $f_year_array[1] - 1;
            $growthPercent = '';

            $sales_data_current_year = DB::table('salestargetcustomers')
            ->where('month', 'Mar')
            ->where('year', $currentYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_current_year'))
            ->first();

            $sales_data_last_year = DB::table('salestargetcustomers')
            ->where('month', 'Mar')
            ->where('year', $previousYear)
            ->whereIn('customer_id', $userIds)
            ->select(DB::raw('SUM(achievement) as achievements_last_year'))
            ->first(); 


            $response[44] = isset($sales_data_last_year->achievements_last_year) ? $sales_data_last_year->achievements_last_year : '0';
            $response[45] = isset($sales_data_current_year->achievements_current_year) ? $sales_data_current_year->achievements_current_year : '0';

            if (isset($sales_data_last_year) && isset($sales_data_current_year)) {
              $lastYearAchievements = $sales_data_last_year->achievements_last_year;
              $currentYearAchievements = $sales_data_current_year->achievements_current_year;

              if ($lastYearAchievements != 0) {
                  $growthPercent = (($currentYearAchievements - $lastYearAchievements) / $lastYearAchievements) * 100;
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

          $response[46] = (string)$growthPercent;
        }else{
            if(!isset($response[44])) {
              $response[44] = '0';
            }
            if(!isset($response[45])) {
              $response[45] = '0';
            }
            if(!isset($response[46])) {
              $response[46] = '0';
            }

        }
    }



    $response[47] = '=AM'.$this->rowIndex.' + AP'.$this->rowIndex.' + AS'.$this->rowIndex;
    $response[48] = '=AN'.$this->rowIndex.' + AQ'.$this->rowIndex.' + AT'.$this->rowIndex;
    // $response[49] = '=(AQ'.$this->rowIndex.' + AR'.$this->rowIndex.' + AU'.$this->rowIndex.') / 3';
    $response[49] = '=IF(AND(AW'.$this->rowIndex.'=0, AV'.$this->rowIndex.'=0), "0", IF(AV'.$this->rowIndex.'=0, 100, ((AW'.$this->rowIndex.' - AV'.$this->rowIndex.') / AW'.$this->rowIndex.') * 100))';

    $response[50] = '=L'.$this->rowIndex.' + X'.$this->rowIndex.' + AJ'.$this->rowIndex.' + AV'.$this->rowIndex;
    $response[51] = '=M'.$this->rowIndex.' + Y'.$this->rowIndex.' + AK'.$this->rowIndex.' + AW'.$this->rowIndex;
    $response[52] = '=(N'.$this->rowIndex.' + Z'.$this->rowIndex.' + AL'.$this->rowIndex.' + AX'.$this->rowIndex.') / 4';

    $response[49] = '=IF(AND(AZ'.$this->rowIndex.'=0, AY'.$this->rowIndex.'=0), "0", IF(AY'.$this->rowIndex.'=0, 100, ((AZ'.$this->rowIndex.' - AY'.$this->rowIndex.') / AW'.$this->rowIndex.') * 100))';

    $this->rowIndex++;

    return $response;

    
    }
}


    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:E1');
        $sheet->mergeCells('F1:H1');
        $sheet->mergeCells('I1:K1');
        $sheet->mergeCells('L1:N1');
        $sheet->mergeCells('O1:Q1');
        $sheet->mergeCells('R1:T1');
        $sheet->mergeCells('U1:W1');
        $sheet->mergeCells('X1:Z1');
        $sheet->mergeCells('AA1:AC1');
        $sheet->mergeCells('AD1:AF1');
        $sheet->mergeCells('AG1:AI1');
        $sheet->mergeCells('AJ1:AL1');
        $sheet->mergeCells('AM1:AO1');
        $sheet->mergeCells('AP1:AR1');
        $sheet->mergeCells('AS1:AU1');
        $sheet->mergeCells('AV1:AX1');
        $sheet->mergeCells('AY1:BA1');
        $sheet->mergeCells('BB1:BD1');

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
