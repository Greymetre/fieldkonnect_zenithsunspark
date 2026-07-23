<?php

namespace App\DataTables;

use App\Models\Gifts;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class RedemptionGiftsDataTable extends DataTable
{

    public function dataTable($query, Request $request)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('created_at', function ($data) {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->addColumn('action', function ($query) use ($request) {
                if ((int)$request->Total_points >= (int)$query->points) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                        <input ' . ($request->data != "[]" && json_decode($request->data)->gift_id == $query->id ? "checked" : "") . ' type="checkbox" name="gift_id[]" data-points="' . $query->points . '" value="' . $query->id . '" />
                        </div>';
                } else {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                        <input ' . ($request->data != "[]" && json_decode($request->data)->gift_id == $query->id ? "checked" : "") . ' disabled type="checkbox" name="gift_id[]" data-points="' . $query->points . '" value="' . $query->id . '" />
                        </div>';
                }
            })
            ->addColumn('image', function ($query) {
                return '<img src="' . asset(!empty($query->product_image) ? 'uploads/' . $query->product_image : 'assets/img/placeholder.jpg') . '" border="0" width="70" class="img-rounded" align="center" />';
            })
            ->rawColumns(['action', 'image']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Gift $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Gifts $model, Request $request)
    {
        return $model->with('createdbyname', 'categories', 'subcategories', 'brands', 'models')->orderBy('points','asc')->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('gifts-table')
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
