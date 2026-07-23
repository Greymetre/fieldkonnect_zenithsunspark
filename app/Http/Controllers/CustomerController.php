<?php

namespace App\Http\Controllers;

use App\Models\{Customers, UserLogin, CustomerType, FirmType, Regions, Pincode, Country, CustomerDetails, Address, Attachment, SurveyData, Field, State, City, Beat, BeatCustomer, DealIn, Division, Redemption, SchemeDetails, ShippingAddress};
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;
use Validator;
use Gate;
use Excel;
use App\DataTables\{CustomersDataTable, DistributorDataTable, CustomerLoginDataTable, SurveyDataTable};
use App\Imports\CustomersImport;
use App\Exports\CustomersExport;
use App\Exports\DistributorExport;
use App\Exports\SurveyExport;
use App\Exports\CustomersTemplate;
use App\Http\Requests\CustomersRequest;
use App\Models\TransactionHistory;
use PDF;
use Laravel\Passport\Token;

use App\Models\EmployeeDetail;
use App\Models\ParentDetail;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->customers = new Customers();
        $this->customerdetails = new CustomerDetails();
        $this->address = new Address();
        $this->shippingaddress = new ShippingAddress();
        $this->path = 'customers';
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();

        $beats = Beat::where('active', '=', 'Y')->whereHas('beatusers', function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('user_id', $userids);
            }
        })->select('id', 'beat_name')->get();
        
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('name')->get();
        $states = State::where('active', '=', 'Y')
            ->whereHas('statecities', function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereHas('assignusers', function ($q) use ($userids) {
                        $q->whereIn('userid', $userids);
                    });
                }
            })
            ->select('id', 'state_name')->orderBy('state_name')->get();
        $cities = City::where('active', '=', 'Y')->whereHas('assignusers', function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('userid', $userids);
            }
        })->select('id', 'city_name')->orderBy('city_name')->get();
        $customertype = CustomerType::select('id', 'customertype_name')->orderBy('id', 'desc')->get();

        $all_user_branches = User::whereDoesntHave('roles', function ($query) {
            $query->wherein('id', config('constants.customer_roles'));
        })->with('getbranch')->whereIn('id', $userids)->orderBy('branch_id')->get();
        $branches = array();
        $all_branch = array();
        $bkey = 0;
        foreach ($all_user_branches as $k => $val) {
            if ($val->getbranch) {
                if (!in_array($val->getbranch->id, $all_branch)) {
                    array_push($all_branch, $val->getbranch->id);
                    $branches[$bkey]['id'] = $val->getbranch->id;
                    $branches[$bkey]['name'] = $val->getbranch->branch_name;
                    $bkey++;
                }
            }
        }




        if ($request->ajax()) {
            if (auth()->user()->hasRole('Customer Dealer')) {
                $request['parent_id'] = auth()->user()->customerid;
            }
            // dd($request['executive_id']);
            $data = Customers::with('customertypes', 'firmtypes', 'createdbyname')
                ->where(function ($query) use ($request, $userids) {
                    if (!empty($request['executive_id'])) {
                        $query->where(function ($q) use ($request) {
                            $q->where('executive_id', $request['executive_id'])
                                ->orWhere('created_by', $request['executive_id']);
                        });
                    }
                    if (!empty($request['parent_id'])) {
                        $customer_idss = ParentDetail::where('parent_id', $request['parent_id'])->pluck('customer_id')->toArray();
                        $customer_idss[] = auth()->user()->customerid;
                        if (!empty($customer_idss)) {
                            $query->whereIn('id', $customer_idss);
                        }
                    }
                    if (!empty($request['customertype'])) {
                        $query->where('customertype', $request['customertype']);
                    }
                    if (!empty($request['created_by'])) {
                        if ($request['created_by'] == 'self') {
                            $query->where('created_by', NULL);
                        } elseif ($request['created_by'] == 'other') {
                            $query->where('created_by', '!=', NULL);
                        }
                    }
                    // if(!empty($request['beat_id']))
                    // {
                    //     $query->whereHas('beatdetails',function($q) use($request){
                    //         $q->where('beat_id', $request['beat_id']);
                    //     });
                    // }

                    if (!empty($request['branch_id'])) {
                        $branch_user_id = User::whereDoesntHave('roles', function ($query) {
                            $query->wherein('id', config('constants.customer_roles'));
                        })->whereIn('branch_id', $request['branch_id'])->pluck('id');
                        if (!empty($branch_user_id)) {
                            $query->whereIn('created_by', $branch_user_id);
                        }
                    }


                    if (!empty($request['state_id'])) {
                        $query->whereHas('customeraddress', function ($q) use ($request) {
                            $q->where('state_id', $request['state_id']);
                        });
                    }
                    if (!empty($request['city_id'])) {
                        $query->whereHas('customeraddress', function ($q) use ($request) {
                            $q->where('city_id', $request['city_id']);
                        });
                    }
                    if (!empty($request['division_id'])) {
                        $division_users = User::where('division_id', $request['division_id'])->pluck('id')->toArray();
                        $query->where(function ($query) use ($division_users) {
                            $query->whereIn('executive_id', $division_users)
                                ->orWhereIn('created_by', $division_users);
                        });
                    }
                    if (!empty($request['active'])) {
                        $query->where('active', $request['active']);
                    }
                    if ($request->start_date && !empty($request->start_date)) {
                        $query->whereDate('created_at', '>=', $request->start_date);
                    }
                    if ($request->end_date && !empty($request->end_date)) {
                        $query->whereDate('created_at', '<=', $request->end_date);
                    }
                    if (!empty($request['search']) && is_array($request['search']) == false) {
                        $search = $request['search'];
                        $query->where(function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->Orwhere('first_name', 'like', "%{$search}%")
                                ->Orwhere('last_name', 'like', "%{$search}%")
                                ->Orwhere('email', 'like', "%{$search}%")
                                ->Orwhere('mobile', 'like', "%{$search}%")
                                ->Orwhere('sap_code', 'like', "%{$search}%");
                        });
                    }
                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Sub_Support')  && !Auth::user()->hasRole('HO_Account')  && !Auth::user()->hasRole('HR_Admin') && !Auth::user()->hasRole('Service Admin') && !Auth::user()->hasRole('All Customers') && !Auth::user()->hasRole('Sub_Admin') && !Auth::user()->hasRole('Customer Dealer') && !Auth::user()->hasRole('Data_Crm')  && !Auth::user()->hasRole('Sub billing')) {
                        $query->whereIn('executive_id', $userids)
                            ->orWhereIn('created_by', $userids);
                    }
                    // $query->whereIn('customertype', ['2','3','4','5','6']);
                })
                // dd($data->toSql());
                ->latest();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($item) {
                    return '<input type="checkbox" id="manual_entry_' . $item->id . '" class="manual_entry_cb" value="' . $item->id . '" />';
                })
                ->editColumn('created_at', function ($data) {
                    return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                })
                ->editColumn('contact_person', function ($data) {
                    return $data->first_name . ' ' . $data->last_name;
                })
                ->editColumn('beat_name', function ($data) {
                    $beat_names = array();
                    $beat_details = BeatCustomer::with('beats')
                        ->where('customer_id', $data->id)
                        ->get();

                    foreach ($beat_details as $key => $value) {
                        array_push($beat_names, $value->beats->beat_name);
                    }
                    if (count($beat_names) > 0) {
                        return implode(',', $beat_names);
                    } else {
                        return '-';
                    }
                })
                ->addColumn('action', function ($query) {
                    $btn = '';
                    $activebtn = '';
                    if (auth()->user()->can(['customer_edit'])) {
                        $btn = $btn . '<a href="' . url("customers/" . encrypt($query->id) . '/edit') . '" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' ' . trans('panel.customers.title_singular') . '">
                                                <i class="material-icons">edit</i>
                                            </a>';
                    }
                    if (auth()->user()->can(['customer_show'])) {
                        $btn = $btn . '<a href="' . url("customers/" . encrypt($query->id)) . '" class="btn btn-theme btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.customers.title_singular') . '">
                                                <i class="material-icons">visibility</i>
                                            </a>';
                    }
                    if (auth()->user()->can(['customer_delete'])) {
                        // $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.customers.title_singular').'">
                        //             <i class="material-icons">clear</i>
                        //           </a>';
                    }
                    if (auth()->user()->can(['customer_active'])) {
                        $active = ($query->active == 'Y') ? 'checked="" value="' . $query->active . '"' : 'value="' . $query->active . '"';
                        $activebtn = '<div class="togglebutton">
                                            <label>
                                              <input type="checkbox"' . $active . ' id="' . $query->id . '" class="customerActive">
                                              <span class="toggle"></span>
                                            </label>
                                          </div>';
                    }
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                            ' . $activebtn . '
                                        </div>';
                })
                ->addColumn('image', function ($query) {
                    $profileimage = !empty($query->profile_image) ? $query->profile_image : asset('assets/img/placeholder.jpg');
                    return '<img src="' . $profileimage . '" border="0" width="70" class="rounded imageDisplayModel" align="center" />';
                })
                ->addColumn('createdbyname.name', function ($query) {
                    return $query->created_by ? $query->createdbyname->name : 'Self';
                })
                ->addColumn('profileimage', function ($query) {
                    $profileimage = !empty($query->shop_image) ? $query->shop_image : asset('assets/img/placeholder.jpg');
                    return '<img src="' . $profileimage . '" border="0" width="70" class="rounded imageDisplayModel" align="center" />';
                })
                ->addColumn('createdbyname.name', function ($query) {
                    return $query->created_by ? $query->createdbyname->name : 'Self';
                })
                ->addColumn('mobile', function ($query) {
                    $whatsappLink = "https://wa.me/" . $query->mobile;
                    return '<a style="display: flex;align-items: center;" href="' . $whatsappLink . '" target="_blank">
                                <i class="fa fa-whatsapp text-success mr-1" style="font-size:20px"></i>
                                ' . $query->mobile . '
                            </a> ';
                })
                ->rawColumns(['action', 'beat_name', 'image', 'checkbox', 'createdbyname.name', 'profileimage', 'mobile', 'contact_person'])
                ->make(true);
        }
        $divisions = Division::where('active', 'Y')->get();
        return view('customers.index', compact('beats', 'users', 'states', 'cities', 'customertype', 'branches', 'divisions'));
    }

    public function distributors(DistributorDataTable $dataTable)
    {
        ////abort_if(Gate::denies('distributor_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('customers.distributor');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        ////abort_if(Gate::denies('customer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //$status = DB::table('status')->whereIn('id',[1,2,5,7])->select('id', 'name')->orderBy('id','desc')->get();
        $userids = getUsersReportingToAuth();
        $pincodes = Pincode::where('active', '=', 'Y')
            ->whereHas('assigncitiesusers', function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('userid', $userids);
                }
            })
            ->select('id', 'pincode')->orderBy('id', 'desc')->get();
        $countries = Country::where('active', '=', 'Y')
            ->whereHas('countrystates', function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereHas('statecities', function ($query) use ($userids) {
                        $query->whereHas('assignusers', function ($q) use ($userids) {
                            $q->whereIn('userid', $userids);
                        });
                    });
                }
            })
            ->select('id', 'country_name')->orderBy('id', 'desc')->get();
        $customertype = CustomerType::select('id', 'customertype_name')->orderBy('id', 'desc')->get();
        $firmtype = FirmType::select('id', 'firmtype_name')->orderBy('id', 'desc')->get();
        $fields = Field::with('fieldsData')->whereIn('module', $customertype->pluck('id'))->where('active', '=', 'Y')->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->wherein('id', config('constants.customer_roles'));
        })->whereDoesntHave('roles', function ($query) {
            $query->wherein('id', config('constants.customer_roles'));
        })->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('id', 'desc')->get();
        $deals = array();

        $parentcustomers = Customers::where('active', '=', 'Y')->where('customertype', '!=', '2')->select('id', 'name')->orderBy('id', 'desc')->get();

        return view('customers.create', compact('pincodes', 'customertype', 'firmtype', 'pincodes', 'countries', 'fields', 'users', 'deals', 'parentcustomers'))->with('customers', $this->customers);
    }

    public function createDistributor()
    {
        ////abort_if(Gate::denies('distributor_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $pincodes = Pincode::where('active', '=', 'Y')->select('id', 'pincode')->orderBy('id', 'desc')->get();
        $countries = Country::where('active', '=', 'Y')->select('id', 'country_name')->orderBy('id', 'desc')->get();
        $customertype = CustomerType::where('type_name', '=', 'distributor')->select('id', 'customertype_name')->orderBy('id', 'desc')->get();
        $firmtype = FirmType::select('id', 'firmtype_name')->orderBy('id', 'desc')->get();
        $fields = Field::with('fieldsData')->whereIn('module', $customertype->pluck('id'))->where('active', '=', 'Y')->get();
        return view('customers.distributor_add', compact('pincodes', 'customertype', 'firmtype', 'pincodes', 'countries', 'fields'))->with('customers', $this->customers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomersRequest $request)
    {

        try {
            abort_if(Gate::denies('customer_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['active'] = 'Y';
            $request['created_by'] = Auth::user()->id;
            $docimages = collect([]);
            if ($request->file('image')) {
                $image = $request->file('image');
                $filename = 'shop';
                unset($request['image']);
                $request['profile_image'] = fileupload($image, $this->path, $filename);
            }
            if ($request->file('profileImage')) {
                $path = 'customers';
                $image = $request->file('profileImage');
                $filename = 'profile_' . $request->id;
                unset($request['image']);
                $request['shop_image'] = fileupload($image, $this->path, $filename);
            }
            if ($request->file('imggstin')) {
                $image = $request->file('imggstin');
                $filename = 'gstin';
                unset($request['imggstin']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'gstin',
                ]);
            }
            if ($request->file('imgpan')) {
                $image = $request->file('imgpan');
                $filename = 'pan';
                unset($request['imgpan']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'pan',
                ]);
            }
            if ($request->file('imgaadhar')) {
                $image = $request->file('imgaadhar');
                $filename = 'aadhar';
                unset($request['image']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'aadhar',
                ]);
            }
            if ($request->file('imgother')) {
                $image = $request->file('imgother');
                $filename = 'other';
                unset($request['imgother']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'other',
                ]);
            }
            // echo '<pre>';
            // print_r($request->all());
            $response = $this->customers->save_data($request);
            if ($response['status'] == 'success') {
                $request['customer_id'] = $response['customer_id'];
                $this->customerdetails->save_data($request);
                $this->address->save_data($request);
                $this->shippingaddress->save_data($request);
                $attachments = $docimages->map(function ($item, $key) use ($request) {
                    $item['customer_id'] = $request['customer_id'];
                    return $item;
                });
                if ($attachments->isNotEmpty()) {
                    Attachment::insert($attachments->toArray());
                }
                if (!empty($request['survey'])) {
                    foreach ($request['survey'] as $key => $rows) {

                        if (!empty($rows['value'])) {
                            $value = (is_array($rows['value'])) ? implode(', ', $rows['value']) : $rows['value'];
                            SurveyData::updateOrCreate(['customer_id' => $request['customer_id'], 'field_id' => $rows['field_id']], [
                                'customer_id' => $request['customer_id'],
                                'field_id' => $rows['field_id'],
                                'value' => $value,
                                'created_by'  => Auth::user()->id,
                            ]);
                        }
                    }
                }

                if ($request['dealing']) {
                    $dealings = $request['dealing'];
                    foreach ($dealings as $key => $deal) {
                        $types = !empty($deal['types']) ? $deal['types'] : '';
                        DealIn::updateOrCreate([
                            'customer_id' => $request['id'],
                            'types' => $types
                        ], [
                            'customer_id'   => !empty($request['id']) ? $request['id'] : null,
                            'types' => $types,
                            'hcv' => isset($deal['hcv']) ? $deal['hcv'] : false,
                            'mav' => isset($deal['mav']) ? $deal['mav'] : false,
                            'lmv' => isset($deal['lmv']) ? $deal['lmv'] : false,
                            'lcv' => isset($deal['lcv']) ? $deal['lcv'] : false,
                            'other' => isset($deal['other']) ? $deal['other'] : false,
                            'tractor' => isset($deal['tractor']) ? $deal['tractor'] : false,
                        ]);
                    }
                }



                //employee start

                if (!empty($request['executive_id'])) {
                    foreach ($request['executive_id'] as $key => $rows) {
                        $employeeDetail = EmployeeDetail::create(
                            [
                                'customer_id' => $request['customer_id'],
                                'user_id' => $rows,
                                'created_by' => Auth::user()->id,
                            ]
                        );
                    }
                }

                // employee end


                //parent start

                if (!empty($request['parent_id'])) {
                    foreach ($request['parent_id'] as $key => $rows) {
                        $parentDetail = ParentDetail::create(
                            [
                                'customer_id' => $request['customer_id'],
                                'parent_id' => $rows,
                                'created_by' => Auth::user()->id,
                            ]
                        );
                    }
                }
                return Redirect::to('customers')->with('message_success', $response['message']);
            }
            return redirect()->back()->with('message_danger', $response['message'])->withInput();
        } catch (\Exception $e) {

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        if ($request->kyc) {
            $kyc = true;
        } else {
            $kyc = false;
        }
        ////abort_if(Gate::denies('customer_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $thistorys = TransactionHistory::where('customer_id', $id)->get();
        $total_points = TransactionHistory::where('customer_id', $id)->sum('point') ?? 0;
        // $active_points = TransactionHistory::where('customer_id', $id)->where('status', '1')->sum('point')??0;
        // $provision_points = TransactionHistory::where('customer_id', $id)->where('status', '0')->sum('point')??0;
        $active_points = 0;
        $provision_points = 0;
        foreach ($thistorys as $thistory) {
            if ($thistory->status == '1') {
                $active_points += $thistory->point;
            } else {
                $active_points += $thistory->active_point;
                $provision_points += $thistory->provision_point;
            }
        }
        $total_redemption = Redemption::where('customer_id', $id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
        $total_rejected = Redemption::where('customer_id', $id)->where('status', '2')->sum('redeem_amount') ?? 0;
        $total_balance = (int)$active_points - (int)$total_redemption;
        $customers = Customers::find($id);
        // dd($customers);
        $customers['due_amount'] = totalDueAmount($id);
        return view('customers.show', compact('total_balance', 'total_points', 'total_redemption', 'active_points', 'provision_points', 'total_rejected', 'kyc'))->with('customers', $customers);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        ////abort_if(Gate::denies('customer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $id = decrypt($id);
        $customers = Customers::with('surveys', 'customershippingaddress')->find($id);
        $deals = DealIn::where('customer_id', '=', $id)->get();
        $pincodes = Pincode::where('active', '=', 'Y')->whereHas('assigncitiesusers', function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('userid', $userids);
            }
        })->select('id', 'pincode')->orderBy('id', 'desc')->get();
        $countries = Country::where('active', '=', 'Y')->whereHas('countrystates', function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereHas('statecities', function ($query) use ($userids) {
                    $query->whereHas('assignusers', function ($q) use ($userids) {
                        $q->whereIn('userid', $userids);
                    });
                });
            }
        })->select('id', 'country_name')->orderBy('id', 'desc')->get();
        $customertype = CustomerType::select('id', 'customertype_name')->orderBy('id', 'desc')->get();
        $firmtype = FirmType::select('id', 'firmtype_name')->orderBy('id', 'desc')->get();
        $customers['gstin_image'] = $customers['customerdocuments']->where('document_name', 'gstin')->pluck('file_path')->first();
        $customers['pan_image'] = $customers['customerdocuments']->where('document_name', 'pan')->pluck('file_path')->first();
        $customers['aadhar_image'] = $customers['customerdocuments']->where('document_name', 'aadhar')->pluck('file_path')->first();
        $customers['other_image'] = $customers['customerdocuments']->where('document_name', 'other')->pluck('file_path')->first();
        $fields = Field::with('fieldsData')->whereIn('module', [$customers->customertype])->where('active', '=', 'Y')->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->wherein('id', config('constants.customer_roles'));
        })->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('id', 'desc')->get();

        $parentcustomers = Customers::where('active', '=', 'Y')->where('customertype', '!=', '2')->select('id', 'name')->orderBy('id', 'desc')->get();
        return view('customers.create', compact('pincodes', 'customertype', 'firmtype', 'pincodes', 'countries', 'fields', 'users', 'deals', 'parentcustomers'))->with('customers', $customers);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'gstin_no' => 'nullable|min:15|max:15',
                'pan_no' => 'nullable|regex:/^[a-zA-Z]{5}\d{4}[a-zA-Z]$/',
                'aadhar_no' => 'nullable|numeric|digits:12',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            ////abort_if(Gate::denies('customer_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request['updated_by'] = Auth::user()->id;
            $docimages = collect([]);
            if ($request->file('image')) {
                $path = 'customers/';
                $image = $request->file('image');
                $filename = 'profile_' . $id;
                unset($request['image']);
                $request['profile_image'] = fileupload($image, $this->path, $filename);
            }
            if ($request->file('profileImage')) {
                $path = 'customers';
                $image = $request->file('profileImage');
                $filename = 'profile_' . $request->id;
                unset($request['image']);
                $request['shop_image'] = fileupload($image, $this->path, $filename);
            }
            if ($request->file('imggstin')) {
                $path = 'customers';
                $image = $request->file('imggstin');
                $filename = 'gstin_' . $id;
                unset($request['imggstin']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $path, $filename),
                    'document_name' =>  'gstin',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['gstin_no_status' => '0']);
            }
            if ($request->file('imgpan')) {
                $path = 'customers';
                $image = $request->file('imgpan');
                $filename = 'pan_' . $id;
                unset($request['imgpan']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $path, $filename),
                    'document_name' =>  'pan',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['pan_no_status' => '0']);
            }
            if ($request->file('imgaadhar')) {
                $path = 'customers';
                $image = $request->file('imgaadhar');
                $filename = 'aadhar_' . $id;
                unset($request['image']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $path, $filename),
                    'document_name' =>  'aadhar',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['aadhar_no_status' => '0']);
            }
            if ($request->file('imgaadharback')) {
                $path = 'customers';
                $image = $request->file('imgaadharback');
                $filename = 'aadharback_' . $id;
                unset($request['image']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $path, $filename),
                    'document_name' =>  'aadharback',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['aadhar_no_status' => '0']);
            }
            if ($request->file('imgbankpass')) {
                $path = 'customers';
                $image = $request->file('imgbankpass');
                $filename = 'bankpass_' . $id;
                unset($request['image']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $path, $filename),
                    'document_name' =>  'bankpass',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['bank_status' => '0']);
            }
            if ($request->file('imgother')) {
                $path = 'customers';
                $image = $request->file('imgother');
                $filename = 'other_' . $id;
                unset($request['imgother']);
                $docimages->push([
                    'active'        => 'Y',
                    'file_path'     => fileupload($image, $this->path, $filename),
                    'document_name' =>  'other',
                ]);
                CustomerDetails::where('customer_id', $request->id)->update(['otherid_no_status' => '0']);
            }
            $request['customer_id'] = $id;
            $response = $this->customers->update_data($request);
            if ($response['status'] == 'success') {
                $this->customerdetails->save_data($request);
                $this->address->save_data($request);
                $this->shippingaddress->save_data($request);
                $attachments = $docimages->map(function ($item, $key) use ($request) {
                    $item['customer_id'] = $request['customer_id'];
                    return $item;
                });


                // if($attachments->isNotEmpty())
                // {
                //     Attachment::insert($attachments->toArray());
                // }

                foreach ($docimages as $docimage) {
                    $existingAttachment = Attachment::where('document_name', $docimage['document_name'])
                        ->where('customer_id', $request['customer_id'])
                        ->first();

                    if ($existingAttachment) {
                        $existingAttachment->update($docimage);
                    } else {
                        Attachment::create(array_merge($docimage, ['customer_id' => $request['customer_id']]));
                    }
                }

                if ($request['survey']) {
                    foreach ($request['survey'] as $key => $rows) {

                        if (!empty($rows['value'])) {
                            $value = (is_array($rows['value'])) ? implode(', ', $rows['value']) : $rows['value'];
                            SurveyData::updateOrCreate(['customer_id' => $id, 'field_id' => $rows['field_id']], [
                                'customer_id' => $id,
                                'field_id' => $rows['field_id'],
                                'value' => $value,
                                'created_by'  => Auth::user()->id,
                            ]);
                        }
                    }
                }

                if ($request['dealing']) {
                    $dealings = $request['dealing'];
                    foreach ($dealings as $key => $deal) {
                        $types = !empty($deal['types']) ? $deal['types'] : '';
                        DealIn::updateOrCreate([
                            'customer_id' => $request['id'],
                            'types' => $types
                        ], [
                            'customer_id'   => !empty($request['id']) ? $request['id'] : null,
                            'types' => $types,
                            'hcv' => isset($deal['hcv']) ? $deal['hcv'] : false,
                            'mav' => isset($deal['mav']) ? $deal['mav'] : false,
                            'lmv' => isset($deal['lmv']) ? $deal['lmv'] : false,
                            'lcv' => isset($deal['lcv']) ? $deal['lcv'] : false,
                            'other' => isset($deal['other']) ? $deal['other'] : false,
                            'tractor' => isset($deal['tractor']) ? $deal['tractor'] : false,
                        ]);
                    }
                }



                //employee start

                if (!empty($request['executive_id'])) {

                    EmployeeDetail::where('customer_id', $request['id'])->delete();
                    foreach ($request['executive_id'] as $key => $rows) {
                        $employeeDetail = EmployeeDetail::updateOrCreate(
                            //['customer_id' => $request['id']],

                            [
                                'customer_id' => $request['id'],
                                'user_id' => $rows,
                                'created_by' => Auth::user()->id,
                            ]
                        );
                    }
                }

                //employee end

                //parent start

                if (!empty($request['parent_id'])) {
                    ParentDetail::where('customer_id', $request['id'])->delete();
                    foreach ($request['parent_id'] as $key => $rows) {
                        $parentDetail = ParentDetail::updateOrCreate(
                            // ['customer_id' => $request['id']],  
                            [
                                'customer_id' => $request['id'],
                                'parent_id' => $rows,
                                'created_by' => Auth::user()->id,
                            ]
                        );
                    }
                }

                // parent end

                return Redirect::to('customers')->with('message_success', $response['message']);
            }
            return redirect()->back()->with('message_danger', $response['message'])->withInput();
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ////abort_if(Gate::denies('customer_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $walletids = DB::table('wallets')->where('customer_id', '=', $id)->select('id')->get()->pluck('id')->toArray();
            DB::table('wallet_details')->whereIn('wallet_id', $walletids)->delete();
            DB::table('wallets')->where('customer_id', '=', $id)->delete();
            $orderids = DB::table('orders')->where('buyer_id', '=', $id)
                ->orWhere('seller_id', '=', $id)
                ->select('id')->get()->pluck('id')->toArray();
            DB::table('order_details')->whereIn('order_id', $orderids)->delete();
            DB::table('orders')->where('buyer_id', '=', $id)->orWhere('seller_id', '=', $id)->delete();
            $saleids = DB::table('sales')->where('buyer_id', '=', $id)
                ->orWhere('seller_id', '=', $id)
                ->select('id')->get()->pluck('id')->toArray();
            DB::table('sales_details')->whereIn('sales_id', $saleids)->delete();
            DB::table('sales')->where('buyer_id', '=', $id)->orWhere('seller_id', '=', $id)->delete();
            DB::table('attachments')->where('customer_id', '=', $id)->delete();
            DB::table('beat_customers')->where('customer_id', '=', $id)->delete();
            // DB::table('check_in')->where('customer_id', '=', $id)->delete();
            DB::table('notifications')->where('customer_id', '=', $id)->delete();
            DB::table('supports')->where('customer_id', '=', $id)->delete();
            DB::table('survey_data')->where('customer_id', '=', $id)->delete();
            DB::table('tasks')->where('customer_id', '=', $id)->delete();
            DB::table('visit_reports')->where('customer_id', '=', $id)->delete();
            DB::table('user_activities')->where('customerid', '=', $id)->delete();
            CustomerDetails::where('customer_id', $id)->delete();
            Address::where('customer_id', $id)->delete();

            EmployeeDetail::where('customer_id', $id)->delete();
            ParentDetail::where('customer_id', $id)->delete();

            $customer = Customers::find($id);
            if ($customer->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Customer deleted successfully!']);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in Customer Delete!']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function active(Request $request)
    {
        $customer = Customers::find($request['id']);

        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found']);
        }

        // Toggle status
        $newStatus = ($request['active'] == 'Y') ? 'N' : 'Y';
        $customer->active = $newStatus;

        if ($customer->save()) {
            $cUser = User::where('customerid', $customer->id)->first();
            if ($cUser) {
                $cUser->active = $newStatus;
                $cUser->save();
            }
            $message = ($newStatus == 'N') ? 'Inactive' : 'Active';

            // If customer is deactivated, revoke all tokens (logout)
            if ($newStatus == 'N') {
                // Revoke access tokens
                Token::where('user_id', $customer->id)->update(['revoked' => true]);

                // Revoke refresh tokens too (optional but safer)
                DB::table('oauth_refresh_tokens')->whereIn(
                    'access_token_id',
                    function ($query) use ($customer) {
                        $query->select('id')
                            ->from('oauth_access_tokens')
                            ->where('user_id', $customer->id);
                    }
                )->update(['revoked' => true]);
            }

            return response()->json(['status' => 'success', 'message' => 'Customer ' . $message . ' Successfully!']);
        }

        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }

    public function customersLogin(CustomerLoginDataTable $dataTable)
    {
        ////abort_if(Gate::denies('customer_login'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('customers.login');
    }

    public function upload(Request $request)
    {
        ////abort_if(Gate::denies('customer_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        $update = Excel::import(new CustomersImport, request()->file('import_file'));
        return back();
    }
    public function download(Request $request)
    {
        ////abort_if(Gate::denies('customer_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CustomersExport($request), 'customers.xlsx');
    }
    public function distributordownload()
    {
        ////abort_if(Gate::denies('customer_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new DistributorExport, 'distributor.xlsx');
    }


    public function template()
    {
        ////abort_if(Gate::denies('customer_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new CustomersTemplate, 'customers.xlsx');
    }

    public function survey(Request $request)
    {
        if ($request->ajax()) {
            $data = SurveyData::with('customers')->select('customer_id', DB::raw('count(value) as total_ans'))
                ->groupBy('customer_id')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('survey', function ($row) {
                    $survey = '';
                    $questions = SurveyData::where('customer_id', $row['customer_id'])->select('field_id', 'value')->get();
                    if (!empty($questions)) {
                        foreach ($questions as $key => $value) {
                            $survey = $survey . '<p>' . $value['fields']['label_name'] . '</p>' . '<b> Ans. ' . $value['value'] . '</b>';
                        }
                    }
                    return $survey;
                })
                ->rawColumns(['survey'])
                ->make(true);
        }
        $customertype = CustomerType::select('id', 'customertype_name')->orderBy('id', 'desc')->get();
        return view('customers.survey', compact('customertype'));
    }

    public function surveyDownload(Request $request)
    {
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new SurveyExport($request), 'survey.xlsx');
    }

    public function balance_confirmation(Request $request)
    {

        return view('redemption.balance_pdf');

        $pdf = PDF::loadView('redemption.balance_pdf');

        return $pdf->download('document.pdf');
    }

    public function customer_balance(Request $request)
    {
        $customers['profile_image'] = Attachment::where([
            'customer_id' => auth()->user()->customerid,
            'document_name' => 'customer_balance_upload',
        ])->value('file_path');

        return view('customers.customer_balance', compact('customers'));
    }

    public function customer_balance_update(Request $request)
    {
        $customer = auth()->user();
        $image = $request->file('image');
        $filename = 'customer_balance_upload_' . $customer->customerid;
        $existingAttachment = Attachment::where('document_name', 'customer_balance_upload')
            ->where('customer_id', $customer->customerid)
            ->first();

        $docimage = [
            'active'        => 'Y',
            'file_path'     => fileupload($image, $this->path, $filename),
            'document_name' =>  'customer_balance_upload',
        ];

        if ($existingAttachment) {
            $existingAttachment->update($docimage);
        } else {
            Attachment::create(array_merge($docimage, ['customer_id' => $customer->customerid]));
        }

        return redirect()->back()->with('message_success', 'Balance confirmation uploaded successfully');
    }

    public function customer_balance_list(Request $request)
    {
        $data = Attachment::with('customer')->where('document_name', 'customer_balance_upload');
        return Datatables::of($data)
            ->addIndexColumn()
            ->editColumn('upload_iamge', function ($item) {
                return '<a href="' . $item->file_path . '" target="_blank"><img width="300" src="' . $item->file_path . '" alt=""></a>';
            })
            ->rawColumns(['upload_iamge'])
            ->make(true);
    }
}
