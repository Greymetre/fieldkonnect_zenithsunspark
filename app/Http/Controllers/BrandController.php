<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\BrandRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\DataTables\BrandDataTable;
use Excel;
use App\Imports\BrandImport;
use App\Exports\BrandExport;
use App\Exports\BrandTemplate;

class BrandController extends Controller
{
     public function __construct() 
    {     
        $this->middleware('auth');   
        $this->brands = new Brand();
        $this->path = 'brands';
    }
    
    public function index(BrandDataTable $dataTable)
    {
        abort_if(Gate::denies('brand_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('brands.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('brand_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('brands.create')->with('brands',$this->brands);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandRequest $request)
    {
        try
        { 
            $permission = !empty($request['id']) ? 'brand_edit' : 'brand_create' ;
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $status = '';
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'brand'.autoIncrementId('Brand','id');
                unset($request['image']);
                $request['brand_image'] = fileupload($image, $this->path, $filename);
            }
            if(!empty($request['id']))
            {
                $status = Brand::where('id',$request['id'])->update($request->except(['_token','id','image']));
            }
            else
            {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = Brand::create($request->except(['_token','image']));
            } 
            if($status)
            {
              return Redirect::to('brands')->with('message_success', 'Brand Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();  
        }        
        catch(\Exception $e)
        {

          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $id = decrypt($id);
        $brands = Brand::find($id);
        return response()->json($brands);
    }

    public function edit($id)
    {
        abort_if(Gate::denies('brand_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $brands = Brand::find($id);
         return view('brands.create')->with('brands',$brands);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('brand_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $brand = Brand::find($id);
        if($brand->delete())
        {
            return response()->json(['status' => 'success','message' => 'Data deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Data deleted successfully!']);
    }

    public function active(Request $request)
    {
        if(Brand::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'User '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('brand_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new BrandImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      //abort_if(Gate::denies('brand_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new BrandExport, 'brands.xlsx');
    }
}
