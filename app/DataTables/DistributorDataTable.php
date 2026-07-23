<?php

namespace App\DataTables;

use App\Models\Customers;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class DistributorDataTable extends DataTable
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
                  if(auth()->user()->can(['distributor_edit']))
                  {
                    $btn = $btn.'<a href="'.url("customers/".encrypt($query->id).'/edit') .'" class="btn btn-info btn-just-icon btn-sm" title="'.trans('panel.global.edit').' '.trans('panel.distributors.title_singular').'">
                                    <i class="material-icons">edit</i>
                                </a>';
                  }
                  if(auth()->user()->can(['distributor_edit']))
                  {
                    $btn = $btn.'<a href="'.url("customers/".encrypt($query->id)).'" class="btn btn-theme btn-just-icon btn-sm" title="'.trans('panel.global.show').' '.trans('panel.distributors.title_singular').'">
                                    <i class="material-icons">visibility</i>
                                </a>';
                  }
                  if(auth()->user()->can(['distributor_delete']))
                  {
                    $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.distributors.title_singular').'">
                                <i class="material-icons">clear</i>
                              </a>';
                  }
                  if(auth()->user()->can(['distributor_active']))
                  {
                    $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                    $activebtn = '<div class="togglebutton">
                                <label>
                                  <input type="checkbox"'.$active.' id="'.$query->id.'" class="distributorActive">
                                  <span class="toggle"></span>
                                </label>
                              </div>';
                  }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>'.$activebtn;
            })
            ->addColumn('image', function ($query) {
                    $profileimage = !empty($query->profile_image) ? env('IMAGE_UPLOADS').$query->profile_image : asset('assets/img/placeholder.jpg') ;
                    return '<img src="'.$profileimage.'" border="0" width="70" class="img-rounded imageDisplayModel" align="center" />';
                })
            ->rawColumns(['action','image']);
    }
    /**
     * Get query source of dataTable.
     *
     * @param \App\DistributorDataTable $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Customers $model)
    {
        $userids = getUsersReportingToAuth();
        return $model->whereIn('customertype', ['1'])
                            ->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('executive_id',$userids);
                                }
                            })
                            ->with('customertypes','firmtypes')
                            ->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('distributordatatable-table')
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
