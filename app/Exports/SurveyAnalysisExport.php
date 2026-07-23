<?php

namespace App\Exports;

use App\Models\User;
use App\Models\DealIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SurveyAnalysisExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        $this->fromdate = date("Y-m-01");
        $this->todate = date("Y-m-t");
        $this->userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
        
        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return DealIn::select('types', DB::raw('count(*) as total'))->groupBy('types')->get();
    }

    public function headings(): array
    {
        return ['Types', 'Total', 'MAV', 'HCV', 'LCV', 'LMV', 'Other', 'Yes'];
    }

    public function map($data): array
    {
        $deals = DealIn::where('types','=',$data['types'])->select([DB::raw("SUM(hcv) as total_hcv"), DB::raw("SUM(mav) as total_mav"), DB::raw("SUM(lmv) as total_lmv"), DB::raw("SUM(lcv) as total_lcv"), DB::raw("SUM(other) as total_other"), DB::raw("SUM(tractor) as total_tractor"),DB::raw('count(*) as total')])->groupBy('types')->first();
        return [
            !empty($data['types']) ? $data['types'] : '' ,
            !empty($data['total']) ? $data['total'] : '' ,
            !empty($deals) ? $deals['total_mav'] : '' ,
            !empty($deals) ? $deals['total_hcv'] : '' ,
            !empty($deals) ? $deals['total_lcv'] : '' ,
            !empty($deals) ? $deals['total_lmv'] : '' ,
            !empty($deals) ? $deals['total_other'] : '' ,
            !empty($deals) ? $deals['total_tractor'] : '' ,      
        ];
    }
}