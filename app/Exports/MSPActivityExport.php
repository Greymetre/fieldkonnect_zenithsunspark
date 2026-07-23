<?php

namespace App\Exports;

use App\Models\MspActivity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class MSPActivityExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->financial_year = $request->input('financial_year');
        $this->month = $request->input('month');
        $this->branch_id = $request->input('branch_id');
        $this->dealer_id = $request->input('dealer_id');
    }

    public function collection()
    {
        $data = MspActivity::with('user', 'activityType', 'cities.city', 'customer.customer');

        if ($this->branch_id && !empty($this->branch_id)) {
            $data->whereHas('user', function ($query) {
                $query->where('branch_id', $this->branch_id);
            });
        }

        if ($this->financial_year && !empty($this->financial_year)) {
            $parts = explode('-', $this->financial_year);
            $financial_year = $parts[0] . '-' . substr($parts[1], -2);
            $data->where('fyear', $financial_year);
        }

        if ($this->month && !empty($this->month)) {
            $data->whereIn('month', $this->month);
        }

        return $data->get();
    }

    public function headings(): array
    {

        return [
            'Activity Date',
            'Activity Type',
            'Emp Code',
            'Emp Name',
            'Branch',
            'Nos Participants',
            'Activity  Location',
            'Activity Event Under'
        ];
    }




    public function map($data): array
    {
        $all_city = array();
        if(count($data->cities) > 0){
            foreach ($data->cities as $key => $value) {
                array_push($all_city, $value->city?->city_name);
            }
        }

        $all_customer = array();
        if(count($data->customer) > 0){
            foreach ($data->customer as $key => $value) {
                array_push($all_customer, $value->customer?->name);
            }
        }


        return [
            $data->activity_date ? date('d-M-Y', strtotime($data->activity_date)) : '-',
            $data->activityType ? $data->activityType->type : '-',
            $data->emp_code ? $data->emp_code : '-',
            $data->user ? $data->user->name : '-',
            $data->user ? ($data->user->getbranch?$data->user->getbranch->branch_name:'-') : '-',
            $data->msp_count ? $data->msp_count : '-',
            count($all_city) > 0 ? implode(',', $all_city) : '-',
            count($all_customer) > 0 ? implode(',', $all_customer) : '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();

                $event->sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
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
                        'startColor' => ['rgb' => '336677'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->getStyle('A2:' . $lastColumn . '' . ($lastRow - 2))->applyFromArray([
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
