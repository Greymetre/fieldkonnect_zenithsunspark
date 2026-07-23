<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\DamageEntry;
use App\Models\OrderDetails;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;


class DamageEntriesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->status = $request->input('status');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
        $this->designation_id = $request->input('designation_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
    }

    public function collection()
    {

        $query = DamageEntry::with('customer', 'scheme', 'scheme_details', 'createdbyname');
        // if ($this->status != '' && $this->status != NULL) {
        //     $query->where('status', $this->status);
        // }
        if ($this->designation_id && $this->designation_id != '' && $this->designation_id != NULL) {
            $query->where('designation_id', $this->designation_id);
        }
        if ($this->division_id && $this->division_id != '' && $this->division_id != NULL) {
            $query->where('division_id', $this->division_id);
        }
        if ($this->branch_id && $this->branch_id != '' && $this->branch_id != NULL) {
            $query->where('branch_id', $this->branch_id);
        }
        if ($this->start_date && !empty($this->start_date) && $this->end_date && !empty($this->end_date)) {
            $query->whereDate('created_at', '>=', $this->start_date)
                ->whereDate('created_at', '<=', $this->end_date);
        }
        $query = $query->latest()->get();

        return $query;
    }

    public function headings(): array
    {
        return ['Date', 'Firm Name', 'Contact Person', 'Parent Name', 'Mobile Number', 'Copoun Code', 'Status', 'Remark'];
    }

    public function map($data): array
    {
        $parents = '';
        if (!empty($data->customer->getparentdetail)) {
            foreach ($data->customer->getparentdetail as $key => $parent_data) {
                if ($key == (count($data->customer->getemployeedetail) - 1)) {
                    $parents .= isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name : '';
                } else {
                    $parents .= isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name . ', ' : '';
                }
            }
        }
        if($data->status == "0"){
            $status = 'Pennding';
        }elseif($data->status == "1"){
            $status = 'Approved';
        }elseif($data->status == "2"){
            $status = 'Reject';
        }
        return [
            isset($data->created_at) ? showdatetimeformat($data->created_at) : '',
            $data['customer']['name'] ?? '',
            ($data['customer']['first_name'] ?? '') . ' ' . ($data['customer']['last_name'] ?? ''),
            $parents,
            $data['customer']['mobile'] ?? '',
            $data['coupon_code'] ?? '',
            $status,
            $data['remark'] ?? '',
        ];
        
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $event->sheet->mergeCells('A' . $lastRow . ':F' . $lastRow);

                $event->sheet->getStyle('A1:AA1')->applyFromArray([
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

                $event->sheet->getStyle('A' . $lastRow . ':AA' . $lastRow)->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'], // Border color
                        ],
                    ],
                ]);
            },
        ];
    }
}
