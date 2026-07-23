<?php

namespace App\DataTables;

use App\Models\Field;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
class FieldsDataTable extends DataTable
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
                  // if(auth()->user()->can(['category_active']))
                  // {
                    // $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                    // $btn = $btn.'<div class="togglebutton">
                    //     <label>
                    //       <input type="checkbox"'.$active.' id="'.$query->id.'" class="is_active">
                    //       <span class="toggle"></span>
                    //     </label>
                    //   </div>';
                  // }
                  // if(auth()->user()->can(['category_edit']))
                  // {
                      $btn = $btn.'<a href="'.url("fields/".encrypt($query->id).'/edit') .'" class="btn btn-info btn-just-icon btn-sm" title="'.trans('panel.global.edit').' '.trans('panel.content.title_singular').'">
                                    <i class="material-icons">edit</i>
                                </a>';
                  // }
                  // if(auth()->user()->can(['category_show']))
                  // {
                  //   $btn = $btn.'<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm show" id="'.encrypt($query->id).'" title="'.trans('panel.global.show').' '.trans('panel.category.title_singular').'">
                  //         <i class="material-icons">visibility</i>
                  //       </a>';
                  // }
                  // if(auth()->user()->can(['category_delete']))
                  // {
                    $btn = $btn.' <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.category.title_singular').'">
                                <i class="material-icons">clear</i>
                              </a>';
                  // }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Field $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Field $model)
    {
        return $model->with('createdbyname','customertypes')->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('fields-table')
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
