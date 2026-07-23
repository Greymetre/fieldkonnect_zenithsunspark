<?php

namespace App\DataTables;

use App\Models\ClaimGenerationDetail;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class ClaimGenerationSingleDatabale extends DataTable
{
    
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('created_by', function($data){
                return isset($data->created_at) ? cretaDateForFront($data->created_at) : '';
            })
            ->addColumn('company_sale_bill_date', function($data){
                return isset($data->complaints->company_sale_bill_date) ? cretaDateForFront($data->complaints->company_sale_bill_date) : '';
            })
           ->editColumn('serce_bill_approve_date', function ($data) {
                 return isset($data->complaints->service_bill->status) && $data->complaints->service_bill->status == 3 ? cretaDateForFront($data->complaints->service_bill->updated_at) : "Not Approved";
            })
            ->editColumn('complaint_work_dones', function ($data) {
                $done_by = optional($data->complaints->complaint_work_dones->sortByDesc('created_at')->first())->done_by;
                return $done_by ?? '';
            })
            ->editColumn('service_charge', function ($data) {
                return getServiceCharge($data, 1);
            })
            ->editColumn('site_visit_charge', function ($data) {
                return getServiceCharge($data, 3);
            })
            ->editColumn('rewinding_charge', function ($data) {
                return getServiceCharge($data, 5);
            })
            ->editColumn('local_spare_charges', function ($data) {
                return getServiceCharge($data, 4);
            })
            ->editColumn('total_changes', function ($data) {
                return number_format(optional($data['complaints']['service_bill']['service_bill_products'])->sum('subtotal') ?? 0.0, 2, '.', '');
            })
            ->rawColumns(['serce_bill_approve_date' , 'complaint_work_dones' , 'service_charge' , 'site_visit_charge' , 'rewinding_charge' , 'local_spare_charges' , 'total_changes']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
     public function query(ClaimGenerationDetail $model, Request $request)
    {
        $data = $model->with(['claim.service_center_details' , 'complaints.service_bill.service_bill_products' , 'complaints.purchased_branch_details' , 'complaints.complaint_work_dones']);
        if (!empty($request->claim_id)) { 
            $data->where('claim_generation_id' , $request->claim_id);
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
