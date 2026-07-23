<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Division;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\{Address, City, ParentDetail, TransactionHistory, Redemption, MobileUserLoginDetails, State};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class LoyaltySummaryReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->state_id = $request->input('state_id');
    }

    public function collection()
    {
        $userid = !empty($userid) ? $userid : Auth::user()->id;
        $userinfo = User::where('id', '=', $userid)->first();
        if ($this->state_id && !empty($this->state_id)) {
            $data = State::where('id', $this->state_id);
        } else if (!$userinfo->hasRole('superadmin') && !$userinfo->hasRole('Admin') && !$userinfo->hasRole('Sub_Admin') && !$userinfo->hasRole('HR_Admin') && !$userinfo->hasRole('HO_Account')  && !$userinfo->hasRole('Sub_Support') && !$userinfo->hasRole('Accounts Order') && !$userinfo->hasRole('Service Admin') && !$userinfo->hasRole('All Customers')) {
            $state_ids = City::whereIn('id', auth()->user()->cities->pluck('city_id'))->pluck('state_id');
            $data = State::whereIn('id', $state_ids)->orderBy('id', 'asc');
        } else {
            $data = State::orderBy('id', 'asc');
        }
        return $data->limit(5000)->latest()->get();
    }

    public function headings(): array
    {

        return ['Branch', 'Total Retailer Registred Nos', 'Total Retailer Under Saarthi Nos', 'Coupon Scan Nos', 'Mobile App Donwload Nos', 'Provision Point', 'Active Point','March 31 2024', 'Total Point', 'Redeem Gift', 'Redeem Neft', 'Total Redeem', 'Balance Active Point'];
    }

    public function map($data): array
    {

        $userids = getUsersReportingToAuth();
        $usersIds = User::where('branch_id', $data['id'])->whereIn('id', $userids)->pluck('id')->toArray();
        $customerIds = Customers::with('customeraddress', 'getemployeedetail.employee_detail')->whereHas('getemployeedetail.employee_detail', function ($q) {
            $q->where('division_id', '10');
        })->whereHas('customeraddress', function ($q) use ($data) {
            $q->where('state_id', $data->id);
        })->where(['customertype' => '2', 'active' => 'Y'])->pluck('id');
        
        $total_retailers = $customerIds->count();
        if($total_retailers <= 0){
            return [];
        }
        $nosOfRetailerRegistredSaarthi = TransactionHistory::whereIn('customer_id', $customerIds)
            ->select('customer_id')
            ->groupBy('customer_id')
            ->havingRaw('COUNT(*) > 1 OR SUM(CASE WHEN scheme_id IS NOT NULL THEN 1 ELSE 0 END) > 0')
            ->count();

        $coupon_scan_nos = TransactionHistory::whereIn('customer_id', $customerIds)
        ->select('customer_id')
        ->whereNotNull('scheme_id')
        ->count();
        $mobile_app_downloads = MobileUserLoginDetails::whereIn('customer_id', $customerIds)->count();
        
        $active_point = 0;
        $provision_point = 0;
        $thistorys = TransactionHistory::whereIn('customer_id', $customerIds)->whereNotNull('scheme_id')->get();
        foreach ($thistorys as $thistory) {
            if ($thistory->status == '1') {
                $active_point += $thistory->point;
            } else {
                $active_point += $thistory->active_point;
                $provision_point += $thistory->provision_point;
            }
        }
        $total_point = TransactionHistory::whereIn('customer_id', $customerIds)->sum('point');
        $total_point_old = TransactionHistory::whereIn('customer_id', $customerIds)->whereNull('scheme_id')->sum('point');
        $redeem_gift = Redemption::with('customer')->where('status', '!=', '2')->whereIn('customer_id', $customerIds)->where('redeem_mode', '1')->sum('redeem_amount');
        $redeem_neft = Redemption::with('customer')->where('status', '!=', '2')->whereIn('customer_id', $customerIds)->where('redeem_mode', '2')->sum('redeem_amount');
        $total_redeem = $redeem_gift + $redeem_neft;
        $balance_active_point = $total_point - $total_redeem;

        
        return [
            $data['state_name'],
            $data['total_registered_retailers'] = $total_retailers,
            $data['total_retailers_under_saarthi'] = $nosOfRetailerRegistredSaarthi,
            $data['coupon_scan_nos'] = $coupon_scan_nos,
            $data['mobile_app_downloads'] = $mobile_app_downloads,
            $data['provision_point'] = $provision_point,
            $data['active_point'] = $active_point,
            $data['total_point_old'] = $total_point_old,
            $data['total_point'] = $total_point,
            $data['redeem_gift'] = $redeem_gift,
            $data['redeem_neft'] = $redeem_neft,
            $data['total_redeem'] = $total_redeem,
            $data['balance_active_point'] = $balance_active_point,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                // $event->sheet->mergeCells('A' . $lastRow.':b' . $lastRow);
                $sheet = $event->sheet->getDelegate();

                $event->sheet->getStyle('A' . $lastRow . ':' . $sheet->getHighestDataColumn().$lastRow)->applyFromArray([
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
                $event->sheet->setCellValue('A' . $lastRow, 'Total');
                $event->sheet->setCellValue('B' . $lastRow, '=SUM(B2:B' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('C' . $lastRow, '=SUM(C2:C' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('D' . $lastRow, '=SUM(D2:D' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('E' . $lastRow, '=SUM(E2:E' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('F' . $lastRow, '=SUM(F2:F' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('G' . $lastRow, '=SUM(G2:G' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('H' . $lastRow, '=SUM(H2:H' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('I' . $lastRow, '=SUM(I2:I' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('J' . $lastRow, '=SUM(J2:J' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('K' . $lastRow, '=SUM(K2:K' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('L' . $lastRow, '=SUM(L2:L' . ($lastRow - 2) . ')');
                $event->sheet->setCellValue('M' . $lastRow, '=SUM(M2:M' . ($lastRow - 2) . ')');

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

                $event->sheet->getStyle('A1:' . $lastColumn . '' . $lastRow-1)->applyFromArray([
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
