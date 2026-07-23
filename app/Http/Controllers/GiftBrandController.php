<?php

namespace App\Http\Controllers;

use App\Models\GiftBrand;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Gate;
use Excel;
use App\DataTables\GiftBrandDataTable;
use App\Exports\GiftBrandExport;
use App\Exports\GiftBrandTemplate;
use App\Http\Requests\GiftBrandRequest;
use App\Imports\GiftBrandImport;

class GiftBrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->brand = new GiftBrand();
        $this->path = 'brand';
    }

    public function index(GiftBrandDataTable $dataTable)
    {
        abort_if(Gate::denies('gift_brand_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('gift_brand.index');
    }

    public function store(GiftBrandRequest $request)
    {
        try {
            $permission = !empty($request['id']) ? 'gift_brand_edit' : 'gift_brand_create';
            abort_if(Gate::denies($permission), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $status = '';
            if ($request->file('image')) {
                $image = $request->file('image');
                $filename = 'gift_brand' . autoIncrementId('GiftBrand', 'id');
                $request['brand_image'] = fileupload($image, $this->path, $filename);
            }
            if (!empty($request['id'])) {
                $status = GiftBrand::where('id', $request['id'])->update($request->except(['_token', 'id', 'image']));
            } else {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = GiftBrand::create($request->except(['_token', 'image']));
            }
            if ($status) {
                return Redirect::to('gift-brands')->with('message_success', 'Gift Brand Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $id = decrypt($id);
        $category = GiftBrand::find($id);
        return response()->json($category);
    }

    public function edit($id)
    {
        abort_if(Gate::denies('gift_brand_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $categories = GiftBrand::find($id);
        return view('gift_brand.create')->with('categories', $categories);
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('gift_brand_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = GiftBrand::find($id);
        if ($user->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Gift Brand deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Category Delete!']);
    }

    public function active(Request $request)
    {
        if (GiftBrand::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y'])) {
            $message = ($request['active'] == 'Y') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'Category ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }
    public function upload(Request $request)
    {
        abort_if(Gate::denies('gift_category_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new GiftBrandImport, request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('gift_category_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new GiftBrandExport, 'gift_Brand.xlsx');
    }
    public function template()
    {
        //abort_if(Gate::denies('category_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new GiftBrandTemplate, 'gift_brand_template.xlsx');
    }
}
