<?php

namespace App\DataTables;

use App\Models\ClaimGeneration;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class ClaimGenerationDatatable extends DataTable
{
    
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('created_by', function($data){
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->addColumn('service_center_name', function($data) {
                return isset($data->service_center_details) 
                    ? '<a href="'. route('claim-generation.show', encrypt($data->id)).'">'.'[' . ($data->service_center_details->customer_code ?? '') . '] ' . ($data->service_center_details->name ?? '') . '</a>' 
                    : '';
            })
            ->addColumn('month_year', function($data) {
                return ($data->month ?? '') . '-' . ($data->year ?? '');
            })
            ->addColumn('action', function ($query) {
                  $btn = '';
                  if(auth()->user()->can(['claim_edit']))
                  {
                   $btn .= '<a href="' . route('claim-generation.edit', encrypt($query->id)) . '" class="btn btn-info btn-just-icon btn-sm" 
                            title="' . trans('panel.global.edit') . ' ' . trans('panel.claim_generation.title_singular') . '">
                            <i class="material-icons">edit</i>
                        </a>';
                  }
                  return $btn;
            })
           ->editColumn('submitted_by_se', function ($query) {
                return isset($query->submitted_by_se) && $query->submitted_by_se == 1 
                    ? '<span class="badge badge-success">Yes</span>' 
                    : '<span class="badge badge-danger">No</span>';
            })
           ->editColumn('claim_approved', function ($query) {
                return isset($query->claim_approved) && $query->claim_approved == 1 
                    ? '<span class="badge badge-success">Yes</span>' 
                    : '<span class="badge badge-danger">No</span>';
            })
           ->editColumn('claim_done', function ($query) {
                return isset($query->claim_done) && $query->claim_done == 1 
                    ? '<span class="badge badge-success">Yes</span>' 
                    : '<span class="badge badge-danger">No</span>';
            })
            ->rawColumns(['action' , 'service_center_name' , 'month_year' , 'submitted_by_se' , 'claim_approved' ,'claim_done']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
     public function query(ClaimGeneration $model, Request $request)
    {
        $data = $model->with(['service_center_details']);
        if (!empty($request->start_month) && empty($request->end_month)) { 
            $formatted_date = Carbon::createFromFormat('F Y', $request->start_month)->startOfMonth();
            $claim_date = $formatted_date->format("Y-m-d");

            $data = $data->whereDate('claim_date', $claim_date);
        }

        if (!empty($request->start_month) && !empty($request->end_month)) { 
            $formatted_date_start = Carbon::createFromFormat('F Y', $request->start_month)->startOfMonth();
            $formatted_date_end = Carbon::createFromFormat('F Y', $request->end_month)->endOfMonth(); // Use endOfMonth for full range

            $claim_date_start = $formatted_date_start->format("Y-m-d");
            $claim_date_end = $formatted_date_end->format("Y-m-d");

            $data = $data->whereBetween('claim_date', [$claim_date_start, $claim_date_end]); // Filter between start and end
        }

        if (!empty($request->service_center)) { 
            $data = $data->where('service_center_id', $request->service_center);
        }

        return $data->latest();
    }


    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('product-table')
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
