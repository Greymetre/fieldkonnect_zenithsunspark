<?php

namespace App\Http\Controllers;

use App\Exports\PrimarySchemeNormalTepmlate;
use App\Exports\PrimarySchemeReportExport;
use App\Exports\PrimarySchemeTepmlate;
use App\Imports\PrimarySchemeImport;
use App\Models\Branch;
use App\Models\Customers;
use App\Models\CustomerType;
use App\Models\PrimarySales;
use App\Models\PrimaryScheme;
use App\Models\PrimarySchemeDetail;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Gate;
use Excel;
use DataTables;
use Symfony\Component\HttpFoundation\Response;

class PrimarySchemeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->schemes = new PrimaryScheme();
        $this->path = 'primary_schemes';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $order_schemes = PrimaryScheme::with(['primaryscheme_details'])->orderBy('id', 'desc');
            $order_schemes = $order_schemes->select(\DB::raw(with(new PrimaryScheme)->getTable() . '.*'))->groupBy('id');
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
                    if (auth()->user()->can('primary_scheme_edit')) {
                        $btn = $btn . '<a href="' . route("primary_scheme.edit", ["primary_scheme" => $query->id]) . '" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' Primary Scheme">
                                   <i class="material-icons">edit</i>
                                    </a>';
                    }

                    $btn = $btn . ' <a type="button" href="" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.orderschemes.title_singular') . '">
                                            <i class="material-icons">clear</i>
                                          </a>';

                    // $active = ($query->active == 'Y') ? 'checked="" value="' . $query->active . '"' : 'value="' . $query->active . '"';
                    // $activebtn = '<div class="togglebutton">
                    //     <label>
                    //       <input type="checkbox"' . $active . ' id="' . $query->id . '" class="orderschemeActive">
                    //       <span class="toggle"></span>
                    //     </label>
                    // </div>';


                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>' . $activebtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('primary_schemes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $primary_customers = PrimarySales::groupBy('customer_id')->pluck('customer_id');
        $primary_branchs = PrimarySales::groupBy('branch_id')->pluck('branch_id');
        $customer_types = CustomerType::where('active', 'Y')->select('id', 'customertype_name')->get();
        $branchs = Branch::whereIn('id', $primary_branchs)->where('active', 'Y')->select('id', 'branch_name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $customers = Customers::whereIn('id', $primary_customers)->where('active', 'Y')->select('id', 'name')->get();
        $primary_divs = PrimarySales::select('division')->groupBy('division')->get();

        return view('primary_schemes.form', compact('customer_types', 'branchs', 'states', 'customers', 'primary_divs'))->with('schemes', $this->schemes);
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
            if ($request['repetition'] == '1') {
                $rule['week'] = 'required';
            } elseif ($request['repetition'] == '2') {
                $rule['week_repeat'] = 'required';
            } elseif ($request['repetition'] == '5') {
                $rule['quarter'] = 'required';
            } else {
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

            if ($id = PrimaryScheme::insertGetId([
                'active' => 'Y',
                'scheme_name' => isset($request['scheme_name']) ? $request['scheme_name'] : '',
                'division' => isset($request['division']) ? $request['division'] : '',
                'scheme_description' => isset($request['scheme_description']) ? $request['scheme_description'] : '',
                'start_date' => isset($request['start_date']) ? $request['start_date'] : '',
                'end_date' => isset($request['end_date']) ? $request['end_date'] : '',
                'repetition' => isset($request['repetition']) ? $request['repetition'] : '',
                'day_repeat' => isset($request['week']) ? implode(',', $request['week']) : NULL,
                'week_repeat' => isset($request['week_repeat']) ? $request['week_repeat'] : NULL,
                'quarter' => isset($request['quarter']) ? $request['quarter'] : NULL,
                'scheme_type' => isset($request['scheme_type']) ? $request['scheme_type'] : '',
                'scheme_basedon' => isset($request['scheme_basedon']) ? $request['scheme_basedon'] : '',
                'per_pcs' => isset($request['per_pcs']) ? $request['per_pcs'] : 0,
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
                    Excel::import(new PrimarySchemeImport(encrypt($id)), $request['import_file']);
                } else {

                    $primarychemedetils = collect([]);
                    if ($request['points']) {
                        foreach ($request['points'] as $key => $value) {
                            $primarychemedetils->push([
                                'active' => 'Y',
                                'primary_scheme_id' => $id,
                                'product_id' => !empty($request['product_id']) ? $request['product_id'][$key] : null,
                                'category_id' => !empty($request['category_id']) ? $request['category_id'][$key] : null,
                                'subcategory_id' => !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null,
                                'groups' => !empty($request['groups']) ? $request['groups'][$key] : null,
                                'group_type' => !empty($request['group_type']) ? $request['group_type'][$key]??null : null,
                                'min' => isset($request['min']) ? $request['min'][$key] : null,
                                'max' => isset($request['max']) ? $request['max'][$key] : null,
                                'slab_min' => isset($request['slab_min']) ? $request['slab_min'][$key] : null,
                                'slab_max' => isset($request['slab_max']) ? $request['slab_max'][$key] : null,
                                'gift' => isset($request['gift']) ? $request['gift'][$key] : null,
                                'points' => isset($request['points']) ? $request['points'][$key] : 0,
                            ]);
                        }
                        if ($primarychemedetils->isNotEmpty()) {
                            PrimarySchemeDetail::insert($primarychemedetils->toArray());
                        }
                    }
                    if ($request['gift']) {
                        foreach ($request['gift'] as $key => $value) {
                            $primarychemedetils->push([
                                'active' => 'Y',
                                'primary_scheme_id' => $id,
                                'product_id' => !empty($request['product_id']) ? $request['product_id'][$key] : null,
                                'category_id' => !empty($request['category_id']) ? $request['category_id'][$key] : null,
                                'subcategory_id' => !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null,
                                'groups' => !empty($request['groups']) ? $request['groups'][$key] : null,
                                'min' => isset($request['min']) ? $request['min'][$key] : null,
                                'max' => isset($request['max']) ? $request['max'][$key] : null,
                                'slab_min' => isset($request['slab_min']) ? $request['slab_min'][$key] : null,
                                'slab_max' => isset($request['slab_max']) ? $request['slab_max'][$key] : null,
                                'gift' => isset($request['gift']) ? $request['gift'][$key] : null,
                                'points' => isset($request['points']) ? $request['points'][$key] : 0,
                            ]);
                        }
                        if ($primarychemedetils->isNotEmpty()) {
                            PrimarySchemeDetail::insert($primarychemedetils->toArray());
                        }
                    }
                }
                return Redirect::to('primary_scheme')->with('message_success', 'Primary Scheme Store Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Primary Scheme Store')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PrimaryScheme  $primaryScheme
     * @return \Illuminate\Http\Response
     */
    public function show(PrimaryScheme $primaryScheme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PrimaryScheme  $primaryScheme
     * @return \Illuminate\Http\Response
     */
    public function edit(PrimaryScheme $primaryScheme)
    {
        abort_if(Gate::denies('primary_scheme_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $primary_customers = PrimarySales::groupBy('customer_id')->pluck('customer_id');
        $primary_branchs = PrimarySales::groupBy('branch_id')->pluck('branch_id');
        $customer_types = CustomerType::where('active', 'Y')->select('id', 'customertype_name')->get();
        $branchs = Branch::whereIn('id', $primary_branchs)->where('active', 'Y')->select('id', 'branch_name')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        $customers = Customers::whereIn('id', $primary_customers)->where('active', 'Y')->select('id', 'name')->get();
        $primary_divs = PrimarySales::select('division')->groupBy('division')->get();

        return view('primary_schemes.form', compact('customer_types', 'branchs', 'states', 'customers', 'primary_divs'))->with('schemes', $primaryScheme);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PrimaryScheme  $primaryScheme
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PrimaryScheme $primaryScheme)
    {
        try {
            $rule = [
                'scheme_name' => 'required',
            ];
            if ($request['repetition'] == '1') {
                $rule['week'] = 'required';
            } elseif ($request['repetition'] == '2') {
                $rule['week_repeat'] = 'required';
            } elseif ($request['repetition'] == '5') {
                $rule['quarter'] = 'required';
            } else {
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

            $primaryScheme->scheme_name = isset($request['scheme_name']) ? $request['scheme_name'] : '';
            $primaryScheme->division = isset($request['division']) ? $request['division'] : '';
            $primaryScheme->scheme_description = isset($request['scheme_description']) ? $request['scheme_description'] : '';
            $primaryScheme->start_date = isset($request['start_date']) ? $request['start_date'] : '';
            $primaryScheme->end_date = isset($request['end_date']) ? $request['end_date'] : '';
            $primaryScheme->repetition = isset($request['repetition']) ? $request['repetition'] : '';
            $primaryScheme->day_repeat = isset($request['week']) ? implode(',', $request['week']) : NULL;
            $primaryScheme->week_repeat = isset($request['week_repeat']) ? $request['week_repeat'] : NULL;
            $primaryScheme->quarter = isset($request['quarter']) ? $request['quarter'] : NULL;
            $primaryScheme->scheme_type = isset($request['scheme_type']) ? $request['scheme_type'] : '';
            $primaryScheme->scheme_basedon = isset($request['scheme_basedon']) ? $request['scheme_basedon'] : '';
            $primaryScheme->per_pcs = isset($request['per_pcs']) ? $request['per_pcs'] : 0;
            $primaryScheme->assign_to = isset($request['assign_to']) ? $request['assign_to'] : '';
            $primaryScheme->branch = (isset($request['branch']) && count($request['branch']) > 0) ? implode(',', $request['branch']) : '';
            $primaryScheme->state = (isset($request['state']) && count($request['state']) > 0) ? implode(',', $request['state']) : '';
            $primaryScheme->customer = (isset($request['customer']) && count($request['customer']) > 0) ? implode(',', $request['customer']) : '';
            $primaryScheme->customer_type = (isset($request['customer_type']) && count($request['customer_type']) > 0) ? implode(',', $request['customer_type']) : '';
            $primaryScheme->minimum = isset($request['minimum']) ? $request['minimum'] : null;
            $primaryScheme->maximum = isset($request['maximum']) ? $request['maximum'] : null;
            $primaryScheme->save();

            if ($request->import_file) {
                if (ob_get_contents()) ob_end_clean();
                ob_start();
                Excel::import(new PrimarySchemeImport(encrypt($primaryScheme->id)), $request['import_file']);
            } else {
                PrimarySchemeDetail::where('primary_scheme_id', $primaryScheme->id)->delete();
                $primarychemedetils = collect([]);
                if ($request['points']) {
                    foreach ($request['points'] as $key => $value) {
                        $primarychemedetils->push([
                            'active' => 'Y',
                            'primary_scheme_id' => $primaryScheme->id,
                            'product_id' => !empty($request['product_id']) ? $request['product_id'][$key] : null,
                            'category_id' => !empty($request['category_id']) ? $request['category_id'][$key] : null,
                            'subcategory_id' => !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null,
                            'groups' => !empty($request['groups']) ? $request['groups'][$key] : null,
                            'group_type' => !empty($request['group_type']) ? ($request['group_type'][$key]??NULL) : null,
                            'min' => isset($request['min']) ? $request['min'][$key] : null,
                            'max' => isset($request['max']) ? $request['max'][$key] : null,
                            'slab_min' => isset($request['slab_min']) ? $request['slab_min'][$key] : null,
                            'slab_max' => isset($request['slab_max']) ? $request['slab_max'][$key] : null,
                            'gift' => isset($request['gift']) ? $request['gift'][$key] : null,
                            'points' => isset($request['points']) ? $request['points'][$key] : 0,
                        ]);
                    }
                    if ($primarychemedetils->isNotEmpty()) {
                        PrimarySchemeDetail::insert($primarychemedetils->toArray());
                    }
                }
                if ($request['gift']) {
                    foreach ($request['gift'] as $key => $value) {
                        $primarychemedetils->push([
                            'active' => 'Y',
                            'primary_scheme_id' => $primaryScheme->id,
                            'product_id' => !empty($request['product_id']) ? $request['product_id'][$key] : null,
                            'category_id' => !empty($request['category_id']) ? $request['category_id'][$key] : null,
                            'subcategory_id' => !empty($request['subcategory_id']) ? $request['subcategory_id'][$key] : null,
                            'groups' => !empty($request['groups']) ? $request['groups'][$key] : null,
                            'group_type' => !empty($request['group_type']) ? ($request['group_type'][$key]??NULL) : null,
                            'min' => isset($request['min']) ? $request['min'][$key] : null,
                            'max' => isset($request['max']) ? $request['max'][$key] : null,
                            'slab_min' => isset($request['slab_min']) ? $request['slab_min'][$key] : null,
                            'slab_max' => isset($request['slab_max']) ? $request['slab_max'][$key] : null,
                            'gift' => isset($request['gift']) ? $request['gift'][$key] : null,
                            'points' => isset($request['points']) ? $request['points'][$key] : 0,
                        ]);
                    }
                    if ($primarychemedetils->isNotEmpty()) {
                        PrimarySchemeDetail::insert($primarychemedetils->toArray());
                    }
                }
            }
            return Redirect::to('primary_scheme')->with('message_success', 'Primary Scheme Store Successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PrimaryScheme  $primaryScheme
     * @return \Illuminate\Http\Response
     */
    public function destroy(PrimaryScheme $primaryScheme)
    {
        PrimarySchemeDetail::where('primary_scheme_id', $primaryScheme->id)->delete();
        $delete = $primaryScheme->delete();

        if ($delete) {
            return response()->json(['status' => 'success', 'message' => 'Scheme deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Scheme Delete!']);
    }

    public function primary_scheme_report(Request $request)
    {
        $primary_branchs = PrimarySales::groupBy('branch_id')->pluck('branch_id');
        $branches = Branch::whereIn('id', $primary_branchs)->where('active', 'Y')->select('id', 'branch_name')->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->where('active', 'Y')->orderBy('name', 'asc')->get();
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 1, $currentYear + 1);
        $currentDate = Carbon::now();
        $primary_schemes = PrimaryScheme::select('id', 'scheme_name')->get();
        $primary_divs = PrimarySales::select('division')->groupBy('division')->get();
        return view('primary_schemes.primary_scheme_report', compact('years', 'users', 'branches', 'primary_schemes', 'primary_divs'));
    }

    public function primary_scheme_report_download(Request $request)
    {
        abort_if(Gate::denies('primary_scheme_report_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        if ($request->financial_year && !empty($request->financial_year)) {
            $fileName = 'primary_scheme_report_' . $request->financial_year . '_' . $request->quarter . '.xlsx';
        } else {
            $fileName = 'primary_scheme_report.xlsx';
        }
        return Excel::download(new PrimarySchemeReportExport($request), $fileName);
    }

    public function primary_scheme_report_template()
    {
        abort_if(Gate::denies('scheme_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PrimarySchemeTepmlate, 'schemesGroupTemplate.xlsx');
    }

    public function primary_scheme_template()
    {
        abort_if(Gate::denies('scheme_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new PrimarySchemeNormalTepmlate, 'schemesTemplate.xlsx');
    }
}
