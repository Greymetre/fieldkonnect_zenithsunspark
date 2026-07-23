<?php

namespace App\Exports;

use App\Models\ServiceBillComplaintType;
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

class ServiceBillComplaintTypeExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    protected $user_id;
    protected $start_date;
    protected $end_date;
    protected $filters;

     public function __construct(Request $request)
    {
        $this->filters = $request->all();
    }

    public function collection()
    {
        $query = ServiceBillComplaintType::with([
            'service_complaint_reasons', 
            'service_group_complaints.subcategory'
        ]);

        if (filled($this->filters['group_name'] ?? null)) {
            $query->whereHas('service_group_complaints.subcategory', function ($subquery) {
                $subquery->where('subcategory_name', 'like', '%' . $this->filters['group_name'] . '%');
            });
        }

        if (filled($this->filters['complaint_type'] ?? null)) {
            $query->where('service_bill_complaint_type_name', 'like', '%' . $this->filters['complaint_type'] . '%');
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Product Group',
            'Complaint Type',
            'Reasons',
        ];
    }


    public function map($data): array
    {
        $complaint_reasons = !empty($data['service_complaint_reasons']) 
            ? implode(',', collect($data['service_complaint_reasons'])->pluck('service_complaint_reasons')->toArray()) 
            : '';
        return [
            $data['service_group_complaints']['subcategory']['subcategory_name'] ?? '',
            $data['service_bill_complaint_type_name'] ?? '',
            $complaint_reasons
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestDataRow();
                $lastColumn = $sheet->getHighestDataColumn();

                // Apply styles to the first row (headers)
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

                // Apply border & alignment styles to all cells
                $event->sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'wrapText' => true,
                    ],
                ]);

                // Auto-size all columns based on content length
                foreach (range('A', $lastColumn) as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // ================= MERGE GROUPS IN COLUMN A ================= //
                $colors = ['FFFF99', 'CCFFCC', 'FFCCCC', 'CCCCFF', 'FF99FF']; // Different colors for groups
                $currentColorIndex = 0;
                $previousValue = null;
                $groupStartRow = 2; // Assuming data starts from row 2

                for ($row = 2; $row <= $lastRow; $row++) {
                    $cellValue = $sheet->getCell('A' . $row)->getValue();

                    if ($cellValue !== $previousValue) {
                        // If a new group starts, finalize the previous merge if applicable
                        if ($previousValue !== null && $groupStartRow < $row - 1) {
                            $mergeRange = "A{$groupStartRow}:A" . ($row - 1);
                            $sheet->mergeCells($mergeRange);
                            $sheet->getStyle("A{$groupStartRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                        }

                        // Switch color for new group
                        $currentColorIndex = ($currentColorIndex + 1) % count($colors);
                        $groupStartRow = $row;
                    }

                    // Apply background color to the current group
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $colors[$currentColorIndex]],
                        ],
                    ]);

                    $previousValue = $cellValue;
                }

                // Merge the last group
                if ($groupStartRow < $lastRow) {
                    $mergeRange = "A{$groupStartRow}:A{$lastRow}";
                    $sheet->mergeCells($mergeRange);
                    $sheet->getStyle("A{$groupStartRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                }
            },
        ];
    }



}
