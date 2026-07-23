<?php

namespace App\DataTables;

use App\Models\SalesTarget;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class SalesTargetDatatable extends DataTable
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

                    $btn = $btn.'<a href="javascript:void(0)" class="btn btn-info btn-just-icon btn-sm edit" id="'.encrypt($query->id).'" title="Edit Target">
                          <i class="material-icons">edit</i>
                        </a>';
 
                    $btn = $btn.' <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="Delete Target">
                                <i class="material-icons">clear</i>
                              </a>';
       
                    $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                    $btn = $btn.'<div class="togglebutton">
                                <label>
                                  <input type="checkbox"'.$active.' id="'.$query->id.'" class="activeRecord">
                                  <span class="toggle"></span>
                                </label>
                              </div>';
               
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>';
            })
            ->rawColumns(['action']);
    }


    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\SalesTargetDatatable $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SalesTarget $model)
    {
        $userids = getUsersReportingToAuth();
        return $model->with('users','createdbyname')->whereHas('users', function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('userid',$userids);
                                }
                            })->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('salestargetdatatable-table')
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
