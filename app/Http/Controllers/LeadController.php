<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

use App\Exports\ExcelExport;
use App\Exports\LeadCheckinExport;
use App\Exports\LeadsTemplate;
use App\Imports\LeadsImport;
use Excel;

use DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Lead;
use App\Models\LeadContact;
use App\Models\LeadNote;
use App\Models\LeadTask;
use App\Models\User;
use App\Models\LeadOpportunity;
use App\Models\Pincode;
use App\Models\Country;
use App\Models\Address;
use App\Models\Branch;
use App\Models\Customers;
use App\Models\Division;
use App\Models\EmployeeDetail;
use App\Models\LeadCheckIn;
use App\Models\LeadLog;
use App\Models\LeadNotification;
use App\Models\OpportunitieStatus;
use App\Models\State;
use App\Models\Status;
use Carbon\Carbon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $users = User::where('active', '=', 'Y')->whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('id')->get();
        $status = Status::where('module', 'LeadStatus')->where('active', 'Y')->get();
        $lead_sources = config('constants.LEAD_SOURCES');
        $pincodes = Pincode::where('active', '=', 'Y')
            ->whereHas('assigncitiesusers', function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('userid', $userids);
                }
            })
            ->select('id', 'pincode')->orderBy('id', 'desc')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        return view('leads.index', compact('users', 'status', 'lead_sources', 'pincodes', 'states'));
    }

    public function getLeads(Request $request)
    {
        $leads = Lead::with(['contacts', 'assign_user', 'status_is']);

        $datetime = $request->input('datetime');
        if ($datetime != "") {
            $datetimes = array_map('trim', explode('-', $datetime));
            $start_time = $datetimes[0] ?? '';
            $end_time = $datetimes[1] ?? '';

            if (isset($start_time) && $start_time != '') {
                $start_time = str_replace('/', '-', $start_time);
                $start_time = \Carbon\Carbon::parse($start_time)->format('Y-m-d');
            }

            if (isset($end_time) && $end_time != '') {
                $end_time = str_replace('/', '-', $end_time);
                $end_time = \Carbon\Carbon::parse($end_time)->format('Y-m-d');
            }

            if ($start_time != "" && $end_time != "") {
                $leads->whereBetween(\DB::raw('DATE(created_at)'), [$start_time, $end_time]);
            } else if ($start_time != "") {
                $leads->where(\DB::raw('DATE(created_at)'), '>=', $start_time);
            } else if ($end_time != "") {
                $leads->where(\DB::raw('DATE(created_at)'), '<=', $end_time);
            }
        }

        if ($request->input('search') != "") {
            $search = $request->input('search');
            $leads->where(function ($query) use ($search) {
                $query->where('company_name', 'like', "%{$search}%")
                    ->orWhereHas('contacts', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->input('assign_to') != "") {
            $assign_to = $request->input('assign_to');
            $leads->where('assign_to', $assign_to);
        }

        if ($request->input('status') != "") {
            $status = $request->input('status');
            $leads->where('status', $status);
        }

        if ($request->input('state_id') != "") {
            $leads->whereHas('address', function ($query) use ($request) {
                $query->where('state_id', $request->input('state_id'));
            });
            if ($request->input('district_id') != "") {
                $leads->whereHas('address', function ($query) use ($request) {
                    $query->where('district_id', $request->input('district_id'));
                });
                if ($request->input('city_id') != "") {
                    $leads->whereHas('address', function ($query) use ($request) {
                        $query->where('city_id', $request->input('city_id'));
                    });
                }
            }
        }

        // dd(auth()->user()->hasRole('superadmin'));
        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $user_ids = getUsersReportingToAuth();
            $leads->whereIn('assign_to', $user_ids);
        }

        $leads = $leads->orderBy('created_at', 'desc')->select(\DB::raw(with(new Lead)->getTable() . '.*'))->groupBy('id');
        return DataTables::of($leads)
            ->editColumn('company_name', function ($lead) {
                $url = route('leads.show', $lead);
                return '<a href="' . $url . '">' . ucwords(strtolower($lead->company_name)) . '</a>';
            })
            ->editColumn('assign_to', function ($lead) {
                return $lead->assign_user ? $lead->assign_user->name : '-';
            })
            ->editColumn('contacts', function ($lead) {
                if (count($lead->contacts) > 0) {
                    if (count($lead->contacts) > 1) {
                        $contacts_name = $lead->contacts[0]->name ?? '';
                        return $contacts_name . " +" . count($lead->contacts) - 1;
                    } else {
                        return $contacts_name = $lead->contacts[0]->name ?? '';
                    }
                    return "";
                }
            })
            ->editColumn('phone', function ($lead) {

                if (count($lead->contacts) > 0) {
                    return $contacts_name = $lead->contacts[0]->phone_number ?? '';
                }
                return "";
            })
            ->editColumn('city', function ($lead) {
                return $lead->address ? $lead->address->cityname?->city_name : '-';
            })
            ->editColumn('email', function ($lead) {
                if (count($lead->contacts) > 0) {
                    return $contacts_name = $lead->contacts[0]->email ?? '';
                }
                return "";
            })
            ->editColumn('created_at', function ($lead) {
                return $lead->lead_generation_date ? \Carbon\Carbon::parse($lead->lead_generation_date)->format('M j, Y \a\t g:i a') : \Carbon\Carbon::parse($lead->created_at)->format('M j, Y \a\t g:i a');
            })

            ->addColumn('checkbox', function ($lead) {
                $lead_id = "'" . $lead->id . "'";

                return '<input type="checkbox" class="lead-checkbox checkbox_cls" value="' . $lead->id . '" name="lead_ids[]">';
            })

            ->editColumn('status', function ($lead) {
                if ($lead->status == '0') {
                    return "<span class='badge badge-warning' style='background-color: orange'>Pending</span>";
                } else {
                    if ($lead->status_is) {
                        if ($lead->status_is->status_name == 'Hot') {
                            return "<span class='badge badge-danger'>" . $lead->status_is->status_name . "</span>";
                        } else if ($lead->status_is->status_name == 'Warm') {
                            return "<span class='badge badge-warning' style='background-color: yellow;color: black'>" . $lead->status_is->status_name . "</span>";
                        } else if ($lead->status_is->status_name == 'Cold') {
                            return "<span class='badge badge-success'>" . $lead->status_is->status_name . "</span>";
                        }
                        return "<span class='badge badge-success'>" . $lead->status_is->status_name . "</span>";
                    } else {
                        return "-";
                    }
                }
            })
            ->editColumn('others', function ($lead) {
                if (!is_array($lead->others)) {
                    $jsonData = '';
                    $lead->others = json_decode($lead->others, true);
                }
                if (!empty($lead->others) && count($lead->others) > 0) {
                    foreach ($lead->others as $key => $value) {
                        $jsonData .= "<strong>" . ucwords(str_replace('_', ' ', $key)) . ":</strong> " . $value . "<br>";
                    }
                }
                return $jsonData;
            })
            ->editColumn('note', function ($lead) {
                $lastNote = $lead->notes->sortByDesc('created_at')->first();
                if ($lastNote) {
                    return $lastNote->note;
                }
                return '';
            })
            ->with('records_filtered_count', $leads->get()->count())
            ->rawColumns(['action', 'company_name', 'checkbox', 'status', 'others', 'note'])
            ->make(true);
    }

    function exportLeads(Request $request)
    {
        $filename = 'leads.xlsx';

        $results_per_page = 8000;
        $page_number = intval($request->input('page_number'));
        $page_result = ($page_number - 1) * $results_per_page;

        $leads = Lead::with(['contacts', 'opportunities']);
        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $user_ids = getUsersReportingToAuth();
            $leads->where('assign_to', $user_ids);
        }

        $datetime = $request->input('datetime');
        if ($datetime != "") {
            $datetimes = array_map('trim', explode('-', $datetime));
            $start_time = $datetimes[0] ?? '';
            $end_time = $datetimes[1] ?? '';

            if (isset($start_time) && $start_time != '') {
                $start_time = str_replace('/', '-', $start_time);
                $start_time = \Carbon\Carbon::parse($start_time)->format('Y-m-d');
            }

            if (isset($end_time) && $end_time != '') {
                $end_time = str_replace('/', '-', $end_time);
                $end_time = \Carbon\Carbon::parse($end_time)->format('Y-m-d');
            }

            if ($start_time != "" && $end_time != "") {
                $leads->whereBetween(\DB::raw('DATE(created_at)'), [$start_time, $end_time]);
            } else if ($start_time != "") {
                $leads->where(\DB::raw('DATE(created_at)'), '>=', $start_time);
            } else if ($end_time != "") {
                $leads->where(\DB::raw('DATE(created_at)'), '<=', $end_time);
            }
        }

        if ($request->assign_to && !empty($request->assign_to)) {
            $leads->where('assign_to', $request->assign_to);
        }

        $leads = $leads->orderBy('created_at', 'desc')->groupBy('id')->get();

        $allOtherKeys = [];
        $othersMap = [];

        // First pass: collect all unique keys from 'others' and store decoded per item
        foreach ($leads as $item) {
            $othersData = is_array($item->others)
                ? $item->others
                : (is_string($item->others) ? json_decode($item->others, true) : []);
            $othersData = is_array($othersData) ? $othersData : [];

            $allOtherKeys = array_unique(array_merge($allOtherKeys, array_keys($othersData)));
            $othersMap[$item->id] = $othersData;
        }

        // Build rows
        $rows = $leads->map(function ($item) use ($allOtherKeys, $othersMap) {
            $contact = $item->contacts->first();

            $contacts_name = $contact?->name ?? '';
            $contacts_number = $contact?->phone_number ?? '';
            $contacts_email = $contact?->email ?? '';
            $contacts_lead_source = $contact?->lead_source ?? '';

            $address = $item->address;

            $othersData = $othersMap[$item->id] ?? [];

            // Latest task
            $latestTaskModel = $item->tasks->sortByDesc('created_at')->first();
            $latestTask = $latestTaskModel
                ? sprintf(
                    '%s (%s)',
                    $latestTaskModel->description ?? '',
                    $latestTaskModel->created_at?->format('d-m-Y') ?? ''
                )
                : '-';

            // Lead time: difference between lead_generation_date and created_at if both present
            $leadTime = '';
            if ($item->lead_generation_date && $item->created_at) {
                try {
                    $leadTime = Carbon::parse($item->lead_generation_date)
                        ->diffInDays(Carbon::parse($item->created_at)) . ' days';
                } catch (\Exception $e) {
                    $leadTime = '';
                }
            }

            // Latest 5 notes (newest first)
            $latestNotes = $item->notes->sortByDesc('created_at')->take(5)->values();
            $noteCols = [];
            for ($i = 0; $i < 5; $i++) {
                if (isset($latestNotes[$i])) {
                    $n = $latestNotes[$i];
                    $dateStr = $n->created_at?->format('d-m-Y') ?? '';
                    $noteText = trim(($n->note ?? ''));
                    //Removed HTML tags
                    $noteText = strip_tags($noteText);
                    $noteCols[] = $noteText;
                    $noteCols[] = $dateStr;
                } else {
                    $noteCols[] = '';
                    $noteCols[] = '';
                }
            }

            // Base export row
            $baseRow = [
                $item->id,
                $item->lead_generation_date,
                $item->company_name,
                $contacts_name,
                $contacts_number,
                $contacts_email,
                $contacts_lead_source,
                $address->pincodename?->pincode ?? '',
                $address->cityname?->city_name ?? '',
                $address->districtname?->district_name ?? '',
                $item->status_is?->status_name ?? 'Pending',
                $address->address1 ?? '',
                $item->assign_user?->name ?? '',
                '', // Lead Status (custom field?)
                $item->close_duration ?? '',
                $item->createdby?->name ?? '',
                $leadTime,
                $item->opportunities->sum('amount') ?? '0',
                $latestTask,
                // latest 5 notes:
                ...$noteCols,
            ];

            // Add ordered others consistently
            $orderedOthers = collect($allOtherKeys)->mapWithKeys(function ($key) use ($othersData) {
                return [$key => $othersData[$key] ?? ''];
            })->toArray();

            return array_merge($baseRow, $orderedOthers);
        })->toArray();

        // Build headers
        $baseHeaders = [
            'ID',
            'Lead Generation Date',
            'Firm Name',
            'Customer Name',
            'Customer Number',
            'Email',
            'Lead Source',
            'Pincode',
            'City',
            'District',
            'Lead Type',
            'Address',
            'Assignee',
            'Lead Status', // custom field placeholder
            'Close Duration',
            'Created By',
            'Lead Time',
            'Sales Value',
            'Task',
            'Note 1',
            'Note 1 Date',
            'Note 2',
            'Note 2 Date',
            'Note 3',
            'Note 3 Date',
            'Note 4',
            'Note 4 Date',
            'Note 5',
            'Note 5 Date',
        ];

        // Append dynamic "others" headers in the same order
        $allOtherKeys = array_values($allOtherKeys); // reindex to preserve order
        $headers = array_merge($baseHeaders, $allOtherKeys);

        // ✅ Export
        $export = new ExcelExport($headers, $rows);
        return Excel::download($export, $filename);
    }


    public function uploadleadFiles(Request $request)
    {

        $rules = [
            'lead_id' => 'required',
            'lead_file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,pdf,xls,xlsx',
                'max:' . (config('media-library.max_file_size') / 1024), // max in KB
            ],
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id();
        $lead_id = $request->lead_id;
        $lead = Lead::where(['id' => $lead_id])->first();
        if ($lead) {

            if ($request->hasFile('lead_file')) {
                $file = $request->file('lead_file');
                $customname = time() . '.' . $file->getClientOriginalExtension();
                $lead->addMedia($file)
                    ->usingFileName($customname)
                    ->toMediaCollection('lead_file');
            }

            $request->session()->flash('message_success', __('Lead file upload successfully.'));
            return redirect()->route('leads.show', $lead);
        } else {
            //$request->session()->flash('message_success',__('Lead file upload successfully.'));
            return redirect()->route('leads.show', $lead);
        }
    }

    public function deleteMedia(Request $request)
    {

        $rules = [
            //'lead_id'=>'required',
            'media_id' => 'required',
        ];

        $request->validate($rules);
        $data = $request->all();
        $created_by = Auth::id();
        $lead_id = $request->lead_id;
        $media_id = $request->media_id;
        $media = Media::find($media_id);

        if ($media && $media->model_type === Lead::class) {
            $media->delete();
            $request->session()->flash('message_success', __('Lead file upload successfully.'));
            return redirect()->back();
        } else {
            return redirect()->back();
        }
    }




    public function searchExistsLead(Request $request)
    {
        $company_name = $request->company_name ?? '-';
        $contact_name = $request->contact_name ?? '-';
        $user_id = Auth::id() ?? '';
        $leads = Lead::where(['created_by' => $user_id])->where(function ($query) use ($company_name, $contact_name) {
            $query->where('company_name', 'like', "%{$company_name}%")
                ->orWhereHas('contacts', function ($subQuery) use ($contact_name) {
                    $subQuery->where('name', 'like', "%{$contact_name}%");
                });
        })->get();

        if ($leads->count() > 0) {
            $text = '<p class="text-muted">We\'ve found similar Leads that already exist:</p>
                <div class="table-responsive">
                  <table class="table align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Created</th>
                      </tr>
                    </thead>
                    <tbody>';

            foreach ($leads as $lead) {
                $firstContact = $lead->contacts->first();
                $contactName = $firstContact ? $firstContact->name : '—';
                $contactCount = $lead->contacts->count();
                if ($contactCount < 2) {
                    $contactname_html  = htmlspecialchars($contactName);
                } else {
                    $contactname_html = htmlspecialchars($contactName) . ' +' . ($contactCount - 1);
                }

                $text .= '<tr>
                    <td>
                      <a href="' . route('leads.show', $lead->id) . '" class="text-primary text-decoration-underline">'
                    . htmlspecialchars($lead->company_name) . '</a><br>
                      <small class="text-muted">' . $contactname_html . '</small>
                    </td>
                    <td>' . htmlspecialchars($lead->status ?? '—') . '</td>
                    <td>' . $lead->created_at->diffForHumans() . '</td>
                  </tr>';
            }

            $text .= '</tbody></table></div>';

            return $text;
        }

        return '';
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userids = getUsersReportingToAuth();
        $users = User::where('active', '=', 'Y')->whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('id')->get();
        $status = Status::where('module', 'LeadStatus')->where('active', 'Y')->get();
        $lead_sources = config('constants.LEAD_SOURCES');
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        return view('leads.create', compact('users', 'status', 'lead_sources', 'states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'status' => 'required',
            'company_name' => 'required',
            'contact_name' => 'required',
            'assign_to' => 'required|exists:users,id',
            'lead_source' => 'required',
        ];

        $request->validate($rules);
        if ($request->other) {
            $otherData = [
                'others' => $request->other,
            ];
            $otherData = json_encode($otherData, JSON_UNESCAPED_UNICODE);
        } else {
            $otherData = null;
        }
        $lead = Lead::create([
            'company_name' => $request->company_name,
            'company_url' => $request->company_url,
            'status' => $request->status ?? 0,
            'created_by' => Auth::id(),
            'lead_generation_date' => date('Y-m-d'),
            'lead_source' => $request->lead_source,
            'assign_to' => $request->assign_to ?? null,
            'others' => $otherData,
        ]);
        if ($lead->id) {
            Address::create([
                'model_type' => 'App\Models\Lead',
                'model_id' => $lead->id,
                'address1' => $request->address ?? 'N/A',
                'country_id' => 1,
                'pincode_id' => $request->pincode_id ?? null,
                'state_id' => $request->state_id ?? null,
                'city_id' => $request->city_id ?? null,
                'district_id' => $request->district_id ?? null,
                'created_by' => Auth::id()
            ]);
            $category = LeadContact::create([
                'name' => $request->contact_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'lead_source' => $request->lead_source,
                'lead_id' => $lead->id,
                'created_by' => Auth::id()
            ]);
            if (isset($request->note) && !empty($request->note)) {
                $note = LeadNote::create([
                    'note' => $request->note,
                    'lead_id' => $lead->id,
                    'created_by' => Auth::id()
                ]);
            }
            if (!empty($request->assign_to)) {
                SendPushNotification($request->assign_to, '🟢 You have been assigned 1 new lead.', 'lead');
                StoreLeadNotification($lead->id, 'Assigned Lead', '🟢 You have been assigned 1 new lead.', $request->assign_to, 'lead');
            }
        }

        return redirect()->route('leads.show', $lead);
    }

    public function storeAddress(Request $request)
    {
        $rules = [
            'lead_id' => 'required',
            'address1' => 'required',
            'address2' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            //'district_id'=>'required',
            'city_id' => 'required',
            'pincode_id' => 'required',

        ];

        $address_id = $request->address_id;
        $address = Address::where(['id' => $address_id])->first();
        if ($address) {
            $address->update(['model_type' => 'App\Models\Lead', 'model_id' => $request->lead_id, 'address1' => $request->address1, 'address2' => $request->address2, 'country_id' => $request->country_id, 'state_id' => $request->state_id, 'district_id' => $request->district_id, 'city_id' => $request->city_id, 'pincode_id' => $request->pincode_id]);
            $request->session()->flash('message_success', __('Lead Address Update successfully.'));
        } else {
            Address::create(['model_type' => 'App\Models\Lead', 'model_id' => $request->lead_id, 'address1' => $request->address1, 'address2' => $request->address2, 'country_id' => $request->country_id, 'state_id' => $request->state_id, 'district_id' => $request->district_id, 'city_id' => $request->city_id, 'pincode_id' => $request->pincode_id]);
            $request->session()->flash('message_success', __('Lead Address Added successfully.'));
        }

        return redirect()->back();
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Lead $lead)
    {
        $userids = getUsersReportingToAuth();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('name')->get();

        $lead_contacts = LeadContact::where(['lead_id' => $lead->id])->get();
        $lead_notes = LeadNote::where(['lead_id' => $lead->id])->get();
        $lead_tasks = LeadTask::where(['lead_id' => $lead->id])->get();
        $lead_opportunities = LeadOpportunity::where(['lead_id' => $lead->id])->get();

        $pincodes = Pincode::where('active', '=', 'Y')
            // ->whereHas('assigncitiesusers', function ($query) use ($userids) {
            //     if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            //         $query->whereIn('userid', $userids);
            //     }
            // })
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
        $address = Address::where(['model_type' => 'App\Models\Lead', 'model_id' => $lead->id])->first();
        if (isset($address)) {
            $address1 = $address->address1;
            $address2 = $address->address2;
            $city_name = $address->cityname->city_name ?? '';
            $state_name = $address->statename->state_name ?? '';
            $pincodename = $address->pincodename->pincode ?? '';
            $address_data = $address1 . "," . $address2 . "," . $city_name . "," . $state_name . "," . $pincodename;
        } else {
            $address_data = "";
        }

        $lead_logs = LeadLog::where(['lead_id' => $lead->id])->get();
        $opportunities = LeadOpportunity::where(['lead_id' => $lead->id])->get();

        $lead_notes->each(function ($item) {
            $item->type = 'note';
        });
        $lead_tasks->each(function ($item) {
            $item->type = 'task';
        });
        $lead_logs->each(function ($item) {
            $item->type = 'log';
        });
        $opportunities->each(function ($item) {
            $item->type = 'opportunity';
        });


        $combined = $lead_notes->merge($lead_tasks)->merge($lead_logs)->merge($opportunities)->sortByDesc('created_at')->values();
        // $combined = $lead_notes->merge($lead_tasks)->merge($lead_logs)->sortByDesc('created_at')->values();

        $media_items = $lead->getMedia('lead_file');
        $status = Status::where('module', 'LeadStatus')->where('active', 'Y')->get();
        $opportunity_status = OpportunitieStatus::orderBy('ordering', 'asc')->pluck('status_name', 'id')->toArray();
        return view('leads.show', compact('lead', 'lead_contacts', 'lead_notes', 'users', 'lead_tasks', 'lead_opportunities', 'countries', 'pincodes', 'address', 'address_data', 'media_items', 'combined', 'status', 'opportunity_status'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Lead $lead)
    {
        $userids = getUsersReportingToAuth();
        $users = User::where('active', '=', 'Y')->whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('id', $userids);
            }
        })->select('id', 'name')->orderBy('id')->get();
        $status = Status::where('module', 'LeadStatus')->where('active', 'Y')->get();
        $lead_sources = config('constants.LEAD_SOURCES');
        // $pincodes = Pincode::where('active', '=', 'Y')->select('id', 'pincode')->orderBy('id', 'desc')->get();
        $states = State::where('active', 'Y')->select('id', 'state_name')->get();
        return view('leads.create', compact('lead', 'users', 'status', 'lead_sources', 'states'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead)
    {
        if ($request->other) {
            $otherData = [
                'others' => $request->other,
            ];
            $otherData = json_encode($otherData, JSON_UNESCAPED_UNICODE);
        } else {
            $otherData = null;
        }

        if ($lead->assign_to != $request->assign_to) {
            SendPushNotification($request->assign_to, '🟢 You have been assigned 1 new lead.', 'lead');
            StoreLeadNotification($lead->id, 'Assigned Lead', '🟢 You have been assigned 1 new lead.', $request->assign_to, 'lead');
        }

        if ($lead->status != $request->status) {
            $old_status = Status::where('id', $lead->status)->first();
            $new_status = Status::where('id', $request->status)->first();
            $msg = 'Lead move from ' . $old_status->display_name . ' to ' . $new_status->display_name .
                ' by ' . Auth::user()->name;
            // SendPushNotification($lead->created_by, $msg);
            // StoreLeadNotification($lead->id, 'Status Changed', $msg, $lead->created_by, 'lead');
            LeadLog::create([
                'lead_id' => $lead->id,
                'message' => $msg,
                'created_by' => Auth::id(),
            ]);
        }

        $lead->update([
            'company_name' => $request->company_name,
            'company_url' => $request->company_url,
            'status' => $request->status ?? 0,
            'lead_generation_date' => date('Y-m-d'),
            'lead_source' => $request->lead_source,
            'assign_to' => $request->assign_to,
            'others' => $otherData,
        ]);
        Address::where('model_type', 'App\Models\Lead')->where('model_id', $lead->id)->update([
            'address1' => $request->address ?? 'N/A',
            'country_id' => 1,
            'pincode_id' => $request->pincode_id ?? null,
            'state_id' => $request->state_id ?? null,
            'city_id' => $request->city_id ?? null,
            'district_id' => $request->district_id ?? null,
        ]);
        LeadContact::where('lead_id', $lead->id)->update([
            'name' => $request->contact_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'url' => $request->url,
            'lead_source' => $request->lead_source,
        ]);
        LeadNote::where('lead_id', $lead->id)->update([
            'note' => $request->note,
        ]);
        return redirect()->route('leads.show', $lead);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        //
    }

    public function assignLead(Request $request)
    {
        $update = Lead::whereIn('id', $request->lead_id)->update(['assign_to' => $request->user_id]);
        if ($update) {
            SendPushNotification($request->user_id, '🟢 You have been assigned ' . count($request->lead_id) . ' new lead.', 'lead');
            StoreLeadNotification(null, 'Assigned Lead', '🟢 You have been assigned ' . count($request->lead_id) . ' new lead.', $request->user_id, 'lead');
            return response()->json(['status' => 'success', 'message' => 'Lead assigned successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    //Multiple delete
    public function deleteLead(Request $request)
    {
        $lead = Lead::whereIn('id', $request->lead_id)->delete();
        if ($lead) {
            return response()->json(['status' => 'success', 'message' => 'Lead deleted successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function changeStatus(Request $request)
    {
        $lead = Lead::where('id', $request->lead_id)->first();
        $old_status = Status::where('id', $lead->status)->first();
        $new_status = Status::where('id', $request->status)->first();
        $msg = 'Lead move from ' . $old_status->display_name . ' to ' . $new_status->display_name .
            ' by ' . Auth::user()->name;
        // SendPushNotification($lead->created_by, $msg);
        // StoreLeadNotification($lead->id, 'Status Changed', $msg, $lead->created_by, 'lead');
        LeadLog::create([
            'lead_id' => $lead->id,
            'message' => $msg,
            'created_by' => Auth::id(),
        ]);
        $update = Lead::where('id', $request->lead_id)->update(['status' => $request->status]);
        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'Status updated successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('lead_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new LeadsImport, request()->file('import_file'));
        return back();
    }

    public function template()
    {
        abort_if(Gate::denies('lead_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new LeadsTemplate, 'LeadTemplate.xlsx');
    }

    public function convert_lead(Request $request)
    {
        foreach ($request->lead_id as $lead_id) {
            $lead = Lead::where('id', $lead_id)->first();
            $len = $lead->contacts && count($lead->contacts) > 0 ? strlen(preg_replace('/\s+/', '', $lead->contacts[0]->phone_number)) : 0;
            $phone = $lead->contacts && count($lead->contacts) > 0 ? $lead->contacts[0]->phone_number : '';
            if (($len === 12 && substr($phone, 0, 2) === '91') ||
                ($len === 13 && substr($phone, 0, 3) === '+91')
            ) {
                $phone = preg_replace('/^\+?91/', '', $phone); // remove +91 or 91 from start
            }
            $check_customer = Customers::where('mobile', 'like', '%' . $phone . '%')->first();
            if ($check_customer && $check_customer->id) {
                return response()->json(['status' => 'error', 'message' => 'Customer already exist with this phone number.']);
            }
            $data = [
                'name' => $lead->company_name,
                'first_name' => $lead->contacts && count($lead->contacts) > 0 ? $lead->contacts[0]->name : '',
                'email' => $lead->contacts && count($lead->contacts) > 0 ? $lead->contacts[0]->email : '',
                'mobile' => $lead->contacts && count($lead->contacts) > 0 ? $lead->contacts[0]->phone_number : '',
                'creation_date' => date('Y-m-d'),
                'customertype' => 9,
                'created_by' => Auth::id(),
            ];
            $customer = new Customers();
            $response = $customer->save_data($data);
            if ($response['status'] == 'success') {
                $new_customer_id = $response['customer_id'];
                $lead->customer_id = $new_customer_id;
                $lead->save();
                if ($lead->address && !empty($lead->address)) {
                    Address::where('id', $lead->address->id)->update(['customer_id' => $new_customer_id]);
                    $employeeDetail = EmployeeDetail::create(
                        [
                            'customer_id' => $new_customer_id,
                            'user_id' => $lead->assign_to ?? Auth::id(),
                            'created_by' => Auth::id(),
                        ]
                    );
                }

                return response()->json(['status' => 'success', 'message' => 'Lead converted successfully.']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
            }
        }
    }

    public function visit_report(Request $request)
    {
        $userids = getUsersReportingToAuth();
        if ($request->ajax()) {
            $data = LeadCheckIn::with('users:id,name', 'lead:id,company_name,lead_source', 'lead.address')
                ->whereHas('users', function ($query) use ($userids, $request) {
                    if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                        $query->whereIn('id', $userids);
                    }
                    if ($request->user_id && $request->user_id != null && $request->user_id != '') {
                        $query->where('user_id', $request->user_id);
                    }
                    if ($request->division_id && $request->division_id != null && $request->division_id != '') {
                        $query->where('division_id', $request->division_id);
                    }
                    if ($request->branch_id && $request->branch_id != null && $request->branch_id != '') {
                        $query->where('branch_id', $request->branch_id);
                    }
                    if ($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != '') {
                        $startDate = date('Y-m-d', strtotime($request->start_date));
                        $endDate = date('Y-m-d', strtotime($request->end_date));
                        $query->whereDate('checkin_date', '>=', $startDate)
                            ->whereDate('checkin_date', '<=', $endDate);
                    }
                })
                ->select('id', 'checkin_date', 'checkin_time', 'user_id', 'lead_id', 'checkout_time', 'checkout_note')
                ->orderBy('checkin_date', 'desc');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('visit_time', function ($query) {
                    if (!empty($query->checkout_time) && !empty($query->checkin_time)) {
                        $parsedTime1 = Carbon::createFromFormat('H:i:s', $query->checkout_time);
                        $parsedTime2 = Carbon::createFromFormat('H:i:s', $query->checkin_time);

                        $difference = $parsedTime1->diff($parsedTime2);
                        $interval = $difference->format('%H:%I:%S');
                        return $interval;
                    } else {
                        return '-';
                    }
                })
                ->addColumn('district_name', function ($query) {
                    return isset($query['lead']['address']['districtname']['district_name']) ? $query['lead']['address']['districtname']['district_name'] : '';
                })
                ->addColumn('city_name', function ($query) {
                    return  isset($query['lead']['address']['cityname']['city_name']) ? $query['lead']['address']['cityname']['city_name'] : '';
                })
                ->addColumn('pincode', function ($query) {
                    return isset($query['lead']['address']['zipcode']) ? $query['lead']['address']['zipcode'] : '';
                })
                ->addColumn('address', function ($query) {
                    return isset($query['lead']['address']['address1']) ? $query['lead']['address']['address1'] : '';
                })
                ->rawColumns(['visit_time', 'district_name', 'city_name', 'pincode', 'address'])
                ->make(true);
        }
        $users = user::whereDoesntHave('roles', function ($query) {
            $query->whereIn('id', config('constants.customer_roles'));
        })->whereIn('id', $userids)->select('id', 'name')->orderBy('name', 'asc')->get();
        $divisions = Division::where('active', 'Y')->get();
        $branches = Branch::where('active', 'Y')->get();
        return view('leads.leadvisit', compact('users', 'divisions', 'branches'));
    }

    public function visit_report_download(Request $request)
    {
        ////abort_if(Gate::denies('visitreport_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new LeadCheckinExport($request), 'Lead CheckIn.xlsx');
    }
}
