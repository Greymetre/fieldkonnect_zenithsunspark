<?php

namespace App\DataTables;

use App\Models\Customers;
use App\Models\ParentDetail;
use App\Models\SchemeDetails;
use App\Models\Services;
use App\Models\Redemption;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class RedemptionDataTable extends DataTable
{

    public function dataTable($query, Request $request)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($data) {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('contact_person', function ($data) {
                return $data->customer->first_name.' '.$data->customer->last_name;
            })
            ->editColumn('customer.name', function ($data) {
                $customer_name = '<a target="_blank" href="' . route('customers.show', [encrypt($data->customer->id)]) . '">' . $data->customer->name . '</a>';

                return $customer_name;
            })
            ->editColumn('parent_name', function ($data) {
                $parents = '';
                if (!empty($data->customer->getparentdetail)) {
                    foreach ($data->customer->getparentdetail as $key => $parent_data) {
                        if ($key == (count($data->customer->getemployeedetail) - 1)) {
                            $parents .= isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name : '';
                        } else {
                            $parents .= isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name . ', ' : '';
                        }
                    }
                }
                return $parents;
            })
            ->addColumn('mode', function ($query) {
                if($query->redeem_mode == '1'){
                    return 'Gift';
                }elseif($query->redeem_mode == '2'){
                    return 'NEFT';
                }
              })
            ->editColumn('status', function ($data) {
                if ($data->status == '0') {
                    return '<button id="' . $data->id . '" data-status="0" class="ChangeStatus btn btn-warning">Pendding</button>';
                } elseif ($data->status == '1') {
                    return '<button id="' . $data->id . '" data-status="1" class="successStatus btn btn-primary">Approved</button>';
                } elseif ($data->status == '2') {
                    return '<button id="' . $data->id . '" class="ChangeStatus btn btn-danger">Rejected</button>';
                }elseif ($data->status == '3') {
                    return "<button id='" . $data->id . "' class='btn btn-success'>Success</button><button data-details='" . json_encode($data->neft_details) . "' class='btn btn-info neft_details'>details</button>";
                }elseif ($data->status == '4') {
                    return '<button id="' . $data->id . '" data-status="2" class="btn btn-warning">Fail</button><p class="badge badge-info">Remark - '.$data->neft_details->remark.'</p>';
                }
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';
                if (auth()->user()->can(['redemption_delete'])) {
                    $btn = $btn . ' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' Transaction History">
                    <i class="material-icons">clear</i>
                    </a>';
                }
                if (auth()->user()->can(['redemption_edit'])) {
                    $btn = $btn . '<a href="'.route('redemptions.edit', $query->id).'" class="btn btn-success btn-just-icon btn-sm edit"title="' . trans('panel.global.edit') . ' Redemption">
                              <i class="material-icons">edit</i>
                            </a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>' . $activebtn;
            })
            ->rawColumns(['action', 'contact_person', 'parent_name', 'mode', 'status', 'customer.name']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Scheme $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Redemption $model, Request $request)
    {
        $data = $model->with('customer')->where('redeem_mode', '2');
        if ($request->branch_id && $request->branch_id != null && count($request->branch_id) > 0) {
            $branch_user_id = User::whereIn('branch_id', $request['branch_id'])->pluck('id');
            if (!empty($branch_user_id)) {
                $branch_customer_id = Customers::whereIn('executive_id', $branch_user_id)->pluck('id');
            }
            if (!empty($branch_customer_id)) {
                $data->whereIn('customer_id', $branch_customer_id);
            }
        }
        if ($request->parent_customer && $request->parent_customer != null  && count($request->parent_customer) > 0) {
            $parent_customer_id = ParentDetail::whereIn('parent_id', $request->parent_customer)->pluck('customer_id');

            if (!empty($parent_customer_id)) {
                $data->whereIn('customer_id', $parent_customer_id);
            }
        }
        if ($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != '') {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $endDate = date('Y-m-d', strtotime($request->end_date));
            $data = $data->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        }
        if ($request->customer_id && $request->customer_id != null  && $request->customer_id != '') {
            $data->where('customer_id', $request->customer_id);
        }
        if ($request->status != null  && $request->status != '') {
            $data->where('status', $request->status);
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
