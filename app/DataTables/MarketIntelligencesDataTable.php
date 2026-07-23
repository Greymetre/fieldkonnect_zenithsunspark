<?php

namespace App\DataTables;

use App\Models\MarketIntelligenceServey;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
class MarketIntelligencesDataTable extends DataTable
{
    

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function($data)
            {
                return isset($data->created_at) ? cretaDateForFront($data->created_at) : '';
            })
           ->editColumn('uploaded_image', function($data) {
                $media = $data->getMedia('servey_image')->first(); // Get first image
                
                if ($media) {
                    $imageUrl = $media->getFullUrl(); // Get image URL
                    
                    return '<a href="' . $imageUrl . '" target="_blank" class="btn btn-sm btn-primary">View Image</a>';
                }

                return 'No';
            })
            ->editColumn('action', function($data) {
                $view = route('market-intelligences.show', $data->id);
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                            <a href="'.$view.'" class="btn btn-info btn-just-icon btn-sm" title="View Market Intelligence Servey" ><i class="material-icons">visibility</i></a>
                        </div>';
            })
            ->rawColumns(['action' , 'uploaded_image']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Field $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(MarketIntelligenceServey $model, Request $request)
    {
        $data = $model->with('createdbyname', 'division');
        
        if($request->division_id && !empty($request->division_id)){
            $data->where('division_id', $request->division_id);
        }
        
        $data = $data->latest()->newQuery();

        return $data;
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
