<?php

namespace App\Http\Controllers;

use App\Models\GiftCategory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\GiftCategoryDataTable;
use App\Imports\GiftCategoryImport;
use App\Exports\GiftCategoryExport;
use App\Exports\CategoryTemplate;
use App\Http\Requests\GiftCategoryRequest;


class GiftCategoryController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->categories = new GiftCategory();
        $this->path = 'category' ;
    }
    
    public function index(GiftCategoryDataTable $dataTable)
    {
        abort_if(Gate::denies('gift_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('gift_categories.index');
    }

    public function store(GiftCategoryRequest $request)
    {
        try
        { 
            $permission = !empty($request['id']) ? 'gift_category_edit' : 'gift_category_create' ;
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $status = '';
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'gift_category'.autoIncrementId('Category','id');
                $request['category_image'] = fileupload($image, $this->path, $filename);
            }
            if(!empty($request['id']))
            {
                $status = GiftCategory::where('id',$request['id'])->update($request->except(['_token','id','image']));
            }
            else
            {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = GiftCategory::create($request->except(['_token','image']));
            } 
            if($status)
            {
              return Redirect::to('gift-categories')->with('message_success', 'Gift Category Store Successfully');
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
        $category = GiftCategory::find($id);
        return response()->json($category);
    }

    public function edit($id)
    {
        abort_if(Gate::denies('gift_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $categories = GiftCategory::find($id);
         return view('categories.create')->with('categories',$categories);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('gift_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = GiftCategory::find($id);
        if($user->delete())
        {
            return response()->json(['status' => 'success','message' => 'Gift Category deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Category Delete!']);
    }

    public function active(Request $request)
    {
        if(GiftCategory::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Category '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }
    public function upload(Request $request) 
    {
      abort_if(Gate::denies('gift_category_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new GiftCategoryImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('gift_category_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new GiftCategoryExport, 'gift_categorys.xlsx');
    }
    public function template()
    {
      //abort_if(Gate::denies('category_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CategoryTemplate, 'gift_categorys.xlsx');
    }
}
