<?php

namespace App\DataTables;

use App\Models\Customers;
use App\Models\ParentDetail;
use App\Models\SchemeDetails;
use App\Models\Services;
use App\Models\EndUser;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class EndUserDataTable extends DataTable
{

    public function dataTable($query, Request $request)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($data) {
                // dd($data);
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('pincodeDetails.pincode', function ($data) {
                return $data->pincodeDetails ? $data->pincodeDetails->pincode : '';
            })
            ->editColumn('action', function ($data) {
                if (Auth::user()->can(['end_user_edit'])) {
                    return '<a href="' . route('end_user.edit', $data->id) . '" class="btn btn-info btn-just-icon btn-sm" title="Edit End User" ><i class="material-icons">edit</i></a>';
                } else {
                    return '';
                }
            })
            ->addColumn('status', function ($data) {
                if (auth()->user()->can(['user_active'])) {
                    $active = ($data->status == '1') ? 'checked="" value="' . $data->status . '"' : 'value="' . $data->status . '"';
                    return '<div class="togglebutton">
                        <label>
                          <input type="checkbox"' . $active . ' id="' . $data->id . '" class="activeRecord">
                          <span class="toggle"></span>
                        </label>
                      </div>';
                }
            })

            ->rawColumns(['pincodeDetails.pincode', 'action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Scheme $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(EndUser $model, Request $request)
    {

        $data = $model->with('state', 'district', 'city', 'pincodeDetails');

        if ($request->start_date != null  && $request->start_date != '') {
            $data->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date != null  && $request->end_date != '') {
            $data->whereDate('created_at', '<=', $request->end_date);
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
