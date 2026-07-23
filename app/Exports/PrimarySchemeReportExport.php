<?php

namespace App\Exports;

use App\Models\Services;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\CustomerOutstanting;
use App\Models\PrimarySales;
use App\Models\PrimaryScheme;
use App\Models\PrimarySchemeDetail;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesTargetUsers;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PrimarySchemeReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithStyles, WithEvents
{

    private $rowIndex = 3;

    public function __construct($request)
    {
        $this->months = array();
        $this->branch_id = $request->input('branch_id');
        $this->financial_year = $request->input('financial_year');
        $this->quarter = $request->input('quarter');
        $this->scheme_id = $request->input('scheme_id');
        $this->division = $request->input('division');
        $this->types = $request->input('types');
        $this->quarter_name = '';
        $this->pSchemes = '';
    }

    public function collection()
    {
        $f_year_array = explode('-', $this->financial_year);
        $pSchemesGroups = PrimarySchemeDetail::where('primary_scheme_id', $this->scheme_id)
            ->select([DB::raw('GROUP_CONCAT(`groups`) as `groups`'), 'group_type'])
            ->groupBy('group_type')
            ->get();
        $pSchemesBranchCol = PrimaryScheme::where('id', $this->scheme_id)->groupBy('branch')->pluck('branch');
        $this->pSchemes = PrimaryScheme::where('id', $this->scheme_id)->first();
        $pSchemesBranch = $pSchemesBranchCol->flatMap(function ($item) {
            return explode(',', $item);
        })->toArray();
        if ($pSchemesGroups[0]->group_type == 'group_2') {
            $data = PrimarySales::with(['user', 'user.getdesignation', 'user.getdivision', 'branch', 'customer'])->select([
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(net_amount) as total_net_amount'),
                DB::raw('final_branch'),
                DB::raw('final_branch'),
                DB::raw('emp_code'),
                DB::raw('branch_id'),
                DB::raw('customer_id'),
                DB::raw('division'),
                DB::raw('group_2'),
                DB::raw('GROUP_CONCAT(DISTINCT new_group_name) as new_group_name'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("CEILING FAN", new_group_name) THEN quantity ELSE 0 END) as ceiling_fan_quantity'),
            ]);
        } else {
            $data = PrimarySales::with(['user', 'user.getdesignation', 'user.getdivision', 'branch', 'customer'])->select([
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(net_amount) as total_net_amount'),
                DB::raw('final_branch'),
                DB::raw('emp_code'),
                DB::raw('branch_id'),
                DB::raw('customer_id'),
                DB::raw('division'),
                DB::raw('new_group_name'),
                DB::raw('GROUP_CONCAT(DISTINCT group_4) as group_4'),
                DB::raw('SUM(CASE WHEN FIND_IN_SET("20 additional", group_4) THEN quantity ELSE 0 END) as group_4_quantity'),
            ]);
        }
        $data->where(function ($query) use ($pSchemesGroups) {
            foreach ($pSchemesGroups as $key => $value) {
                $groupsArray = explode(',', $value->groups);
                // Apply OR WHERE IN for each group type
                $query->orWhereIn($value->group_type, $groupsArray);
            }
        });

        if ($this->pSchemes->assign_to == 'branch') {
            $data->whereIn('branch_id', $pSchemesBranch);
        }

        if ($this->pSchemes->repetition == '3') {
            $data->where('invoice_date', '>=', $this->pSchemes->start_date)->where('invoice_date', '<=', $this->pSchemes->end_date);
        }

        if (auth()->user()->hasRole('Customer Dealer')) {
            $data->where('customer_id', auth()->user()->customerid);
        }

        if ($this->pSchemes->repetition == '5') {
            if ($this->quarter && !empty($this->quarter)) {
                if ($this->quarter == '1') {
                    $this->quarter_name = 'Q1';
                    $data->where(function ($query) use ($f_year_array) {
                        $query->whereYear('invoice_date', '=', $f_year_array[0])
                            ->whereIn('month', ['Apr', 'May', 'Jun']);
                    });
                    $this->months = ['Apr', 'May', 'Jun'];
                } elseif ($this->quarter == '2') {
                    $this->quarter_name = 'Q2';
                    $data->where(function ($query) use ($f_year_array) {
                        $query->whereYear('invoice_date', '=', $f_year_array[0])
                            ->whereIn('month', ['Jul', 'Aug', 'Sep']);
                    });
                    $this->months = ['Jul', 'Aug', 'Sep'];
                } elseif ($this->quarter == '3') {
                    $this->quarter_name = 'Q3';
                    $data->where(function ($query) use ($f_year_array) {
                        $query->whereYear('invoice_date', '=', $f_year_array[0])
                            ->whereIn('month', ['Oct', 'Nov', 'Dec']);
                    });
                    $this->months = ['Oct', 'Nov', 'Dec'];
                } elseif ($this->quarter == '4') {
                    $this->quarter_name = 'Q4';
                    $data->where(function ($query) use ($f_year_array) {
                        $query->whereYear('invoice_date', '=', $f_year_array[1])
                            ->whereIn('month', ['Jan', 'Feb', 'Mar']);
                    });
                    $this->months = ['Oct', 'Nov', 'Dec'];
                }
            }
        }

        if ($this->division && !empty($this->division)) {
            $data->whereIn('division', $this->division);
        }


        // dd($data->groupBy('customer_id', 'emp_code', 'final_branch', 'branch_id', 'division', 'new_group_name')->toSql(), $this->pSchemes->start_date, $this->pSchemes->end_date, $pSchemesGroups);
        if ($pSchemesGroups[0]->group_type == 'group_2') {
            $data = $data->groupBy('customer_id', 'final_branch', 'branch_id', 'division', 'group_2')->orderBy('month')->get();
        } else {
            $data = $data->groupBy('customer_id', 'final_branch', 'branch_id', 'division', 'new_group_name')->orderBy('month')->get();
        }
        return $data;
    }


    public function headings(): array
    {

        $headings = ['FY', 'Quarter', 'Div', 'Dealer', 'BP Code','Customer ID', 'City', 'State', 'Final Branch', 'Sales person', 'Emp Code', 'New Group Name', 'Sale Return Qty', 'Sale Return Value', 'Sales Quantity', 'Sales Net Amount', 'After  Sales Return Quantity', 'After Sales Return Net Amount', 'Discount (CN)', 'Scheme Name'];

        return $headings;
    }


    public function map($data): array
    {
        $pSchemesGroups = PrimarySchemeDetail::where('primary_scheme_id', $this->scheme_id)
            ->select([DB::raw('GROUP_CONCAT(`groups`) as `groups`'), 'group_type'])
            ->groupBy('group_type')
            ->get();
        if ($pSchemesGroups[0]->group_type == 'group_2') {
            if($this->pSchemes->id == 20){
                $CM = PrimarySchemeDetail::whereIn('groups', explode(',', $data['group_2']))->where('min', '<=', $data['ceiling_fan_quantity'])->where('max', '>=', $data['ceiling_fan_quantity'])->where('primary_scheme_id', $this->scheme_id)->first();
            }else{
                $CM = PrimarySchemeDetail::whereIn('groups', explode(',', $data['group_2']))->where('min', '<=', $data['total_quantity'])->where('max', '>=', $data['total_quantity'])->where('primary_scheme_id', $this->scheme_id)->first();

            }
        } else {
            if($this->pSchemes->primaryscheme_details[0]->max == '0'){
                $CM = PrimarySchemeDetail::where('groups', $data['new_group_name'])->where('slab_min', '<=', $data['total_net_amount'] / 100000)->where('slab_max', '>=', $data['total_net_amount'] / 100000)->where('primary_scheme_id', $this->scheme_id)->first();
            }else{
                $CM = PrimarySchemeDetail::where('groups', $data['new_group_name'])->where('min', '<=', $data['total_quantity'])->where('max', '>=', $data['total_quantity'])->where('primary_scheme_id', $this->scheme_id)->first();
            }

        }

        // dd($CM);

        if ($this->types && !empty($this->types)) {
            if ($this->types == 'qualified') {
                if (!$CM) {
                    return array();
                }
            } else if ($this->types == 'unqualified') {
                if ($CM) {
                    return array();
                }
            }
        }

        $response = array();
        $response[0] = $this->financial_year;
        $response[1] = $this->quarter ? 'Q' . $this->quarter : '-';
        $response[2] = $data['division'] ?? '';
        $response[3] = $data['customer'] ? $data['customer']['name'] : '-';
        $response[4] = $data['customer'] ? $data['customer']['sap_code'] : '-';
        $response[5] = $data['customer_id'] ? $data['customer_id'] : '-';
        $response[6] = data_get($data, 'customer.customeraddress.cityname.city_name', '-');
        $response[7] = data_get($data, 'customer.customeraddress.statename.state_name', '-');
        $response[8] = $data['branch'] ? $data['branch']['branch_name'] : $data['final_branch'];
        $response[9] = $data['user'] ? $data['user']['name'] : '-';
        $response[10] = $data['emp_code'] ?? '-';
        $response[11] = $data['new_group_name'] ?? '-';
        $response[12] = '-';
        $response[13] = '-';
        if($this->pSchemes->id == 20){
            $response[14] = $data['total_quantity'].' ('. $data['ceiling_fan_quantity'] .')' ?? '-';
        }else{
            $response[14] = $data['total_quantity'] ?? '-';
        }
        $response[15] = $data['total_net_amount'] ?? '-';
        $response[16] = $data['total_quantity'] ?? '-';
        $response[17] = $data['total_net_amount'] > 0 ? number_format($data['total_net_amount'] / 100000, 2, '.', '') : '-';
        if (!in_array('FAN', $this->division)) {
            $response[18] = $CM ? $CM->points . '%' : '0%';
            $response[19] = $CM ? $CM->primaryscheme->scheme_name : '-';
        } else {
            if ($this->pSchemes->scheme_type == 'gift') {
                if ($CM) {
                    if ($CM->slab_min <= $data['total_net_amount'] / 100000) {
                        $response[18] = $CM->gift;
                        $response[19] = $CM->primaryscheme->scheme_name;
                    } else {
                        $checkOthers = PrimarySchemeDetail::where('primary_scheme_id', $this->scheme_id)->where('slab_min', '<=', $response[17])->first();
                        $response[18] = $checkOthers ? $checkOthers->gift : '-';
                        $response[19] = $checkOthers ? $CM->primaryscheme->scheme_name : '-';
                    }
                } else {
                    $response[18] = '-';
                    $response[19] = '-';
                }
            } else {
                if($this->pSchemes->id == 38){
                    if($data->group_4_quantity > 0 && $data->total_quantity >= 300){
                        $additional = $data->group_4_quantity*40;
                        $remain = ($data->total_quantity-$data->group_4_quantity)*20;
                        $response[18] = $additional+$remain;
                    }else{
                        $response[18] =$CM ? ($CM->primaryscheme->per_pcs == 1 ? $CM->points * $data['total_quantity'] : $CM->points) : '0';
                        $response[19] = $CM ? ($CM->primaryscheme->per_pcs == 1 ? $CM->points * $data['total_quantity'] : $CM->points) : '0';
                    }
                }else{
                    $response[18] = $CM ? ($CM->primaryscheme->per_pcs == 1 ? $CM->points * $data['total_quantity'] : $CM->points) : '0';
                }
                $response[19] = $CM ? $CM->primaryscheme->scheme_name : '-';
            }
        }
        return $response;
    }

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle('A1:T1')->applyFromArray([
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
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();

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
