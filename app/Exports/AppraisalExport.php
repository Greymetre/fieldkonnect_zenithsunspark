<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AppraisalExport implements WithHeadings, WithStyles, FromCollection, WithEvents
{
    protected $headings;
    protected $data;
    protected $cons_count;

    public function __construct(array $data, $headings, $cons_count)
    { 
        $this->data = $data;
        $this->headings = $headings;
        $this->cons_count = $cons_count;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function styles(Worksheet $sheet)
    {
        // Apply styles to the header row
        $sheet->getStyle('A1:ZZ1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach (range('A', 'Z') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        foreach (range('A', 'Z') as $firstLetter) {
            foreach (range('A', 'Z') as $secondLetter) {
                $column = $firstLetter . $secondLetter;
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                              
            },
        ];
    }
}
