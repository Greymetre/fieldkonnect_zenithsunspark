<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\NewJoining;
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

class NewJoiningExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
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

        $query = NewJoining::with('branch_details', 'department_details', 'designation_details');
        
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
        return [['Date', 'Email', 'First Name', 'Middle Name', 'Last Name', 'Gender', 'Date Of birth', 'Mobile Number', 'Emergency Contact Number', 'Father Name', 'Father Occupation', 'Mother Name', 'Mother Occupation', 'Marital Status', 'Spouse\'s Name', 'Spouse\'s DOB', 'Spouse\'s Education', 'Spouse\'s Occupation', 'Anniversary', 'Present Address', 'Permanent Address', 'PAN Number', 'Adhar Number', 'Driving Licence', 'Blood Group', 'Language', ' ', ' ', ' ', 'Other Language', 'Qualification', 'Experience', 'skill', 'Occupy', 'Branch', 'Department', 'date_of_joining', 'designation'], ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'English', 'Hindi', 'Gujarati', 'Other', '', '', '', '', '', '', '', '', '']];
    }

    public function map($data): array
    {
        $lagns = json_decode($data['language']);
        foreach ($lagns as $k => $lang) {
            if (is_array($lang)) {
                $mappedArray[$k] = array_map(function ($item) {
                    if ($item == 's') {
                        return 'Speak';
                    } elseif ($item == 'w') {
                        return 'Write';
                    } elseif ($item == 'r') {
                        return 'Read';
                    } else {
                        return '';
                    }
                }, $lang);
            } else {
                $mappedArray[$k] = [];
            }
        }
        return [
            $data['created_at'] ? date('d M Y', strtotime($data['created_at'])) : '',
            $data['email'] ?? '',
            $data['first_name'] ?? '',
            $data['middle_name'] ?? '',
            $data['last_name'] ?? '',
            $data['gender'] ?? '',
            $data['dob'] ? date('d M Y', strtotime($data['dob'])) : '',
            $data['mobile_number'] ?? '',
            $data['contact_number'] ?? '',
            $data['father_name'] ?? '',
            $data['father_occupation'] ?? '',
            $data['mother_name'] ?? '',
            $data['mother_occupation'] ?? '',
            $data['marital_status'] ?? '',
            $data['spouse_name'] ?? '',
            $data['spouse_dob'] ? date('d M Y', strtotime($data['spouse_dob'])) : '',
            $data['spouse_education'] ?? '',
            $data['spouse_occupation'] ?? '',
            $data['anniversary'] ? date('d M Y', strtotime($data['anniversary'])) : '',
            $data['present_address'] . ' ' . $data['present_city'] . ' ' . $data['present_state'] . ' ' . $data['present_pincode'],
            $data['permanent_address'] . ' ' . $data['permanent_city'] . ' ' . $data['permanent_state'] . ' ' . $data['permanent_pincode'],
            $data['pan'] ?? '',
            $data['aadhar'] ?? '',
            $data['driving_licence'] ?? '',
            $data['blood_group'] ?? '',
            count($mappedArray['english']) > 0 ? implode(',', $mappedArray['english']) : 'Nothing',
            count($mappedArray['hindi']) > 0 ? implode(',', $mappedArray['hindi']) : 'Nothing',
            count($mappedArray['gujarati']) > 0 ? implode(',', $mappedArray['gujarati']) : 'Nothing',
            count($mappedArray['other']) > 0 ? implode(',', $mappedArray['other']) : 'Nothing',
            $data['other_language'] ?? '',
            $data['qualification'] ?? '',
            $data['experience'] ?? '',
            $data['skill'] ?? '',
            $data['occupy'] ?? '',
            $data['branch_details']['branch_name'] ?? '',
            $data['department_details']['name'] ?? '',
            $data['date_of_joining'] ? date('d M Y', strtotime($data['date_of_joining'])) : '',
            $data['designation_details']['designation_name'] ?? '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 1;

                $event->sheet->mergeCells('Z1:AC1');
                $columns = range('A', 'Y');

                foreach ($columns as $column) {
                    $event->sheet->mergeCells($column . '1:' . $column . '2');
                }
                $event->sheet->mergeCells('AD1:AD2');
                $event->sheet->mergeCells('AE1:AE2');
                $event->sheet->mergeCells('AF1:AF2');
                $event->sheet->mergeCells('AG1:AG2');
                $event->sheet->mergeCells('AH1:AH2');
                $event->sheet->mergeCells('AI1:AI2');
                $event->sheet->mergeCells('AJ1:AJ2');
                $event->sheet->mergeCells('AK1:AK2');
                $event->sheet->mergeCells('AL1:AL2');


                $event->sheet->getStyle('A1:AL2')->applyFromArray([
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

                $event->sheet->getStyle('A3:AL' . ($lastRow - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'], // Border color
                        ],
                    ],
                ]);
            },
        ];
    }
}
