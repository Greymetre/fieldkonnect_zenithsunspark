<?php

namespace App\Exports;

use App\Models\PlannedSOP;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PrimarySales;
use App\Models\Product;
use App\Models\BranchOprningQuantity;

class PlannedSopPUMExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    protected $filters;
    protected $year;

    public function __construct(Request $request)
    {
        $this->filters = $request->all();
    }

   public function collection()
    {
        $start_date = "";
        $end_date   = "";
        if(isset($this->filters['financial_year'])){
            $this->year = explode('-',$this->filters['financial_year']);
            $start_date = $this->year[0].'-04-01';
            $end_date   = $this->year[1].'-03-31';
        }


        $data = PlannedSOP::with(['getProduct.subcategories' , 'getProduct.productdetails', 'getProduct.categories', 'getBranch', 'primarySale']);       
        if (isset($start_date) && isset($end_date)) {
            $data->whereBetween('planning_month', [$start_date, $end_date]);
        }

        if(!Auth::user()->hasRole('superadmin')){
            $data->whereRaw("FIND_IN_SET(?, view_only)", [Auth::user()->division_id]);
        }
        if(!Auth::user()->hasRole('superadmin')){
            $branch_ids = explode(',', Auth::user()->branch_id);
            $data->whereIn('branch_id' , $branch_ids);
        }
        foreach ($this->filters as $key => $value) {
               if (isset($value))  {
                switch ($key) {
                    case "created_by" : 
                    case 'top_sku':
                        $data->where($key, 'like', "%$value%");
                        break;
                    case "product_name" :
                    case "description": 
                    case "product_code":
                        $data->whereHas('getProduct', function ($q) use ($value , $key) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                    case "branch_name":
                        $data->whereHas('getBranch', function ($q) use ($value , $key) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                    case "division_id" : 
                        $data->whereHas('getProduct.categories', function ($q) use ($value) {
                            $q->where('id', $value);
                        });
                        break;
                    case "group_name" : 
                        $data->whereHas('getProduct.subcategories', function ($q) use ($value) {
                            $q->where('subcategory_name', 'like', "%$value%");
                        });
                        break;
                    case 'plan_next_month':
                    case 'budget_for_month':
                    case 'last_month_sale':
                    case 'last_three_month_avg':
                    case 'last_year_month_sale':
                    case 'sku_unit_price':
                    case 's_op_val':
                    case 'status':
                     $data->where($key, 'like', "$value");
                     break;
                }
            }
        }
        if(isset($this->filters['planning_month'])){
          try{
            $formatted_date = Carbon::createFromFormat('F Y', $this->filters['planning_month'])->startOfMonth();
            $last_month = Carbon::createFromFormat('F Y', $this->filters['planning_month'])->subMonth()->startOfMonth()->format("Y-m-d");
            $planning_month = $formatted_date->format("Y-m-d");
            $data->whereDate('planning_month' , $planning_month);
            $lat_pro_ids = $data->pluck('product_id');
            $data->orWhereDate('planning_month', $last_month)->whereNotIn('product_id' , $lat_pro_ids);
            // dd($data->get()->count());
          }catch(\Exception $e){
             $data->latest()->get();
          }
        }
        return $data->latest()->get();
    }

    public function headings(): array
    {
        if(isset($this->filters['financial_year'])){
            $this->year = explode('-',$this->filters['financial_year']);
        }
        $heading1 = ["Order Id" , "Planing Month","Branch Name","Division Name", "Group Name", "Sap Code", "Product Name","Sale : Quantity","","","","","","","","","","","","","","","Open Order (Prod.)","","Forecast (Sales Plan)","" ,'Created By', 'Verify By' ,'Created At'];

        $heading2 = ["","","","","","",""];
        for ($i=4; $i <= 12 ; $i++) {
            $month = ($i < 10 ? '0' . $i : $i) . '/' . ($this->year[0] - 1);            
            array_push($heading2, $month);
        }
        for ($i=1; $i <= 3 ; $i++) {
            $month = '0'.$i.'/'.($this->year[0]);
            array_push($heading2, $month);
        }

        $heading2 = array_merge($heading2, ['Min', "Max", "Avg","Open Order Qty" , "Open Order Value",
           'Forecast Qty', 'Forecast Value',]);


        $headings = array($heading1, $heading2); // Merge arrays
        return $headings;
    }


    public function map($data): array
    {    
        $product_price = (float) ($data['getProduct']['productdetails'][0]['price'] ?? 0);
        $product_price -= ($product_price * 41) / 100;
    
        $plan_next_month = (int) ($data['plan_next_month'] ?? 0);
        $plan_next_month_value = $product_price * $plan_next_month;

        $planning_month = isset($data['planning_month'])
            ? \Carbon\Carbon::parse($data['planning_month'])->subMonth()->startOfMonth()->format('Y-m-d')
            : '';

        $product = $data['getProduct'] ?? '';
        $open_order =  PlannedSOP::where('product_id', $product->id)
        ->where("branch_id", $data->branch_id)
        ->whereDate('planning_month', $planning_month)
        ->first();


        $open_order_stock = isset($open_order->plan_next_month) && is_numeric($open_order->plan_next_month)
            ? (int) $open_order->plan_next_month
            : 0;

        $openderValue        = round($product_price * $open_order_stock, 2);

        $opening_stock = isset($data['opening_stock']) && is_numeric($data['opening_stock'])
            ? (int) $data['opening_stock']
            : 0;
        $opening_stock_value =round($product_price * $opening_stock,2);

        if(isset($this->filters['planning_month'])){
            $last_month = Carbon::createFromFormat('F Y', $this->filters['planning_month'])->subMonth()->startOfMonth()->format("Y-m-d");
            $plan_next_month = $data['planning_month'] == $last_month ? '0' : $plan_next_month;
            $plan_next_month_value = $data['planning_month'] == $last_month ? '0' : $plan_next_month;
            $data['planning_month'] = $data['planning_month'] == $last_month ? Carbon::createFromFormat('F Y', $this->filters['planning_month'])->startOfMonth()->format("Y-m-d") : $data['planning_month'];
        }

        return [
            $data['order_id'] ?? '',
            isset($data['planning_month']) ? \Carbon\Carbon::parse($data['planning_month'])->format('F Y') : '',
            $data['getBranch']['branch_name'] ?? '',
            $data['getProduct']['categories']['category_name'] ?? '',
            $data['getProduct']['subcategories']['subcategory_name'] ?? '',
            $data['getProduct']['sap_code'] ?? '',
            $data['getProduct']['product_name'] ?? '',
            $data->primarySale->month_1,
            $data->primarySale->month_2,
            $data->primarySale->month_3,
            $data->primarySale->month_4,
            $data->primarySale->month_5,
            $data->primarySale->month_6,
            $data->primarySale->month_7,
            $data->primarySale->month_8,
            $data->primarySale->month_9,
            $data->primarySale->month_10,
            $data->primarySale->month_11,
            $data->primarySale->month_12,
            $data->primarySale->min,
            $data->primarySale->max,
            $data->primarySale->avg,
            (string) $open_order_stock ?? 0,
            (string) $openderValue ?? 0,
            $plan_next_month,
            round($plan_next_month_value,2),
            $data['created_by'] ?? '',
            $data['verify_by']  ?? '',
            isset($data['created_at']) ? \Carbon\Carbon::parse($data['created_at'])->format('d-m-Y H:i:s') : '',
        ];
    }
    

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestDataRow();
                $lastColumn = $sheet->getHighestDataColumn();

                // Merge Cells for Headers
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:D2');
                $sheet->mergeCells('E1:E2');
                $sheet->mergeCells('F1:F2');
                $sheet->mergeCells('G1:G2');
                $sheet->mergeCells('H1:V1');
                $sheet->mergeCells('W1:X1');
                $sheet->mergeCells('Y1:Z1');
                $sheet->mergeCells('AA1:AA2');
                $sheet->mergeCells('AB1:AB2');
                $sheet->mergeCells('AC1:AC2');
                // Style for First Row (Header)
                $firstRowRange = 'A1:' . $lastColumn . '2';
                $sheet->getRowDimension(1)->setRowHeight(40); // Increase Header Row Height
                $sheet->getRowDimension(2)->setRowHeight(40);
                $sheet->getStyle($firstRowRange)->getAlignment()->setWrapText(true);
                $sheet->getStyle($firstRowRange)->getFont()->setSize(14);

                $event->sheet->getStyle($firstRowRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '00aadb'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Apply Text Alignment and Border to All Rows
                $allRowsRange = 'A1:' . $lastColumn . $lastRow;
                $event->sheet->getStyle($allRowsRange)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Center text
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Center vertically
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Set Row Height for All Rows
                for ($i = 3; $i <= $lastRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(30); // Increase row height for better readability
                }
            },
        ];
    }

}
