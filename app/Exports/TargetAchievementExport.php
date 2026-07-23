<?php

namespace App\Exports;

use App\Models\User;
use App\Models\SalesDetails;
use App\Models\SalesTarget;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class TargetAchievementExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
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
        return User::with('reportinginfo')->whereIn('id', $this->userids)
                            ->select('id','name','location','reportingid')
                            ->get();
    }

    public function headings(): array
    {
        return ['ZSM', 'ASM Name', 'Base Location', 'GAJRA GEARS Target', 'GAJRA GEARS 10th', 'GAJRA GEARS 20th', 'GAJRA GEARS 30th', 'Product Division Target', 'Product Division 10th', 'Product Division 20th', 'Product Division 30th', 'Differential Target', 'Differential 10th', 'Differential 20th', 'Differential 30th' , 'Group Target', 'Group 10th', 'Group 20th', 'Group 30th'];
    }

    public function map($data): array
    {

        $sales = SalesDetails::with('products','sales')->whereHas('sales',function($query) use($data) {
                                    $query->where('created_by','=',$this->userid);
                                    $query->whereYear('invoice_date', '=', date('Y'));
                                    $query->whereMonth('invoice_date', '=', date('m'));
                                })
                                ->select('product_id','line_total')
                                ->get();
        $targets = SalesTarget::where('userid','=',$this->userid)->whereMonth('startdate','=',date('m'))->whereYear('startdate','=',date('Y'))->sum('amount');

        return [
            isset($data['reportinginfo']['name']) ? $data['reportinginfo']['name'] : '' ,
            isset($data['name']) ? $data['name'] : '' ,
            isset($data['location']) ? $data['location'] :'',
            $targets,
            $sales->where('products.category_id','=','3')->where('sales.invoice_date', '>=', date("Y-m-01"))->where('sales.invoice_date', '<=', date("Y-m-10"))->sum('line_total'),
            $sales->where('products.category_id','=','3')->where('sales.invoice_date', '>=', date("Y-m-11"))->where('sales.invoice_date', '<=', date("Y-m-20"))->sum('line_total'),
            $sales->where('products.category_id','=','3')->where('sales.invoice_date', '>=', date("Y-m-21"))->where('sales.invoice_date', '<=', date("Y-m-t"))->sum('line_total'),
            $targets,
            $sales->where('products.category_id','=','2')->where('sales.invoice_date', '>=', date("Y-m-01"))->where('sales.invoice_date', '<=', date("Y-m-10"))->sum('line_total'),
            $sales->where('products.category_id','=','2')->where('sales.invoice_date', '>=', date("Y-m-11"))->where('sales.invoice_date', '<=', date("Y-m-20"))->sum('line_total'),
            $sales->where('products.category_id','=','2')->where('sales.invoice_date', '>=', date("Y-m-21"))->where('sales.invoice_date', '<=', date("Y-m-t"))->sum('line_total'),
            $targets,
            $sales->where('products.category_id','=','1')->where('sales.invoice_date', '>=', date("Y-m-01"))->where('sales.invoice_date', '<=', date("Y-m-10"))->sum('line_total'),
            $sales->where('products.category_id','=','1')->where('sales.invoice_date', '>=', date("Y-m-11"))->where('sales.invoice_date', '<=', date("Y-m-20"))->sum('line_total'),
            $sales->where('products.category_id','=','1')->where('sales.invoice_date', '>=', date("Y-m-21"))->where('sales.invoice_date', '<=', date("Y-m-t"))->sum('line_total'),
            $targets,
            $sales->where('sales.invoice_date', '>=', date("Y-m-01"))->where('sales.invoice_date', '<=', date("Y-m-10"))->sum('line_total'),
            $sales->where('sales.invoice_date', '>=', date("Y-m-11"))->where('sales.invoice_date', '<=', date("Y-m-20"))->sum('line_total'),
            $sales->where('sales.invoice_date', '>=', date("Y-m-11"))->where('sales.invoice_date', '<=', date("Y-m-t"))->sum('line_total'),
        ];
    }
}