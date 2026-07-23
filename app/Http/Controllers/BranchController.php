<?php

namespace App\Http\Controllers;
use App\Models\Branch;
use Illuminate\Http\Request;
// use App\Http\Requests\BranchRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\BranchExport;
use DataTables;
use Validator;
use Gate;
use App\DataTables\BranchDataTable;
use App\Models\WareHouse;
use Excel;


class BranchController extends Controller
{

    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->branches = new Branch();
        $this->path = 'branches';
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BranchDataTable $dataTable)
    {
        abort_if(Gate::denies('branch'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $warehouses = WareHouse::all();
        return $dataTable->render('branches.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //abort_if(Gate::denies('branch_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('branches.create')->with('branches',$this->branches);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
        try
        { 
            $permission = !empty($request['id']) ? 'branch_edit' : 'branch_create' ;
            //abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $status = '';

            if(!empty($request['id']))
            {
                $status = Branch::where('id',$request['id'])->update($request->except(['_token','id','image']));
            }
            else
            {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = Branch::create($request->except(['_token','image']));
            } 
            if($status)
            {
              return Redirect::to('branches')->with('message_success', 'Branch Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
        }        
        catch(\Exception $e)
        {

          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
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
	    $branch = Branch::find($id);

	    if (!$branch) {
		return response()->json(['status' => 'error', 'message' => 'Branch not found']);
	    }

	    return response()->json($branch);
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
	    $branch = Branch::find($id);

	    if (!$branch) {
		return response()->json(['status' => 'error', 'message' => 'Branch not found']);
	    }

	    // Toggle the 'active' field
	    $branch->update(['active' => $branch->active === 'Y' ? 'N' : 'Y']);

	    return response()->json(['status' => 'success', 'message' => 'Branch status changed successfully']);
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Branch::find($id);
        if($user->delete())
        {
            return response()->json(['status' => 'success','message' => 'Branch deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Branch Delete!']);
    }

    /**
     *  Export the branch report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function branch_report_download(Request $request) {
        if($request->export_branch_report) {

            abort_if(Gate::denies('branch_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();

            return Excel::download(new BranchExport($request), 'branch.xlsx');
        }
    }
}
