<?php

namespace App\DataTables;

use App\Models\Customers;
use App\Models\ParentDetail;
use App\Models\SchemeDetails;
use App\Models\Services;
use App\Models\Expenses;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpensesDataTable extends DataTable
{

    public function dataTable($query, Request $request)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('id', function ($query) {
                return $query->id ?? '';
            })
            ->addColumn('checkbox', function ($query) {
                if($query->user_id == Auth::user()->id){
                    return '';
                }
                return '<input type="checkbox" class="row-checkbox" value="' . $query->id . '">';
            })
            ->addColumn('users.name', function ($query) {
                $name = '(' . $query->users->employee_codes . ')' . $query->users->name;
                return $name;
            })
            ->addColumn('users.getdesignation.designation_name', function ($query) {
                return $query->users->getdesignation->designation_name ?? '';
            })
            ->addColumn('expense_type.name', function ($query) {
                return $query->expense_type->name ?? '';
            })

            ->editColumn('date', function ($query) {
                // return $query->date ?? '';
                return $query->date ? date("d/m/Y", strtotime($query->date)) : date("d/m/Y", strtotime($query->created_at));
            })
            ->editColumn('claim_amount', function ($query) {
                return $query->claim_amount ?? '';
            })
            ->editColumn('approve_amount', function ($query) {
                return $query->approve_amount ?? '';
            })
            ->editColumn('note', function ($query) {
                return $query->note ?? '';
            })
            ->editColumn('total_km', function ($query) {
                return $query->total_km ?? '';
            })
            ->editColumn('attech', function ($query) {
                $is_avail = 'No';
                if ($query->getMedia('expense_file')->count() > 0) {
                    foreach ($query->getMedia('expense_file') as $image) {
                        if (Storage::disk('s3')->exists($image->getPath())) {
                            $is_avail = 'Yes';
                        }
                    }
                }

                return $is_avail;
            })

            ->addColumn('users.getbranch.branch_name', function ($query) {
                return $query->users->getbranch->branch_name ?? '';
            })

            ->addColumn('date_create', function ($query) {
                $genrate = $query->get_time_history->where('status_type', 'generated')->first();
                if ($genrate) {
                    return  date("d/m/Y", strtotime($genrate->created_at));
                } else {
                    return  date("d/m/Y", strtotime($query->created_at));
                }
            })

            ->addColumn('checker_status', function ($query) use ($request) {
                $btn = '';
                $activebtn = '';
                if ($query->checker_status == '1') {
                    $btn = $btn . "<button type='button' onclick='showExpense($query->id)' class='btn btn-success'>Approved</span></button>";
                } elseif ($query->checker_status == '2') {
                    $btn = $btn . "<button type='button' onclick='showExpense($query->id)' class='btn btn-danger'>Rejected</span></button>";
                } elseif ($query->checker_status == '3') {
                    $btn = $btn . "<button type='button' onclick='showExpense($query->id)' class='btn btn-dark'>Checked</span></button>";
                } elseif ($query->checker_status == '4') {
                    $btn = $btn . "<button type='button' onclick='showExpense($query->id)' class='btn btn-info'>Checked By Reporting</span></button>";
                }elseif ($query->checker_status == '5') {
                    $btn = $btn . "<button type='button' onclick='showExpense($query->id)' class='btn btn-dark'>Hold</span></button>";
                } else {
                    $btn = $btn . "<button type='button' onclick='showExpense($query->id)' class='btn btn-warning'>Pending</span></button>";
                }
                // if ($request->ip() == '111.118.252.250' || $request->ip() == 'http://192.168.0.210/') {
                //     $btn = $btn . "<a href='".url('/map-all').'?id='.$query->user_id."'  class='btn btn-warning'>LOC</span></a>";
                // }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;
            })

            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';

                if (auth()->user()->can(['expenses_delete'])) {

                    $btn = $btn . ' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.expenses.title_singular') . '">
                                            <i class="material-icons">clear</i>
                                          </a>';
                }


                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;
            })
            ->rawColumns(['checker_status', 'action', 'users.name', 'checkbox']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Scheme $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Expenses $model, Request $request)
    {
        $login_userid = Auth::user()->id;
        $all_users = User::all();
        $userinfo = User::where('id', '=', $login_userid)->first();
        if (!$userinfo->hasRole('superadmin') && !$userinfo->hasRole('Admin') && !$userinfo->hasRole('Sub_Admin') && !$userinfo->hasRole('HR_Admin') && !$userinfo->hasRole('HO_Account')  && !$userinfo->hasRole('Sub_Support') && !$userinfo->hasRole('Accounts Order') && !$userinfo->hasRole('Service Admin') && !$userinfo->hasRole('All Customers')) {
            $userids = array($login_userid);
            $test = getAllChild(array($login_userid), $all_users);
            while (count($test) > 0) {
                $userids = array_merge($userids, $test);
                $test = getAllChild($test, $all_users);
            }
        } elseif ($userinfo->hasRole('Accounts Order')) {
            $userids = User::whereIn('branch_id', explode(',', $userinfo->branch_show))->pluck('id')->toArray();
            $test = getAllChild(array($login_userid), $all_users);
            while (count($test) > 0) {
                $userids = array_merge($userids, $test);
                $test = getAllChild($test, $all_users);
            }
        } else {
            $userids = User::pluck('id')->toArray();
        }
        $data = $model->with('expense_type', 'users');
        if (!empty($request['payroll'])) {

            $payrollid = $request['payroll'];
            $data->whereHas('users', function ($query) use ($payrollid) {
                $query->where('payroll', $payrollid);
            });
        }
        if (!$userinfo->hasRole('superadmin') && !$userinfo->hasRole('Admin') && !$userinfo->hasRole('Sub_Admin')){
            $data->whereIn('user_id', $userids);
        }

        if (!empty($request['executive_id'])) {
            if ($request->executive_id) {
                $request->session()->put('executive_id', $request->executive_id);
            }
            $data->where('user_id', $request['executive_id']);
        }
        if (!empty($request['search']['value']) && $request['search']['value'] != '' && $request['search']['value'] != NULL) {
            $data->where('claim_amount', $request['search']['value']);
        }

        if (!empty($request['expenses_type'])) {
            $data->where('expenses_type', $request['expenses_type']);
        }
        
        if (!empty($request['attechments'])) {
            if ($request['attechments'] == 'yes') {
                $data->whereHas('media');
            } elseif ($request['attechments'] == 'no') {
                $data->whereDoesntHave('media');
            }
        }

        if (!empty($request['branch_id'])) {
            $branch_user_id = User::where('branch_id', $request['branch_id'])->pluck('id');
            if (!empty($branch_user_id)) {
                $data->whereIn('user_id', $branch_user_id);
            }
        }
        if (!empty($request['division_id'])) {
            $division_user_id = User::where('division_id', $request['division_id'])->pluck('id');
            if (!empty($division_user_id)) {
                $data->whereIn('user_id', $division_user_id);
            }
        }

        if (!empty($request['expense_id'])) {
            $data->where('id', $request['expense_id']);
        }


        if (!empty($request['start_date']) && !empty($request['end_date'])) {
            $data->whereBetween('date', [$request['start_date'], $request['end_date']]);
        }


        if ($request['status'] != NULL) {
            $data->where('checker_status', $request['status']);
        }

        // dd($data->toSql());
        $data = $data->newQuery();
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
            ->setTableId('getallexpenses')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
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
