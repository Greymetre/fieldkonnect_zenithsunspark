<?php

namespace App\DataTables;

use App\Models\Category;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class CategoryDataTable extends DataTable
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
                  if(auth()->user()->can(['category_edit']))
                  {
                    $btn = $btn.'<a href"javascript:void(0)" class="btn btn-info btn-just-icon btn-sm edit" id="'.encrypt($query->id).'" title="'.trans('panel.global.edit').' '.trans('panel.category.title_singular').'">
                          <i class="material-icons">edit</i>
                        </a>';
                  }
                  if(auth()->user()->can(['category_delete']))
                  {
                    $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.category.title_singular').'">
                                <i class="material-icons">clear</i>
                              </a>';
                  }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>';
            })
            ->addColumn('active', function ($query) {
                  if(auth()->user()->can(['category_active']))
                  {
                    $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                    return '<div class="togglebutton">
                        <label>
                          <input type="checkbox"'.$active.' id="'.$query->id.'" class="categoryActive">
                          <span class="toggle"></span>
                        </label>
                      </div>';
                  }
            })
            ->addColumn('image', function ($query) {
                $category_image = !empty($query->category_image) ? '/public/uploads/'.$query->category_image : asset('assets/img/placeholder.jpg') ;
                return '<img src="'.$category_image.'" border="0" width="70" class="img-rounded imageDisplayModel" align="center" />';
            })
            ->rawColumns(['action','active','image']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Category $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Category $model)
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
                    ->setTableId('category-table')
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
