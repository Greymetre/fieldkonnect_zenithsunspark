<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use App\Http\Requests\BrandRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\DivisionExport;
use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\DivisionDataTable;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->division = new Division();
    }
    
    
     public function index(DivisionDataTable $dataTable)
    {
        abort_if(Gate::denies('division'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('division.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       try {
	    if (!empty($request['id'])) {
		$status = Division::where('id', $request['id'])->update($request->except(['_token', 'id', 'image']));
	    } else {
		$request['active'] = 'Y';
		$request['created_by'] = Auth::user()->id;
		// Ensure 'designation_name' is present in the request data
		$request['division_name'] = $request->input('division_name', ''); // Replace 'designation_name' with the actual field name
		$status = Division::create($request->except(['_token', 'image']));
	    }

	    if ($status) {
		return Redirect::to('division')->with('message_success', 'Division Store Successfully');
	    }

	    return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
	} catch (\Exception $e) {
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
	$division = Division::find($id);

	if (!$division) {
          return response()->json(['status' => 'error', 'message' => 'Division not found']);
	}

	return response()->json($division);
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
	    $divsion = Division::find($id);

	    if (!$divsion) {
		return response()->json(['status' => 'error', 'message' => 'Divsion not found']);
	    }

	    // Toggle the 'active' field
	    $divsion->update(['active' => $divsion->active === 'Y' ? 'N' : 'Y']);
	    return response()->json(['status' => 'success', 'message' => 'Divsion status changed successfully']);
	}
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy($id)
    {
        $user = Division::find($id);
        if($user->delete())
        {
            return response()->json(['status' => 'success','message' => 'Division deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Division Delete!']);
    }

    public function division_report_download(Request $request) {

     if($request->export_division_report) {

         abort_if(Gate::denies('division_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
         if (ob_get_contents()) ob_end_clean();
         ob_start();

         return Excel::download(new DivisionExport($request), 'divisions.xlsx');
     }
 }
}
