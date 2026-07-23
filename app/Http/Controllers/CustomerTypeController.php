<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\Imports\CustomerTypeImport;
use App\Exports\CustomerTypeExport;
use App\Exports\CustomerTypeTemplate;
use App\Http\Requests\CustomerTypeRequest;
use App\DataTables\CustomerTypeDataTable;

class CustomerTypeController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->customertype = new CustomerType();
        
    }
    
    public function index(CustomerTypeDataTable $dataTable)
    {
        abort_if(Gate::denies('customertype_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('customertype.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerTypeRequest $request)
    {
         try
        { 
            $useraccess = !empty($request['id']) ? 'customertype_edit' : 'customertype_create' ;
            abort_if(Gate::denies($useraccess), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['type_name'] = isset($request['type_name']) ? strtolower($request['type_name']) :'';
            if(!empty($request['id']))
            {
                $category = CustomerType::where('id',$request['id'])->first();
                $category->customertype_name = isset($request['customertype_name']) ? $request['customertype_name'] :'';
                $category->type_name = isset($request['type_name']) ? $request['type_name'] :'';
                $category->updated_by = isset($request['updated_by']) ? $request['updated_by'] :Auth::user()->id;
                $category->save();
            }
            else
            {
                $request['created_by'] = Auth::user()->id;
                $request['active'] = 'Y';
                $category = CustomerType::create($request->except(['_token']));
            } 
            if($category)
            {
              return Redirect::to('customertype')->with('message_success', 'CustomerType Store Successfully');
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
     * @param  \App\Models\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerType $customerType)
    {
        abort_if(Gate::denies('customertype_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('customertype_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $customertype = CustomerType::find($id);
        return response()->json($customertype);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerType  $customerType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('customertype_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customertype = CustomerType::find($id);
        if($customertype->delete())
        {
            return response()->json(['status' => 'success','message' => 'CustomerType deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in CustomerType Delete!']);
    }
    
    public function active(Request $request)
    {
        if(CustomerType::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'CustomerType '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('customertype_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new CustomerTypeImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('customertype_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CustomerTypeExport, 'customertypes.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('customertype_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CustomerTypeTemplate, 'customertypes.xlsx');
    }
}
