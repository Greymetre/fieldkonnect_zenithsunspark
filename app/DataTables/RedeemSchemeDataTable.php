<?php

namespace App\DataTables;

use App\Models\RedeemptionScheme;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
class RedeemSchemeDataTable extends DataTable
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
                                <a href="'.url("redeemptionschemes/".encrypt($query->id).'/edit') .'" class="btn btn-primary btn-just-icon btn-sm">
                                    <i class="material-icons">edit</i>
                                </a>
                                <a href="'.url("redeemptionschemes/".encrypt($query->id)).'" class="btn btn-success btn-just-icon btn-sm">
                                    <i class="material-icons">visibility</i>
                                </a>
                            </div>';
            })
            ->addColumn('image', function ($query) {
                    return '<img src="'.asset(!empty($query->scheme_image) ? $query->scheme_image : 'public/uploads/product.jpeg').'" border="0" width="70" class="img-rounded" align="center" />';
            })
            ->rawColumns(['action','image']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\RedeemScheme $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RedeemptionScheme $model)
    {
        return $model->with('createdbyname')->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('redeemscheme-table')
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
