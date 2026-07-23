<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\PrimarySales;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use DB;

class TopDealerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
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
        $currentDate = Carbon::now();
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $query = PrimarySales::select(
            'dealer',
            'customer_id',
            'final_branch',
            'city',
            DB::raw('SUM(net_amount) as total_net_amounts'),
            DB::raw('GROUP_CONCAT(net_amount) as net_amounts'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(invoice_date) as invoice_dates'),
        );

        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $userids = getUsersReportingToAuth();
            $customer_ids = Customers::whereIn('executive_id', $userids)->orWhereIn('created_by', $userids)->pluck('id');
            $query->whereIn('customer_id', $customer_ids);
        }

        if ($this->month && is_array($this->month) && count($this->month) > 0 && $this->financial_year && !empty($this->financial_year)) {
            $f_year_array = explode('-', $this->financial_year);

            // Determine if months are in Jan-Mar and set the correct year
            $isJanToMar = in_array('Jan', $this->month) || in_array('Feb', $this->month) || in_array('Mar', $this->month);
            $currentYear = $isJanToMar ? $f_year_array[1] : $f_year_array[0];

            // Get the first and last months from the array
            $firstMonth = $this->month[0];
            $lastMonth = $this->month[count($this->month) - 1];

            // Format the month and create start and end dates
            $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$firstMonth")->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$lastMonth")->endOfMonth();

            // Convert to date strings
            $startDateFormatted = $startDate->toDateString();
            $endDateFormatted = $endDate->toDateString();

            // Apply the date range to the query
            $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->where('invoice_date', '>=', $startDateFormatted)
                    ->where('invoice_date', '<=', $endDateFormatted);
            });
        } elseif ($this->financial_year && $this->financial_year != '' && $this->financial_year != null) {
            $f_year_array = explode('-', $this->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->whereBetween('invoice_date', [$financial_year_start, $financial_year_end]);
        } else {
            $currentDate = Carbon::now();
            $startDatethree = $currentDate->copy()->subMonthsNoOverflow(3)->firstOfMonth()->format('Y-m-d');
            $endDatethree = $currentDate->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d');
            $query->whereBetween('invoice_date', [$startDatethree, $endDatethree]);
        }

        if ($this->branch_id && $this->branch_id != '' && $this->branch_id != null) {
            $query->where('final_branch', $this->branch_id);
        }

        if ($this->division_id && $this->division_id != '' && count($this->division_id) > 0) {
            $query->whereIn('division', $this->division_id);
        }

        if ($this->dealer_id && $this->dealer_id != '' && $this->dealer_id != null) {
            $query->where('dealer', 'like', '%' . $this->dealer_id . '%');
        }

        if ($this->product_model && $this->product_model != '' && $this->product_model != null) {
            $query->where('model_name', $this->product_model);
        }

        if ($this->new_group && $this->new_group != '' && $this->new_group != null) {
            $query->where('new_group', $this->new_group);
        }

        if ($this->executive_id && $this->executive_id != '' && $this->executive_id != null) {
            $query->where('sales_person', $this->executive_id);
        }

        $query = $query->groupBy('dealer', 'customer_id', 'final_branch', 'city')->orderBy('total_net_amounts', 'desc');
        return $query->get();
    }

    public function headings(): array
    {
        $label1 = [
            'Dealer Name',
            'City',
            'Final Branch Name',
        ];

        if ($this->month && is_array($this->month) && count($this->month) > 0 && $this->financial_year && !empty($this->financial_year)) {
            $f_year_array = explode('-', $this->financial_year);

            // Determine if months are in Jan-Mar and set the correct year
            $isJanToMar = in_array('Jan', $this->month) || in_array('Feb', $this->month) || in_array('Mar', $this->month);
            $currentYear = $isJanToMar ? $f_year_array[1] : $f_year_array[0];

            // Get the first and last months from the array
            $firstMonth = $this->month[0];
            $lastMonth = $this->month[count($this->month) - 1];

            // Format the month and create start and end dates
            $startDate = Carbon::createFromFormat('Y-M', "$currentYear-$firstMonth")->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-M', "$currentYear-$lastMonth")->endOfMonth();

            // Convert to date strings
            $startDateFormatted = $startDate->toDateString();
            $endDateFormatted = $endDate->toDateString();

            $startDate = Carbon::createFromFormat('Y-m-d', $startDateFormatted);
            $endDate = Carbon::createFromFormat('Y-m-d', $endDateFormatted);
            $today = Carbon::today();

            if ($endDate->greaterThan($today)) {
                $endDate = $today;
            }
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $monthName = $currentDate->format('F');
                if (!in_array($monthName, $this->months)) {
                    $this->months[] = $monthName;
                }
                $currentDate->addMonth()->startOfMonth();
            }
        } elseif ($this->financial_year && $this->financial_year != '' && $this->financial_year != null) {
            $f_year_array = explode('-', $this->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $startDate = Carbon::createFromFormat('Y-m-d', $financial_year_start);
            $endDate = Carbon::createFromFormat('Y-m-d', $financial_year_end);
            $today = Carbon::today();

            if ($endDate->greaterThan($today)) {
                $endDate = $today;
            }
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $monthName = $currentDate->format('F');
                if (!in_array($monthName, $this->months)) {
                    $this->months[] = $monthName;
                }
                $currentDate->addMonth()->startOfMonth();
            }
        } else {
            $currentDate = Carbon::now();
            $startDatethree = $currentDate->copy()->subMonthsNoOverflow(3)->firstOfMonth()->format('Y-m-d');
            $endDatethree = $currentDate->copy()->subMonthNoOverflow()->endOfMonth()->format('Y-m-d');
            $startDate = Carbon::createFromFormat('Y-m-d', $startDatethree);
            $endDate = Carbon::createFromFormat('Y-m-d', $endDatethree);
            $today = Carbon::today();

            if ($endDate->greaterThan($today)) {
                $endDate = $today;
            }
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                $monthName = $currentDate->format('F');
                if (!in_array($monthName, $this->months)) {
                    $this->months[] = $monthName;
                }
                $currentDate->addMonth()->startOfMonth();
            }
        }

        $label2 = [];

        foreach ($this->months as $key => $value) {
            $label2[] = $value;
        }

        $label3 = [
            'T-SALE',
            'AVR SALE',
            'PROJ SALE',
            'SLAB',
            'Proj SLAB'
        ];

        $headings = array_merge($label1, $label2, $label3);
        return $headings;
    }

    public function map($data): array
    {
        $invoice_dates = explode(',', $data->invoice_dates);
        $net_amounts = explode(',', $data->net_amounts);
        $response[0] = $data->dealer;
        $response[1] = $data->city;
        $response[2] = $data->final_branch;
        $indx = 0;
        foreach ($this->months as $k => $val) {
            $tsale = 0;
            foreach ($invoice_dates as $key => $value) {
                $invDate = Carbon::createFromFormat('Y-m-d', $value);
                $currentDate = $invDate->copy();
                $monthName = $currentDate->format('F');
                if ($monthName == $val) {
                    $tsale += $net_amounts[$key];
                }
            }
            if ($tsale > 0) {
                $response[3 + $indx] = number_format(($tsale / 100000), 2, '.', '');
                $indx++;
            } else {
                $response[3 + $indx] = "0";
                $indx++;
            }
        }

        $response[3 + $indx] = $data->total_net_amounts > 0 ? number_format(($data->total_net_amounts / 100000), 2, '.', '') : "0";
        $response[4 + $indx] = $data->total_net_amounts > 0 ? number_format((($data->total_net_amounts / 100000) / count($this->months)), 2, '.', '') : "0";
        $response[5 + $indx] = $data->total_net_amounts > 0 ? (number_format((($data->total_net_amounts / 100000) / count($this->months)), 2, '.', '') * 12) : "0";

        if($data->total_net_amounts > 0){
            if($data->total_net_amounts / 100000 > 99.99){
                $response[6 + $indx] = "1Cr-5Cr";
            }elseif($data->total_net_amounts / 100000 <= 99.99 && $data->total_net_amounts / 100000 >= 75){
                $response[6 + $indx] = "75L-1Cr";
            }elseif($data->total_net_amounts / 100000 <= 74.99 && $data->total_net_amounts / 100000 >= 50){
                $response[6 + $indx] = "50L-75L";
            }elseif($data->total_net_amounts / 100000 <= 49.99 && $data->total_net_amounts / 100000 >= 25){
                $response[6 + $indx] = "25L-50L";
            }elseif($data->total_net_amounts / 100000 <= 24.99 && $data->total_net_amounts / 100000 >= 15){
                $response[6 + $indx] = "15L-25L";
            }elseif($data->total_net_amounts / 100000 <= 14.99 && $data->total_net_amounts / 100000 >= 10){
                $response[6 + $indx] = "10L-15L";
            }elseif($data->total_net_amounts / 100000 <= 9.99 && $data->total_net_amounts / 100000 >= 5){
                $response[6 + $indx] = "5L-10L";
            }elseif($data->total_net_amounts / 100000 <= 4.99){
                $response[6 + $indx] = "0L-5L";
            }
        }else{
            $response[6 + $indx] = "0L-5L";
        }

        if($data->total_net_amounts > 0){
            if(((($data->total_net_amounts / 100000) / count($this->months))*12) > 99.99){
                $response[7 + $indx] = "1Cr-5Cr";
            }elseif(((($data->total_net_amounts / 100000) / count($this->months))*12) <= 99.99 && ((($data->total_net_amounts / 100000) / count($this->months))*12) >= 75){
                $response[7 + $indx] = "75L-1Cr";
            }elseif(((($data->total_net_amounts / 100000) / count($this->months))*12) <= 74.99 && ((($data->total_net_amounts / 100000) / count($this->months))*12) >= 50){
                $response[7 + $indx] = "50L-75L";
            }elseif(((($data->total_net_amounts / 100000) / count($this->months))*12) <= 49.99 && ((($data->total_net_amounts / 100000) / count($this->months))*12) >= 25){
                $response[7 + $indx] = "25L-50L";
            }elseif(((($data->total_net_amounts / 100000) / count($this->months))*12) <= 24.99 && ((($data->total_net_amounts / 100000) / count($this->months))*12) >= 15){
                $response[7 + $indx] = "15L-25L";
            }elseif(((($data->total_net_amounts / 100000) / count($this->months))*12) <= 14.99 && ((($data->total_net_amounts / 100000) / count($this->months))*12) >= 10){
                $response[7 + $indx] = "10L-15L";
            }elseif(((($data->total_net_amounts / 100000) / count($this->months))*12) <= 9.99 && ((($data->total_net_amounts / 100000) / count($this->months))*12) >= 5){
                $response[7 + $indx] = "5L-10L";
            }elseif(((($data->total_net_amounts / 100000) / count($this->months))*12) <= 4.99){
                $response[7 + $indx] = "0L-5L";
            }
        }else{
            $response[7 + $indx] = "0L-5L";
        }

        return $response;
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
