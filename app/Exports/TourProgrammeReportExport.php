<?php

namespace App\Exports;

use App\Models\TourProgramme;
use App\Models\TourDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class TourProgrammeReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    { 
        $this->fromdate = date("Y-m-01");
        $this->todate = date("Y-m-t");
        $this->userid = isset($request->user_id) ? $request->user_id : Auth::user()->id;
    }

    public function collection()
    {  
        return TourProgramme::with('tourdetails')->where('userid','=',$this->userid)
                                    ->where('date', '>=', $this->fromdate)
                                    ->where('date', '<=', $this->todate)
                                    ->select('id','date','objectives','town','type','status')
                                    ->get();
    }

    public function headings(): array
    {
        return ['Date','Town', 'Category','Objectives', 'Last Visit Date'];
    }

    public function map($data): array
    {
        $category = collect([]);
        $visited_date = collect([]);
        foreach ($data['tourdetails'] as $key => $detail) {
            if(!empty($detail['cityname']))
            {
                $category->push($detail['cityname']['grade']);
            }
            if(!empty($detail['visited_date']))
            {
                $visited_date->push($detail['visited_date']);
            }
        }
        $unique_category = $category->unique()->toArray();
        $unique_visited = $visited_date->unique()->toArray();
        $data['category'] = implode(',', $unique_category);
        $data['last_visit_date'] = implode(',', $unique_visited);
        return [
            isset($data['date']) ? $data['date'] : '' ,
            isset($data['town']) ? $data['town'] :'',
            isset($data['category']) ? $data['category'] :'',
            isset($data['objectives']) ? $data['objectives'] :'',
            isset($data['last_visit_date']) ? $data['last_visit_date'] :'',
        ];
    }
}