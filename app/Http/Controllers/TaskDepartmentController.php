<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\TaskDepartment;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;
use Validator;
use Gate;
use Excel;
use App\Exports\DepartmentExport;



class TaskDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->departments = new Department();
        $this->path = 'departments';
    }



    public function index(Request $request)
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $name = $request->name;

        $taskDepartment=TaskDepartment::create([
            'name' => $name,
            'created_by' => Auth::user()->id,
            'active' => 'Y'
        ]);

        return response()->json(['status' => true, 'message' => 'Department added successfully!','data'=>$taskDepartment]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $departments = Department::find($id);
        if ($departments) {
            $departments->name = $request->name;
            $departments->save();
            return redirect(route('departments.index'))->with('message', 'Department updated successfully');
        } else {
            return redirect(route('departments.index'))->with('error', 'Somthing went wroung');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sub_division = Department::find($id);
        if ($sub_division->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Department deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Department Delete!']);
    }

    public function active(Request $request)
    {

        if (Department::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y'])) {
            $message = ($request['active'] == 'Y') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'Department ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }

    public function department_report_download(Request $request)
    {
        if ($request->export_department_report) {

            abort_if(Gate::denies('department_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();

            return Excel::download(new DepartmentExport($request), 'departments.xlsx');
        }
    }
}
