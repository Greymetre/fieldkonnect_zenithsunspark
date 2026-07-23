<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\MassDestroyPermissionRequest;
use App\Http\Requests\PermissionRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\Imports\PermissionImport;
use App\Exports\PermissionExport;
use App\Exports\PermissionTemplate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->permissions = new Permission();
        
    }
    public function index(Request $request)
    {
        abort_if(Gate::denies('permission_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $data = $this->permissions->latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function($data)
                    {
                        return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                    })
                    ->make(true);
        }
        return view('permissions.index');
    }

    public function create()
    {
        abort_if(Gate::denies('permission_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('permissions.create');
    }

    public function store(PermissionRequest $request)
    {
        $request['guard_name'] = 'App\Models\User';
        $permission = Permission::create($request->all());
        return redirect()->route('permissions.index');
    }

    public function edit(Permission $permission)
    {
        abort_if(Gate::denies('permission_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('permissions.edit', compact('permission'));
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->update($request->all());

        return redirect()->route('permissions.index');

    }

    public function show(Permission $permission)
    {
        abort_if(Gate::denies('permission_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('permissions.show', compact('permission'));
    }

    public function destroy(Permission $permission)
    {
        abort_if(Gate::denies('permission_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permission->delete();

        return back();

    }

    public function massDestroy(MassDestroyPermissionRequest $request)
    {
        Permission::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }

    public function upload(Request $request) 
    {
        abort_if(Gate::denies('permission_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new PermissionImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('permission_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PermissionExport, 'permissions.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('permission_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PermissionTemplate, 'permissions.xlsx');
    }
}
