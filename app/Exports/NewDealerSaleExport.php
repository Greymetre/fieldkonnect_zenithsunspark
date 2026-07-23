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

class NewDealerSaleExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
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
        $firstDateOfApril = Carbon::createFromDate(null, 4, 1)->startOfDay()->toDateString();
        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $userids = getUsersReportingToAuth();
            $customer_ids = Customers::whereIn('executive_id', $userids)->orWhereIn('created_by', $userids)->pluck('id');
            $new_dealers = Customers::where('creation_date', '>=', $firstDateOfApril)->whereIn('id', $customer_ids)->pluck('id');
        } else {
            $new_dealers = Customers::where('creation_date', '>=', $firstDateOfApril)->pluck('id');
        }
        DB::statement("SET SESSION group_concat_max_len = 10000000");
        $query = PrimarySales::with('customer')->select(
            'dealer',
            'final_branch',
            'city',
            'customer_id',
            'division',
            DB::raw('SUM(net_amount) as total_net_amounts'),
            DB::raw('GROUP_CONCAT(net_amount) as net_amounts'),
            DB::raw('GROUP_CONCAT(month) as months'),
            DB::raw('GROUP_CONCAT(invoice_date) as invoice_dates'),
        )->whereIn('customer_id', $new_dealers);

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

        $query = $query->groupBy('dealer', 'final_branch', 'division', 'city', 'customer_id')->orderBy('total_net_amounts', 'desc');
        return $query->get();
    }

    public function headings(): array
    {
        $label1 = [
            'Branch Name',
            'Customer Name',
            'Creation Date',
            'Customer Type',
            'Division',
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
            'Total Sale',
            'SLAB'
        ];

        $headings = array_merge($label1, $label2, $label3);
        return $headings;
    }

    public function map($data): array
    {
        $invoice_dates = explode(',', $data->invoice_dates);
        $net_amounts = explode(',', $data->net_amounts);
        $response[0] = $data->final_branch;
        $response[1] = $data->dealer;
        $response[2] = date('d M Y', strtotime($data->customer->creation_date));
        $response[3] = $data->customer->customertypes->customertype_name;
        $response[4] = $data->division;
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
                $response[5 + $indx] = number_format(($tsale / 100000), 2, '.', '');
                $indx++;
            } else {
                $response[5 + $indx] = "0";
                $indx++;
            }
        }

        $response[5 + $indx] = $data->total_net_amounts > 0 ? number_format(($data->total_net_amounts / 100000), 2, '.', '') : "0";

        $sales = number_format(($data->total_net_amounts / 100000), 2, '.', '');
        if ($sales > 0 && $sales < 2) {
            $response[6 + $indx] =  '0L-2L';
        } elseif ($sales >= 2 && $sales < 5) {
            $response[6 + $indx] =  '2L-5L';
        } elseif ($sales >= 5 && $sales < 10) {
            $response[6 + $indx] =  '5L-10L';
        } elseif ($sales >= 10 && $sales < 15) {
            $response[6 + $indx] =  '10L-15L';
        } elseif ($sales >= 15 && $sales < 25) {
            $response[6 + $indx] =  '15L-25L';
        } elseif ($sales >= 25 && $sales < 75) {
            $response[6 + $indx] =  '25L-75L';
        } elseif ($sales >= 75 && $sales < 100) {
            $response[6 + $indx] =  '75L-1Cr';
        } elseif ($sales >= 100) {
            $response[6 + $indx] =  '1Cr Plus';
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
