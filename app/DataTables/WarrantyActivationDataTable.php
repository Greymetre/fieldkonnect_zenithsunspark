<?php

namespace App\DataTables;

use App\Models\Customers;
use App\Models\EndUser;
use App\Models\ParentDetail;
use App\Models\SchemeDetails;
use App\Models\Services;
use App\Models\WarrantyActivation;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class WarrantyActivationDataTable extends DataTable
{

    public function dataTable($query, Request $request)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('cust_status', function ($query) {
                if ($query->customer) {
                    if ($query->customer->status == '0') {
                        return 'Inactive';
                    } elseif ($query->customer->status == '1') {
                        return 'Active';
                    }
                } else {
                    return 'Inactive';
                }
                return '-';
            })
            ->addColumn('customer.customer_name', function ($query) {
                if ($query->customer) {
                    return '<a href="' . route("warranty_activation.show", encrypt($query->id)) . '">' . $query->customer->customer_name . '</a>';
                } else {
                    return '-';
                }
            })
            ->addColumn('status', function ($query) {
                if ($query->status == '0') {
                    return 'In Verification';
                } elseif ($query->status == '1') {
                    return 'Activated';
                } elseif ($query->status == '2') {
                    return 'Pending Activated';
                } elseif ($query->status == '3') {
                    return 'Reject';
                }
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';
                if (auth()->user()->can(['scheme_delete'])) {
                    $btn = $btn . ' <a href="' . route("warranty_activation.edit", encrypt($query->id)) . '" class="btn btn-success btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' Warranty Activation">
                                <i class="material-icons">edit</i>
                              </a>';
                }
                if (auth()->user()->can(['scheme_delete'])) {
                    $btn = $btn . ' <a href="#" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' Warranty Activation">
                                <i class="material-icons">clear</i>
                              </a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>' . $activebtn;
            })
            ->rawColumns(['action', 'cust_status', 'customer.customer_name']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Scheme $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(WarrantyActivation $model, Request $request)
    {

        $data = $model->with('customer', 'seller_details', 'product_details');
        if ($request->branch_id && $request->branch_id != null && $request->branch_id != '') {
            $data->where('branch_id', $request->branch_id);
        }
        if ($request->parent_customer && $request->parent_customer != null  && $request->parent_customer != '') {
            $data->where('customer_id', $request->parent_customer);
        }
        if ($request->status != null  && $request->status != '') {
            $data->where('status', $request->status);
        }
        if ($request->product_id && $request->product_id != null  && $request->product_id != '') {
            $data->where('product_id', $request->product_id);
        }
        if ($request->state_id && $request->state_id != null  && $request->state_id != '') {
            $all_end_users = EndUser::where('state_id', $request->state_id)->pluck('id');
            if (count($all_end_users) > 0) {
                $data->whereIn('end_user_id', $all_end_users);
            } else {
                $data->where('id', '0');
            }
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
