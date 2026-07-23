<?php

namespace App\Exports;

use App\Models\ParentDetail;
use App\Models\PrimarySales;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use DB;
use Spatie\Permission\Models\Role;

class PrimarySalesExport implements FromCollection, WithHeadings,WithMapping, ShouldAutoSize, WithEvents
{

    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->branch_id = $request->input('branch_id');
        $this->division_id = $request->input('division');
        $this->dealer_id = $request->input('dealer_id');
        $this->product_model = $request->input('product_model');
        $this->new_group = $request->input('new_group');
        $this->executive_id = $request->input('executive_id');
        $this->financial_year = $request->input('financial_year');
        $this->month = $request->input('month');
        $this->months = [];
        $this->t_data = '';
    }

    public function collection()
    {
        $query = PrimarySales::query();

        if ($this->user_id && $this->user_id != '' && $this->user_id != null) {
            $usersIds = User::where('id', $this->user_id)->where('sales_type', 'Secondary')->pluck('id');
        } else {
            $usersIds = User::with('attendance_details')->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($this->branch_id && $this->branch_id != '' && $this->branch_id != null) {
            $query->where('final_branch', $this->branch_id);
        }

        $role = Role::find(29);
        if ($role && auth()->user()->hasRole($role->name)) {
            $child_customer = ParentDetail::where('parent_id', auth()->user()->customerid)
                ->pluck('customer_id')
                ->push(auth()->user()->customerid);
            $query->whereIn('customer_id', $child_customer);
        }

        if ($this->division_id && $this->division_id != '' && count($this->division_id) > 0) {
            $query->whereIn('division', $this->division_id);
        }

        if ($this->dealer_id && $this->dealer_id != '' && $this->dealer_id != null) {
            $query->where('dealer', 'like', '%' . $this->dealer_id . '%');
        }

        if ($this->product_model && $this->product_model != '' && $this->product_model != null) {
            $query->where('product_name', $this->product_model);
        }

        if ($this->new_group && $this->new_group != '' && $this->new_group != null) {
            $query->where('new_group', $this->new_group);
        }

        if ($this->executive_id && $this->executive_id != '' && $this->executive_id != null) {
            $query->where('sales_person', $this->executive_id);
        }

        if ($this->financial_year && $this->financial_year != '' && $this->financial_year != null) {
            $f_year_array = explode('-', $this->financial_year);

            $startDateFormatted = $f_year_array[0] . '-04-01';
            $endDateFormatted = $f_year_array[1] . '-03-31';
        }

        if ($this->month && $this->month != '' && $this->month != null && $this->financial_year && $this->financial_year != '' && $this->financial_year != null) {

            $f_year_array = explode('-', $this->financial_year);
            if (array_intersect($this->month, ['Jan', 'Feb', 'Mar'])) {
                $currentYear = $f_year_array[1];
                $monthNumbers = array_map(function($month) {
                    return Carbon::parse($month)->month;
                }, $this->month);
            
                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);
            
                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $startDateFormatted = $firstDate->toDateString();
                $endDateFormatted = $lastDate->toDateString();
            } else {
                $currentYear = $f_year_array[0];
                $monthNumbers = array_map(function($month) {
                    return Carbon::parse($month)->month;
                }, $this->month);
            
                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);
            
                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $startDateFormatted = $firstDate->toDateString();
                $endDateFormatted = $lastDate->toDateString();
            }

        }

        $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
            $q->where('invoice_date', '>=', $startDateFormatted)
                ->where('invoice_date', '<=', $endDateFormatted);;
        });
        
        $query = $query->latest()->get(); 
        
        return $query;
    }

    public function headings(): array
    {
        return [
            'id',
            'Invoice No',  
            'Invoice Date',
            'Month',
            'DIV',
            'BP Code',
            'Dealer',
            'City',
            'State',
            'Final Branch',
            'Branch ID',
            'Sales person',
            'Emp Code',
            'Model Name',
            'Product Sap Code',
            'Product Name',
            'Quantity',
            'Rate',
            'Net Amount',
            'Tax %',
            'CGST Amt',
            'SGST Amt',
            'IGST Amt',
            'Total',
            'Store Name',
            'Group',
            'Branch',
            'New Group Name',
            'Product ID',
            'Customer Id',
            'New Product',
            'New Dealer',
            'group_1',
            'group_2',
            'group_3',
            'group_4',
            'Delete This',   
        ];
    }

    public function map($data): array
    {
        return[
            $data['id'],
            $data['invoiceno'],
            date('Y/m/d', strtotime($data['invoice_date'])),
            $data['month'],
            $data['division'],
            $data['bp_code'],
            $data['dealer'],
            $data['city'],
            $data['state'],
            $data['final_branch'],
            $data['branch_id'],
            $data['sales_person'],
            $data['emp_code'],
            $data['model_name'],
            $data['sap_code'],
            $data['product_name'],
            $data['quantity'],
            $data['rate'],
            $data['net_amount'],
            $data['tax_amount'],
            $data['cgst_amount'],
            $data['sgst_amount'],
            $data['igst_amount'],
            (string)$data['total_amount'],
            $data['store_name'],
            $data['new_group'],
            $data['branch'],
            $data['new_group_name'],
            $data['product_id'],
            $data['customer_id'],
            $data['new_product'],
            $data['new_dealer'],
            $data['group_1'],
            $data['group_2'],
            $data['group_3'],
            $data['group_4'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();
             
                $event->sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray([
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