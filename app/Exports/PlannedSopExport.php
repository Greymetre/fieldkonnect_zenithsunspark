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
use App\Models\Product;

class PlannedSopExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
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
             "Order Id" , "Month","Branch Name","Division Name", "Group Name",  "Item Name" , "Product Code", "Product Desc." ,  "Opening stock as on 1st (Qty)", "Curent Stk","S&OP Plan for Next running month (M+1) (Qty.)" ,  "Budget for the month (Qty.)", "LM Sale (Qty.)" , "L3M Avg Sale (Qty.)" ,"LY same month sale (Qty.)" ,  "SKU Unit Price" , "S&OP Val_L (Unit Price *Qty.)"  , "TOP 20 SKU for the Branch (*)","Dispatch against Plan" , "Pending against Plan",   "Created By" , "Created At"
        ];
    }


    public function map($data): array
    {
        $result = (int) ($data['plan_next_month'] ?? 0) - (int) ($data['dispatch_against_plan'] ?? 0);
        $current_opening_stock = 0;
        if(isset($data['product_id']) && isset($data['branch_id'])){
            $product = Product::find($data['product_id']);
            $current_opening_stock = getCurrentOpeningStk($product , $data['branch_id']);
        }
        return [
            $data['order_id'] ?? '',
            isset($data['planning_month']) ? \Carbon\Carbon::parse($data->planning_month)->format('F Y') : '',
            $data['getBranch']['branch_name'] ?? '',
            $data['getProduct']['categories']['category_name'] ?? '',
            $data['getProduct']['subcategories']['subcategory_name'] ?? '',
            $data['getProduct']['product_name'] ?? '',
            $data['getProduct']['product_code'] ?? '',
            $data['getProduct']['description'] ?? '',
            $data['opening_stock'] ?? '',
            $current_opening_stock == 0 ? "0" : $current_opening_stock,
            $data['plan_next_month'] ?? '',
            $data['budget_for_month'] ?? 0,
            $data['last_month_sale'] ?? 0,
            $data['last_three_month_avg'] ?? 0,
            $data['last_year_month_sale'] ?? 0,
            $data['sku_unit_price'] ?? '',
            $data['s_op_val'] ?? '',
            $data['top_sku'] ?? '',
            isset($data['dispatch_against_plan']) 
            ? ($data['dispatch_against_plan'] == 0 ? "0" : $data['dispatch_against_plan']) 
            : '',
            $result === 0 ? "0" : $result,
            $data['created_by'] ?? '',
            isset($data['created_at']) ? cretaDateForFront($data['created_at']) :  ''
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
