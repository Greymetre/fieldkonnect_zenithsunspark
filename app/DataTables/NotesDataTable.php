<?php

namespace App\DataTables;

use App\Models\Notes;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class NotesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    

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
                  $activebtn = '';
                  if(auth()->user()->can(['notes_edit']))
                  {
                    $btn = $btn.'<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm edit" id="'.encrypt($query->id).'" title="'.trans('panel.global.edit').' '.trans('panel.category.title_singular').'">
                          <i class="material-icons">edit</i>
                        </a>';
                  }
                  if(auth()->user()->can(['notes_show']))
                  {
                    $btn = $btn.'<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm show" id="'.encrypt($query->id).'" title="'.trans('panel.global.show').' '.trans('panel.category.title_singular').'">
                          <i class="material-icons">visibility</i>
                        </a>';
                  }
                  if(auth()->user()->can(['notes_delete']))
                  {
                    $btn = $btn.' <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.category.title_singular').'">
                                <i class="material-icons">clear</i>
                              </a>';
                  }
                  if(auth()->user()->can(['notes_active']))
                  {
                    $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                    $activebtn = '<div class="togglebutton">
                        <label>
                          <input type="checkbox"'.$active.' id="'.$query->id.'" class="activeRecord">
                          <span class="toggle"></span>
                        </label>
                      </div>';
                  }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>'.$activebtn;
            })
            ->addColumn('full_name', function ($data) {
                  return isset($data['leads']['first_name']) ? $data['leads']['first_name'].' '.$data['leads']['last_name'] : '';
            })
            ->rawColumns(['action','full_name']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Note $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Notes $model)
    {
        return $model->with('users:id,name','customerinfo:id,name,mobile,executive_id,customertype','customeraddress:customer_id,state_id')->select('id','user_id', 'customer_id', 'note', 'purpose', 'callstatus', 'status_id', 'created_at')->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('notes-table')
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
