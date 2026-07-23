<?php

namespace App\Http\Controllers;

use App\Models\SchemeHeader;
use Illuminate\Http\Request;
use App\Http\Requests\SchemeRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\Models\SchemeDetails;
use App\DataTables\SchemesDataTable;
use App\Imports\SchemeImport;
use App\Exports\SchemeExport;
use App\Exports\SchemeTepmlate;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Customers;
use App\Models\CustomerType;
use App\Models\Product;
use App\Models\State;
use App\Models\Subcategory;
use Excel;
use PDF;

class SchemeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->schemes = new SchemeHeader();
        $this->path = 'schemes';
    }

    public function index(SchemesDataTable $dataTable)
    {
        abort_if(Gate::denies('scheme_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('schemes.index');
    }


    public function create()
    {
        abort_if(Gate::denies('scheme_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customer_types = CustomerType::where('active', 'Y')->select('id', 'customertype_name')->get();
        $branchs = Branch::where('active', 'Y')->select('id', 'branch_name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        // $customers = Customers::where('active', 'Y')->select('id', 'name')->get();
        $customers = [];
        return view('schemes.create', compact('customer_types', 'branchs', 'states', 'customers'))->with('schemes', $this->schemes);
    }


    public function store(SchemeRequest $request)
    {
        try {
            abort_if(Gate::denies('scheme_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $validator = Validator::make($request->all(), [
                'scheme_name' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $request['scheme_image'] = '';
            if ($request->file('image')) {
                $image = $request->file('image');
                $filename = 'schemeheader' . autoIncrementId('SchemeHeader', 'id');
                unset($request['image']);
                $request['scheme_image'] = fileupload($image, $this->path, $filename);
            }
            if ($id = SchemeHeader::insertGetId([
                'active' => 'Y',
                'scheme_name' => isset($request['scheme_name']) ? $request['scheme_name'] : '',
                'scheme_description' => isset($request['scheme_description']) ? $request['scheme_description'] : '',
                'start_date' => isset($request['start_date']) ? $request['start_date'] : '',
                'end_date' => isset($request['end_date']) ? $request['end_date'] : '',
                'scheme_image' => isset($request['scheme_image']) ? $request['scheme_image'] : '',
                'scheme_type' => isset($request['scheme_type']) ? $request['scheme_type'] : '',
                'scheme_basedon' => isset($request['scheme_basedon']) ? $request['scheme_basedon'] : '',
                'assign_to' => isset($request['assign_to']) ? $request['assign_to'] : '',
                'branch' => (isset($request['branch']) && count($request['branch']) > 0) ? implode(',', $request['branch']) : '',
                'state' => (isset($request['state']) && count($request['state']) > 0) ? implode(',', $request['state']) : '',
                'customer' => (isset($request['customer']) && count($request['customer']) > 0) ? implode(',', $request['customer']) : '',
                'customer_type' => isset($request['customer_type']) ? $request['customer_type'] : '',
                'points_start_date' => isset($request['points_start_date']) ? $request['points_start_date'] : null,
                'points_end_date' => isset($request['points_end_date']) ? $request['points_end_date'] : null,
                // 'block_points' => isset($request['block_points']) ? $request['block_points'] : 0,
                // 'block_percents' => isset($request['block_percents']) ? $request['block_percents'] : 0,
                'created_at' => getcurentDateTime(),
            ])) {
                if ($request->import_file) {
                    if (ob_get_contents()) ob_end_clean();
                    ob_start();
                    Excel::import(new SchemeImport(encrypt($id)), $request['import_file']);
                } else {
                    $schmedetils = collect([]);
                    if ($request['points']) {
                        foreach ($request['points'] as $key => $value) {
                            $schmedetils->push([
                                'active' => 'Y',
                                'scheme_id' => $id,
                                'product_id' => !empty($request['product_id']) ? $request['product_id'][$key] : null,
                                'category_id' => !empty($request['category_id']) ? $request['category_id'][$key] : null,
                                'subcategory_id' => !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null,
                                'active_point' => isset($request['active_point']) ? $request['active_point'][$key] : 0,
                                'provision_point' => isset($request['provision_point']) ? $request['provision_point'][$key] : 0,
                                'points' => isset($request['points']) ? $request['points'][$key] : 0,
                            ]);
                        }
                        if ($schmedetils->isNotEmpty()) {
                            SchemeDetails::insert($schmedetils->toArray());
                        }
                    }
                }
                return Redirect::to('schemes')->with('message_success', 'Scheme Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Scheme Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function show(SchemeHeader $schemeheader)
    {
        abort_if(Gate::denies('scheme_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }


    public function edit($id)
    {
        abort_if(Gate::denies('scheme_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $customer_types = CustomerType::where('active', 'Y')->select('id', 'customertype_name')->get();
        $branchs = Branch::where('active', 'Y')->select('id', 'branch_name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $customers = Customers::where('active', 'Y')->select('id', 'name')->get();
        // $customers = [];
        $id = decrypt($id);
        $schemes = SchemeHeader::find($id);
        return view('schemes.create', compact('customer_types', 'branchs', 'states', 'customers'))->with('schemes', $schemes);
    }


    public function update(SchemeRequest $request, $id)
    {
        try {
            abort_if(Gate::denies('scheme_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if ($request->import_file) {
                Excel::import(new SchemeImport($id), request()->file('import_file'));
            }
            $validator = Validator::make($request->all(), [
                'scheme_name' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            if ($request['assign_to'] == 'branch') {
                $request['state'] = [];
                $request['customer'] = [];
            } else if ($request['assign_to'] == 'state') {
                $request['branch'] = [];
                $request['customer'] = [];
            } else if ($request['assign_to'] == 'customer') {
                $request['state'] = [];
                $request['branch'] = [];
            } else {
                $request['state'] = [];
                $request['customer'] = [];
                $request['branch'] = [];
            }
            $id = decrypt($id);
            $scheme = SchemeHeader::find($id);
            $scheme->scheme_name = isset($request['scheme_name']) ? $request['scheme_name'] : '';
            $scheme->scheme_description = isset($request['scheme_description']) ? $request['scheme_description'] : '';
            $scheme->start_date = $request['start_date'];
            $scheme->end_date = $request['end_date'];
            $scheme->scheme_type = isset($request['scheme_type']) ? $request['scheme_type'] : '';
            $scheme->scheme_basedon = isset($request['scheme_basedon']) ? $request['scheme_basedon'] : '';
            $scheme->assign_to = isset($request['assign_to']) ? $request['assign_to'] : '';
            $scheme->branch = (isset($request['branch']) && count($request['branch']) > 0) ? implode(',', $request['branch']) : '';
            $scheme->state = (isset($request['state']) && count($request['state']) > 0) ? implode(',', $request['state']) : '';
            $scheme->customer = (isset($request['customer']) && count($request['customer']) > 0) ? implode(',', $request['customer']) : '';
            $scheme->customer_type = isset($request['customer_type']) ? $request['customer_type'] : '';
            if ($request->file('image')) {
                $image = $request->file('image');
                $filename = 'scheme' . $id;
                unset($request['image']);
                $scheme->scheme_image = fileupload($image, $this->path, $filename);
            }
            if ($scheme->save()) {
                if (!$request->import_file) {

                    // $existdetails = SchemeDetails::where('scheme_id',$id)->select('id','product_id','category_id','minimum','maximum','points')->get();
                    $schmedetils = collect([]);
                    if ($request['points'] && $request['points'] != null && count($request['points']) > 0) {
                        foreach ($request['points'] as $key => $value) {
                            if (!empty($request['detail_id'][$key])) {
                                $schmedetils = SchemeDetails::firstOrNew(array('id' => $request['detail_id'][$key]));
                            } else {
                                $schmedetils = new SchemeDetails();
                            }
                            $schmedetils->active = 'Y';
                            $schmedetils->scheme_id = $id;
                            $schmedetils->product_id = !empty($request['product_id']) ? $request['product_id'][$key] : null;
                            $schmedetils->category_id = !empty($request['category_id']) ? $request['category_id'][$key] : null;
                            $schmedetils->subcategory_id = !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null;
                            $schmedetils->active_point = !empty($request['active_point']) ? $request['active_point'][$key] : "0";
                            $schmedetils->provision_point = !empty($request['provision_point']) ? $request['provision_point'][$key] : "0";
                            $schmedetils->points = !empty($request['points']) ? $request['points'][$key] : "0";
                            $schmedetils->save();
                        }
                    }
                }


                return Redirect::to('schemes')->with('message_success', 'Scheme Update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Scheme Update')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
    public function destroy($id)
    {
        abort_if(Gate::denies('scheme_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        SchemeDetails::where('scheme_id', '=', $id)->delete();
        $scheme = SchemeHeader::find($id);
        if ($scheme->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Scheme deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Scheme Delete!']);
    }

    public function active(Request $request)
    {
        if (SchemeHeader::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y'])) {
            SchemeDetails::where('scheme_id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y']);
            $message = ($request['active'] == 'Y') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'Scheme ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('scheme_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new SchemeImport, request()->file('import_file'));
        return back();
    }
    public function download(Request $request)
    {
        abort_if(Gate::denies('scheme_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SchemeExport($request->id), 'schemesProducts.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('scheme_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SchemeTepmlate, 'schemesProductTemplate.xlsx');
    }

    public function scheme_product_list(Request $request)
    {

        $data = SchemeDetails::with(['products', 'categories', 'subcategories'])->where('scheme_id', $request->scheme_id)->latest();

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('active_point', function ($query) {
                return '<input type="text" name="active_point[]" class="form-control active_point rowchange" value="' . $query->active_point . '" />';
            })
            ->addColumn('provision_point', function ($query) {
                return '<input type="text" name="provision_point[]" class="form-control provision_point rowchange" value="' . $query->provision_point . '" />';
            })
            ->addColumn('points', function ($query) {
                return '<input type="text" name="points[]" class="form-control points rowchange" value="' . $query->points . '" />';
            })
            ->addColumn('categories.category_name', function ($query) {
                return '<input type="hidden" name="detail_id[]" value="' . $query->id . '"><input type="hidden" name="category_id[]" value="' . $query->category_id . '"><input type="hidden" name="subcategory_id[]" value="' . $query->subcategory_id . '"><input type="hidden" name="product_id[]" value="' . $query->product_id . '">' . $query->categories->category_name;
            })
            ->rawColumns(['active_point', 'provision_point', 'points', 'categories.category_name'])
            ->make(true);
    }

    public function generatePdf(Request $request)
    {

        // return view('work_in_progress');
        if ($request->group == 'no') {
            $groupw = 'no';
            $data = SchemeDetails::with(['products', 'categories', 'subcategories'])->where('scheme_id', $request->id)->orderBy('points', 'asc')->get();
        } else if ($request->group == 'yes') {
            $groupw = 'yes';
            $data = SchemeDetails::select(
                'points',
                'active_point',
                'provision_point',
                DB::raw('GROUP_CONCAT(DISTINCT product_id) as product_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT category_id) as category_ids'),
                DB::raw('GROUP_CONCAT(DISTINCT subcategory_id) as subcategory_ids')
            )
                ->where('scheme_id', $request->id)
                ->groupBy('points', 'active_point', 'provision_point')
                ->orderBy('points', 'asc')
                ->get();

            foreach ($data as $k => $val) {
                $products = Product::whereIn('id', explode(',', $val->product_ids))->pluck('product_name')->toArray();
                $subcategories = Subcategory::whereIn('id', explode(',', $val->subcategory_ids))->pluck('subcategory_name')->toArray();
                $categories = Category::whereIn('id', explode(',', $val->category_ids))->pluck('category_name')->toArray();
                $data[$k]['products'] = trim(implode(', ', $products));
                $data[$k]['subcategories'] = implode(', ', $subcategories);
                $data[$k]['categories'] = implode(', ', $categories);
            }
        }

        // dd($data);

        // return view('schemes.scheme-product-pdf', compact('data'));
        $pdf = PDF::loadView('schemes.scheme-product-pdf', compact('data','groupw'));

        return $pdf->download('Scheme_Product.pdf');
    }
}
