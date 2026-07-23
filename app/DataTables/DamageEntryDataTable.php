<?php

namespace App\DataTables;

use App\Models\Customers;
use App\Models\ParentDetail;
use App\Models\SchemeDetails;
use App\Models\Services;
use App\Models\DamageEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class DamageEntryDataTable extends DataTable
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
                return $data->customer->first_name . ' ' . $data->customer->last_name;
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
            ->editColumn('subcategory_name', function ($data) {

                return $data->scheme ? $data->scheme->product?->subcategories?->subcategory_name : '';
            })
            ->editColumn('product_name', function ($data) {

                return $data->scheme ? $data->scheme->product?->product_name : '';
            })
            ->editColumn('attach', function ($data) {
                $html = '';
                $attachments = $data->getMedia("*");
                if (count($attachments) > 0) {
                    foreach ($attachments  as $attachment) {
                        $html .= '<a target="_blank" href="' . $attachment->getFullUrl() . '" data-lightbox="mygallery">
                        <img class="img-fluid rounded m-2" src="' . $attachment->getFullUrl() . '"  width="100">
                         </a>';
                    }
                }
                return $html;
            })
            ->editColumn('status', function ($data) {
                if ($data->status == "0") {
                    return '<button type="button" data-ccode="' . $data->coupon_code . '" data-status="' . $data->status . '" id="' . $data->id . '" class="btn btn-warning changeStatus">Pennding</button>';
                } elseif ($data->status == "1") {
                    return '<button type="button" data-ccode="' . $data->coupon_code . '" data-status="' . $data->status . '" id="' . $data->id . '" class="btn btn-success changeStatus">Approved</button>';
                } elseif ($data->status == "2") {
                    return '<button type="button" data-ccode="' . $data->coupon_code . '" data-status="' . $data->status . '" id="' . $data->id . '" class="btn btn-danger changeStatus">Reject</button>';
                }
            })
            ->rawColumns(['contact_person', 'parent_name', 'subcategory_name', 'product_name', 'customer.name', 'attach', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Scheme $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DamageEntry $model, Request $request)
    {

        $data = $model->with('customer', 'scheme');

        if ($request->status != null  && $request->status != '') {
            $data->where('status', $request->status);
        }

        if ($request->start_date && !empty($request->start_date) && $request->end_date && !empty($request->end_date)) {
            $data->whereDate('created_at', '>=', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date);
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
