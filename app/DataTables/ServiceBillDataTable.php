<?php

namespace App\DataTables;

use App\Models\ServiceBill;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class ServiceBillDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('status', function ($query) {
                if($query->status == '0'){
                    return '<a href="'.route('service_bills.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Service Bill"><span class="badge badge-secondary">Draft</span></a>';
                }elseif($query->status == '1'){
                    return '<a href="'.route('service_bills.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Service Bill"><span class="badge badge-warning">Claimed</span></a>';
                }elseif($query->status == '2'){
                    return '<a href="'.route('service_bills.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Service Bill"><span class="badge badge-info">Customer payble</span></a>';
                }elseif($query->status == '3'){
                    return '<a href="'.route('service_bills.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Service Bill"><span class="badge badge-success">Approved</span></a>';
                }elseif($query->status == '4'){
                    return '<a href="'.route('service_bills.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Service Bill"><span class="badge badge-danger">Cancel</span></a>';
                }
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';
                if (auth()->user()->can(['complaint_edit'])) {
                    $btn = $btn . '<a href="'.route('complaints.edit', $query->id).'" class="btn btn-success btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' Service Bill">
                          <i class="material-icons">edit</i>
                          </a>';
                }
                if (auth()->user()->can(['complaint_view'])) {
                    $btn = $btn . '<a href="'.route('complaints.show', $query->id).'" class="btn btn-info btn-just-icon btn-sm" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Service Bill">
                                <i class="material-icons">visibility</i>
                                </a>';
                }
                // $btn = '';
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
            })
            ->addColumn('complaint_no', function ($query) {
                if (auth()->user()->can(['complaint_view'])) {
                    $btn = '<a href="'.route('complaints.show', $query->complaint_id).'" value="' . $query->complaint_id . '" title="' . trans('panel.global.show') . ' Complaint">
                                '.$query->complaint_no.'
                                </a>';
                }else{
                    $btn = $query->complaint_no;
                }
                // $btn = '';
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
            })
            ->addColumn('bill_no', function ($query) {
                if (auth()->user()->can(['service_bill_view'])) {
                    $btn = '<a href="'.route('service_bills.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Service Bill">
                                '.$query->bill_no.'
                                </a>';
                }else{
                    $btn = $query->bill_no;
                }
                // $btn = '';
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
            })
            ->rawColumns(['action', 'status','complaint_no', 'bill_no']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\City $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ServiceBill $model)
    {
        $userids = getUsersReportingToAuth();
        $data = $model->with('complaint');
        if(!auth()->user()->hasRole(['superadmin']) || !auth()->user()->hasRole(['Sub_Admin']) || !auth()->user()->hasRole(['Service Admin'])){
            if(auth()->user()->hasRole(['Service_center_user'])){
                $data = $data->whereHas('complaint', function ($query) use ($userids) {
                    $query->where(function ($q) {
                        $q->where('service_center', auth()->user()->customerid);
                    });
                });    
            }else{
                $data = $data->whereHas('complaint', function ($query) use ($userids) {
                    $query->where(function ($q) use ($userids) {
                        $q->whereIn('assign_user', $userids);
                    });
                });
            }
        }
        return $data->latest()->newQuery();
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
