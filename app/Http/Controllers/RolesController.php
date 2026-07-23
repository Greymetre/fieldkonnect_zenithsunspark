<?php

namespace App\Http\Controllers;
ini_set('max_execution_time', 18000);
set_time_limit(18000);

use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\Models\User;
use App\Imports\RoleImport;
use App\Exports\RoleExport;
use App\Exports\RoleTemplate;
use App\Jobs\SyncUserPermissionsJob;

class RolesController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->roles = new Role();
        $this->users = new User();
        
    }
    public function index(Request $request)
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            if(auth()->user()->hasRole('superadmin')){
                $data = $this->roles->latest();
            }else{
                $data = $this->roles->where('id', '>', '1')->latest();    
            }
            
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function($data)
                    {
                        return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                    })
                    ->addColumn('action', function ($query) {
                        $btn = '';
                        // if(auth()->user()->can(['user_edit']))
                        // {
                            $btn = $btn.'<a href="'.url("roles/".$query->id.'/edit') .'" class="btn btn-info btn-just-icon btn-sm edit" id="'.encrypt($query->id).'" title="'.trans('panel.global.edit').' '.trans('panel.user.title_singular').'">
                                  <i class="material-icons">edit</i>
                                </a>';
                        //}
                        return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                        '.$btn.'
                                    </div>';
                    })
                    ->rawColumns(['action','added_by'])
                    ->make(true);
        }
        return view('roles.index');
    }

    public function create()
    {
        //abort_if(Gate::denies('role_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // if(Auth::user()->hasRole('superadmin'))
        // {
        //     $permissions = Permission::pluck('name', 'id');
        // }
        // else
        // {
        //     $permissions = Permission::whereIn('id',Auth::user()->getAllPermissions()->pluck('id'))->pluck('name', 'id');
        // }
        $permissions = Permission::pluck('name', 'id');
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $role = Role::create($request->all());
        $role->permissions()->sync($request->input('permissions', []));
        return redirect()->route('roles.index');

    }

    public function edit(Role $role)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // if(Auth::user()->hasRole('superadmin'))
        // {
        //     $permissions = Permission::pluck('name', 'id');
        // }
        // else
        // {
        //     $permissions = Permission::whereIn('id',Auth::user()->getAllPermissions()->pluck('id'))->pluck('name', 'id');
        // }
        $permissions = Permission::pluck('name', 'id');
        $role->load('permissions');

        return view('roles.edit', compact('permissions', 'role'));
    }
    public function update(Request $request, Role $role)
    {
        $oldpermissions = DB::table('role_has_permissions')->where('role_id', $role['id'])->pluck('permission_id')->toArray();
        $role->update($request->all());
        $role->permissions()->sync($request->input('permissions', []));
        // $newpermissions = $request['permissions'];
        // $changes = array_merge(array_diff($newpermissions, $oldpermissions), array_diff($oldpermissions, $newpermissions));
        // $permissions = Permission::whereIn('id',$request['permissions'])->select('name','guard_name')->get();
        // $all_user_ids = DB::table('model_has_roles')->where('role_id', '=', $role->id)->pluck('model_id')->toArray();
        // dispatch(new SyncUserPermissionsJob($all_user_ids, $newpermissions));
        
        return redirect()->route('roles.index');
    }
    public function show(Role $role)
    {
        abort_if(Gate::denies('role_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->load('permissions');

        return view('roles.show', compact('role'));
    }

    public function destroy(Role $role)
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->delete();

        return back();

    }

    public function massDestroy(MassDestroyRoleRequest $request)
    {
        Role::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('role_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new RoleImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('role_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new RoleExport, 'roles.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('role_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new RoleTemplate, 'roles.xlsx');
    }
}
