<?php

namespace App\DataTables;

use App\Models\Beat;
use App\Models\City;
use App\Models\District;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class SchedulesDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($data) {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('city_name', function ($data) {
                $city_names = City::whereIn('id', explode(',', $data->city_id))->pluck('city_name')->toArray();
                return implode(',', $city_names);
            })
            ->editColumn('district_name', function ($data) {
                $district_names = District::whereIn('id', explode(',', $data->district_id))->pluck('district_name')->toArray();
                return implode(',', $district_names);
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                if (auth()->user()->can(['beat_edit'])) {
                    $btn = $btn . '<a href="' . url("beats/" . encrypt($query->id) . '/edit') . '" class="btn btn-info btn-just-icon btn-sm">
                                    <i class="material-icons">edit</i>
                                </a>';
                }
                if (auth()->user()->can(['beat_access'])) {
                    $btn = $btn . '<a href="' . url("beats/" . encrypt($query->id)) . '" class="btn btn-theme btn-just-icon btn-sm">
                                    <i class="material-icons">visibility</i>
                                </a>';
                }
                if (auth()->user()->can(['beat_delete'])) {
                    $btn = $btn . ' <a href="' . url("beats-schedule/" . encrypt($query->id)) . '" class="btn btn-warning btn-just-icon btn-sm">
                                    <i class="material-icons">schedule</i>
                                </a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Schedule $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Beat $model)
    {
        $userids = getUsersReportingToAuth();
        $data = $model->with('createdbyname', 'countryname', 'statename')
            ->whereHas('beatusers', function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('user_id', $userids);
                }
            })->latest()->newQuery();
        return $data;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('schedules-table')
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
