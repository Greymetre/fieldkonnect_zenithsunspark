<?php

namespace App\DataTables;

use App\Models\Customers;
use App\Models\ParentDetail;
use App\Models\SchemeDetails;
use App\Models\Services;
use App\Models\Resignation;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class ResignationDataTable extends DataTable
{

    public function dataTable($query, Request $request)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($data) {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('name', function ($data) {
                return $data->first_name . ' ' . $data->middle_name . ' ' . $data->last_name;
            })
            ->editColumn('status', function ($data) {
                $status = '';
                if ($data->status == '0') {
                    $status = '<a href="' . route('resignations.show', $data->id) . '" class="badge badge-warning">Pending</a>';
                } else if ($data->status == '1') {
                    $status = '<a href="' . route('resignations.show', $data->id) . '" class="badge badge-info">Accept</a>';
                } else if ($data->status == '2') {
                    $status = '<a href="' . route('resignations.show', $data->id) . '" class="badge badge-danger">Reject</a>';
                } else if ($data->status == '3') {
                    $status = '<a href="' . route('resignations.show', $data->id) . '" class="badge badge-success">Revoke</a>';
                } else if ($data->status == '4') {
                    $status = '<a href="' . route('resignations.show', $data->id) . '" class="badge badge-success">Approve</a>';
                } else if ($data->status == '5') {
                    $status = '<a href="' . route('resignations.show', $data->id) . '" class="badge badge-info">Hold</a>';
                }

                return $status;
            })
            ->editColumn('action', function ($data) {
                return '
                    <form action="' . route('resignations.destroy', $data->id) . '" method="POST" style="display: inline;" id="delete-form-' . $data->id . '">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <a href="javascript:void(0);" 
                            class="btn btn-danger btn-just-icon btn-sm" 
                            title="Delete Resignation" 
                            onclick="confirmDelete(' . $data->id . ')">
                            <i class="material-icons">close</i>
                        </a>
                    </form>
                    <script>
                        function confirmDelete(id) {
                            if (confirm(\'Are you sure you want to delete this resignation?\')) {
                                document.getElementById(\'delete-form-\' + id).submit();
                            }
                        }
                    </script>';
            })

            ->rawColumns(['name', 'action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Scheme $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Resignation $model, Request $request)
    {
        $data = $model->with('division', 'branch', 'user.reportinginfo', 'user.getdesignation');

        if ($request->status != null  && $request->status != '') {
            $data->where('status', $request->status);
        }
        if ($request->branch_id != null  && $request->branch_id != '') {
            $data->where('branch_id', $request->branch_id);
        }
        if ($request->user_id != null  && $request->user_id != '') {
            $data->where('user_id', $request->user_id);
        }
        if ($request->division_id != null  && $request->division_id != '') {
            $data->where('division_id', $request->division_id);
        }
        if ($request->start_date != null  && $request->start_date != '') {
            $data->where('submit_date', '>=', $request->start_date);
        }
        if ($request->end_date != null  && $request->end_date != '') {
            $data->where('submit_date', '<=', $request->end_date);
        }

        $all_users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->get();

        if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Sub_Admin') && !auth()->user()->hasRole('HR_Admin')) {
            $userid = !empty($userid) ? $userid : Auth::user()->id;
            $all_ids_array = array($userid);
            $test = getAllChild(array($userid), $all_users);
            while (count($test) > 0) {
                $all_ids_array = array_merge($all_ids_array, $test);
                $test = getAllChild($test, $all_users);
            }
            $data->whereIn('user_id', $all_ids_array);
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
