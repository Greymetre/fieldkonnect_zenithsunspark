<?php

namespace App\Exports;

use App\Models\Branch;
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
use App\Models\WareHouse;
use App\Models\BranchOprningQuantity;
use App\Models\OpeningStock;
use Illuminate\Support\Facades\DB;

class PlannedSopSalePUMExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
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
        if (isset($this->filters['financial_year'])) {
            $this->year = explode('-', $this->filters['financial_year']);
            $start_date = $this->year[0] . '-04-01';
            $end_date   = $this->year[1] . '-03-31';
        }


        $data = PlannedSOP::with(['getProduct.subcategories', 'getProduct.productdetails', 'getProduct.categories', 'getBranch', 'primarySale']);
        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Sub_Admin')) {
            $data->whereRaw("FIND_IN_SET(?, view_only)", [Auth::user()->division_id]);
        }
        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Sub_Admin')) {
            $branch_ids = explode(',', Auth::user()->branch_id);
            $data->whereIn('branch_id', $branch_ids);
        }
        if (isset($start_date) && isset($end_date)) {
            $data->whereBetween('planning_month', [$start_date, $end_date]);
        }
        foreach ($this->filters as $key => $value) {
            if (isset($value)) {
                switch ($key) {
                    case "created_by":
                    case 'top_sku':
                        $data->where($key, 'like', "%$value%");
                        break;
                    case "product_name":
                    case "description":
                    case "product_code":
                        $data->whereHas('getProduct', function ($q) use ($value, $key) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                    case "branch_name":
                        $data->whereHas('getBranch', function ($q) use ($value, $key) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                    case "division_id":
                        $data->whereHas('getProduct.categories', function ($q) use ($value) {
                            $q->where('id', $value);
                        });
                        break;
                    case "group_name":
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
        if (isset($this->filters['planning_month'])) {
            try {
                $formatted_date = Carbon::createFromFormat('F Y', $this->filters['planning_month'])->startOfMonth();
                $last_month = Carbon::createFromFormat('F Y', $this->filters['planning_month'])->subMonth()->startOfMonth()->format("Y-m-d");
                $planning_month = $formatted_date->format("Y-m-d");

                // Get main month data
                $data = $data->whereDate('planning_month', $planning_month);

                // Get existing product+branch combinations from planning month
                $currentMonthCombinations = clone $data;
                $currentCombinations = $currentMonthCombinations->get(['product_id', 'branch_id'])->map(function ($item) {
                    return $item->product_id . '-' . $item->branch_id;
                });

                // Add data from last month where product_id+branch_id is NOT in current combinations
                $data = $data->orWhere(function ($sub) use ($last_month, $currentCombinations) {
                    $sub->whereDate('planning_month', $last_month)
                        ->whereNotIn(DB::raw("CONCAT(product_id, '-', branch_id)"), $currentCombinations->all());
                });
                $product_ids = Product::whereNotNull('branch_id')
                    ->where('branch_id', '!=', '')
                    ->pluck('id')
                    ->toArray();

                $openingStocks = OpeningStock::all();
                $all_product_ids = $data->pluck('product_id', 'branch_id'); // [branch_id => product_id]

                $others = $openingStocks->filter(function ($stock) use ($all_product_ids, $product_ids) {
                    $product = $stock->product();
                    if (!$product) return false;

                    $stockBranchIds = array_map('trim', explode(',', $stock->branch_id)); // e.g. [8, 44]
                    $filteredBranchIds = [];

                    foreach ($stockBranchIds as $branchId) {
                        // If this branchId-productId pair exists in $all_product_ids
                        if (
                            isset($all_product_ids[$branchId]) &&
                            $all_product_ids[$branchId] == $product->id
                        ) {
                            // skip this branchId (i.e., remove it)
                            continue;
                        }

                        $filteredBranchIds[] = $branchId;
                    }

                    // If any filtered branch IDs left, update the branch_id
                    if (count($filteredBranchIds)) {
                        $stock->branch_id = implode(',', $filteredBranchIds);
                        return in_array($product->id, $product_ids); // only return if product is allowed
                    }

                    // No branches left → skip this stock
                    return false;
                });
            } catch (\Exception $e) {
                $data->latest()->get();
            }
        }
        // dd($data->pluck('product_id'));
        if (isset($others) && !empty($others)) {
            $data = $data->latest()->get();

            $transformedOthers = $others->flatMap(function ($stock) {
                $product = $stock->product();
                $results = [];

                foreach (explode(',', $stock->branch_id) as $branch_id) {
                    $nBranch = Branch::find($branch_id);

                    $results[] = (object)[
                        'order_id'        => '-',
                        'getBranch'       => $nBranch ?? null,
                        'branch_id'       => $nBranch->id ?? null,
                        'getProduct'      => $product,
                        'opening_stock'   => $stock->opening_stocks,
                        'planning_month'  => $this->filters['planning_month'],
                    ];
                }

                return $results;
            });

            return $data->concat($transformedOthers)->values();
        }

        return $data->latest()->get();
    }

    public function headings(): array
    {
        if (isset($this->filters['financial_year'])) {
            $this->year = explode('-', $this->filters['financial_year']);
        }
        $heading1 = ["Order Id", "Branch Name", "Warehouse", "Division Name", "Group Name", "Sap Code", "Product Name", "Sale : Quantity", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "CY Opening Stock (Branch)", "", "Last Month Planing", "Open Order (Prod.)", "", "For Prodcution", "", "Planing Month", "Forecast (Sales Plan)", "", "For Prodcution", "", 'Created By', 'Verify By', 'Created At'];

        $heading2 = ["", "", "", "", "", "", ""];
        for ($i = 4; $i <= 12; $i++) {
            $month = ($i < 10 ? '0' . $i : $i) . '/' . ($this->year[0] - 1);
            array_push($heading2, $month);
        }
        for ($i = 1; $i <= 3; $i++) {
            $month = '0' . $i . '/' . ($this->year[0]);
            array_push($heading2, $month);
        }

        $heading2 = array_merge($heading2, [
            'Min',
            "Max",
            "Avg",
            'CY Stk Qty',
            'CY Value',
            "",
            'Open Order Qty',
            'Open Order Value',
            'For Prodcution qty',
            'For Prodcution Value',
            "",
            'Forecast Qty',
            'Forecast Value',
            "For Prodcution qty",
            "Forecast Value"
        ]);


        $headings = array($heading1, $heading2); // Merge arrays
        return $headings;
    }


    public function map($data): array
    {
        if (!isset($data->getBranch) ||  !$data->getBranch || $data->getBranch == null) {
            dd($data);
        }
        static $rowNumber = 3;
        $ware_house = WareHouse::find($data->getBranch->warehouse_id);
        if (isset($this->filters['financial_year'])) {
            $this->year = explode('-', $this->filters['financial_year']);
        }
        // $startYear = $this->year[0] - 1;
        // $endYear = $startYear + 1;
        // $months = [];
        // for ($m = 4; $m <= 12; $m++) {
        //     $months["$startYear-" . str_pad($m, 2, '0', STR_PAD_LEFT)] = 0;
        // }
        // for ($m = 1; $m <= 3; $m++) {
        //     $months["$endYear-" . str_pad($m, 2, '0', STR_PAD_LEFT)] = 0;
        // }


        // $salesData = PrimarySales::where(['product_id'=> $data->product_id , 'branch_id' => $data->branch_id])
        //         ->whereBetween('invoice_date', ["$startYear-04-01", "$endYear-03-31"])
        //         ->selectRaw('DATE_FORMAT(invoice_date, "%Y-%m") as month, SUM(quantity) as total_qty')
        //         ->groupBy('month')
        //         ->orderBy('month')
        //         ->pluck('total_qty', 'month')
        //         ->toArray();

        // $salesByMonth = array_merge($months, $salesData);
        // $salesValues = array_filter($salesByMonth, function ($value) {
        //     return $value > 0; // Exclude zero values
        // });
        // $min = count($salesValues) > 0 ? min($salesValues) : 0;
        // $max = count($salesValues) > 0 ? max($salesValues) : 0;
        // $avg = count($salesValues) > 0 ? round(array_sum($salesValues) / count($salesByMonth), 0) : 0;
        // $result = (int) ($data->plan_next_month ?? 0) - (int) ($data['dispatch_against_plan'] ?? 0);

        if (isset($this->filters['planning_month'])) {
            $last_month = Carbon::createFromFormat('F Y', $this->filters['planning_month'])->subMonth()->startOfMonth()->format("Y-m-d");
            $plan_next_month = $data->planning_month == $last_month ? '0' : $this->filters['planning_month'];
            $plan_next_month_value = $data->planning_month == $last_month ? '0' : $plan_next_month;
            $data->planning_month = $data->planning_month == $last_month ? Carbon::createFromFormat('F Y', $this->filters['planning_month'])->startOfMonth()->format("Y-m-d") : $data->planning_month;
        }

        $opening_stock = isset($data->opening_stock) && is_numeric($data->opening_stock)
            ? (int) $data->opening_stock
            : 0;

        $product = $data->getProduct ?? '';
        $planning_month = isset($data->planning_month)
            ? \Carbon\Carbon::parse($data->planning_month)->subMonth()->startOfMonth()->format('Y-m-d')
            : '';

        $open_order = PlannedSOP::where('product_id', $product->id)
            ->where("branch_id", $data->branch_id)
            ->whereDate('planning_month', $planning_month)
            ->first();

        $open_order_stock = isset($open_order->plan_next_month) && is_numeric($open_order->plan_next_month)
            ? (int) $open_order->plan_next_month
            : 0;

        $production_qty = isset($data->production_qty) && is_numeric($data->production_qty)
            ? (int) $data->production_qty
            : 0;


        $product_price = isset($data->getProduct->productpriceinfo->price) && is_numeric($data->getProduct->productpriceinfo->price)
            ? (float) $data->getProduct->productpriceinfo->price
            : 0;

        $new_price = ($product_price * 41) / 100;
        $product_price = $product_price - $new_price;

        $opening_stock_value = round($product_price * $opening_stock, 2);
        $openderValue        = round($product_price * $open_order_stock, 2);
        $production_value    = round($product_price * abs($production_qty), 2);
        $plan_next_month = isset($data->plan_next_month) && is_numeric($data->plan_next_month)
            ? (int) $data->plan_next_month
            : 0;
        $plan_next_month_value = round($product_price * $plan_next_month, 2);

        $last_month_pro_qty = max(($open_order_stock - $opening_stock), 0);
        $last_month_pro_qty_value = $last_month_pro_qty > 0 ? $last_month_pro_qty * $product_price : 0;

        // $current_month_pro_qty = "=IF(IF(AE{$rowNumber}+Z{$rowNumber}-W{$rowNumber}<AE{$rowNumber},AE{$rowNumber}+Z{$rowNumber}-W{$rowNumber},AE{$rowNumber})<0,0,IF(AE{$rowNumber}+Z{$rowNumber}-W{$rowNumber}<AE{$rowNumber},AE{$rowNumber}+Z{$rowNumber}-W{$rowNumber},AE{$rowNumber}))";

        $current_month_pro_qty_value  = "=AG{$rowNumber}*{$product_price}";

        $row = [
            $data->order_id ?? '',
            $data->getBranch->branch_name ?? '',
            isset($ware_house) ? $ware_house->warehouse_name : '',
            $data->getProduct->categories->category_name ?? '',
            $data->getProduct->subcategories->subcategory_name ?? '',
            $data->getProduct->sap_code ?? '',
            $data->getProduct->product_name ?? '',
            isset($data->primarySale) ? $data->primarySale->month_1 : '-',
            isset($data->primarySale) ? $data->primarySale->month_2 : '-',
            isset($data->primarySale) ? $data->primarySale->month_3 : '-',
            isset($data->primarySale) ? $data->primarySale->month_4 : '-',
            isset($data->primarySale) ? $data->primarySale->month_5 : '-',
            isset($data->primarySale) ? $data->primarySale->month_6 : '-',
            isset($data->primarySale) ? $data->primarySale->month_7 : '-',
            isset($data->primarySale) ? $data->primarySale->month_8 : '-',
            isset($data->primarySale) ? $data->primarySale->month_9 : '-',
            isset($data->primarySale) ? $data->primarySale->month_10 : '-',
            isset($data->primarySale) ? $data->primarySale->month_11 : '-',
            isset($data->primarySale) ? $data->primarySale->month_12 : '-',
            isset($data->primarySale) ? $data->primarySale->min : '-',
            isset($data->primarySale) ? $data->primarySale->max : '-',
            isset($data->primarySale) ? $data->primarySale->avg : '-',
            $opening_stock ?? "0",
            $opening_stock_value ?? "0",
            isset($data->planning_month)
                ? \Carbon\Carbon::parse($data->planning_month)->subMonth()->format('F Y')
                : '',
            (string) $open_order_stock, // Convert to string to ensure Excel displays it
            (string) $openderValue,
            (string) $last_month_pro_qty,
            (string) $last_month_pro_qty_value,
            isset($data->planning_month) ? \Carbon\Carbon::parse($data->planning_month)->format('F Y') : '',
            (string)  $plan_next_month,
            (string)  $plan_next_month_value,
            ($plan_next_month + $open_order_stock) - ($last_month_pro_qty + $opening_stock) > 0 ? ($plan_next_month + $open_order_stock) - ($last_month_pro_qty + $opening_stock) : '0',
            // $current_month_pro_qty,
            $current_month_pro_qty_value,
            $data->created_by ?? '',
            $data->verify_by  ?? '',
            isset($data->created_at) ? \Carbon\Carbon::parse($data['created_at'])->format('d-m-Y') : '',
        ];

        $rowNumber++;
        return $row;
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
                $sheet->mergeCells('Y1:Y2');
                $sheet->mergeCells('Z1:AA1');
                $sheet->mergeCells('AB1:AC1');
                $sheet->mergeCells('AD1:AD2');
                $sheet->mergeCells('AE1:AF1');
                $sheet->mergeCells('AG1:AH1');
                $sheet->mergeCells('AI1:AI2');
                $sheet->mergeCells('AJ1:AJ2');
                $sheet->mergeCells('AK1:AK2');
                // $sheet->mergeCells('AB1:AC2');
                // $sheet->mergeCells('AC1:AD1');
                // $sheet->mergeCells('AE1:AE2');
                // $sheet->mergeCells('AF1:AF2');
                // $sheet->mergeCells('AG1:AG2');


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

                $event->sheet->getStyle('Y1:Y2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000'], // Black Text Color
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'ADD8E6'], // Light Blue Color
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $event->sheet->getStyle('AD1:AD2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000'], // Black Text Color
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '90EE90'], // Light Green Background
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
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
