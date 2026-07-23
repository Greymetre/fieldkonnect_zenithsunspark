<?php

namespace App\Exports;

use App\Models\OpeningStock;
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

class OpeningStockExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    protected $filters;

    public function __construct(Request $request)
    {
        $this->filters = $request->all();
    }

   public function collection()
    {
        //  $data = OpeningStock::with([
        //     'branch',
        //     'product.subcategories',
        //     'warehouse',
        // ])->latest()->get() // Fetch all data first
        // ->groupBy(function ($item) {
        //     return $item->item_code . '-' . $item->item_description . '-' . $item->item_group . '-' . $item->opening_stocks . '-' . $item->open_order_qty;
        // })
        // ->map(function ($group) {
        //     // Extract unique branch IDs and convert them into a comma-separated string
        //     $branchIds = $group->pluck('branch_id')->unique()->implode(',');

        //     // Take the first item and update its branch_id field
        //     $firstItem = $group->first();
        //     $firstItem->branch_id = $branchIds;

        //     return $firstItem;
        // })
        // ->values(); // Reset indexes

        // return $data;
        $data = OpeningStock::with([
            'warehouse',
        ])->latest()->newQuery();

        return $data->latest()->get();
    }

    public function headings(): array
    {
        return [
             "Itm_Code" ,"Itm_Desc" , "Itm_Grp_Name" , "WareHouse_Name" , "Branch_Id" ,"InStock_Qty"
        ];
    }


    public function map($data): array
    {
        return [
            $data['item_code'] ?? '',
            $data['item_description'] ?? '',
            $data['item_group'] ?? '',
            $data['ware_house_name'] ?? '',
            $data['branch_id'] ?? '',
            isset($data['opening_stocks']) ? (string) $data['opening_stocks'] : '0',
            // isset($data['open_order_qty']) ? (string) $data['open_order_qty'] : '0',
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
