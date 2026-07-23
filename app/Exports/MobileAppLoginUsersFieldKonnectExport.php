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

class MobileAppLoginUsersFieldKonnectExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{

    private $rowIndex = 3;

    public function __construct($request)
    {
        $this->user_id = $request->input('user');
        $this->month = $request->input('month');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
        $this->financial_year = $request->input('financial_year');
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);

        $data = MobileUserLoginDetails::with(['user'])->where('app', '2');

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

        $headings = ['S. No.', 'User ID', 'User Name', 'Mobile Number', 'Branch', 'App Version', 'Device Name', 'First Login date', 'Last Login date', 'Login Status'];



        return $headings;
    }

    public function map($data): array
    {
        
        return [
            $data['id'],
            $data['user']['id'],
            isset($data['user']['name']) ? $data['user']['name'] : '',
            isset($data['user']['mobile']) ? $data['user']['mobile'] : '',
            isset($data['user']['getbranch']['branch_name']) ? $data['user']['getbranch']['branch_name'] : '',
            isset($data['app_version']) ? $data['app_version'] : '',
            isset($data['device_name']) ? $data['device_name'] : '',
            isset($data['first_login_date']) ? date('Y-m-d', strtotime($data['first_login_date'])) : '',
            isset($data['last_login_date']) ? date('Y-m-d', strtotime($data['last_login_date'])) : '',
            isset($data['login_status']) ? ($data['login_status'] == '0' ? 'Logout' : 'Login') : '',
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
