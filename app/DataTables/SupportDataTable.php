<?php

namespace App\DataTables;

use App\Models\Support;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class SupportDataTable extends DataTable
{
    
    
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function($data)
            {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->addColumn('action', function ($query) {
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                    <a href="'.url("supports/".encrypt($query->id).'/edit') .'" class="btn btn-primary btn-just-icon btn-xs">
                        <i class="material-icons">edit</i>
                    </a>
                    <a href="'.url("supports/".encrypt($query->id)).'" class="btn btn-success btn-just-icon btn-xs">
                        <i class="material-icons">visibility</i>
                    </a>
                </div>';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Support $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Support $model)
    {
        return $model->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('support-table')
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
