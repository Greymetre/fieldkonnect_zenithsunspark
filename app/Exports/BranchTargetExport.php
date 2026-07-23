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
use Carbon\Carbon;

class BranchTargetExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping,WithStyles
{

    private $rowIndex = 3;
    public function __construct($request)
    {    
        $this->division = $request->input('division');
        $this->branch_id = $request->input('branch_id');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
        $this->target = $request->input('target');  
        $this->user = $request->input('user');  
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
        //  // DB::raw('SUM(target) as targets'),
        //  // DB::raw('SUM(achievement) as achievements'),
        //  DB::raw('GROUP_CONCAT(month) as months'),  
        //  DB::raw('GROUP_CONCAT(user_id) as user_ids'),
        //  DB::raw('GROUP_CONCAT(year) as years'),
        //  DB::raw('branch_name'),
        //  DB::raw('division_name'),
        //  // DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
        //  'users.branch_id',
        //  'users.division_id',
        //  'users.designation_id',
        // ]);

        // dd($this->division);
        $data = BranchWiseTarget::with('user')->select([
         DB::raw('GROUP_CONCAT(month) as months'),  
         DB::raw('GROUP_CONCAT(user_id) as user_ids'),
         DB::raw('GROUP_CONCAT(year) as years'),
         DB::raw('branch_name'),
         DB::raw('division_name'),
         'branch_id',
         'div_id',
         'target',
         'user_id',
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
                if($this->month != '' && !empty($this->month)) {
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
        
        // $data = $data->groupBy('branch_id','div_id','user_id')->orderBy('month')->get();
        $data = $data->groupBy('branch_id','div_id','user_id')->orderBy('month')->get();

        return $data;
    }


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
            $sub_headings = ['','','','','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%'];
            $final_heading = [$allMonths, $sub_headings];
        }


        if (!empty($this->month) && count($this->month)>0) {
           foreach ($this->month as $selectedMonth) {
               array_push($sub_headings, 'Tgt', 'Ach', 'Ach%');
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
    $data['months'] = $this->month;
    $status = true;

    if($data['months'] == null) {
        $data['months'] =  ['Apr','May','Jun','Jul', 'Aug', 'Sep','Oct','Nov','Dec','Jan','Feb','Mar'];
        $status = false;
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        if(isset($month) && isset($year[$key]) && in_array('Apr',$data['months']) && $f_year_array[0] ) {

        $sales_data = DB::table('branchwise_targets')
            ->where(['month'=> 'Apr', 'year' => $f_year_array[0]])->where('user_id', $data['user_id'])
            ->where('branch_id', $data['branch_id'])
            ->where('div_id', $data['div_id'])
            ->get();

        if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
            $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement /$sales_data[0]->target);
            $achievementPercent = number_format($achievementPercent, 2);

        }else{
            $achievementPercent = '';
        }    

            $response[4] = $sales_data[0]->target  ?? '';
            $response[5] = $sales_data[0]->achievement ?? '';
            $response[6] = $achievementPercent;

        }elseif($status == false){
            if(!isset($response[4])) {
               $response[4] = '';
            }
            if(!isset($response[5])) {
               $response[5] = '';
            }
            if(!isset($response[6])) {
               $response[6] = '';
            }
        }
    }


    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if(isset($month) && isset($year[$key]) && in_array('May',$data['months']) && $f_year_array[0]) {
            $sales_data = DB::table('branchwise_targets')->where(['month'=> 'May', 'year' => $f_year_array[0],'user_id' => $data['user_id'], 'branch_id'=> $data['branch_id'],'div_id'=> $data['div_id']])->get();
            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }  
                        
            $response[7] = $sales_data[0]->target ?? '';
            $response[8] = $sales_data[0]->achievement ??'';
            $response[9] = $achievementPercent;

        }elseif($status == false){
            if(!isset($response[7])) {
              $response[7] = '';
            }
            if(!isset($response[8])) {
              $response[8] = '';
            }
            if(!isset($response[9])) {
              $response[9] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if(isset($month) && isset($year[$key]) && in_array('Jun',$data['months']) && $f_year_array[0] ) {
            $sales_data = DB::table('branchwise_targets')
                        ->where(['month'=> 'Jun', 'year' => $f_year_array[0],'user_id' => $data['user_id'], 'branch_id'=> $data['branch_id'],'div_id'=> $data['div_id']])
                        ->get();

            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }

            $response[10] = $sales_data[0]->target  ?? '';
            $response[11] = $sales_data[0]->achievement ??'';
            $response[12] = $achievementPercent;

        }elseif($status == false){
            if(!isset($response[10])) {
              $response[10] = '';
            }
            if(!isset($response[11])) {
              $response[11] = '';
            }
            if(!isset($response[12])) {
              $response[12] = '';
            }
        }
    }

    if($status == false) {
        $response[13] = '=E'.$this->rowIndex.' + H'.$this->rowIndex.' + K'.$this->rowIndex;
        $response[14] = '=F'.$this->rowIndex.' + I'.$this->rowIndex.' + L'.$this->rowIndex;
        $response[15] = '=ROUND((G'.$this->rowIndex.' + J'.$this->rowIndex.' + M'.$this->rowIndex.') / 3,2)';
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if(isset($month) && isset($year[$key]) && in_array('Jul',$data['months']) && $f_year_array[0]) {
            $sales_data = DB::table('branchwise_targets')
                        ->where(['month'=> 'Jul', 'year' => $f_year_array[0],'user_id' => $data['user_id'], 'branch_id'=> $data['branch_id'],'div_id'=> $data['div_id']])
                        ->get();
            
            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }
            $response[16] = $sales_data[0]->target ?? '';
            $response[17] = $sales_data[0]->achievement ??'';
            $response[18] = $achievementPercent;
        }elseif($status == false){
            if(!isset($response[16])) {
               $response[16] = '';
            }
            if(!isset($response[17])) {
               $response[17] = '';
            }
            if(!isset($response[18])) {
               $response[18] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if(isset($month) && isset($year[$key]) && in_array('Aug',$data['months']) && $f_year_array[0]) {
            $sales_data = DB::table('branchwise_targets')
                        ->where(['month'=> 'Aug', 'year' => $f_year_array[0],'user_id' => $data['user_id'], 'branch_id'=> $data['branch_id'],'div_id'=> $data['div_id']])
                        ->get();
            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }
                        
            $response[19] = $sales_data[0]->target  ?? '';
            $response[20] = $sales_data[0]->achievement ??'';
            $response[21] = $achievementPercent;
        }elseif($status == false){
            if(!isset($response[19])) {
               $response[19] = '';
            }
            if(!isset($response[20])) {
               $response[20] = '';
            }
            if(!isset($response[21])) {
               $response[21] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if(isset($month) && isset($year[$key]) && in_array('Sep',$data['months']) && $f_year_array[0]) {
            $sales_data = DB::table('branchwise_targets')
                        ->where(['month'=> 'Sep', 'year' => $f_year_array[0],'user_id' => $data['user_id'], 'branch_id'=> $data['branch_id'],'div_id'=> $data['div_id']])
                        ->get();
            
            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }

            $response[22] = $sales_data[0]->target ?? '';
            $response[23] = $sales_data[0]->achievement ??'';
            $response[24] = $achievementPercent;
        }elseif($status == false){
            if(!isset($response[22])) {
               $response[22] = '';
            }
            if(!isset($response[23])) {
               $response[23] = '';
            }
            if(!isset($response[24])) {
               $response[24] = '';
            }
        }
    }
 
    if($status == false) {
        $response[25] = '=Q'.$this->rowIndex.' + T'.$this->rowIndex.' + W'.$this->rowIndex;
        $response[26] = '=R'.$this->rowIndex.' + U'.$this->rowIndex.' + X'.$this->rowIndex;
        $response[27] = '=ROUND((S'.$this->rowIndex.' + V'.$this->rowIndex.' + Y'.$this->rowIndex.') / 3,2)';
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if(isset($month) && isset($year[$key]) && in_array('Oct',$data['months']) && $f_year_array[0] ) {
            $sales_data = DB::table('branchwise_targets')
                        ->where(['month'=> 'Oct', 'year' => $f_year_array[0],'user_id' => $data['user_id'], 'branch_id'=> $data['branch_id'],'div_id'=> $data['div_id']])
                        ->get();

            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }
                        
            $response[28] = $sales_data[0]->target ?? '';
            $response[29] = $sales_data[0]->achievement??'';
            $response[30] = $achievementPercent;
        }elseif($status == false){
            if(!isset($response[28])) {
               $response[28] = '';
            }
            if(!isset($response[29])) {
               $response[29] = '';
            }
            if(!isset($response[30])) {
               $response[30] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if(isset($month) && isset($year[$key]) && in_array('Nov',$data['months']) && $f_year_array[0] ) {
            $sales_data = DB::table('branchwise_targets')
                        ->where(['month'=> 'Nov', 'year' => $f_year_array[0],'user_id' => $data['user_id'], 'branch_id'=> $data['branch_id'],'div_id'=> $data['div_id']])
                        ->get();
            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }
            $response[31] = $sales_data[0]->target ?? '';
            $response[32] = $sales_data[0]->achievement ??'';
            $response[33] = $achievementPercent;
        }elseif($status == false){
            if(!isset($response[31])) {
               $response[31] = '';
            }
            if(!isset($response[32])) {
               $response[32] = '';
            }
            if(!isset($response[33])) {
               $response[33] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if(isset($month) && isset($year[$key]) && in_array('Dec',$data['months']) && $f_year_array[0] ) {
            $sales_data = DB::table('branchwise_targets')
                        ->where(['month'=> 'Dec', 'year' => $f_year_array[0],'user_id' => $data['user_id']])
                        ->get();
            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }

            $response[34] = $sales_data[0]->target ?? '';
            $response[35] = $sales_data[0]->achievement??'';
            $response[36] = $achievementPercent;
        }elseif($status == false){
            if(!isset($response[34])) {
              $response[34] = '';
            }
            if(!isset($response[35])) {
              $response[35] = '';
            }
            if(!isset($response[36])) {
              $response[36] = '';
            }
        }
    }

    if($status == false) {
        $response[37] = '=AC'.$this->rowIndex.'+AF'.$this->rowIndex.'+AI'.$this->rowIndex;
        $response[38] = '=AD'.$this->rowIndex.'+AG'.$this->rowIndex.'+AJ'.$this->rowIndex;
        $response[39] = '=ROUND((AE'.$this->rowIndex.'+AH'.$this->rowIndex.'+AK'.$this->rowIndex.')/3,2)';
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);

        // if(isset($month) && isset($year[$key]) && in_array('Jan',$data['months']) && $f_year_array[1] == $year[$key]);
        
        if(isset($month) && isset($year[$key]) && in_array('Jan',$data['months']) && $f_year_array[1]) {
            $sales_data = DB::table('branchwise_targets')
                        ->where(['month'=> 'Jan', 'year' => $f_year_array[1],'user_id' => $data['user_id']])
                        ->get();
            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }

            $response[40] = $sales_data[0]->target ?? '';
            $response[41] = $sales_data[0]->achievement??'';
            $response[42] = $achievementPercent;
        }elseif($status == false){
            if(!isset($response[40])) {
              $response[40] = '';
            }
            if(!isset($response[41])) {
              $response[41] = '';
            }
            if(!isset($response[42])) {
              $response[42] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if(isset($month) && isset($year[$key]) && in_array('Feb',$data['months']) && $f_year_array[1] ) {
            $sales_data = DB::table('branchwise_targets')
                        ->where(['month'=> 'Feb', 'year' => $f_year_array[1],'user_id' => $data['user_id']])
                        ->get();
            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }
                        
            $response[43] = $sales_data[0]->target  ?? '';
            $response[44] = $sales_data[0]->achievement ??'';
            $response[45] = $achievementPercent;
        }elseif($status == false){
            if(!isset($response[43])) {
              $response[43] = '';
            }
            if(!isset($response[44])) {
              $response[44] = '';
            }
            if(!isset($response[45])) {
              $response[45] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if(isset($month) && isset($year[$key]) && in_array('Mar',$data['months']) && $f_year_array[1] ) {
            $sales_data = DB::table('branchwise_targets')
                        ->where(['month'=> 'Mar', 'year' => $f_year_array[1],'user_id' => $data['user_id']])
                        ->get();

            if(isset($sales_data[0]->achievement) && isset($sales_data[0]->target) && !empty($sales_data[0]->achievement) && !empty($sales_data[0]->target)) {
                $achievementPercent = ($sales_data[0]->target == 0) ? 0 : ($sales_data[0]->achievement * 100 / $sales_data[0]->target);
                $achievementPercent = number_format($achievementPercent, 2);
            }else{
                $achievementPercent = '';
            }
         
            $response[46] = $sales_data[0]->target ?? '';
            $response[47] = $sales_data[0]->achievement??'';
            $response[48] = $achievementPercent;
        }elseif($status == false){
            if(!isset($response[46])) {
              $response[46] = '';
            }
            if(!isset($response[47])) {
              $response[47] = '';
            }
            if(!isset($response[48])) {
              $response[48] = '';
            }
        }
    }

    if($status == false) {
        $response[49] = '=AO'.$this->rowIndex.'+AR'.$this->rowIndex.'+AU'.$this->rowIndex;
        $response[50] = '=AP'.$this->rowIndex.'+AS'.$this->rowIndex.'+AV'.$this->rowIndex;
        $response[51] = '=ROUND((AQ'.$this->rowIndex.'+AT'.$this->rowIndex.'+AW'.$this->rowIndex.')/3,2)';

        $response[52] = '=N'.$this->rowIndex.'+Z'.$this->rowIndex.'+AL'.$this->rowIndex.'+AX'.$this->rowIndex;
        $response[53] = '=O'.$this->rowIndex.'+AA'.$this->rowIndex.'+AM'.$this->rowIndex.'+AY'.$this->rowIndex;
        $response[54] = '=ROUND((P'.$this->rowIndex.'+AB'.$this->rowIndex.'+AN'.$this->rowIndex.'+AZ'.$this->rowIndex.')/4,2)';

    }


    $this->rowIndex++;

    // dd($response);

    return $response;
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
