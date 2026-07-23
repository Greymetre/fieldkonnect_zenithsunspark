<?php

namespace App\DataTables;

use App\Models\Complaint;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use App\Models\ComplaintTimeline;
use Carbon\Carbon;
use App\Models\ComplaintWorkDone;

use Illuminate\Support\Facades\Auth;

class ComplaintDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('status', function ($query) {
                if($query->complaint_status == '0'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-secondary">Open</span></a>';
                }elseif($query->complaint_status == '1'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-warning">Pending</span></a>';
                }elseif($query->complaint_status == '2'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-info">Work Done</span></a>';
                }elseif($query->complaint_status == '3'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-success">Completed</span></a>';
                }elseif($query->complaint_status == '4'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-primary">Closed</span></a>';
                }elseif($query->complaint_status == '5'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-danger">Canceled</span></a>';
                }
            })
            ->editColumn('created_at' , function($query){
                try {
                    return $query->created_at ? Carbon::parse($query->created_at)->format('d-m-Y h:i:s') : '';
                } catch (\Exception $e) {
                    return ''; // Return null if parsing fails
                }
            })
            ->addColumn('last_status', function ($query) {
                if($query->complaint_status == '0'){
                    return '<span class="badge badge-secondary">Open</span>';
                }elseif($query->complaint_status == '1'){
                    return '<span class="badge badge-warning">Pending</span>';
                }elseif($query->complaint_status == '2'){
                    return '<span class="badge badge-info">Work Done</span>';
                }elseif($query->complaint_status == '3'){
                    return '<span class="badge badge-success">Completed</span>';
                }elseif($query->complaint_status == '4'){
                    return '<span class="badge badge-primary">Closed</span>';
                }elseif($query->complaint_status == '5'){
                    return '<span class="badge badge-danger">Canceled</span>';
                }
            })
            ->addColumn('work_done_time', function ($query) {
                $date = $query->complaint_time_line->where('status',2)->sortByDesc('id')->first();
                if(isset($date)){
                   return Carbon::parse($date->created_at)->format('d-m-Y h:i a');
                } 
                return "NOT DONE";
            })
            ->addColumn('complaint_work_dones', function ($query) {
                $data = $query->complaint_work_dones->sortByDesc('id')->first();
                if(isset($data)){
                    return $data->done_by ?? '';
                } 
                return "NOT DONE";
            })
            ->addColumn('customer_pindcode', function ($query) {
                 return getPincode($query->customer->customer_pindcode)??'';    
            })
            ->addColumn('complaint_work_remark', function ($query) {
                $data = $query->complaint_work_dones->sortByDesc('id')->first();
                if(isset($data)){
                    return $data->remark ?? '';
                } 
                return "NOT DONE";
            })
            ->addColumn('pending_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '1')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "00:00:00";
            })
            ->addColumn('open_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '0')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "Action Not Performed";
            })
            ->addColumn('canceled_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '5')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "Action Not Performed";
            })
             ->addColumn('work_done_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '2')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "Action Not Performed";
            })
            ->addColumn('compleated_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '3')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "Action Not Performed";
            })
            ->addColumn('close_tat', function ($query) {
                $complaint_status_date = $query->complaint_time_line
                    ->where('status', '4')
                    ->sortByDesc('id') // Correct ordering method for collections
                    ->first();
              if(isset($complaint_status_date->created_at) && isset($query->created_at)){
                return calculatedTAT($complaint_status_date->created_at,$query->created_at);
              }
              return "Action Not Performed";
            })
            ->addColumn('service_bill_status', function ($query) {
                if (!$query->service_bill) {
                    return "No Action"; // Handle the case where service_bill is null
                }

                switch ($query->service_bill->status) {
                    case '0':
                        return '<a href="' . route('service_bills.show', $query->service_bill->id) . '" title="Show Service Bill">
                                    <span class="badge badge-secondary">Draft</span>
                                </a>';
                    case '1':
                        return '<span class="badge badge-warning">Claimed</span>';
                    case '2':
                        return '<span class="badge badge-info">Customer Payable</span>';
                    case '3':
                        return '<span class="badge badge-success">Approved</span>';
                    case '4':
                        return '<span class="badge badge-danger">Cancelled</span>';
                    default:
                        return "No Action";
                }
            })
            ->addColumn('service_bill_date', function ($query) {
                if (!$query->service_bill) {
                    return "Not Done Yet"; // Handle the case where service_bill is null
                }
                if($query->service_bill->status == '3'){
                     return $query->service_bill->updated_at ?? '';
                }else{
                    return "Not Done Yet"; // Handle the case where service_bill is null
                }  
            })
             ->addColumn('service_center_remark', function ($query) {
                if (!isset($query->complaint_work_dones)) {
                    return "No remark"; // Handle the case where service_bill is null
                }

                $service_bill = $query->complaint_work_dones->sortByDesc('id')->first();
                if($service_bill){
                     return  $service_bill->remark;
                }else{
                    return "No remark"; // Handle the case where service_bill is null
                }
            })
           ->addColumn('service_branch', function ($query) {
                return ($query->purchased_branch_details->branch_code ?? '-') . ' ' . ($query->purchased_branch_details->branch_name ?? '-');
            })
           ->addColumn('work_complated_duration', function ($query) {
                $complaint_status = $query->complaint_time_line->where('status' , 3)->sortByDesc('id')->first();
                if(isset($complaint_status->created_at) && isset($query->created_at)){
                   return calculatedTAT($complaint_status->created_at,$query->created_at);
                }
                return "Not Completed Yet";
            })
           ->addColumn('open_duration', function ($query) {
                $complaint_status = $query->complaint_time_line->where('status' , 0)->sortByDesc('id')->first();
                if(isset($complaint_status->created_at) && isset($query->created_at)){
                   return calculatedTAT($complaint_status->created_at,$query->created_at);
                }
                return "Not Open Yet";
            })
            ->addColumn('closed_date', function ($query) {
                $complaint_status = $query->complaint_time_line->where('status' , 4)->sortByDesc('id')->first();
                if(isset($complaint_status->created_at)){
                    return getDateInIndFomate($complaint_status->created_at) ?? '';
                }
                return "Not Closed Yet";
            })
            // ->addColumn('action', function ($query) {
            //     $btn = '';
            //     $activebtn = '';
            //     if (auth()->user()->can(['complaint_edit'])) {
            //         $btn = $btn . '<a href="'.route('complaints.edit', $query->id).'" class="btn btn-success btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' Complaint">
            //               <i class="material-icons">edit</i>
            //               </a>';
            //     }
            //     if (auth()->user()->can(['complaint_view'])) {
            //         $btn = $btn . ' <a href="'.route('complaints.show', $query->id).'" class="btn btn-info btn-just-icon btn-sm" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint">
            //                     <i class="material-icons">visibility</i>
            //                     </a>';
            //     }
            //     // $btn = '';
            //     return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
            //                     ' . $btn . '
            //                 </div>';
            // })
            ->addColumn('complaint_date', function ($query) {
                return date('d-m-Y', strtotime($query->complaint_date));
            })
            ->addColumn('complaint_number', function ($query) {
                if (auth()->user()->can(['complaint_view'])) {
                    $btn = ' <a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint">
                                '.$query->complaint_number.'
                                </a>';
                }else{
                    $btn = $query->complaint_number;
                }
                // $btn = '';
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
            })
            ->rawColumns(['action', 'status','complaint_number' , 'service_bill_status' , 'service_bill_date']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\City $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Complaint $model)
    {
        return $model->with([
            'party',
            'service_center_details',
            'customer',
            'complaint_type_details',
            'product_details.categories',
            'division_details',
            'complaint_time_line', // Ensure this is included
            'service_bill',
            'purchased_branch_details',
            'product_details.categories',
            'createdbyname',
            'complaint_work_dones'
        ])->latest()->newQuery();
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
