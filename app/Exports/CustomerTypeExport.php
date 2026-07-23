<?php

namespace App\Exports;

use App\Models\CustomerType;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerTypeExport implements FromCollection, WithHeadings, WithStyles, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return CustomerType::all();
    }

    /**
     * Define static headings.
     * 
     * @return array
     */
    public function headings(): array
    {
        // Manually define the column headings
        return [
            'ID',
            'active',
            'Customer Type name',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * Map the data to the respective columns.
     * 
     * @param mixed $customerType
     * @return array
     */
    public function map($customerType): array
    {
        return [
            $customerType->id,
            $customerType->active,
            $customerType->customertype_name,
            $customerType->created_at->format('Y-m-d H:i:s'),
            $customerType->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Apply styles to the heading row.
     * 
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Apply background color and text color to the heading row
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'color' => ['rgb' => 'FFFFFF'], // White text color
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '336677'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // Set all text to be aligned left for the whole sheet
        $sheet->getStyle($sheet->calculateWorksheetDimension())->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        return [];
    }
}
