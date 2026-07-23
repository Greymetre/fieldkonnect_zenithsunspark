<?php

namespace App\Exports;

use App\Models\EndUser;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class EndUserExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->designation_id = $request->input('designation_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
    }

    public function collection()
    {

        $query = EndUser::with('pincodeDetails', 'state', 'district', 'city');
        
        if ($this->designation_id && $this->designation_id != '' && $this->designation_id != NULL) {
            $query->where('designation_id', $this->designation_id);
        }
        if ($this->division_id && $this->division_id != '' && $this->division_id != NULL) {
            $query->where('division_id', $this->division_id);
        }
        if ($this->branch_id && $this->branch_id != '' && $this->branch_id != NULL) {
            $query->where('branch_id', $this->branch_id);
        }
        if ($this->startdate && $this->startdate != '' && $this->startdate != NULL && $this->enddate && $this->enddate != '' && $this->enddate != NULL) {
            
            $startDate = date('Y-m-d', strtotime($this->startdate));
            $endDate = date('Y-m-d', strtotime($this->enddate));
            $query = $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        }
        
        $query = $query->latest()->get();

        return $query;
    }

    public function headings(): array
    {
        return ['Customer Name', 'Email', 'Mobile Number', 'Address', 'Place', 'State', 'District', 'City', 'Pincode', 'Created Date'];
    }

    public function map($data): array
    {
        
        return [
            $data['customer_name'] ?? '',
            $data['customer_email'] ?? '',
            $data['customer_number'] ?? '',
            $data['customer_address'] ?? '',
            $data['customer_place'] ?? '',
            $data['state'] ? $data['state']['state_name'] : $data['customer_state'],
            $data['district'] ? $data['district']['district_name'] : $data['customer_district'],
            $data['city'] ? $data['city']['city_name'] : $data['customer_city'],
            $data['pincodeDetails'] ? $data['pincodeDetails']['pincode'] : $data['customer_pindcode'],
            $data['created_at'] ? date('d M Y', strtotime($data['created_at'])) : '',
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
