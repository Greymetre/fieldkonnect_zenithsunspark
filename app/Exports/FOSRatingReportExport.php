<?php

namespace App\Exports;

use App\Models\City;
use App\Models\Customers;
use App\Models\District;
use App\Models\EmployeeDetail;
use App\Models\MobileUserLoginDetails;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Redemption;
use App\Models\TransactionHistory;
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
use Excel;


class FOSRatingReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
        $this->designation_id = $request->input('designation_id');
        $this->division_id = $request->input('division_id');
        $this->branch_id = $request->input('branch_id');
        $this->month = $request->input('month');
        $this->srno = 0;
    }

    public function collection()
    {
        $user_ids = getUsersReportingToAuth();
        $query = User::with('reportinginfo', 'getbranch', 'getdivision', 'getdesignation', 'all_attendance_details', 'visits', 'customers', 'userinfo', 'cities');
        if ($this->user_id && $this->user_id != '' && $this->user_id != NULL) {
            $query->where('id', $this->user_id);
        } else {
            $query->whereIn('id', $user_ids);
        }
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
            $start_date = $this->start_date;
            $end_date = $this->end_date;
            $query->whereHas('userinfo', function ($query) use ($end_date, $start_date) {
                $query->where('date_of_joining', '>=', $start_date)
                    ->where('date_of_joining', '<=', $end_date);
            });
        }
        $query = $query->where('sales_type', 'Secondary')->latest()->get();

        if ($this->month && !empty($this->month)) {
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;
            $month = intval($this->month);
            if ($month != $currentMonth) {
                $lastDate = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth()->toDateString();
            }
        } else {
            $lastDate = Carbon::now()->toDateString();
        }

        $query = $query->map(function ($query) use ($lastDate) {
            $working_days = $query->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
            $order_value = Order::where('created_by', $query->id)->where('order_date', '<=', $lastDate)->sum('sub_total');
            $sale_index = $working_days ? (($order_value / 100000) / $working_days) * 100 : 0;
            $registered_retailers = Customers::where(['customertype' => '2', 'created_by' => $query->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->count();
            $registration_index = $working_days ? (($registered_retailers / $working_days) / 5) * 100 : 0;
            $total_visit = $query->visits->where('checkin_date', '<=', $lastDate)->count();
            $visit_index = $working_days ? (($total_visit / $working_days) / 10) * 100 : 0;
            $sharthi_customer = TransactionHistory::where('created_at', '<=', $lastDate . ' 23:59:59')->groupBy('customer_id')->pluck('customer_id')->toArray();
            $activation_retailers = Customers::where(['customertype' => '2', 'created_by' => $query->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->whereIn('id', $sharthi_customer)->count();
            $activation_index = $working_days ? (($activation_retailers / $working_days) / 5) * 100 : 0;
            $query->performance_rating = ($sale_index * 0.5) + ($registration_index * 0.1) + ($visit_index * 0.1) + ($activation_index * 0.3);
            return $query;
        })->sortByDesc('performance_rating');

        return $query;
    }

    public function headings(): array
    {
        return ['S No', 'Emp Code', 'FOS Name', 'Area Of Operation (Districts Covered)', 'Date of Appointment', 'Yesterday Productivity vs Visit', 'Yesterday Retailer Order Count', 'Yesterday Retailer Order Value in Lacs', 'Yesterday Counter Visit', 'Weekly Visit Count (Last 7 Day)', 'Order Value Jun\'24 (in Lacs After 35%)', 'Total Retailer Registred Fieldkonnect Jun\'24 (Nos)', 'Total Retailer Visited Fieldkonnect Jun\'24 (Nos)', 'Total New Retailer (First_TimeOrder) (Nos)', 'Total Order Value in Lacs After 35%', 'Total No of Field working days till date', 'Average Per day sale', 'Sale Index', 'Total Retailer Registred Till Date Fieldkonnect (Nos)', 'Average No of RetailersRegistered /day', 'Registration Index', 'Total Visited Till Date Fieldkonnect (Nos)', 'Average No of Retailers visited /day', 'Visit Index', 'Total Retailer Registered under Saarthi (Nos)', 'Average No of Retailers Registered under Sarathi / day', 'Activation Index', 'Total New Retailer  Activated (Order) (Nos)', 'Performance Rating', 'Total Mobile App Downloaded', 'Total Active Retailer', 'Total Coupon Scan Count', 'Total Points', 'Total Unique Redemption', 'Total Redemption Value', 'Last Week Performance Rating'];
    }

    public function map($query): array
    {
        if ($this->month && !empty($this->month)) {
            $currentYear = Carbon::now()->year;
            $month = intval($this->month);
            $firstDate = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth()->toDateString();
            $lastDate = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth()->toDateString();
            $yesterday = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth()->subDay()->toDateString();
        } else {
            $firstDate = Carbon::now()->startOfMonth()->toDateString();
            $lastDate = Carbon::now()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();
        }
        $currentDate = Carbon::parse($lastDate);
        $dateBeforeSixDays = $currentDate->subDays(6)->toDateString();
        $dis_ids = City::whereIn('id', $query->cities->pluck('city_id')->toArray())->pluck('district_id');
        $retailers = Customers::where('customertype', '2')->pluck('id');
        $order_counts = Order::where(['order_date' => $yesterday, 'created_by' => $query->id])->whereIn('buyer_id', $retailers)->count('id');
        $order_value = Order::where(['order_date' => $yesterday, 'created_by' => $query->id])->whereIn('buyer_id', $retailers)->sum('sub_total');
        $yesterday_visit = $query->visits->where('checkin_date', $yesterday)->count('id');
        $weekly_visit = $query->visits->where('checkin_date', '>=', $dateBeforeSixDays)->where('checkin_date', '<=', $lastDate)->count('id');
        if ($order_counts < 1) {
            $yesterday_productivity_visit = "0.00";
        } else {
            $productvity = number_format((($order_counts / $yesterday_visit) * 100), 2);
            $yesterday_productivity_visit = $productvity;
        }
        $month_order_value = Order::where('order_date', '>=', $firstDate)->where('order_date', '<=', $lastDate)->where('created_by', $query->id)->sum('sub_total');
        $month_registered_retailers = Customers::where('created_at', '>=', $firstDate)->where(['customertype' => '2', 'created_by' => $query->id])->count('id');
        $total_registered_retailers = Customers::where('created_at', '>=', $firstDate)->where(['customertype' => '2', 'created_by' => $query->id])->count('id');
        $month_visit = $query->visits->where('checkin_date', '>=', $firstDate)->where('checkin_date', '<=', $lastDate)->whereIn('customer_id', $retailers)->count('id');
        $month_order_unique = Order::where('order_date', '>=', $firstDate)->where('order_date', '<=', $lastDate)->where('order_date', '<=', $lastDate)->where('created_by', $query->id)->whereIn('buyer_id', $retailers)->count('id');
        $total_order_unique = Order::where('created_by', $query->id)->where('order_date', '<=', $lastDate)->groupBy('buyer_id')->count('id');
        $total_order_value = Order::where('created_by', $query->id)->where('order_date', '<=', $lastDate)->sum('sub_total');
        $working_days = $query->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('punchin_date', '<=', $lastDate)->count();
        $lastw_working_days = $query->all_attendance_details->whereNotIn('working_type', ['Office Work', 'Full Day Leave', 'Leave', 'Holiday'])->where('created_at', '<=', $dateBeforeSixDays)->count();
        $lastw_total_order_value = Order::where('created_by', $query->id)->where('order_date', '<=', $dateBeforeSixDays)->sum('sub_total');
        $lastw_total_registered_retailers = Customers::where(['customertype' => '2', 'created_by' => $query->id])->where('created_at', '<=', $dateBeforeSixDays)->count('id');

        $sharthi_customer = TransactionHistory::where('created_at', '<=', $lastDate . ' 23:59:59')->groupBy('customer_id')->pluck('customer_id')->toArray();
        $registred_sharthi_retailers = Customers::where(['customertype' => '2', 'created_by' => $query->id])->where('created_at', '<=', $lastDate . ' 23:59:59')->whereIn('id', $sharthi_customer)->count('id');
        $lastw_registred_sharthi_retailers = Customers::where(['customertype' => '2', 'created_by' => $query->id])->whereIn('id', $sharthi_customer)->where('created_at', '<=', $dateBeforeSixDays)->count('id');

        $sale_index = ($total_order_value > 0 && $working_days > 0) ? number_format(((($total_order_value / 100000) / $working_days) * 100), 0, '.', '') : "0.00";
        $registration_index = ($total_registered_retailers > 0 && $working_days > 0) ? number_format(((($total_registered_retailers / $working_days) / 5) * 100), 0, '.', '') : "0.00";
        $visit_index = ($query->visits->count() > 0 && $working_days > 0) ? number_format(((($query->visits->count() / $working_days) / 10) * 100), 0, '.', '') : "0.00";
        $activation_index = ($registred_sharthi_retailers > 0 && $working_days > 0) ? number_format(((($registred_sharthi_retailers / $working_days) / 5) * 100), 0, '.', '') : "0.00";

        $lastw_sale_index = ($lastw_total_order_value > 0 && $lastw_working_days > 0) ? number_format(((($lastw_total_order_value / 100000) / $lastw_working_days) * 100), 2, '.', '') : "0.00";
        $lastw_registration_index = $lastw_total_registered_retailers > 0 ? number_format(((($lastw_total_registered_retailers / $lastw_working_days) / 5) * 100), 2, '.', '') : "0.00";
        $lastw_visit_index = $query->visits->where('checkin_date', '<=', $dateBeforeSixDays)->count() > 0 ? ($lastw_working_days > 0 ? number_format(((($query->visits->where('checkin_date', '<=', $dateBeforeSixDays)->count() / $lastw_working_days) / 10) * 100), 2, '.', '') : "0.00") : "0.00";
        $lastw_activation_index = ($lastw_registred_sharthi_retailers > 0 && $lastw_working_days > 0) ? number_format(((($lastw_registred_sharthi_retailers / $lastw_working_days) / 5) * 100), 2, '.', '') : "0.00";

        $lastw_performance_rating = ((($lastw_sale_index * 0.5) + ($lastw_registration_index * 0.1) + ($lastw_visit_index * 0.1) + ($lastw_activation_index * 0.3)));

        $total_assign_customer_ids = EmployeeDetail::where('user_id', $query->id)->pluck('customer_id');
        $app_download = MobileUserLoginDetails::whereIn('customer_id', $total_assign_customer_ids)->where('first_login_date', '<=', $lastDate)->count('id');
        $active_customer = TransactionHistory::where('created_at', '<=', $lastDate . ' 23:59:59')->whereIn('customer_id', $total_assign_customer_ids)->groupBy('customer_id')->count('customer_id');
        $copoun_scan = TransactionHistory::where('created_at', '<=', $lastDate . ' 23:59:59')->whereIn('customer_id', $total_assign_customer_ids)->count('customer_id');
        $total_points = TransactionHistory::where('created_at', '<=', $lastDate . ' 23:59:59')->whereIn('customer_id', $total_assign_customer_ids)->sum('point');
        $unique_redemption_count = Redemption::whereIn('customer_id', $total_assign_customer_ids)->groupBy('customer_id')->count('customer_id');
        $redemption_points = Redemption::where('created_at', '<=', $lastDate . ' 23:59:59')->whereIn('customer_id', $total_assign_customer_ids)->sum('redeem_amount');
        return [
            ++$this->srno,
            $query['employee_codes'] ?? '',
            $query['name'] ?? '',
            implode(", ", District::whereIn('id', $dis_ids)->pluck('district_name')->toArray()),
            date('d M y', strtotime($query->userinfo->date_of_joining)),
            $yesterday_productivity_visit,
            $order_counts > 0 ? $order_counts : "0",
            $order_value > 0 ? number_format(($order_value / 100000), 0, '.', '') : "0.00",
            $yesterday_visit > 0 ? $yesterday_visit : "0",
            $weekly_visit > 0 ? $weekly_visit : "0",
            $month_order_value > 0 ? number_format(($month_order_value / 100000), 0, '.', '') : "0.00",
            $month_registered_retailers > 0 ? $month_registered_retailers : "0",
            $month_visit > 0 ? $month_visit : "0",
            $month_order_unique > 0 ? $month_order_unique : "0",
            $total_order_value > 0 ? number_format(($total_order_value / 100000), 0, '.', '') : "0.00",
            $working_days > 0 ? $working_days : "0",
            ($total_order_value > 0 && $working_days > 0) ? number_format((($total_order_value / 100000) / $working_days), 0, '.', '') : "0.00",
            $sale_index,
            $total_registered_retailers > 0 ? $total_registered_retailers : "0",
            ($total_registered_retailers > 0 && $working_days > 0) ? number_format(($total_registered_retailers / $working_days), 0, '.', '') : "0.00",
            $registration_index,
            $query->visits->count(),
            ($query->visits->count() && $working_days > 0) > 0 ? number_format(($query->visits->count() / $working_days), 0, '.', '') : "0.00",
            $visit_index,
            $registred_sharthi_retailers > 0 ? $registred_sharthi_retailers : "0",
            ($registred_sharthi_retailers > 0 && $working_days > 0) ? number_format(($registred_sharthi_retailers / $working_days), 0, '.', '') : "0.00",
            $activation_index,
            $total_order_unique > 0 ? $total_order_unique : "0",
            number_format($query->performance_rating, 2, '.', ''),
            $app_download > 0 ? $app_download : "0",
            $active_customer > 0 ? $active_customer : "0",
            $copoun_scan > 0 ? $copoun_scan : "0",
            $total_points > 0 ? $total_points : "0",
            $unique_redemption_count > 0 ? $unique_redemption_count : "0",
            $redemption_points > 0 ? $redemption_points : "0",
            $lastw_performance_rating > 0 ?  number_format($lastw_performance_rating, 2, '.', '') : "0",
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();
                $rowCount = $event->sheet->getHighestDataRow();
                $event->sheet->mergeCells('A' . $lastRow . ':E' . $lastRow);

                for ($row = 1; $row <= $rowCount; $row++) {
                    $cellValue = $event->sheet->getCell('AC' . $row)->getValue();
                    $color = self::getColorBasedOnValue($cellValue);

                    $event->sheet->getStyle('AC' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $color],
                        ],
                    ]);
                    $event->sheet->getStyle('C' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $color],
                        ],
                    ]);
                }

                $event->sheet->getStyle('A1:AJ1')->applyFromArray([
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

                $event->sheet->getStyle('A' . $lastRow . ':AJ' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'], // Border color
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
                $event->sheet->setCellValue('A' . $lastRow, 'Total');
                $event->sheet->setCellValue('F' . $lastRow, '=SUM(F3:F' . ($lastRow - 2) . ')/' . ($rowCount - 1));
                $event->sheet->setCellValue('G' . $lastRow, '=SUM(G3:G' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('H' . $lastRow, '=SUM(H3:H' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('I' . $lastRow, '=SUM(I3:I' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('J' . $lastRow, '=SUM(J3:J' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('K' . $lastRow, '=SUM(K3:K' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('L' . $lastRow, '=SUM(L3:L' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('M' . $lastRow, '=SUM(M3:M' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('N' . $lastRow, '=SUM(N3:N' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('O' . $lastRow, '=SUM(O3:O' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('P' . $lastRow, '=SUM(P3:P' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('Q' . $lastRow, '=SUM(Q3:Q' . ($lastRow - 2) . ')/' . ($rowCount - 1));
                $event->sheet->setCellValue('R' . $lastRow, '=SUM(R3:R' . ($lastRow - 2) . ')/' . ($rowCount - 1));
                $event->sheet->setCellValue('S' . $lastRow, '=SUM(S3:S' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('T' . $lastRow, '=SUM(T3:T' . ($lastRow - 2) . ')/' . ($rowCount - 1));
                $event->sheet->setCellValue('U' . $lastRow, '=SUM(U3:U' . ($lastRow - 2) . ')/' . ($rowCount - 1));
                $event->sheet->setCellValue('V' . $lastRow, '=SUM(V3:V' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('W' . $lastRow, '=SUM(W3:W' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('X' . $lastRow, '=SUM(X3:X' . ($lastRow - 2) . ')/' . ($rowCount - 1));
                $event->sheet->setCellValue('Y' . $lastRow, '=SUM(Y3:Y' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('Z' . $lastRow, '=SUM(Z3:Z' . ($lastRow - 2) . ')/' . ($rowCount - 1));
                $event->sheet->setCellValue('AA' . $lastRow, '=SUM(AA3:AA' . ($lastRow - 2) . ')/' . ($rowCount - 1));
                $event->sheet->setCellValue('AB' . $lastRow, '=SUM(AB3:AB' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('AC' . $lastRow, '=SUM(AC3:AC' . ($lastRow - 2) . ')/' . ($rowCount - 1));
                $event->sheet->setCellValue('AD' . $lastRow, '=SUM(AD3:AD' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('AE' . $lastRow, '=SUM(AE3:AE' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('AF' . $lastRow, '=SUM(AF3:AF' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('AG' . $lastRow, '=SUM(AG3:AG' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('AH' . $lastRow, '=SUM(AH3:AH' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('AI' . $lastRow, '=SUM(AI3:AI' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('AJ' . $lastRow, '=SUM(AJ3:AJ' . ($lastRow - 2) . ')');
            },
        ];
    }

    private static function getColorBasedOnValue($value)
    {
        if ($value <= 24.99) {
            return 'FF0000'; // Red
        } elseif ($value >= 25 && $value <= 29.99) {
            return 'FFFF00'; // Yellow
        } elseif ($value >= 29.99) {
            return '00FF00'; // Green
        }
    }
}
