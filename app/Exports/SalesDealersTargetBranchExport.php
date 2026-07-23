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

class SalesDealersTargetBranchExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping,WithStyles
{

    private $rowIndex = 3;
    public function __construct($request)
    {    
        // dd($request->all());
        $this->user_id = $request->input('user_id');
        $this->month = $request->input('month');
        $this->financial_year = $request->input('financial_year');
        $this->target = $request->input('target');  
      
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);

        // target user branch wise
        // $data = SalesTargetUsers::join('users', 'salestargetusers.user_id', '=', 'users.id')->with(['user','user.getdesignation','user.getdivision','user.getbranch'])->select([
        //  DB::raw('SUM(target) as targets'),
        //  DB::raw('SUM(achievement) as achievements'),
        //  DB::raw('GROUP_CONCAT(month) as months'),  
        //  DB::raw('GROUP_CONCAT(year) as years'),
        //  DB::raw('GROUP_CONCAT(achievement_percent) as achievement_percents'),
        //  'user_id',
        //  'users.branch_id',
        //  'users.division_id',
        // ]);

        // ->with(['user','user.getdesignation','user.getdivision','user.getbranch'])->

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
        // dd($data);
    
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

       $sub_headings = ['','','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%','Tgt','Ach','Ach%'];

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

        $sales_data = DB::table('salestargetcustomers')
            ->where(['month'=> 'Apr', 'year' => $f_year_array[0]])->whereIn('customer_id', $userIds)->select(
                DB::raw('SUM(target ) as targets'),
                DB::raw('SUM(achievement) as achievements')
            )
            ->get();

        if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
            $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
        }else{
            $achievementPercent = '';
        }    

            $response[2] = $sales_data[0]->targets  ?? '';
            $response[3] = $sales_data[0]->achievements ?? '';
            $response[4] = $achievementPercent;
        }else{
            if(!isset($response[2])) {
               $response[2] = '';
            }
            if(!isset($response[3])) {
               $response[3] = '';
            }
            if(!isset($response[4])) {
               $response[4] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'May' && $f_year_array[0] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'May', 'year' => $f_year_array[0]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();
            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }  
                        
            $response[5] = $sales_data[0]->targets  ?? '';
            $response[6] = $sales_data[0]->achievements ??'';
            $response[7] = $achievementPercent;
        }else{
            if(!isset($response[5])) {
              $response[5] = '';
            }
            if(!isset($response[6])) {
              $response[6] = '';
            }
            if(!isset($response[7])) {
              $response[7] = '';
            }

        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Jun' && $f_year_array[0] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'Jun', 'year' => $f_year_array[0]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();
            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }

            $response[8] = $sales_data[0]->targets  ?? '';
            $response[9] = $sales_data[0]->achievements ??'';
            $response[10] = $achievementPercent;
        }else{
            if(!isset($response[8])) {
              $response[8] = '';
            }
            if(!isset($response[9])) {
              $response[9] = '';
            }
            if(!isset($response[10])) {
              $response[10] = '';
            }
        }
    }

    $response[11] = '=C'.$this->rowIndex.' + F'.$this->rowIndex.' + I'.$this->rowIndex;
    $response[12] = '=D'.$this->rowIndex.' + G'.$this->rowIndex.' + J'.$this->rowIndex;
    $response[13] = '=(E'.$this->rowIndex.' + H'.$this->rowIndex.' + K'.$this->rowIndex.') / 3';

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Jul' && $f_year_array[0] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'Jul', 'year' => $f_year_array[0]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();
            
            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }
            $response[14] = $sales_data[0]->targets  ?? '';
            $response[15] = $sales_data[0]->achievements ??'';
            $response[16] = $achievementPercent;
        }else{
            if(!isset($response[14])) {
               $response[14] = '';
            }
            if(!isset($response[15])) {
               $response[15] = '';
            }
            if(!isset($response[16])) {
               $response[16] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Aug' && $f_year_array[0] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'Aug', 'year' => $f_year_array[0]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();
            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }
                        
            $response[17] = $sales_data[0]->targets  ?? '';
            $response[18] = $sales_data[0]->achievements ??'';
            $response[19] = $achievementPercent;
        }else{
            if(!isset($response[17])) {
               $response[17] = '';
            }
            if(!isset($response[18])) {
               $response[18] = '';
            }
            if(!isset($response[19])) {
               $response[19] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Sep' && $f_year_array[0] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'Sep', 'year' => $f_year_array[0]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();
            
            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }

            $response[20] = $sales_data[0]->targets  ?? '';
            $response[21] = $sales_data[0]->achievements ??'';
            $response[22] = $achievementPercent;
        }else{
            if(!isset($response[20])) {
               $response[20] = '';
            }
            if(!isset($response[21])) {
               $response[21] = '';
            }
            if(!isset($response[22])) {
               $response[22] = '';
            }
        }
    }

    $response[23] = '=R'.$this->rowIndex.' + U'.$this->rowIndex.' + X'.$this->rowIndex;
    $response[24] = '=S'.$this->rowIndex.' + V'.$this->rowIndex.' + Y'.$this->rowIndex;
    $response[25] = '=(T'.$this->rowIndex.' + W'.$this->rowIndex.' + Z'.$this->rowIndex.') / 3';


    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Oct' && $f_year_array[0] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'Oct', 'year' => $f_year_array[0]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();

            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }
                        
            $response[26] = $sales_data[0]->targets ?? '';
            $response[27] = $sales_data[0]->achievements??'';
            $response[28] = $achievementPercent;
        }else{
            if(!isset($response[26])) {
               $response[26] = '';
            }
            if(!isset($response[27])) {
               $response[27] = '';
            }
            if(!isset($response[28])) {
               $response[28] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Nov' && $f_year_array[0] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'Nov', 'year' => $f_year_array[0]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();
            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }
            $response[29] = $sales_data[0]->targets  ?? '';
            $response[30] = $sales_data[0]->achievements ??'';
            $response[31] = $achievementPercent;
        }else{
            if(!isset($response[29])) {
               $response[29] = '';
            }
            if(!isset($response[30])) {
               $response[30] = '';
            }
            if(!isset($response[31])) {
               $response[31] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Dec' && $f_year_array[0] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'Dec', 'year' => $f_year_array[0]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();
            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }

            $response[32] = $sales_data[0]->targets ?? '';
            $response[33] = $sales_data[0]->achievements??'';
            $response[34] = $achievementPercent;
        }else{
            if(!isset($response[32])) {
              $response[32] = '';
            }
            if(!isset($response[33])) {
              $response[33] = '';
            }
            if(!isset($response[34])) {
              $response[34] = '';
            }
        }
    }

    $response[35] = '=AD'.$this->rowIndex.' + AJ'.$this->rowIndex.' + AI'.$this->rowIndex;
    $response[36] = '=AE'.$this->rowIndex.' + AH'.$this->rowIndex.' + AK'.$this->rowIndex;
    $response[37] = '=(AF'.$this->rowIndex.' + AI'.$this->rowIndex.' + AL'.$this->rowIndex.') / 3';

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Jan' && $f_year_array[1] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'Jan', 'year' => $f_year_array[1]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();
            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }

            $response[38] = $sales_data[0]->targets ?? '';
            $response[39] = $sales_data[0]->achievements??'';
            $response[40] = $achievementPercent;
        }else{
            if(!isset($response[38])) {
              $response[38] = '';
            }
            if(!isset($response[39])) {
              $response[39] = '';
            }
            if(!isset($response[40])) {
              $response[40] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Feb' && $f_year_array[1] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'Feb', 'year' => $f_year_array[1]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();
            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }
                        
            $response[41] = $sales_data[0]->targets  ?? '';
            $response[42] = $sales_data[0]->achievements ??'';
            $response[43] = $achievementPercent;
        }else{
            if(!isset($response[41])) {
              $response[41] = '';
            }
            if(!isset($response[42])) {
              $response[42] = '';
            }
            if(!isset($response[43])) {
              $response[43] = '';
            }
        }
    }

    foreach($data['months'] as $key=>$month) {
        $year = explode(',',$data['years']);
        
        if($month == 'Mar' && $f_year_array[1] == $year[$key]) {
            $sales_data = DB::table('salestargetcustomers')
                        ->where(['month'=> 'Mar', 'year' => $f_year_array[1]])->whereIn('customer_id', $userIds)->select(
                            DB::raw('SUM(target ) as targets'),
                            DB::raw('SUM(achievement) as achievements')
                        )
                        ->get();
            if(isset($sales_data[0]->achievements) && isset($sales_data[0]->targets) && !empty($sales_data[0]->achievements) && !empty($sales_data[0]->targets)) {
                $achievementPercent = ($sales_data[0]->targets == 0) ? 0 : ($sales_data[0]->achievements * 100 / $sales_data[0]->targets);
            }else{
                $achievementPercent = '';
            }
         
            $response[44] = $sales_data[0]->targets ?? '';
            $response[45] = $sales_data[0]->achievements??'';
            $response[46] = $achievementPercent;
        }else{
            if(!isset($response[44])) {
              $response[44] = '';
            }
            if(!isset($response[45])) {
              $response[45] = '';
            }
            if(!isset($response[46])) {
              $response[46] = '';
            }

        }
    }

    $response[47] = '=AP'.$this->rowIndex.' + AS'.$this->rowIndex.' + AV'.$this->rowIndex;
    $response[48] = '=AQ'.$this->rowIndex.' + AT'.$this->rowIndex.' + AW'.$this->rowIndex;
    $response[49] = '=(AR'.$this->rowIndex.' + AU'.$this->rowIndex.' + AX'.$this->rowIndex.') / 3';

    $response[50] = '=L'.$this->rowIndex.' + X'.$this->rowIndex.' + AJ'.$this->rowIndex.' + AV'.$this->rowIndex;
    $response[51] = '=M'.$this->rowIndex.' + Y'.$this->rowIndex.' + AK'.$this->rowIndex.' + AW'.$this->rowIndex;
    $response[52] = '=(N'.$this->rowIndex.' + Z'.$this->rowIndex.' + AL'.$this->rowIndex.' + AX'.$this->rowIndex.') / 4';

    $this->rowIndex++;

    return $response;
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
