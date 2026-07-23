<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;
use App\Http\Requests\SubcategoryRequest;
use App\Models\Category;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\SubCategoryDataTable;
use App\Imports\SubcategoryImport;
use App\Exports\SubcategoryExport;
use App\Exports\SubcategoryTemplate;

class SubCategoryController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->subcategories = new Subcategory();
        $this->path = 'subcategory';
    }
    
    public function index(SubCategoryDataTable $dataTable)
    {
        abort_if(Gate::denies('subcategory_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = Category::where('active','=','Y')->select('id', 'category_name')->get();
        return $dataTable->render('subcategories.index',compact('categories'));
    }

    public function store(SubcategoryRequest $request)
    {
      try
        { 
            $permission = !empty($request['id']) ? 'subcategory_edit' : 'subcategory_create' ;
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $status = '';
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'subcategory';
                $request['subcategory_image'] = fileupload($image, $this->path, $filename);
            }
            if(!empty($request['id']))
            {
                $status = Subcategory::where('id',$request['id'])->update($request->except(['_token','id','image']));
            }
            else
            {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = Subcategory::create($request->except(['_token','image']));
            } 
            if($status)
            {
              return Redirect::to('subcategories')->with('message_success', 'Subcategory Store Successfully');
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
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        abort_if(Gate::denies('subcategory_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('subcategory_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $subcategory = Subcategory::find($id);
        $subcategory['category_name'] = isset($subcategory['categories']['category_name'])? $subcategory['categories']['category_name'] :'';
        return response()->json($subcategory);
    }

    
    public function destroy($id)
    {
        abort_if(Gate::denies('subcategory_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = Subcategory::find($id);
        if($user->delete())
        {
            return response()->json(['status' => 'success','message' => 'Subcategory deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Subcategory Delete!']);
    }
    
    public function active(Request $request)
    {
        if(Subcategory::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Subcategory '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
      //abort_if(Gate::denies('subcategory_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new SubcategoryImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      //abort_if(Gate::denies('subcategory_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SubcategoryExport, 'subcategories.xlsx');
    }
    public function template()
    {
      //abort_if(Gate::denies('subcategory_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SubcategoryTemplate, 'subcategories.xlsx');
    }
}
