<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Leave;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class AttendanceExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->start_date = $request->start_date;
        $this->end_date = $request->end_date;
        $this->executive_id = $request->executive_id;
        $this->active = $request->active;
        $this->status = $request->status;
        $this->division_id = $request->division_id;
    }

    public function collection()
    {
        return Attendance::with('users', 'approveReject')->where(function ($query) {

            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('user_id', getUsersReportingToAuth());
            }
            if ($this->start_date) {
                $query->whereDate('punchin_date', '>=', $this->start_date);
            }
            if ($this->end_date) {
                $query->whereDate('punchin_date', '<=', $this->end_date);
            }
            if ($this->executive_id) {
                $query->where('user_id', $this->executive_id);
            }
            if ($this->status != null && $this->status != "") {
                $query->where('attendance_status', $this->status);
            }
            if (!empty($this->active) && $this->active != null && $this->active != "") {
                $active = $this->active;
                $query->whereHas('users', function ($query) use ($active) {
                    $query->where('active', $active);
                });
            }
            if (!empty($this->division_id) && $this->division_id != null && $this->division_id != "") {
                $division_id = $this->division_id;
                $query->whereHas('users', function ($query) use ($division_id) {
                    $query->where('division_id', $division_id);
                });
            }
        })
            ->select('id', 'user_id', 'punchin_date', 'punchin_time', 'punchin_address', 'punchout_date', 'punchout_time', 'punchout_address', 'worked_time', 'punchin_summary', 'punchout_summary', 'punchin_longitude', 'punchin_latitude', 'punchout_latitude', 'punchout_longitude', 'working_type', 'attendance_status', 'remark_status', 'attendance_status','punchin_from', 'approve_reject_by')
            ->limit(5000)->latest()->get();
    }

    public function headings(): array
    {
        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('Admin')) {
            return ['id', 'Employee Code', 'user_id', 'Designation', 'Branch', 'Division', 'punchin_date', 'punchin_time', 'punchout_time', 'worked_time', 'Working Type', 'Attendance Status', 'Remark Status', 'punchin_address', 'punchout_address', 'punchin_summary', 'punchin_longitude', 'punchin_latitude', 'punchout_longitude', 'punchout_latitude', 'From', 'Approve/Reject By'];
        } else {
            return ['id', 'Employee Code', 'user_id', 'Designation', 'Branch', 'Division', 'punchin_date', 'punchin_time', 'punchout_time', 'worked_time', 'Working Type', 'Attendance Status', 'Remark Status', 'punchin_address', 'punchout_address', 'punchin_summary', 'punchin_longitude', 'punchin_latitude', 'punchout_longitude', 'punchout_latitude'];
        }
    }

    public function map($data): array
    {

        $status = '';
        if ($data['attendance_status'] == '0') {
            $status = 'Pending';
        } elseif ($data['attendance_status'] == '1') {
            $status = 'Approved';
        } else {
            $status = 'Rejected';
        }
        if(in_array($data['working_type'], ['Full Day Leave', 'Second Half Leave','First Half Leave'])) {
            $leave_details = Leave::where('user_id', $data['user_id'])->where('from_date', '<=', $data['punchin_date'])->where('to_date', '>=', $data['punchin_date'])->first();
        }

        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('Admin')) {
            return [
                $data['id'],
                isset($data['users']['employee_codes']) ? $data['users']['employee_codes'] : '',
                isset($data['users']['name']) ? $data['users']['name'] : '',
                isset($data['users']['getdesignation']['designation_name']) ? $data['users']['getdesignation']['designation_name'] : '',
                isset($data['users']['getbranch']['branch_name']) ? $data['users']['getbranch']['branch_name'] : '',
                isset($data['users']['getdivision']['division_name']) ? $data['users']['getdivision']['division_name'] : '',

                $data['punchin_date'],
                $data['punchin_time'],
                isset($data['punchout_time']) ? $data['punchout_time'] : 'misspunch',
                $data['worked_time'],
                isset($data['working_type']) ? $data['working_type'] . (isset($leave_details) && $leave_details ? ' - ' . $leave_details['bal_type'] : '') : '',
                $status,
                $data['remark_status'],

                $data['punchin_address'],

                $data['punchout_address'],
                $data['punchin_summary'],
                isset($data['punchin_longitude']) ? $data['punchin_longitude'] : '',
                isset($data['punchin_latitude']) ? $data['punchin_latitude'] : '',
                isset($data['punchout_longitude']) ? $data['punchout_longitude'] : '',
                isset($data['punchout_latitude']) ? $data['punchout_latitude'] : '',
                $data['punchin_from'],
                isset($data['approveReject']) ? $data['approveReject']['name'] : '',
            ];
        } else {
            return [
                $data['id'],
                isset($data['users']['employee_codes']) ? $data['users']['employee_codes'] : '',
                isset($data['users']['name']) ? $data['users']['name'] : '',
                isset($data['users']['getdesignation']['designation_name']) ? $data['users']['getdesignation']['designation_name'] : '',
                isset($data['users']['getbranch']['branch_name']) ? $data['users']['getbranch']['branch_name'] : '',
                isset($data['users']['getdivision']['division_name']) ? $data['users']['getdivision']['division_name'] : '',

                $data['punchin_date'],
                $data['punchin_time'],
                isset($data['punchout_time']) ? $data['punchout_time'] : 'misspunch',
                $data['worked_time'],
                isset($data['working_type']) ? $data['working_type'] . (isset($leave_details) && $leave_details ? ' - ' . $leave_details['bal_type'] : '') : '',
                $status,
                $data['remark_status'],

                $data['punchin_address'],

                $data['punchout_address'],
                $data['punchin_summary'],
                isset($data['punchin_longitude']) ? $data['punchin_longitude'] : '',
                isset($data['punchin_latitude']) ? $data['punchin_latitude'] : '',
                isset($data['punchout_longitude']) ? $data['punchout_longitude'] : '',
                isset($data['punchout_latitude']) ? $data['punchout_latitude'] : '',

            ];
        }
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
