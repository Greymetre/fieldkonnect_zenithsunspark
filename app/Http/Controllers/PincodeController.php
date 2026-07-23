<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Illuminate\Http\Request;
use App\Http\Requests\PincodeRequest;
use App\Models\City;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\PincodeDataTable;
use App\Imports\PincodeImport;
use App\Exports\PincodeExport;
use App\Exports\PincodeTemplate;

class PincodeController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->pincode = new Pincode();
        
    }
    
    public function index(PincodeDataTable $dataTable)
    {
        abort_if(Gate::denies('pincode_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $cities = City::where('active','=','Y')->select('id', 'city_name')->orderBy('city_name','asc')->get();
        return $dataTable->render('pincode.index',compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PincodeRequest $request)
        {
            try {
                $useraccess = !empty($request['id']) ? 'pincode_edit' : 'pincode_create';
                abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');

                if (!empty($request['id'])) {
                    $pincode = Pincode::find($request['id']);

                    if (!$pincode) {
                        return request()->ajax()
                            ? response()->json(['success' => false, 'message' => 'Pincode not found'], 404)
                            : redirect()->back()->with('message_danger', 'Pincode not found');
                    }

                    $pincode->pincode = $request->pincode ?? '';
                    $pincode->city_id = $request->city_id ?? '';
                    $pincode->updated_by = $request->updated_by ?? Auth::user()->id;
                    $pincode->save();
                } else {
                    $request['created_by'] = Auth::user()->id;
                    $request['active'] = 'Y';
                    $pincode = Pincode::create($request->except(['_token']));
                }

                if (request()->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Pincode stored successfully']);
                }

                return Redirect::to('pincode')->with('message_success', 'Pincode stored successfully');
            } catch (\Exception $e) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }

                return redirect()->back()->withErrors($e->getMessage())->withInput();
            }
        }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pincode  $pincode
     * @return \Illuminate\Http\Response
     */
    public function show(pincode $pincode)
    {
        abort_if(Gate::denies('pincode_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pincode  $pincode
     * @return \Illuminate\Http\Response
     */
   public function edit($id)
    {
        abort_if(Gate::denies('pincode_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $city = Pincode::find($id);
        $city['city_name'] = isset($city['cityname']['city_name'])? $city['cityname']['city_name'] :'';
        return response()->json($city);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('pincode_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $city = Pincode::find($id);
        if($city->delete())
        {
            return response()->json(['status' => 'success','message' => 'Pincode deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in User Delete!']);
    }
    
    public function active(Request $request)
    {
        if(Pincode::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Pincode '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
      //abort_if(Gate::denies('pincode_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new PincodeImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      //abort_if(Gate::denies('pincode_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PincodeExport, 'pincodes.xlsx');
    }
    public function template()
    {
      //abort_if(Gate::denies('pincode_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PincodeTemplate, 'pincodes.xlsx');
    }
}
