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

class PlannedSopTemplate implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    protected $filters;

    public function __construct(Request $request)
    {
        $this->filters = $request->all();
    }

   public function collection()
    {
        $data = PlannedSOP::with(['getProduct.subcategories' , 'getProduct.categories', 'getBranch']);
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
                    case "category_name" : 
                        $data->whereHas('getProduct.categories', function ($q) use ($value) {
                            $q->where('category_name', 'like', "%$value%");
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
            $planning_month = $formatted_date->format("Y-m-d");
            $data->whereDate('planning_month' , $planning_month);
          }catch(\Exception $e){
             $data->latest()->get();
          }
        }
        return $data->latest()->get();
    }

    public function headings(): array
    {
        return [
             "Order Id" , "S&OP Plan for Next running month (M+1) (Qty.)" ,"Dispatch against Plan" ,
        ];
    }


     public function map($data): array
    {
        // If order_id is empty, skip the row
        if (empty($data['order_id'])) {
            return [];
        }

        return [
            $data['order_id'],
            $data['plan_next_month'] ?? '',
            isset($data['dispatch_against_plan']) 
                ? ($data['dispatch_against_plan'] == 0 ? "0" : $data['dispatch_against_plan']) 
                : ''
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestDataRow();
                $lastColumn = $sheet->getHighestDataColumn();

                $firstRowRange = 'A1:' . $lastColumn . '1';
                $sheet->getRowDimension(1)->setRowHeight(25);
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

                $event->sheet->getStyle('A1:' . $lastColumn . '' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
            },
        ];
    }
}
