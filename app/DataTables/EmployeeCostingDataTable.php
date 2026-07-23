<?php

namespace App\DataTables;

use App\Models\Complaint;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeCostingDataTable extends DataTable
{

    public function __construct()
    {
        $this->stdate = '';
        $this->eddate = '';
    }

    public function dataTable($query)
    {
        return datatables($query)
            // ->eloquent($query)
            // ->addIndexColumn()
            ->addColumn('emp_code', function ($query) {
                return count(explode(',', $query->emp_codes)) > 0 ? explode(',', $query->emp_codes)[0] : '-';
            })
            ->addColumn('doj', function ($query) {
                if ($query->userinfo) {
                    return date('d M Y', strtotime($query->userinfo->date_of_joining));
                } else {
                    return '-';
                }
            })
            ->addColumn('sales', function ($query) {
                return $query->sales;
            })
            ->addColumn('ta_da', function ($query) {
                return $query->expensesSum ?? '0';
                // if (count($query->expenses) > 0) {
                //     return $query->expenses->where('date', '>=', $this->stdate)->where('date', '<=', $this->eddate)->sum('claim_amount') > 0 ? number_format($query->expenses->where('date', '>=', $this->stdate)->where('date', '<=', $this->eddate)->sum('claim_amount'), 2, '.', '') : 0;
                // } else {
                //     return 0;
                // }
            })
            ->addColumn('total_exp', function ($query) {
                return $query->total_expe;
            })
            ->addColumn('sal_exp', function ($query) {
                if ($query->sal_exp > 0) {
                    if ($query->sal_exp <= 5) {
                        return $query->sal_exp . '%';
                    } else {
                        return '<span class="badge badge-danger">' . $query->sal_exp . '%</span>';
                    }
                } else {
                    return '<span class="badge badge-danger">0%</span>';
                }
            })

            ->rawColumns(['doj', 'sales', 'ta_da', 'total_exp', 'sal_exp']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\City $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Request $request)
    {
        DB::statement("SET SESSION group_concat_max_len = 10000000");

        // Financial Year & Date Filter Logic
        $startDateFormatted = $endDateFormatted = null;
        if ($request->month && is_array($request->month) && count($request->month) > 0 && $request->financial_year) {
            $f_year_array = explode('-', $request->financial_year);
            $isJanToMar = in_array('Jan', $request->month) || in_array('Feb', $request->month) || in_array('Mar', $request->month);
            $currentYear = $isJanToMar ? $f_year_array[1] : $f_year_array[0];
            $startDate = Carbon::createFromFormat('Y-M', "$currentYear-{$request->month[0]}")->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-M', "$currentYear-{$request->month[count($request->month) - 1]}")->endOfMonth();
        } elseif ($request->financial_year) {
            $f_year_array = explode('-', $request->financial_year);
            $startDate = Carbon::createFromFormat('Y-m-d', "{$f_year_array[0]}-04-01");
            $endDate = Carbon::createFromFormat('Y-m-d', "{$f_year_array[1]}-03-31");
        } else {
            $startDate = Carbon::now()->subMonthsNoOverflow(3)->startOfMonth();
            $endDate = Carbon::now()->subMonthNoOverflow()->endOfMonth();
        }

        // Ensure end date does not exceed today
        $today = Carbon::now('Asia/Kolkata');
        if ($endDate->greaterThan($today)) {
            $endDate = $today;
        }

        $startDateFormatted = $startDate->toDateString();
        $endDateFormatted = $endDate->toDateString();

        // Build Query
        $query = User::with(['primarySales:id,emp_code,invoice_date,net_amount', 'getdesignation', 'getbranch', 'getdivision', 'userinfo', 'expenses'])
            ->where('active', 'Y')
            ->whereHas('roles', function ($query) {
                $query->whereIn('id', ['13', '6', '3', '2']);
            });
            if ($request->division_id && count($request->division_id) > 0) {
                $query->whereIn('division_id', $request->division_id);
            }

        // Apply Filters
        $filters = [
            'branch_id' => $request->branch_id,
            'dealer' => $request->dealer_id ? ['like', "%{$request->dealer_id}%"] : null,
            'model_name' => $request->product_model,
            'new_group' => $request->new_group,
            'id' => $request->executive_id
        ];

        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                $query->where($field, $value);
            }
        }

        // Get the result
        $users = $query->get();

        // Prepare Calculations
        $all_months = getMonthsBetween($startDate, $endDate);

        foreach ($users as $user) {
            $user->userinfo->gross_salary_monthly *= count($all_months);

            $user->expensesSum = $user->expenses->whereBetween('date', [$startDateFormatted, $endDateFormatted])->sum('claim_amount');
            $user->total_expe = $user->expensesSum + $user->userinfo->gross_salary_monthly;

            if ($user->sales_type == 'Primary') {
                $salesSum = $user->primarySales->whereBetween('invoice_date', [$startDateFormatted, $endDateFormatted])->sum('net_amount');
            } else {
                $salesSum = Order::where('created_by', $user->id)
                    ->whereBetween('order_date', [$startDateFormatted, $endDateFormatted])
                    ->sum('sub_total');
            }

            $user->sales = $salesSum > 0 ? number_format($salesSum / 100000, 2) : 0;

            // Calculate Salary/Expense ratio
            $user->sal_exp = $user->sales > 0
                ? number_format(($user->total_expe / 100000) / $user->sales * 100, 2)
                : 0;
        }

        // Sort and Return
        return $users->sortByDesc('sal_exp');
    }


    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('city-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('id'),
            Column::make('add your columns'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }
}
