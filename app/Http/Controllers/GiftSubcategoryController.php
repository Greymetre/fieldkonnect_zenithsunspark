<?php

namespace App\Http\Controllers;

use App\Models\GiftSubcategory;
use Illuminate\Http\Request;
use App\Http\Requests\GiftSubcategoryRequest;
use App\Models\GiftCategory;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\GiftSubcategoryDataTable;
use App\Imports\GiftSubcategoryImport;
use App\Exports\GiftSubcategoryExport;
use App\Exports\GiftSubcategoryTemplate;

class GiftSubcategoryController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');   
        $this->subcategories = new GiftSubcategory();
        $this->path = 'Subcategory';
    }
    
    public function index(GiftSubcategoryDataTable $dataTable)
    {
        abort_if(Gate::denies('gift_subcategory_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = GiftCategory::where('active','=','Y')->select('id', 'category_name')->get();
        return $dataTable->render('giftsubcategories.index',compact('categories'));
    }

    public function store(GiftSubcategoryRequest $request)
    {
      try
        { 
            $permission = !empty($request['id']) ? 'gift_subcategory_edit' : 'gift_subcategory_create' ;
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $status = '';
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'GiftSubcategory';
                $request['subcategory_image'] = fileupload($image, $this->path, $filename);
            }
            if(!empty($request['id']))
            {
                $status = GiftSubcategory::where('id',$request['id'])->update($request->except(['_token','id','image']));
            }
            else
            {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = GiftSubcategory::create($request->except(['_token','image']));
            } 
            if($status)
            {
              return Redirect::to('gift-subcategories')->with('message_success', 'Gift Subcategory Store Successfully');
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
        abort_if(Gate::denies('gift_subcategory_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('gift_subcategory_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $GiftSubcategory = GiftSubcategory::find($id);
        $GiftSubcategory['category_name'] = isset($GiftSubcategory['categories']['category_name'])? $GiftSubcategory['categories']['category_name'] :'';
        return response()->json($GiftSubcategory);
    }

    
    public function destroy($id)
    {
        abort_if(Gate::denies('gift_subcategory_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = GiftSubcategory::find($id);
        if($user->delete())
        {
            return response()->json(['status' => 'success','message' => 'Gift Subcategory deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Gift Subcategory Delete!']);
    }
    
    public function active(Request $request)
    {
        if(GiftSubcategory::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'GiftSubcategory '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
      //abort_if(Gate::denies('GiftSubcategory_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new GiftSubcategoryImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      //abort_if(Gate::denies('GiftSubcategory_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new GiftSubcategoryExport, 'giftsubcategories.xlsx');
    }
    public function template()
    {
      //abort_if(Gate::denies('GiftSubcategory_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new GiftSubcategoryTemplate, 'giftsubcategories.xlsx');
    }
}
