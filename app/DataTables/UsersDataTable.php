<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UsersDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($data) {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('name', function ($data) {
                return $data->name . ($data->user_customer ? ' (' . $data->user_customer->sap_code . ')' : '');
            })
            ->editColumn('getBranchNames', function ($data) {
                $branchIds = array_filter(explode(',', $data->branch_id)); // Remove empty values
                if (!empty($branchIds)) {
                    return \App\Models\Branch::whereIn('id', $branchIds)->pluck('branch_name')->implode(', ');
                } else {
                    return "-";
                }
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';
                if (auth()->user()->can(['user_edit'])) {
                    if ($query->roles()->where('id', '29')->exists()) {
                        $btn = $btn . '<a href="' . url("customer-user?id=" . encrypt($query->id)) . '" class="btn btn-info btn-just-icon btn-sm edit" id="' . encrypt($query->id) . '" title="' . trans('panel.global.edit') . ' ' . trans('panel.user.title_singular') . '">
                              <i class="material-icons">edit</i>
                            </a>';
                    } else {
                        $btn = $btn . '<a href="' . url("users/" . encrypt($query->id) . '/edit') . '" class="btn btn-info btn-just-icon btn-sm edit" id="' . encrypt($query->id) . '" title="' . trans('panel.global.edit') . ' ' . trans('panel.user.title_singular') . '">
                              <i class="material-icons">edit</i>
                            </a>';
                    }
                }
                // if(auth()->user()->can(['user_show']))
                // {
                //   $btn = $btn.'<a href="'.url("users/".encrypt($query->id)).'" class="btn btn-theme btn-just-icon btn-sm show" id="'.encrypt($query->id).'" title="'.trans('panel.global.show').' '.trans('panel.user.title_singular').'">
                //         <i class="material-icons">visibility</i>
                //       </a>';
                // }
                if (auth()->user()->can(['user_delete'])) {
                    $btn = $btn . ' <a type="button" href="#" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.user.title_singular') . '">
                                <i class="material-icons">clear</i>
                              </a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
            })
            ->addColumn('active', function ($query) {
                if (auth()->user()->can(['user_active'])) {
                    $active = ($query->active == 'Y') ? 'checked="" value="' . $query->active . '"' : 'value="' . $query->active . '"';
                    return '<div class="togglebutton">
                        <label>
                          <input type="checkbox"' . $active . ' id="' . $query->id . '" class="activeRecord">
                          <span class="toggle"></span>
                        </label>
                      </div>';
                }
            })
            ->addColumn('image', function ($query) {
                $profileimage = asset('assets/img/placeholder.jpg');
                if ($query->getMedia('profile_image')->count() > 0 && Storage::disk('s3')->exists($query->getMedia('profile_image')[0]->getPath())) {
                    return '<img src="' . $query->getMedia('profile_image')[0]->getFullUrl() . '" border="0" width="70" class="img-rounded imageDisplayModel" align="center" />';
                } else {
                    return '<img src="' . $profileimage . '" border="0" width="70" class="img-rounded imageDisplayModel" align="center" />';
                }
            })
            ->addColumn('roles', function ($query) {
                $roles = '';
                if (count($query->roles) > 0) {
                    foreach ($query->roles as $k => $role) {
                        if (count($query->roles) == ($k + 1)) {
                            $roles .= $role->name;
                        } else {
                            $roles .= $role->name . ', ';
                        }
                    }
                }
                return $roles;
            })
            ->addColumn('wahtsappmobile', function ($query) {
                $whatsappLink = "https://wa.me/" . $query->mobile;
                return '<a style="display: flex;align-items: center;" href="' . $whatsappLink . '" target="_blank">
                            <i class="fa fa-whatsapp text-success mr-1" style="font-size:20px"></i>
                        </a> ';
            })
            ->rawColumns(['action', 'image', 'active', 'roles', 'getBranchNames', 'wahtsappmobile']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model, Request $request)
    {
        $userids = getUsersReportingToAuth();
        $data = $model->with('createdbyname', 'getbranch', 'getdesignation', 'reportinginfo', 'userinfo', 'getdivision')
            ->where(function ($query) use ($userids, $request) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Sub_Admin')) {
                    $query->whereIn('id', $userids);
                }
                if($request->active && !empty($request->active)){
                    $query->where('active', $request->active);
                }
                if($request->division_id && !empty($request->division_id)){
                    $query->where('division_id', $request->division_id);
                }
                if($request->branch_id && !empty($request->branch_id)){
                    $query->where('branch_id', $request->branch_id);
                }
                if($request->department_id && !empty($request->department_id)){
                    $query->where('department_id', $request->department_id);
                }
            })
            ->whereHas('roles', function ($query) use ($request) {
                if ($request->user_type == 'customer') {
                    $query->whereIn('id', config('constants.customer_roles'));
                } else {
                    $query->whereNotIn('id', config('constants.customer_roles'));
                }
            })
            ->latest()
            ->newQuery();

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
            ->setTableId('users-table')
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
