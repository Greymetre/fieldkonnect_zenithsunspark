<?php

namespace App\DataTables;

use App\Models\Customers;
use App\Models\ParentDetail;
use App\Models\SchemeDetails;
use App\Models\Services;
use App\Models\DealerAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DealerAppointmentDataTable extends DataTable
{

    public function dataTable($query, Request $request)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('appointment_date', function ($data) {
                return isset($data->appointment_date) ? date('d M Y', strtotime($data->appointment_date)) : '';
            })
            ->editColumn('certificate', function ($data) {
                if($data->getMedia('certificate')->count() > 0 && Storage::disk('s3')->exists($data->getMedia('certificate')[0]->getPath())){
                    return '<a target="_blank" href="'.$data->getMedia('certificate')[0]->getFullUrl().'" class="btn btn-success btn-just-icon btn-sm" title="Edit Appointment Form" ><i class="material-icons">card_membership</i></a>';
                }else{
                    return '-';
                }
            })
            ->editColumn('name', function ($data) {
                return $data->first_name.' '.$data->middle_name.' '.$data->last_name;
            })
            ->editColumn('approval_status', function ($data) {
                if($data->approval_status == '0'){
                    return '<span class="badge badge-warning">Pending</span>';
                }elseif($data->approval_status == '1'){
                    return '<span class="badge badge-dark">Approved By Sales Team</span>';
                }elseif($data->approval_status == '2'){
                    return '<span class="badge badge-info">Approved By Account</span>';
                }elseif($data->approval_status == '3'){
                    return '<span class="badge badge-success">Approved By HO</span>';
                }elseif($data->approval_status == '4'){
                    return '<span class="badge badge-danger">Rejected</span>';
                }
            })
            ->editColumn('createdbyname.name', function ($data) {
                return $data->createdbyname?$data->createdbyname->name:'-';
            })
            ->editColumn('action', function ($data) {
                $btn = '';
                if(auth()->user()->can(['dealer_appointment_edit'])){
                    $btn .= '<a href="'.route('dealer-appointment.edit', $data->id).'" class="btn btn-success btn-just-icon btn-sm" title="Edit Appointment Form" ><i class="material-icons">edit</i></a>';
                }
                if(auth()->user()->can(['dealer_appointment_show'])){
                    $btn .= '<a href="'.route('dealer-appointment.show', $data->id).'" class="btn btn-info btn-just-icon btn-sm" title="Show Appointment Form" ><i class="material-icons">visibility</i></a>';
                }
                if(auth()->user()->can(['dealer_appointment_delete'])){
                    $btn .= '<a href="'.route('dealer-appointment.destroy', $data->id).'" onclick="return confirmDeletion();" class="btn btn-danger btn-just-icon btn-sm" title="Show Appointment Form" ><i class="material-icons">close</i></a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">'.$btn.'</div>';
            })

            ->rawColumns(['appointment_date', 'action','createdbyname.name','approval_status','certificate']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Scheme $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DealerAppointment $model, Request $request)
    {
        $user_ids = getUsersReportingToAuth();
        $data = $model->with('branch_details', 'district_details', 'city_details', 'appointment_kyc_detail');
        if(auth()->user()->hasRole('Customer Dealer')){
            $data->where('parent_id' , auth()->user()->customerid);
        }elseif(!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('Sub_Admin') && !auth()->user()->hasRole('Sub billing') && !auth()->user()->hasRole('Head office')){
            $data->whereIn('created_by' , $user_ids);
        }

        if ($request->startdate && $request->startdate != '' && $request->startdate != NULL && $request->enddate && $request->enddate != '' && $request->enddate != NULL) {
            
            $startDate = date('Y-m-d', strtotime($request->startdate));
            $endDate = date('Y-m-d', strtotime($request->enddate));
            $data = $data->whereDate('appointment_date', '>=', $startDate)
                ->whereDate('appointment_date', '<=', $endDate);
        }

        if($request->status_id != '' && $request->status_id != NULL){
            $data->where('approval_status', $request->status_id);
        }

        if($request->division_id != '' && $request->division_id != NULL){
            $data->where('division', $request->division_id);
        }

        if($request->branch_id != '' && $request->branch_id != NULL){
            $data->where('branch', $request->branch_id);
        }

        if($request->created_by != '' && $request->created_by != NULL){
            $data->where('created_by', $request->created_by);
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
            ->setTableId('getTransactionHistory')
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
