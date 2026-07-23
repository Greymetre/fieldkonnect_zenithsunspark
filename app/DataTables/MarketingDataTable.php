<?php

namespace App\DataTables;

use App\Models\Marketing;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class MarketingDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('action', function ($data) {
                $btn = '';

                // if (auth()->user()->can(['marketing_master_show'])) {
                //     $btn .= '<a href="' . route('dealer-appointment.show', $data->id) . '" class="btn btn-info btn-just-icon btn-sm" title="Show Appointment Form" ><i class="material-icons">visibility</i></a>';
                // }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">' . $btn . '</div>';
            })
            ->editColumn('event_date', function ($data) {
                return isset($data->event_date) ? date('d M Y', strtotime($data->event_date)) : '';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Coupon $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Marketing $model, Request $request)
    {
        $data = $model->query();

        if ($request->state != null && $request->state != '') {
            $data->where('state', $request->state);
        }

        if ($request->district != null && $request->district != '') {
            $data->where('event_district', $request->district);
        }
        if ($request->division != null && $request->division != '') {
            $data->where('division', $request->division);
        }

        if ($request->event_under != null && $request->event_under != '') {
            $data->where('event_under_name', $request->event_under);
        }

        if ($request->branch != null && $request->branch != '') {
            $data->where('branch', $request->branch);
        }

        if ($request->event_center != null && $request->event_center != '') {
            $data->where('event_center', $request->event_center);
        }

        if ($request->category_of_participant != null && $request->category_of_participant != '') {
            $data->where('category_of_participant', $request->category_of_participant);
        }

        if ($request->branding_team_member != null && $request->branding_team_member != '') {
            $data->where('branding_team_member', $request->branding_team_member);
        }

        if ($request->start_date != null && $request->start_date != '') {
            $data->where('event_date', '>=', $request->start_date);
        }

        if ($request->end_date != null && $request->end_date != '') {
            $data->where('event_date', '<=', $request->end_date);
        }
        if ($request->created_by != null && $request->created_by != '') {
            $data->where('created_by', $request->created_by);
        }

        return $data->latest();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('coupons-table')
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
