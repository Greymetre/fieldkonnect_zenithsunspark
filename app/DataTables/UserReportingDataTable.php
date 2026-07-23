<?php

namespace App\DataTables;

use App\Models\UserReporting;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserReportingDataTable extends DataTable
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
            ->editColumn('users', function($data)
            {
                $users = User::whereIn('id', json_decode($data->users))->select('id','name','profile_image')->get();
                $member = '';
                if(!empty($users))
                {
                    foreach ($users as $key => $value) {
                        $image = !empty($value['profile_image']) ? $value['profile_image'] : asset('public/assets/img/placeholder.jpg') ;
                        $member = $member.'
                            <img src="'.$image.'" alt="..." class="avatar-sm">
                            <span class="username">'.$value['name'].'</span><br>';
                    }
                }
                return $member;
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                  $btn = $btn.'<a href="javascript:void(0)" class="btn btn-info btn-just-icon btn-sm edit" id="'.encrypt($query->id).'" title="'.trans('panel.global.edit').' '.trans('panel.teams.title_singular').'">
                        <i class="material-icons">edit</i>
                      </a>';
                  $btn = $btn.' <a href="javascript:void(0)" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.teams.title_singular').'">
                              <i class="material-icons">clear</i>
                            </a>';
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                              '.$btn.'
                          </div>';
          })
          ->rawColumns(['action','users']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\UserReporting $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(UserReporting $model)
    {
        return $model->with('reportinginfo','createdbyname')->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('userreportingdatatable-table')
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
