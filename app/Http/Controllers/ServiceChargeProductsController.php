<?php

namespace App\Http\Controllers;

use App\DataTables\ServiceProductCategoryDataTable;
use App\DataTables\ServiceProductChargeTypeDataTable;
use App\DataTables\ServiceProductDivisionDataTable;
use App\DataTables\ServiceProductProductsDataTable;
use App\Exports\ServiceChargeProductExport;
use App\Exports\ServiceProductCategoryExport;
use App\Exports\ServiceProductChargeTypeExport;
use App\Exports\ServiceProductDivisionExport;
use App\Imports\ServiceProductCategoryImport;
use App\Imports\ServiceProductImport;
use App\Models\ServiceChargeCategories;
use App\Models\ServiceChargeChargeType;
use App\Models\ServiceChargeDivision;
use App\Models\ServiceChargeProducts;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Excel;


class ServiceChargeProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function divisionindex(ServiceProductDivisionDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('services_product_division'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('service_dividion.index');
    }

    public function divisionstore(Request $request)
    {
        try {
            if (!empty($request['id'])) {
                $status = ServiceChargeDivision::where('id', $request['id'])->update($request->except(['_token', 'id', 'image']));
                $msg = 'Division Update Successfully';
            } else {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;

                $request['division_name'] = $request->input('division_name', '');
                $status = ServiceChargeDivision::create($request->except(['_token', 'image']));
                $msg = 'Division Store Successfully';
            }

            if ($status) {
                return Redirect::to('service-charge/chargetype')->with('message_success', $msg);
            }

            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function divisionedit($id)
    {
        $division = ServiceChargeDivision::find($id);

        if (!$division) {
            return response()->json(['status' => 'error', 'message' => 'Division not found']);
        }

        return response()->json($division);
    }

    public function divisionactive(Request $request, $id)
    {
        $divsion = ServiceChargeDivision::find($id);

        if (!$divsion) {
            return response()->json(['status' => 'error', 'message' => 'Divsion not found']);
        }
        $divsion->update(['active' => $divsion->active === 'Y' ? 'N' : 'Y']);
        return response()->json(['status' => 'success', 'message' => 'Divsion status changed successfully']);
    }

    public function divisiondelete($id)
    {
        $user = ServiceChargeDivision::find($id);
        if ($user->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Division deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Division Delete!']);
    }

    public function divisiondownload()
    {
        abort_if(Gate::denies('services_product_division_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ServiceProductDivisionExport, 'service_product_division.xlsx');
    }

    public function categoryindex(ServiceProductCategoryDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('services_product_category'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = ServiceChargeDivision::all();
        return $dataTable->render('service_category.index', compact('categories'));
    }

    public function categorystore(Request $request)
    {
        try {
            $status = '';
            if (!empty($request['id'])) {
                $status = ServiceChargeCategories::where('id', $request['id'])->update($request->except(['_token', 'id', 'image']));
                $msg = 'Category Update Successfully';
            } else {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = ServiceChargeCategories::create($request->except(['_token', 'image']));
                $msg = 'Category Store Successfully';
            }
            if ($status) {
                return Redirect::to('service-charge/categories')->with('message_success', $msg);
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function categoryedit($id)
    {
        $division = ServiceChargeCategories::find($id);

        if (!$division) {
            return response()->json(['status' => 'error', 'message' => 'Category not found']);
        }

        return response()->json($division);
    }

    public function categoryactive(Request $request, $id)
    {
        $divsion = ServiceChargeCategories::find($id);

        if (!$divsion) {
            return response()->json(['status' => 'error', 'message' => 'Category not found']);
        }
        $divsion->update(['active' => $divsion->active === 'Y' ? 'N' : 'Y']);
        return response()->json(['status' => 'success', 'message' => 'Category status changed successfully']);
    }

    public function categorydelete($id)
    {
        $user = ServiceChargeCategories::find($id);
        if ($user->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Category deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Category Delete!']);
    }

    public function categorydownload(Request $request)
    {
        abort_if(Gate::denies('services_product_category_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ServiceProductCategoryExport($request), 'service_product_categorycategories.xlsx');
    }

    public function categoryupload(Request $request)
    {
        abort_if(Gate::denies('services_product_category_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new ServiceProductCategoryImport, request()->file('import_file'));
        return back();
    }

    public function chargetypeindex(ServiceProductChargeTypeDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('services_product_division'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('service_charge_type.index');        
    }

    public function chargetypestore(Request $request)
    {
        try {
            if (!empty($request['id'])) {
                $status = ServiceChargeChargeType::where('id', $request['id'])->update($request->except(['_token', 'id', 'image']));
                $msg = 'Charge Type Update Successfully';
            } else {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;

                $request['division_name'] = $request->input('division_name', '');
                $status = ServiceChargeChargeType::create($request->except(['_token', 'image']));
                $msg = 'Charge Type Store Successfully';
            }

            if ($status) {
                return Redirect::to('service-charge/chargetype')->with('message_success', $msg);
            }

            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function chargetypeedit($id)
    {
        $division = ServiceChargeChargeType::find($id);

        if (!$division) {
            return response()->json(['status' => 'error', 'message' => 'Charge Type not found']);
        }

        return response()->json($division);
    }

    public function chargetypeactive(Request $request, $id)
    {
        $divsion = ServiceChargeChargeType::find($id);

        if (!$divsion) {
            return response()->json(['status' => 'error', 'message' => 'Charge Type not found']);
        }
        $divsion->update(['active' => $divsion->active === 'Y' ? 'N' : 'Y']);
        return response()->json(['status' => 'success', 'message' => 'Charge Type status changed successfully']);
    }

    public function chargetypedelete($id)
    {
        $user = ServiceChargeChargeType::find($id);
        if ($user->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Charge Type deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Charge Type Delete!']);
    }

    public function chargetypedownload()
    {
        abort_if(Gate::denies('services_product_division_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ServiceProductChargeTypeExport, 'service_product_charge_type.xlsx');
    }

    public function productindex(ServiceProductProductsDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('services_product_products'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = ServiceChargeDivision::all();
        return $dataTable->render('service_product.index', compact('categories'));
    }

    public function productcreate(Request $request)
    {
        abort_if(Gate::denies('services_product_products_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $divisions = ServiceChargeDivision::all();
        $chargetypes = ServiceChargeChargeType::all();
        if($request->id && !empty($request->id)){
            $product = ServiceChargeProducts::find($request->id);
        }else{
            $product = new ServiceChargeProducts();
        }
        return view('service_product.create', compact('divisions', 'chargetypes'))->with('product', $product);
    }

    public function productstore(Request $request)
    {
        try {
            $status = '';
            if (!empty($request['id'])) {
                $status = ServiceChargeProducts::where('id', $request['id'])->update($request->except(['_token', 'id', 'image', '_method']));
                $msg = 'Product Update Successfully';
            } else {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $status = ServiceChargeProducts::create($request->except(['_token', 'image']));
                $msg = 'Product Store Successfully';
            }
            if ($status) {
                return Redirect::to('service-charge/products')->with('message_success', $msg);
            }
            return redirect()->back()->with('message_danger', 'Error in Data Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
    
    public function productedit($id)
    {
        $division = ServiceChargeCategories::find($id);

        if (!$division) {
            return response()->json(['status' => 'error', 'message' => 'Category not found']);
        }

        return response()->json($division);
    }

    public function productactive(Request $request, $id)
    {
        $product = ServiceChargeProducts::find($id);
        
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found']);
        }
        $product->update(['active' => $product->active === 'Y' ? 'N' : 'Y']);
        return response()->json(['status' => 'success', 'message' => 'Product status changed successfully']);
    }

    public function productdelete($id)
    {
        $user = ServiceChargeProducts::find($id);
        if ($user->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Product deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Product Delete!']);
    }

    public function productdownload(Request $request)
    {
        abort_if(Gate::denies('services_product_category_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new ServiceChargeProductExport($request), 'service_charge_products.xlsx');
    }

    public function productupload(Request $request)
    {
        abort_if(Gate::denies('services_product_products_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new ServiceProductImport, request()->file('import_file'));
        return back();
    }
}
