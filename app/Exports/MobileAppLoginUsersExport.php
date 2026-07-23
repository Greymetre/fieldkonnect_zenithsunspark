<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use App\Models\MobileUserLoginDetails;
use App\Models\User;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MobileAppLoginUsersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{

    private $rowIndex = 3;

    public function __construct($request)
    {
        $this->user_id = $request->input('user');
        $this->month = $request->input('month');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
        $this->financial_year = $request->input('financial_year');
        $this->designation = $request->input('designation');
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);

        $data = MobileUserLoginDetails::with(['customer'])->where('app', '1');

        if ($this->start_date && !empty($this->start_date) && $this->end_date && !empty($this->end_date)) {
            $startDate = date('Y-m-d', strtotime($this->start_date));
            $endDate = date('Y-m-d', strtotime($this->end_date));
            $data->whereDate('first_login_date', '>=', $startDate)
                ->whereDate('first_login_date', '<=', $endDate);
        }

        $data = $data->get();

        return $data;
    }


    public function headings(): array
    {

        $headings = ['S. No.', 'Customer ID', 'Firm Name', 'Contact Person', 'Mobile Number', 'Branch', 'State', 'District', 'City', 'App Version', 'Device Type', 'Device Name', 'First Login date', 'Last Login date', 'Login Status', 'User Name'];



        return $headings;
    }

    public function map($data): array
    {
        // dd($data);
        $branch_arr = array();
        if ($data->customer->getemployeedetail && !empty($data->customer->getemployeedetail) && count($data->customer->getemployeedetail) > 0) {
            foreach ($data->customer->getemployeedetail as $key_new => $datas) {
                if (isset($datas->employee_detail->getbranch->branch_name) && !in_array($datas->employee_detail->getbranch->branch_name, $branch_arr)) {
                    $branch_arr[] = $datas->employee_detail->getbranch->branch_name;
                }
            }
        }
        $all_assign_emp = array();
        if (count($data['customer']['getemployeedetail']) > 0) {
            foreach ($data['customer']['getemployeedetail'] as $key => $value) {
                if ($this->designation && !empty($this->designation) && $value->employee_detail && !empty($value->employee_detail)) {
                    if ($value->employee_detail->designation_id == $this->designation) {
                        array_push($all_assign_emp, $value->employee_detail->name);
                    }
                }
            }
        }
        return [
            $data['id'],
            $data['customer']['id'],
            isset($data['customer']['name']) ? $data['customer']['name'] : '',
            isset($data['customer']['first_name']) ? $data['customer']['first_name'] : '',
            isset($data['customer']['mobile']) ? $data['customer']['mobile'] : '',
            implode(',', $branch_arr),
            isset($data['customer']['customeraddress']) ? ($data['customer']['customeraddress']['statename'] ? $data['customer']['customeraddress']['statename']['state_name'] : '') : '',
            isset($data['customer']['customeraddress']) ? ($data['customer']['customeraddress']['districtname'] ? $data['customer']['customeraddress']['districtname']['district_name'] : '') : '',
            isset($data['customer']['customeraddress']) ? ($data['customer']['customeraddress']['cityname'] ? $data['customer']['customeraddress']['cityname']['city_name'] : '') : '',
            isset($data['app_version']) ? $data['app_version'] : '',
            isset($data['device_type']) ? $data['device_type'] : '',
            isset($data['device_name']) ? $data['device_name'] : '',
            isset($data['first_login_date']) ? date('Y-m-d', strtotime($data['first_login_date'])) : '',
            isset($data['last_login_date']) ? date('Y-m-d', strtotime($data['last_login_date'])) : '',
            isset($data['login_status']) ? ($data['login_status'] == '0' ? 'Logout' : 'Login') : '',
            count($all_assign_emp) > 0 ? implode(',', $all_assign_emp) : '-',
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
