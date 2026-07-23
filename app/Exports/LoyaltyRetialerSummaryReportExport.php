<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Division;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\{ParentDetail, TransactionHistory, Redemption, MobileUserLoginDetails};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class LoyaltyRetialerSummaryReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->branch_id = $request->input('branch_id');
        $this->dealer_id = $request->input('dealer_id');
        $this->pageSize = $request->input('pageSize');
        $this->page = $request->input('page');
    }

    public function collection()
    {
        DB::statement("SET SESSION group_concat_max_len = 100000");

        $retailers_sarthi = TransactionHistory::groupBy('customer_id')->havingRaw('COUNT(*) > 1 OR SUM(CASE WHEN scheme_id IS NOT NULL THEN 1 ELSE 0 END) > 0')->pluck('customer_id');
        $userids = getUsersReportingToAuth();

        $query = Customers::with([
            'customertypes',
            'createdbyname.getbranch',
            'customeraddress.cityname',
            'customeraddress.statename',
            'getparentdetail.parent_detail',
            'getemployeedetail.employee_detail',
            'transactions' => function ($q) {
                $q->select('customer_id', 'status', 'point', 'active_point', 'provision_point', 'scheme_id');
            },
            'redemptions' => function ($q) {
                $q->select('customer_id', 'status', 'redeem_mode', 'redeem_amount')
                    ->whereNot('status', '2');
            }
        ])
            ->whereHas('getemployeedetail.employee_detail', function ($q) {
                $q->where('division_id', '10');
            })
            ->where(['customertype' => '2', 'active' => 'Y'])
            ->whereIn('id', $retailers_sarthi);

        if ($this->branch_id) {
            $userIdsss = User::where('branch_id', $this->branch_id)
                ->whereIn('id', $userids)
                ->pluck('id');

            $query->where(function ($q) use ($userIdsss) {
                $q->whereIn('executive_id', $userIdsss)
                    ->orWhereIn('created_by', $userIdsss);
            });
        } else {
            if (
                !auth()->user()->hasRole('superadmin') &&
                !auth()->user()->hasRole('Admin') &&
                !auth()->user()->hasRole('Sub_Admin')
            ) {
                $query->where(function ($q) use ($userids) {
                    $q->whereIn('executive_id', $userids)
                        ->orWhereIn('created_by', $userids);
                });
            }
        }

        if ($this->dealer_id) {
            $query->whereHas('getparentdetail', function ($q) {
                $q->where('parent_id', $this->dealer_id);
            });
        }
        return $query->orderBy('id', 'asc')->get();
    }


    public function headings(): array
    {

        return [
            'Branch',
            'Retailer Id',
            'Retailer Name',
            'Distributor/Dealer Name',
            'State',
            'City',
            'Coupon Scan Nos',
            'Provision Point',
            'Active Point',
            'March 31 2024',
            'Total Point',
            'Redeem Gift',
            'Redeem Neft',
            'Total Redeem',
            'Balance Active Point'
        ];
    }




    public function map($data): array
    {
        // Get parent details
        $all_parents = collect($data->getparentdetail)
            ->map(fn($value) => $value->parent_detail->name ?? '')
            ->filter()
            ->implode(', ');

        // if($data['id'] == 804){
        //     dd($data);
        // }

        // Preloaded transaction data
        $coupon_scan_nos = $data->transactions->whereNotNull('scheme_id')->count() ?? 0;
        $total_points = $data->transactions->sum('point') ?? 0;
        $total_points_old = $data->transactions->whereNull('scheme_id')->sum('point') ?? 0;
        $active_points = $data->transactions->whereNotNull('scheme_id')->where('status', '1')->sum('point') ?? 0;
        $active_points += $data->transactions->whereNotNull('scheme_id')->where('status', '0')->sum('active_point') ?? 0;
        $provision_points = $data->transactions->whereNotNull('scheme_id')->where('status', '0')->sum('provision_point') ?? 0;


        // Preloaded redemption data
        $redeem_gift = $data->redemptions->where('redeem_mode', '1')->sum('redeem_amount') ?? '0';
        $redeem_neft = $data->redemptions->where('redeem_mode', '2')->sum('redeem_amount') ?? '0';
        $total_redemption = $data->redemptions->sum('redeem_amount') ?? '0';
        $total_balance = (int)$total_points - (int)$total_redemption;

        return [
            $data['createdbyname']['getbranch']['branch_name'] ?? '',
            $data['id'] ?? '',
            $data['name'] ?? '',
            $all_parents,
            $data['customeraddress']['statename']['state_name'] ?? '',
            $data['customeraddress']['cityname']['city_name'] ?? '',
            $coupon_scan_nos,
            $provision_points,
            $active_points,
            $total_points_old,
            $total_points,
            $redeem_gift,
            $redeem_neft,
            $total_redemption,
            $total_balance,
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
            },
        ];
    }
}
