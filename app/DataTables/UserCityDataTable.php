<?php

namespace App\DataTables;

use App\Models\UserCityAssign;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class UserCityDataTable extends DataTable
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
                  $btn = '';
                  if(auth()->user()->can(['usercity_delete']))
                  {
                    $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="User City Delete">
                                <i class="material-icons">clear</i>
                        </a>';
                  }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">'.$btn.'</div>';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\UserCity $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(UserCityAssign $model)
    {
         return $model->with('userinfo','userinfo.getdesignation','reportinginfo','reportinginfo.getdesignation','cityname','cityname.districtname','cityname.districtname.statename')->orderBy('userid')->newQuery();

    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('usercity-table')
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
