<?php

namespace App\Exports;

use App\Models\CheckIn;
use App\Models\LeadCheckIn;
use App\Models\OrderDetails;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class LeadCheckinExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->user_id = $request->input('user_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
        $this->userids = getUsersReportingToAuth();
    }
    public function collection()
    {
        return LeadCheckIn::with('users:id,name', 'lead:id,company_name,lead_source', 'lead.address')->where(function ($query) {
            if ($this->user_id) {
                $query->where('user_id', $this->user_id);
            } elseif (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Sub_Admin') && !Auth::user()->hasRole('HR_Admin')) {
                $query->whereIn('user_id', $this->userids);
            }
            if ($this->division_id && $this->division_id != null && $this->division_id != '') {
                $query->whereHas('users', function ($query) {
                    $query->where('division_id', $this->division_id);
                });
            }
            if ($this->branch_id && $this->branch_id != null && $this->branch_id != '') {
                $query->whereHas('users', function ($query) {
                    $query->where('branch_id', $this->branch_id);
                });
            }
            if ($this->startdate) {
                $query->whereDate('checkin_date', '>=', $this->startdate);
            }
            if ($this->enddate) {
                $query->whereDate('checkin_date', '<=', $this->enddate);
            }
        })
            ->select('id', 'lead_id', 'user_id', 'checkin_date', 'checkin_time', 'checkout_date', 'checkout_time', 'time_interval', 'checkin_address', 'checkout_address', 'distance', 'checkout_note', 'created_at')
            ->latest()->get();
    }

    public function headings(): array
    {
        return ['ID', 'Visit Person Name', 'Visit Date', 'Firm Name', 'Customer Name', 'Customer Number', 'Lead Source', 'Pincode', 'City', 'District', 'State', 'Address', 'Geo Location', 'Check In Address', 'Check Out Address', 'Check In Time', 'Check Out Time', 'Spend time', 'Note'];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->users->name ?? '',
            $data->checkin_date ?? '',
            $data->lead->company_name ?? '',
            optional($data->lead?->contacts->first())->name ?? '',
            optional($data->lead?->contacts->first())->phone_number ?? '',
            $data->lead->lead_source ?? '',
            $data->lead->address?->pincodename?->pincode ?? '',
            $data->lead->address?->cityname?->city_name ?? '',
            $data->lead->address?->districtname?->district_name ?? '',
            $data->lead->address?->statename?->state_name ?? '',
            $data->lead->address?->full_address ?? '',
            '-', // Geo Location placeholder
            $data->checkin_address ?? '',
            $data->checkout_address ?? '',
            $data->checkin_time ?? '',
            $data->checkout_time ?? '',
            $data->time_interval ?? '-',
            $data->checkout_note ?? '',
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
