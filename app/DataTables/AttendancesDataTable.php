<?php

namespace App\DataTables;

use App\Models\Attendance;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class AttendancesDataTable extends DataTable
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
            ->editColumn('punchin_date', function($data)
            {
                return isset($data->punchin_date) ? stringtodate($data->punchin_date) : '';
            })
            ->editColumn('punchout_date', function($data)
            {
                return isset($data->punchout_date) ? stringtodate($data->punchout_date) : stringtodate($data->punchin_date);
            })
            
            ->addColumn('punchin', function ($query) {
                $punchin_image = !empty($query->punchin_image) ? env('IMAGE_UPLOADS').$query->punchin_image : asset('assets/img/placeholder.jpg') ;
                    return '<img src="'.$punchin_image.'" border="0" width="70" class="img-rounded imageDisplayModel" align="center" />';
                })
            ->addColumn('punchout', function ($query) {
                $punchout_image = !empty($query->punchout_image) ? env('IMAGE_UPLOADS').$query->punchout_image : asset('assets/img/placeholder.jpg') ;
                    return '<img src="'.$punchout_image.'" border="0" width="70" class="img-rounded imageDisplayModel" align="center" />';
                })
            ->rawColumns(['punchin','punchout']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Attendance $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Attendance $model)
    {
         $userids = getUsersReportingToAuth();
        return $model->with('users')->where(function($query) use($userids){
                                    if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                    {
                                        $query->whereIn('user_id', $userids);
                                    }
                                })->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('attendances-table')
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
