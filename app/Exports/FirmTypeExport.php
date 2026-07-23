<?php

namespace App\Exports;

use App\Models\FirmType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FirmTypeExport implements FromCollection, WithHeadings, WithStyles, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return FirmType::all();
    }

    public function headings(): array
    {
        // Manually define the column headings
        return [
            'ID',
            'active',
            'Firm Type name',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * Map the data to the respective columns.
     * 
     * @param mixed $firmtype
     * @return array
     */
    public function map($firmtype): array
    {
        return [
            $firmtype->id,
            $firmtype->active,
            $firmtype->firmtype_name,
            $firmtype->created_at->format('Y-m-d H:i:s'),
            $firmtype->updated_at->format('Y-m-d H:i:s'),
        ];
    }

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
