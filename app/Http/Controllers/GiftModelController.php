<?php

namespace App\Http\Controllers;

use App\Models\GiftModel;
use App\Models\GiftSubcategory;
use Illuminate\Http\Request;
use App\DataTables\GiftModelDataTable;
use App\Exports\GiftModelExport;
use App\Http\Requests\GiftModelRequest;
use App\Imports\GiftModelImport;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;

class GiftModelController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');   
        $this->GiftModel = new GiftModel();
        $this->path = 'GiftModel';
    }
    
    public function index(GiftModelDataTable $dataTable)
    {
        abort_if(Gate::denies('gift_model_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = GiftSubcategory::where('active','=','Y')->select('id', 'subcategory_name')->get();
        return $dataTable->render('gift_model.index',compact('categories'));
    }

    public function store(GiftModelRequest $request)
    {
      try
        { 
            $permission = !empty($request['id']) ? 'gift_model_edit' : 'gift_model_create' ;
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $status = '';
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'GiftModel';
                $request['model_image'] = fileupload($image, $this->path, $filename);
            }
            if(!empty($request['id']))
            {
                $status = GiftModel::where('id',$request['id'])->update($request->except(['_token','id','image']));
            }
            else
            {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = GiftModel::create($request->except(['_token','image']));
            } 
            if($status)
            {
              return Redirect::to('gift-model')->with('message_success', 'Gift Model Store Successfully');
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
        abort_if(Gate::denies('gift_model_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function edit($id)
    {
        abort_if(Gate::denies('gift_model_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $GiftModel = GiftModel::find($id);
        $GiftModel['sub_category_name'] = isset($GiftModel['subCategories']['subcategory_name'])? $GiftModel['subCategories']['subcategory_name'] :'';
        $GiftModel['sub_category_id'] = isset($GiftModel['subCategories']['id'])? $GiftModel['subCategories']['id'] :'';
        return response()->json($GiftModel);
    }

    
    public function destroy($id)
    {
        abort_if(Gate::denies('gift_model_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = GiftModel::find($id);
        if($user->delete())
        {
            return response()->json(['status' => 'success','message' => 'Gift Model deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Gift Model Delete!']);
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
      abort_if(Gate::denies('gift_model_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new GiftModelImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('gift_model_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new GiftModelExport, 'giftmodel.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('gift_model_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new GiftSubcategoryTemplate, 'giftsubcategories.xlsx');
    }
}
