<?php

namespace App\DataTables;

use App\Models\Gifts;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
class GiftsDataTable extends DataTable
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
                                <a href="'.url("gifts/".encrypt($query->id).'/edit') .'" class="btn btn-success btn-just-icon btn-sm">
                                    <i class="material-icons">edit</i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.gift.title_singular').'">
                                <i class="material-icons">clear</i>
                              </a>
                            </div>';
            })
            ->addColumn('active', function ($query) {
                if(auth()->user()->can(['category_active']))
                {
                  $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                  return '<div class="togglebutton">
                      <label>
                        <input type="checkbox"'.$active.' id="'.$query->id.'" class="activeRecord">
                        <span class="toggle"></span>
                      </label>
                    </div>';
                }
          })
            ->addColumn('image', function ($query) {
                return '<img src="'.asset(!empty($query->product_image) ? 'uploads/'.$query->product_image : 'assets/img/placeholder.jpg').'" border="0" width="70" class="img-rounded" align="center" />';
              })
            ->rawColumns(['action','image','active']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Gift $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Gifts $model)
    {
        return $model->with('createdbyname','categories','subcategories','brands','models')->latest()->newQuery();
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
