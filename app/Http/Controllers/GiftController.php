<?php

namespace App\Http\Controllers;

use App\Models\Gifts;
use Illuminate\Http\Request;
use App\Models\Subcategory;
use App\Models\Category;
use App\Models\Brand;
use App\Models\UnitMeasure;
use App\Models\ProductDetails;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\GiftsDataTable;
use App\Imports\GiftImport;
use App\Exports\GiftExport;
use App\Exports\GiftTemplate;
use App\Http\Requests\GiftsRequest;
use App\Models\CustomerType;
use App\Models\GiftBrand;
use App\Models\GiftCategory;
use App\Models\GiftModel;
use App\Models\GiftSubcategory;

use PDF;
use Illuminate\Support\Facades\View;

class GiftController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        $this->gifts = new Gifts();
        $this->path = 'gifts';
    }

    public function index(GiftsDataTable $dataTable)
    {
        abort_if(Gate::denies('gift_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('gifts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('gift_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = GiftCategory::where('active','=','Y')->select('id', 'category_name')->get();
        $brands = GiftBrand::where('active','=','Y')->select('id', 'brand_name')->get();
        $customer_types = CustomerType::where('active','=','Y')->get();
        return view('gifts.create',compact('categories','brands', 'customer_types') )->with('gifts',$this->gifts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GiftsRequest $request)
    {
        try
        { 
            abort_if(Gate::denies('gift_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['product_image'] = '';
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'product';
                unset($request['image']);
                $request['product_image'] = fileupload($image, $this->path, $filename);
            }
            $request['active'] = 'Y';
            $request['created_by'] = Auth::user()->id;
            if($product_id = Gifts::insertGetId([
                'active'        => 'Y', 
                'product_name'  => isset($request['product_name']) ? $request['product_name'] :'',
                'display_name'  => isset($request['display_name']) ? $request['display_name'] :'',
                'description'   => isset($request['description']) ? $request['description'] :'',
                'subcategory_id'=> isset($request['subcategory_id']) ? $request['subcategory_id'] :null,
                'category_id'   => isset($request['category_id']) ? $request['category_id'] :null,
                'brand_id'      => isset($request['brand_id']) ? $request['brand_id'] :null,
                'product_image' => isset($request['product_image']) ? $request['product_image'] :'',
                'unit_id'       => isset($request['unit_id']) ? $request['unit_id'] :null,
                'customer_type_id'       => isset($request['customer_type_id']) ? $request['customer_type_id'] :null,
                'mrp'   => isset($request['mrp']) ? $request['mrp'] :0.00,
                'price'      => isset($request['price']) ? $request['price'] :0.00,
                'points' => isset($request['points']) ? $request['points'] :0,
                'created_by'    => Auth::user()->id,
                'created_at'    => getcurentDateTime()
            ]))
            {
              return Redirect::to('gifts')->with('message_success', 'Product Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Product Store')->withInput();  
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gifts  $gift
     * @return \Illuminate\Http\Response
     */
    public function show(Gifts $gift)
    {
        abort_if(Gate::denies('gift_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gifts  $gift
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('gift_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $gifts = Gifts::find($id);
        $categories = GiftCategory::where('active','=','Y')->select('id', 'category_name')->get();
        $brands = GiftBrand::where('active','=','Y')->select('id', 'brand_name')->get();
        $customer_types = CustomerType::where('active','=','Y')->get();
        return view('gifts.create',compact('categories','brands','customer_types') )->with('gifts',$gifts);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gift  $gift
     * @return \Illuminate\Http\Response
     */
    public function update(GiftsRequest $request, $id)
    {
        try
        { 
            abort_if(Gate::denies('gift_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $id = decrypt($id);
            $product = Gifts::find($id);
            $product->product_name = isset($request['product_name'])? $request['product_name'] :'';
            $product->display_name = isset($request['display_name']) ? $request['display_name'] :'';
            $product->description = isset($request['description']) ? $request['description'] :'';
            $product->subcategory_id = isset($request['subcategory_id']) ? $request['subcategory_id'] :null;
            $product->category_id = isset($request['category_id']) ? $request['category_id'] :null;
            $product->brand_id = isset($request['brand_id']) ? $request['brand_id'] :null;
            $product->unit_id = isset($request['unit_id']) ? $request['unit_id'] :null;
            $product->customer_type_id = isset($request['customer_type_id']) ? $request['customer_type_id'] :null;
            $product->mrp   = isset($request['mrp']) ? $request['mrp'] :0.00;
            $product->price = isset($request['price']) ? $request['price'] :0.00;
            $product->points = isset($request['points']) ? $request['points'] :0;
            if($request->file('image')){
                $image = $request->file('image');
                $filename = 'category'.$id;
                unset($request['image']);
                $product->product_image = fileupload($image, $this->path, $filename);
            }
            $product->updated_by = Auth::user()->id;
            if($product->save())
            {
              return Redirect::to('gifts')->with('message_success', 'Product Update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Category Update')->withInput();
        }         
        catch(\Exception $e)
        {
          return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gift  $gift
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gifts $gift)
    {
        abort_if(Gate::denies('gift_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($gift->delete())
        {
            return response()->json(['status' => 'success','message' => 'Gift Catalogue deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Gift Catalogue Delete!']);
    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('gift_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new GiftImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('gift_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new GiftExport, 'gifts.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('gift_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new GiftTemplate, 'giftsTemplate.xlsx');
    }

    public function active(Request $request)
    {
        if(Gifts::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Gift Catalogue '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    // generate pdf 
    public function generatePdf(){
        $gifts = $this->gifts->with(['categories','subcategories','brands' , 'models'])->orderBy('points', 'asc')->get();
        // return view('gifts.gift-pdf' , compact('gifts'));
        $pdf = PDF::loadView('gifts.gift-pdf', compact('gifts'));

        return $pdf->download('document.pdf');
  
     
    } 
}
