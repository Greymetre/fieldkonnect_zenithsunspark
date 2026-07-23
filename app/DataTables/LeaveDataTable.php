<?php

namespace App\DataTables;

use App\Models\Leave;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class LeaveDataTable extends DataTable
{
    
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
                // ->editColumn('punchin_date', function ($data) {
                //     return isset($data->punchin_date) ? stringtodate($data->punchin_date) : '';
                // })

                ->addColumn('status', function ($query) {
                    $status = '';
                    if ($query->status == '0') {
                        $status = 'Pending';
                    } elseif ($query->status == '1') {
                        $status = 'Approved';
                    } elseif ($query->status == '2') {
                        $status = 'Rejected';
                    }
                    return $status;
                })


                ->addColumn('action', function ($query) {
                    $btn = '';
                     if(auth()->user()->can(['leave_delete'])){
                     $btn = $btn. '<a href="#" class="btn btn-danger btn-just-icon btn-sm deleteLeave" value="' . $query->id . '" title="Delete Leave">
                                        <i class="material-icons">clear</i>
                                      </a>';
                        }  

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
                })

                ->addColumn('action_status', function ($query) {
                    $btn = '';

                    if ($query->status == 0) {

                        $btn = '<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm approve_status" value="' . $query->id . '" title="Approve Status">
                                        <i class="material-icons">approval</i>
                                      </a>
                                      <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm reject_status" value="' . $query->id . '" title="Reject Status">
                                    <i class="material-icons">cancel</i>
                                  </a>
                                  ';
                    }
                    if ($query->status == 1) {

                        $btn = '<a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm reject_status" value="' . $query->id . '" title="Reject Status">
                                    <i class="material-icons">cancel</i>
                                  </a>';
                    }
                    if ($query->status == 2) {

                        $btn = '<a href="javascript:void(0)" class="btn btn-theme btn-just-icon btn-sm approve_status" value="' . $query->id . '" title="Approve Status">
                                        <i class="material-icons">approval</i>
                                      </a>';
                    }

                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
                })
                ->rawColumns(['action', 'action_status', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\City $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Leave $model)
    {
       return $model->with('users', 'createdbyname')->orderBy('id', 'desc')->newQuery();
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
