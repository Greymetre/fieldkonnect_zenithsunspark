<?php

namespace App\Exports;

use App\Models\Marketing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MarketingMasterExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping, WithEvents
{

    /**
     * Constructor
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = Marketing::with('createdByName');
        
        if ($this->request->state) {
            $data->where('state', $this->request->state);
        }

        if ($this->request->district) {
            $data->where('event_district', $this->request->district);
        }

        if ($this->request->event_under) {
            $data->where('event_under_name', $this->request->event_under);
        }

        if ($this->request->branch) {
            $data->where('branch', $this->request->branch);
        }

        if ($this->request->event_center) {
            $data->where('event_center', $this->request->event_center);
        }

        if ($this->request->category_of_participant) {
            $data->where('category_of_participant', $this->request->category_of_participant);
        }

        if ($this->request->branding_team_member != null && $this->request->branding_team_member != '') {
            $data->where('branding_team_member', $this->request->branding_team_member);
        }
        if ($this->request->created_by != null && $this->request->created_by != '') {
            $data->where('created_by', $this->request->created_by);
        }

        if ($this->request->start_date) {
            $data->where('event_date', '>=', $this->request->start_date);
        }

        if ($this->request->end_date) {
            $data->where('event_date', '<=', $this->request->end_date);
        }

        
        return $data->get();
    }

    public function headings(): array
    {
        return ['Event Date','Event Center','Place of Participant','Event District','State','Event Under Dealer','Event Under Type','Branch','Division','TM/ ASM Name Responsible for Event','Branding Team Member','Name of Participant','Category of Participant','Mob. No. of Participant','Google Drive Photo Link','No. of Participant','Create Date','Create By', 'id'];
    }

    public function map($data): array
    {
        return [
            !empty($data['event_date']) ? $data['event_date'] : '' ,
            !empty($data['event_center']) ? $data['event_center'] : '' ,
            !empty($data['place_of_participant']) ? $data['place_of_participant'] : '' ,
            !empty($data['event_district']) ? $data['event_district'] : '' ,
            !empty($data['state']) ? $data['state'] : '' ,
            !empty($data['event_under_name']) ? $data['event_under_name'] : '' ,
            !empty($data['event_under_type']) ? $data['event_under_type'] : '' ,
            !empty($data['branch']) ? $data['branch'] : '' ,
            !empty($data['division']) ? $data['division'] : '' ,
            !empty($data['responsible_for_event']) ? $data['responsible_for_event'] : '' ,
            !empty($data['branding_team_member']) ? $data['branding_team_member'] : '' ,
            !empty($data['name_of_participant']) ? $data['name_of_participant'] : '' ,
            !empty($data['category_of_participant']) ? $data['category_of_participant'] : '' ,
            !empty($data['mob_no_of_participant']) ? $data['mob_no_of_participant'] : '' ,
            !empty($data['google_drivelink']) ? $data['google_drivelink'] : '' ,
            !empty($data['count_of_participant']) ? $data['count_of_participant'] : '' ,
            !empty($data['created_at']) ? date('d-M-Y', strtotime($data['created_at'])) : '' ,
            !empty($data['created_by']) ? $data['createdByName']['name'] : '' ,
            !empty($data['id']) ? $data['id'] : '' ,

        
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
