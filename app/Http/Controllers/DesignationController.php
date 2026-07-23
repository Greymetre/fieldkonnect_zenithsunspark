<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use App\Http\Requests\BrandRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;

use App\DataTables\DesignationDataTable;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct() 
    {     
        $this->middleware('auth');   
        $this->designation = new Designation();
    }
    
    
     public function index(DesignationDataTable $dataTable)
    {
        abort_if(Gate::denies('designation'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('designation.index');
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
		$status = Designation::where('id', $request['id'])->update($request->except(['_token', 'id', 'image']));
	    } else {
		$request['active'] = 'Y';
		$request['created_by'] = Auth::user()->id;
		// Ensure 'designation_name' is present in the request data
		$request['designation_name'] = $request->input('designation_name', ''); // Replace 'designation_name' with the actual field name
		$status = Designation::create($request->except(['_token', 'image']));
	    }

	    if ($status) {
		return Redirect::to('designation')->with('message_success', 'Designation Store Successfully');
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
	$designation = Designation::find($id);

	if (!$designation) {
          return response()->json(['status' => 'error', 'message' => 'Designation not found']);
	}

	return response()->json($designation);
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
	    $designation = Designation::find($id);

	    if (!$designation) {
		return response()->json(['status' => 'error', 'message' => 'Designation not found']);
	    }

	    // Toggle the 'active' field
	    $designation->update(['active' => $designation->active === 'Y' ? 'N' : 'Y']);
	    return response()->json(['status' => 'success', 'message' => 'Designation status changed successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Designation::find($id);
        if($user->delete())
        {
            return response()->json(['status' => 'success','message' => 'Designation deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Designation Delete!']);
    }
}
