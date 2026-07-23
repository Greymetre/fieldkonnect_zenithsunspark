<?php

namespace App\DataTables;

use App\Models\ComplaintType;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class ComplaintTypeDataTable extends DataTable
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
                  $activebtn ='';
                  if(auth()->user()->can(['complaint_type_edit']))
                  {
                    $btn = $btn.'<a href="javascript:void(0)" class="btn btn-info btn-just-icon btn-sm edit" id="'.encrypt($query->id).'" title="'.trans('panel.global.edit').' '.trans('panel.complaint_type.title_singular').'">
                          <i class="material-icons">edit</i>
                        </a>';
                  }
                  if(auth()->user()->can(['complaint_type_delete']))
                  {
                    $btn = $btn.' <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.complaint_type.title_singular').'">
                                <i class="material-icons">clear</i>
                              </a>';
                  }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>';
            })
            ->addColumn('active', function ($query) {
                  // if(auth()->user()->can(['city_active']))
                  // {
                    $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                    return '<div class="togglebutton">
                        <label>
                          <input type="checkbox"'.$active.' id="'.$query->id.'" class="complaintTypeActive">
                          <span class="toggle"></span>
                        </label>
                      </div>';
                  // }
            })
            ->rawColumns(['action','active']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\City $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ComplaintType $model)
    {
        return $model->newQuery();
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
