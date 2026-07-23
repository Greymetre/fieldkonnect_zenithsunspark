<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\Models\OrderScheme;
use App\Models\OrderSchemeDetail;
use App\Models\CustomerType;
use App\Models\Branch;
use App\Models\State;
use App\Exports\OrderSchemeTemplate;
use App\Imports\OrderSchemeImport;
use App\Exports\OrderSchemeExport;
use Carbon\Carbon;
use Excel;




class OrderSchemeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->schemes = new OrderScheme();
        $this->path = 'order_schemes';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $order_schemes = OrderScheme::with(['orderscheme_details'])->orderBy('id', 'desc');
            $order_schemes = $order_schemes->select(\DB::raw(with(new OrderScheme)->getTable() . '.*'))->groupBy('id');
            return Datatables::of($order_schemes)
                ->addIndexColumn()
                ->editColumn('id', function ($query) {
                    return $query->id ?? '';
                })
                ->editColumn('scheme_name', function ($query) {
                    return $query->scheme_name ?? '';
                })
                ->editColumn('scheme_description', function ($query) {
                    return $query->scheme_description ?? '';
                })
                ->editColumn('start_date', function ($query) {
                    return $query->start_date ?? '';
                })
                ->editColumn('end_date', function ($query) {
                    return $query->end_date ?? '';
                })
                ->editColumn('scheme_type', function ($query) {
                    return $query->scheme_type ?? '';
                })
                ->editColumn('created_at', function ($query) {
                    return  date("Y-m-d", strtotime($query->created_at));
                })

                ->addColumn('action', function ($query) {
                    $btn = '';
                    $activebtn = '';

                    $btn = $btn . '<a href="' . route("orderschemes.edit", ["orderscheme" => $query->id]) . '" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' ' . trans('panel.orderschemes.title_singular') . '">
                               <i class="material-icons">edit</i>
                                </a>';

                    $btn = $btn . ' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.orderschemes.title_singular') . '">
                                            <i class="material-icons">clear</i>
                                          </a>';

                    $active = ($query->active == 'Y') ? 'checked="" value="' . $query->active . '"' : 'value="' . $query->active . '"';
                    $activebtn = '<div class="togglebutton">
                                        <label>
                                          <input type="checkbox"' . $active . ' id="' . $query->id . '" class="orderschemeActive">
                                          <span class="toggle"></span>
                                        </label>
                                    </div>';


                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('order_schemes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customer_types = CustomerType::where('active', 'Y')->select('id', 'customertype_name')->get();
        $branchs = Branch::where('active', 'Y')->select('id', 'branch_name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $customers = [];

        return view('order_schemes.form', compact('customer_types', 'branchs', 'states', 'customers'))->with('schemes', $this->schemes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {  
            $rule = [
                'scheme_name' => 'required',
            ];
            if($request['repetition'] == '1'){
                $rule['week'] = 'required';
            }elseif($request['repetition'] == '2'){
                $rule['week_repeat'] = 'required';
            }else{
                $rule['start_date'] = 'required';
                $rule['end_date'] = 'required';
            }
            $message = [
                'week.required' => 'Please select at least one day.'
            ];
            $validator = Validator::make($request->all(), $rule, $message);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            if ($id = OrderScheme::insertGetId([
                'active' => 'Y',
                'scheme_name' => isset($request['scheme_name']) ? $request['scheme_name'] : '',
                'scheme_description' => isset($request['scheme_description']) ? $request['scheme_description'] : '',
                'start_date' => isset($request['start_date']) ? $request['start_date'] : '',
                'end_date' => isset($request['end_date']) ? $request['end_date'] : '',
                'repetition' => isset($request['repetition']) ? $request['repetition'] : '',
                'day_repeat' => isset($request['week']) ? implode(',',$request['week']) : NULL,
                'week_repeat' => isset($request['week_repeat']) ? $request['week_repeat'] : NULL,
                'scheme_type' => isset($request['scheme_type']) ? $request['scheme_type'] : '',
                'scheme_basedon' => isset($request['scheme_basedon']) ? $request['scheme_basedon'] : '',
                'assign_to' => isset($request['assign_to']) ? $request['assign_to'] : '',
                'branch' => (isset($request['branch']) && count($request['branch']) > 0) ? implode(',', $request['branch']) : '',
                'state' => (isset($request['state']) && count($request['state']) > 0) ? implode(',', $request['state']) : '',
                'customer' => (isset($request['customer']) && count($request['customer']) > 0) ? implode(',', $request['customer']) : '',
                'customer_type' => (isset($request['customer_type']) && count($request['customer_type']) > 0) ? implode(',', $request['customer_type']) : '',
                'minimum' => isset($request['minimum']) ? $request['minimum'] : null,
                'maximum' => isset($request['maximum']) ? $request['maximum'] : null,
                'created_at' => getcurentDateTime(),
            ])) {
                if ($request->import_file) {
                    if (ob_get_contents()) ob_end_clean();
                    ob_start();
                    Excel::import(new OrderSchemeImport(encrypt($id)), $request['import_file']);
                } else {

                    $orderschemedetils = collect([]);
                    if ($request['points']) {
                        foreach ($request['points'] as $key => $value) {
                            $orderschemedetils->push([
                                'active' => 'Y',
                                'order_scheme_id' => $id,
                                'product_id' => !empty($request['product_id']) ? $request['product_id'][$key] : null,
                                'category_id' => !empty($request['category_id']) ? $request['category_id'][$key] : null,
                                'subcategory_id' => !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null,
                                //'minimum' => isset($request['minimum']) ? $request['minimum'][$key] : null,
                                //'maximum' => isset($request['maximum']) ? $request['maximum'][$key] : null,
                                'points' => isset($request['points']) ? $request['points'][$key] : 0,
                            ]);
                        }
                        if ($orderschemedetils->isNotEmpty()) {
                            OrderSchemeDetail::insert($orderschemedetils->toArray());
                        }
                    }
                }
                return Redirect::to('orderschemes')->with('message_success', 'Order Scheme Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Order Scheme Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderScheme $orderscheme)
    {

        $customer_types = CustomerType::where('active', 'Y')->select('id', 'customertype_name')->get();
        $branchs = Branch::where('active', 'Y')->select('id', 'branch_name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $customers = [];
        return view('order_schemes.edit', compact('customer_types', 'branchs', 'states', 'customers'))->with('schemes', $orderscheme);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $rule = [
                'scheme_name' => 'required',
            ];
            if($request['repetition'] == '1'){
                $rule['week'] = 'required';
            }elseif($request['repetition'] == '2'){
                $rule['week_repeat'] = 'required';
            }else{
                $rule['start_date'] = 'required';
                $rule['end_date'] = 'required';
            }
            $message = [
                'week.required' => 'Please select at least one day.'
            ];
            $validator = Validator::make($request->all(), $rule, $message);
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
            $scheme = OrderScheme::find($id);
            $scheme->scheme_name = isset($request['scheme_name']) ? $request['scheme_name'] : '';
            $scheme->scheme_description = isset($request['scheme_description']) ? $request['scheme_description'] : '';
            $scheme->start_date = $request['start_date'];
            $scheme->end_date = $request['end_date'];
            $scheme->repetition = isset($request['repetition']) ? $request['repetition'] : '';
            $scheme->day_repeat = isset($request['week']) ? implode(',',$request['week']) : NULL;
            $scheme->week_repeat = isset($request['week_repeat']) ? $request['week_repeat'] : NULL;
            $scheme->scheme_type = isset($request['scheme_type']) ? $request['scheme_type'] : '';
            $scheme->scheme_basedon = isset($request['scheme_basedon']) ? $request['scheme_basedon'] : '';
            $scheme->assign_to = isset($request['assign_to']) ? $request['assign_to'] : '';
            $scheme->branch = (isset($request['branch']) && count($request['branch']) > 0) ? implode(',', $request['branch']) : '';
            $scheme->state = (isset($request['state']) && count($request['state']) > 0) ? implode(',', $request['state']) : '';
            $scheme->customer = (isset($request['customer']) && count($request['customer']) > 0) ? implode(',', $request['customer']) : '';

            $scheme->customer_type = (isset($request['customer_type']) && count($request['customer_type']) > 0) ? implode(',', $request['customer_type']) : '';

            $scheme->minimum = isset($request['minimum']) ? $request['minimum'] : 0;
            $scheme->maximum = isset($request['maximum']) ? $request['maximum'] : 0;

            if ($scheme->save()) {
                if (!$request->import_file) {
                    $existdetails = OrderSchemeDetail::where('order_scheme_id', $id)->select('id', 'product_id', 'category_id', 'points')->get();
                    $schmedetils = collect([]);
                    if ($request['points'] && $request['points'] != null && count($request['points']) > 0) {
                        foreach ($request['points'] as $key => $value) {
                            if (!empty($request['detail_id'][$key])) {
                                $schmedetils = OrderSchemeDetail::firstOrNew(array('id' => $request['detail_id'][$key]));
                            } else {
                                $schmedetils = new OrderSchemeDetail();
                            }
                            $schmedetils->active = 'Y';
                            $schmedetils->order_scheme_id = $id;
                            $schmedetils->product_id = !empty($request['product_id']) ? $request['product_id'][$key] : null;
                            $schmedetils->category_id = !empty($request['category_id']) ? $request['category_id'][$key] : null;
                            $schmedetils->subcategory_id = !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null;
                            $schmedetils->points = !empty($request['points']) ? $request['points'][$key] : null;
                            $schmedetils->save();
                        }
                    }
                }

                return Redirect::to('orderschemes')->with('message_success', 'Order Scheme Update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Order Scheme Update')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        //abort_if(Gate::denies('scheme_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        OrderSchemeDetail::where('order_scheme_id', '=', $id)->delete();
        $scheme = OrderScheme::find($id);
        if ($scheme->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Order Scheme deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Scheme Delete!']);
    }


    public function active(Request $request)
    {
        if (OrderScheme::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y'])) {
            OrderSchemeDetail::where('order_scheme_id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y']);
            $message = ($request['active'] == 'Y') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'Scheme ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }

    public function template()
    {
        //abort_if(Gate::denies('order_scheme_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new OrderSchemeTemplate, 'Scheme Template.xlsx');
    }


    public function download(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new OrderSchemeExport($request->id), 'order_schemes_products.xlsx');
    }
}
