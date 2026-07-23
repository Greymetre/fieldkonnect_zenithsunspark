<?php

namespace App\DataTables;

use App\Models\SchemeHeader;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class SchemesDataTable extends DataTable
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
            ->editColumn('invoice_date', function($data)
            {
                return isset($data->invoice_date) ? showdateformat($data->invoice_date) : '';
            })
            ->addColumn('action', function ($query) {
                  $btn = '';
                  $activebtn ='';
                  if(auth()->user()->can(['scheme_edit']))
                  {
                    $btn = $btn.'<a href="'.url("schemes/".encrypt($query->id).'/edit') .'" class="btn btn-info btn-just-icon btn-sm" title="'.trans('panel.global.show').' '.trans('panel.scheme.title_singular').'">
                                    <i class="material-icons">edit</i>
                                </a>';
                  }
                  if(auth()->user()->can(['scheme_show']))
                  {
                    //$btn = $btn.'<a href="'.url("schemes/".encrypt($query->id)).'" class="btn btn-theme btn-just-icon btn-sm" title="'.trans('panel.global.show').' '.trans('panel.scheme.title_singular').'">
                      //              <i class="material-icons">visibility</i>
                        //        </a>';
                  }
                  if(auth()->user()->can(['scheme_delete']))
                  {
                    $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.scheme.title_singular').'">
                                <i class="material-icons">clear</i>
                              </a>';
                  }
                  // if(auth()->user()->can(['scheme_active']))
                  // {
                    $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                    $activebtn = '<div class="togglebutton">
                                <label>
                                  <input type="checkbox"'.$active.' id="'.$query->id.'" class="activeRecord">
                                  <span class="toggle"></span>
                                </label>
                              </div>';
                  // }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>'.$activebtn;
            })
            ->addColumn('image', function ($query) {
                    return '<img src="'.asset(!empty($query->scheme_image) ? 'uploads/'.$query->scheme_image : 'public/uploads/product.jpeg').'" border="0" width="70" class="img-rounded" align="center" />';
                })
            ->rawColumns(['action','image']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Scheme $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SchemeHeader $model)
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
                    ->setTableId('schemes-table')
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
