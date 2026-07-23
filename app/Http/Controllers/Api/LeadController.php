<?php

namespace App\Http\Controllers\Api;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\LeadCheckIn;
use App\Models\LeadContact;
use App\Models\LeadLog;
use App\Models\LeadNote;
use App\Models\LeadNotification;
use App\Models\LeadOpportunity;
use App\Models\LeadTask;
use App\Models\OpportunitieStatus;
use App\Models\Status;
use App\Models\TaskAssignment;
use App\Models\TaskComment;
use App\Models\Tasks;
use App\Models\TaskStatusLog;
use App\Models\User;
use App\Models\VisitReport;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class LeadController extends Controller
{

    public function __construct()
    {
        $this->checkin = new LeadCheckIn();

        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }
    public function getLeads(Request $request)
    {
        $user = $request->user();
        $pageSize = (int) $request->input('pageSize', 10);

        // -----------------------
        // DATA (filtered)
        // -----------------------
        $listQuery = Lead::query()
            ->with(['address', 'status_is', 'contacts', 'notes', 'opportunities']);

        if (!$user->hasRole('superadmin')) {
            $reporting_users = getUsersReportingToAuth($user->id);
            $listQuery->where(function ($q) use ($reporting_users) {
                $q->whereIn('created_by', $reporting_users)
                    ->orWhereIn('assign_to', $reporting_users);
            });
        }

        if ($request->filled('search')) {
            $listQuery->where(function ($query) use ($request) {
                $query->where('company_name', 'like', "%{$request->search}%")
                    ->orWhereHas('contacts', function ($subQuery) use ($request) {
                        $subQuery->where('name', 'like', "%{$request->search}%")
                            ->orWhere('phone_number', 'like', "%{$request->search}%");
                    });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $listQuery->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date]);
        }

        if ($request->filled('user_id')) {
            $listQuery->where('assign_to', $request->user_id);
        }

        if ($request->filled('lead_source')) {
            $listQuery->where('lead_source', $request->lead_source);
        }

        if ($request->filled('status')) {
            $listQuery->where('status', (int) $request->status);
        }

        $leads = $listQuery->latest()->paginate($pageSize);

        // Shape items
        $leads = $leads->map(function ($lead) {
            return [
                'id' => $lead->id,
                'name' => $lead->company_name,
                'address' => $lead->address ? $lead->address->full_address : '',
                'city' => $lead->address ? $lead->address?->cityname?->city_name : '',
                'lead_source_lead' => $lead->lead_source,
                'status' => [
                    'id' => $lead->status_is ? $lead->status_is->id : 0,
                    'display_name' => $lead->status_is ? $lead->status_is->display_name : 'Pending',
                ],
                'contact' => [
                    'name' => $lead->contacts->first()->name ?? null,
                    'phone_number' => $lead->contacts->first()->phone_number ?? null,
                    'email' => $lead->contacts->first()->email ?? null,
                    'url' => $lead->contacts->first()->url ?? null,
                    'lead_source' => $lead->contacts->first()->lead_source ?? null,
                ],
                'note' => ($note = optional($lead->notes()->latest()->first())->note) ? strip_tags($note) : null,
                'opportunity_status' => $lead->opportunities()->latest()->first()?->status_is->status_name ?? '',
                'created_at' => $lead->created_at->toDateTimeString(),
            ];
        });

        // -----------------------
        // COUNTS (UNFILTERED)
        // -----------------------
        $leadStatus = Status::where('module', 'LeadStatus')->select('id', 'display_name')->get();

        $grouped = Lead::select('status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('status');
        if (!$user->hasRole('superadmin')) {
            $reporting_users = getUsersReportingToAuth($user->id);
            $grouped->where(function ($q) use ($reporting_users) {
                $q->whereIn('created_by', $reporting_users)
                    ->orWhereIn('assign_to', $reporting_users);
            });
        };
        $grouped = $grouped->pluck('cnt', 'status');

        $counts = [
            ['id' => -1, 'display_name' => 'total',   'count' => $grouped->sum()],
            ['id' => 0,  'display_name' => 'pending', 'count' => $grouped[0] ?? 0],
        ];

        foreach ($leadStatus as $s) {
            $counts[] = [
                'id' => $s->id,
                'display_name' => strtolower($s->display_name),
                'count' => $grouped[$s->id] ?? 0,
            ];
        }

        $notification_count = LeadNotification::where(['user_id' => $user->id, 'read' => 0])->count();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data retrieved successfully.',
            'data'    => $leads,
            'counts'  => $counts,
            'notification_count' => (string)$notification_count
        ], 200);
    }

    public function leadStatusSource(Request $request)
    {
        $status = Status::where('module', 'LeadStatus')->select('id', 'display_name')->get();
        $source = [
            [
                'key' => 'Google',
                'value' => 'Google'
            ],
            [
                'key' => 'Facebook',
                'value' => 'Facebook'
            ],
            [
                'key' => 'Instagram',
                'value' => 'Instagram'
            ],
            [
                'key' => 'Indiamart',
                'value' => 'Indiamart'
            ],
            [
                'key' => 'Justdial',
                'value' => 'Justdial'
            ],
            [
                'key' => 'Self',
                'value' => 'Self'
            ],
        ];
        $data = [
            'status' => $status,
            'source' => $source,
        ];
        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
    }

    public function leadCreate(Request $request)
    {
        $validate = validator($request->all(), [
            'company_name' => 'required',
            'contact_name' => 'required',
            'phone_number' => 'required',
            'status' => [
                'required',
                Rule::exists('statuses', 'id')->where('module', 'LeadStatus'),
            ],
            'lead_source' => 'required|in:Google,Indiamart,Justdial,Instagram,Facebook,Self',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        if ($request->other) {
            $otherData = [
                'others' => $request->other,
            ];
            $otherData = json_encode($otherData, JSON_UNESCAPED_UNICODE);
        } else {
            $otherData = null;
        }
        $user = $request->user();
        if (isset($request->lead_id) && !empty($request->lead_id)) {
            $lead = Lead::find($request->lead_id);
            $old_status = Status::where('id', $lead->status)->first();
            $new_status = Status::where('id', $request->status)->first();
            $msg = 'Lead move from ' . $old_status->display_name . ' to ' . $new_status->display_name .
                ' by ' . $user->name;
            // SendPushNotification($lead->created_by, $msg);
            // StoreLeadNotification($lead->id, 'Status Changed', $msg, $lead->created_by, 'lead');
            LeadLog::create([
                'lead_id' => $lead->id,
                'message' => $msg,
                'created_by' => $user->id,
            ]);
            if ($lead->assign_to != $request->assign_to) {
                SendPushNotification($request->assign_to, '🟢 You have been assigned 1 new lead.', 'lead');
                StoreLeadNotification($lead->id, 'Assigned Lead', '🟢 You have been assigned 1 new lead.', $request->assign_to);
            }
            $lead->update([
                'company_name' => $request->company_name,
                'company_url' => $request->website,
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

            $firstContact = LeadContact::where('lead_id', $lead->id)->first();
            if ($firstContact) {
                $firstContact->update([
                    'name' => $request->contact_name,
                    'phone_number' => $request->phone_number,
                    'email' => $request->email,
                    'url' => $request->url,
                    'lead_source' => $request->lead_source,
                ]);
            }

            $latestNote = LeadNote::where('lead_id', $lead->id)->latest()->first();
            if ($latestNote) {
                $latestNote->update([
                    'note' => $request->note,
                ]);
            } else {
                LeadNote::create([
                    'lead_id' => $lead->id,
                    'note' => $request->note,
                    'created_by' => auth()->id(), // if you track who created the note
                ]);
            }
            return response()->json(['status' => 'success', 'message' => 'Lead updated successfully.']);
        } else {
            $lead = Lead::create([
                'company_name' => $request->company_name,
                'company_url' => $request->website,
                'status' => $request->status ?? 0,
                'created_by' => $user->id,
                'lead_generation_date' => date('Y-m-d'),
                'lead_source' => $request->lead_source,
                'assign_to' => $user->id,
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
                    'created_by' => $user->id,
                ]);
                LeadContact::create([
                    'name' => $request->contact_name,
                    'phone_number' => $request->phone_number,
                    'email' => $request->email,
                    'lead_source' => $request->lead_source,
                    'lead_id' => $lead->id,
                    'created_by' => $user->id
                ]);
                if (isset($request->note) && !empty($request->note)) {
                    $note = LeadNote::create([
                        'note' => $request->note,
                        'lead_id' => $lead->id,
                        'created_by' => $user->id
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Lead created successfully.', 'data' => $lead], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
            }
        }
    }

    public function leadDetails(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        $lead = Lead::with('assign_user:id,name')->find($request->lead_id);
        if ($lead) {
            $data = [
                'id' => $lead->id,
                'company_name' => $lead->company_name,
                'contact_id' => $lead->contacts->first()->id ?? null,
                'contact_name' => $lead->contacts->first()->name ?? null,
                'website' => $lead->company_url,
                'phone_number' => $lead->contacts->first()->phone_number ?? null,
                'email' => $lead->contacts->first()->email ?? null,
                'address' => $lead->address?->full_address ?? null,
                'pincode' => $lead->address?->pincodename?->pincode ?? null,
                'pincode_id' => $lead->address?->pincodename?->id ?? null,
                'city' => $lead->address?->cityname?->city_name ?? null,
                'city_id' => $lead->address?->cityname?->id ?? null,
                'district' => $lead->address?->districtname?->district_name ?? null,
                'district_id' => $lead->address?->districtname?->id ?? null,
                'state' => $lead->address?->statename?->state_name ?? null,
                'state_id' => $lead->address?->statename?->id ?? null,
                'status' => $lead->status_is ? $lead->status_is->display_name : 'Pending',
                'status_id' => $lead->status_is ? $lead->status_is->id : '0',
                'lead_source' => $lead->lead_source,
                'assign_user_id' => $lead->assign_user ? $lead->assign_user->id : null,
                'assign_user_name' => $lead->assign_user ? $lead->assign_user->name : null,
                'note' => ($note = optional($lead->notes()->latest()->first())->note) ? strip_tags($note) : null,
                'lead_generation_date' => (
                    !empty($lead->lead_generation_date) && $lead->lead_generation_date != '0000-00-00'
                    ? date('d M Y', strtotime($lead->lead_generation_date))
                    : $lead->created_at->format('d M Y')
                ),
                'conversion_date' => $lead->conversion_date ? date('d M Y', strtotime($lead->conversion_date)) : null,
                'updated_at' => $lead->updated_at->format('d M Y'),
            ];
            $lead_notes = LeadNote::with('createdby:id,name')->where(['lead_id' => $lead->id])->get();
            $lead_tasks = LeadTask::with('assignUser:id,name', 'createdby:id,name')->where(['lead_id' => $lead->id])->get();
            $lead_logs = LeadLog::where(['lead_id' => $lead->id])->get();
            $opportunities = LeadOpportunity::where(['lead_id' => $lead->id])->get();
            $lead_notes->each(function ($item) {
                $item->type = 'note';
                $item->created_at_formatted = $item->created_at->format('d M Y');
                $item->note = strip_tags($item->note);
            });
            $lead_tasks->each(function ($item) {
                $item->type = 'task';
                $item->created_at_formatted = $item->created_at->format('d M Y');
                $item->assignUser = $item->assignUser ?? '';
                $item->date = date('d-m-Y', strtotime($item->date));
            });
            $lead_logs->each(function ($item) {
                $item->type = 'log';
                $item->created_at_formatted = $item->created_at->format('d M Y');
            });
            $opportunities->each(function ($item) {
                $item->type = 'opportunity';
                $item->created_at_formatted = $item->created_at->format('d M Y');
            });

            $combined = $lead_notes->merge($lead_tasks)->merge($lead_logs)->merge($opportunities)->sortByDesc('created_at')->values();
            // $combined = $lead_notes->merge($lead_tasks)->merge($lead_logs)->sortByDesc('created_at')->values();

            $notification_count = LeadNotification::where(['user_id' => $request->user()->id, 'read' => 0])->count();
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data, 'notes_tasks' => $combined, 'notification_count' => $notification_count], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data not found.']);
        }
    }

    public function addNote(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id' => 'required',
            'note' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        if ($request->note_id && !empty($request->note_id)) {
            $note = LeadNote::find($request->note_id);
            if ($note) {
                $note->update(['note' => $request->note]);
                return response()->json(['status' => 'success', 'message' => 'Note updated successfully.', 'data' => $note], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Note not found.']);
            }
        } else {
            $lead = Lead::find($request->lead_id);
            if ($lead) {
                $note = LeadNote::create([
                    'note' => $request->note,
                    'lead_id' => $lead->id,
                    'created_by' => $request->user()->id
                ]);
                return response()->json(['status' => 'success', 'message' => 'Note added successfully.', 'data' => $note], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Lead not found.']);
            }
        }
    }

    public function getTaskDropdowns(Request $request)
    {
        $priorities = [
            [
                "id" => "low",
                "name" => "Low"
            ],
            [
                "id" => "medium",
                "name" => "Medium"
            ],
            [
                "id" => "high",
                "name" => "High"
            ]
        ];
        $status = [
            [
                "id" => "open",
                "name" => "Open"
            ],
            [
                "id" => "in_progress",
                "name" => "In Progress"
            ],
            [
                "id" => "completed",
                "name" => "Completed"
            ]
        ];
        $user_ids = getUsersReportingToAuth($request->user()->id);
        $users = User::select('id', 'name');
        if ($request->user()->hasRole('superadmin')) {
            $users->where(function ($query) use ($user_ids) {
                $query->whereIn('id', $user_ids);
            });
        }
        $users = $users->get();
        $data = [
            'users' => $users,
            'priorities' => $priorities,
            'status' => $status,
        ];

        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
    }

    public function addleadTask(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id' => 'required',
            'assigned_to' => 'required',
            'description' => 'required',
            'date' => 'required',
            'priority' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        $created_by = $request->user()->id;
        $task_id = $request->task_id;
        $lead_task = LeadTask::where(['id' => $task_id])->first();

        if ($lead_task) {
            $lead_task->update(['assigned_to' => $request->assigned_to, 'lead_id' => $request->lead_id, 'created_by' => $created_by, 'description' => $request->description, 'date' => $request->date, 'time' => $request->time, 'priority' => $request->priority]);
            $new = false;
        } else {
            $lead_task = LeadTask::create(['assigned_to' => $request->assigned_to, 'lead_id' => $request->lead_id, 'created_by' => $created_by, 'description' => $request->description, 'date' => $request->date, 'time' => $request->time, 'priority' => $request->priority]);
            $new = true;

            SendPushNotification($request->assigned_to, '📝 A new task has been assigned to you.', 'task');
            StoreLeadNotification($lead_task->id, 'Assigned Task', '📝 A new task has been assigned to you.', $request->assigned_to, 'task');
        }

        if ($new) {
            return response()->json(['status' => 'success', 'message' => 'Task added successfully.', 'data' => $lead_task], 200);
        } else {
            return response()->json(['status' => 'success', 'message' => 'Task updated successfully.', 'data' => $lead_task], 200);
        }
    }

    public function getLeadContacts(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        $lead = Lead::with('contacts:id,lead_id,name,title')->find($request->lead_id);
        $opportunity_statuses = OpportunitieStatus::select('id', 'status_name')->orderBy('ordering', 'asc')->get();
        if ($lead) {
            $contacts = $lead->contacts;
            $data = [
                'contacts' => $contacts,
                'opportunity_statuses' => $opportunity_statuses
            ];
            return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Data not found.']);
        }
    }

    public function addLeadopportunity(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id' => 'required',
            'assigned_to' => 'required',
            'lead_contact_id' => 'required',
            'amount' => 'required',
            //'type'=>'required',
            'estimated_close_date' => 'required',
            'confidence' => 'required',
            'note' => 'required',
            'status' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        $created_by = Auth::id();
        $opportunity_id = $request->opportunity_id;
        $lead_opportunity = LeadOpportunity::where(['id' => $opportunity_id])->first();
        if ($lead_opportunity) {
            if ($lead_opportunity->status != $request->status) {
                $oppo_status = OpportunitieStatus::orderBy('ordering', 'desc')->first();
                if ($request->status == $oppo_status->id) {
                    Lead::where(['id' => $lead_opportunity->lead_id])->update(['conversion_date' => date('Y-m-d')]);
                }
            }
            $lead_opportunity->update(['note' => $request->note, 'created_by' => $created_by, 'assigned_to' => $request->assigned_to, 'lead_contact_id' => $request->lead_contact_id, 'estimated_close_date' => $request->estimated_close_date, 'confidence' => $request->confidence, 'status' => $request->status, 'amount' => $request->amount]);
            $new = false;
        } else {
            $lead_opportunity = LeadOpportunity::create(['note' => $request->note, 'lead_id' => $request->lead_id, 'created_by' => $created_by, 'assigned_to' => $request->assigned_to, 'lead_contact_id' => $request->lead_contact_id, 'amount' => $request->amount, 'type' => $request->type, 'estimated_close_date' => $request->estimated_close_date, 'confidence' => $request->confidence, 'status' => $request->status]);
            $new = true;
        }

        $cur_status = OpportunitieStatus::where(['id' => $lead_opportunity->status])->first();
        $msg = '🎯 Lead move to opportunity ' . $cur_status->status_name .
            ': ' . Str::limit($lead_opportunity->lead->company_name, 10, '...') .
            ' by ' . Auth::user()->name;
        SendPushNotification($lead_opportunity->lead->created_by, $msg, 'opportunity');
        StoreLeadNotification($lead_opportunity->id, 'New Opportunity', $msg, $lead_opportunity->lead->created_by, 'opportunity');

        if ($new) {
            return response()->json(['status' => 'success', 'message' => 'Opportunity added successfully.', 'data' => $lead_opportunity], 200);
        } else {
            return response()->json(['status' => 'success', 'message' => 'Opportunity updated successfully.', 'data' => $lead_opportunity], 200);
        }
    }

    public function getAllOpportunities(Request $request)
    {
        $opportunity_statuses = OpportunitieStatus::select('id', 'status_name')->orderBy('ordering', 'asc')->get();
        $all_opportunities = LeadOpportunity::with('lead:id,company_name', 'assignUser:id,name', 'leadContact:id,name', 'status_is:id,status_name');
        if (!$request->user()->hasRole('superadmin')) {
            $user_ids = getUsersReportingToAuth($request->user()->id);
            $lead_ids = Lead::where('assign_to', $user_ids)->pluck('id');
            $all_opportunities->where('assigned_to', $user_ids);
        }
        $data = [];
        $data[0]['status_id'] = -1;
        $data[0]['status_name'] = 'All';
        $data[0]['total_opportunities'] = $all_opportunities->get()->count();
        $data[0]['total_amount'] = $all_opportunities->get()->sum('amount');
        if ($request->status != -1 && !empty($request->status)) {
            $all_opportunities->where('status', $request->status);
        }
        if ($request->user_id != -1 && !empty($request->user_id)) {
            $all_opportunities->where('assigned_to', $request->user_id);
        }
        foreach ($opportunity_statuses as $key => $opportunity_status) {
            $status_opportunities = LeadOpportunity::with('lead:id,company_name', 'assignUser:id,name');
            if (!$request->user()->hasRole('superadmin')) {
                $user_ids = getUsersReportingToAuth($request->user()->id);
                $lead_ids = Lead::where('assign_to', $user_ids)->pluck('id');
                $status_opportunities->where('assigned_to', $user_ids);
            }
            $status_opportunities = $status_opportunities->where('status', $opportunity_status->id);
            $data[$key + 1]['status_id'] = $opportunity_status->id;
            $data[$key + 1]['status_name'] = $opportunity_status->status_name;
            $data[$key + 1]['total_opportunities'] = $status_opportunities->where('status', $opportunity_status->id)->count();
            $data[$key + 1]['total_amount'] = $status_opportunities->where('status', $opportunity_status->id)->sum('amount');
            // $data[$key+1]['opportunities'] = $all_opportunities;
        }
        $user_ids = getUsersReportingToAuth($request->user()->id);
        $users = User::select('id', 'name');
        if ($request->user()->hasRole('superadmin')) {
            $users->where(function ($query) use ($user_ids) {
                $query->whereIn('id', $user_ids);
            });
        }
        $users = $users->get();
        $all_opportunities = $all_opportunities->orderBy('created_at', 'desc')->paginate($request->pageSize ?? 30);
        $all_opportunities->each(function ($lead_opportunity) {
            $lead_opportunity->estimated_close_date = date('d-m-Y', strtotime($lead_opportunity->estimated_close_date));
        });
        $main_data = [];
        $main_data['opportunities'] = $all_opportunities->items();
        $main_data['counter'] = $data;
        $main_data['users'] = $users;
        $notification_count = LeadNotification::where(['user_id' => $request->user()->id, 'read' => 0])->count();
        return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $main_data, 'notification_count' => $notification_count], 200);
    }

    public function deleteOpportunity(Request $request)
    {
        $validate = validator($request->all(), [
            'opportunity_id' => 'required',
        ]);
        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        $opportunity = LeadOpportunity::find($request->opportunity_id);
        if (!$opportunity) {
            return response()->json(['status' => 'error', 'message' => 'Opportunity not found.']);
        }
        if ($opportunity->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Opportunity deleted successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function updateLeadStatus(Request $request)
    {
        $validate = validator($request->all(), [
            'lead_id' => 'required',
            'status' => [
                'required',
                Rule::exists('statuses', 'id')->where('module', 'LeadStatus'),
            ],
        ]);
        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }
        $lead = Lead::find($request->lead_id);
        if (!$lead) {
            return response()->json(['status' => 'error', 'message' => 'Lead not found.']);
        }
        $old_status = Status::where('id', $lead->status)->first();
        $new_status = Status::where('id', $request->status)->first();
        $msg = 'Lead move from ' . $old_status->display_name . ' to ' . $new_status->display_name .
            ' by ' . $request->user()->name;
        // SendPushNotification($lead->created_by, $msg);
        // StoreLeadNotification($lead->id, 'Status Changed', $msg, $lead->created_by, 'lead');
        LeadLog::create([
            'lead_id' => $lead->id,
            'message' => $msg,
            'created_by' => $request->user()->id,
        ]);
        $lead->status = $request->status;
        if ($lead->save()) {
            return response()->json(['status' => 'success', 'message' => 'Lead status updated successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function getCheckin(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->active == 'N') {
                return response()->json(['status' => 'error', 'message' =>  'User Inactive'], 401);
            }
            $user_id = $user->id;
            $pageSize = $request->input('pageSize');
            $query = $this->checkin->where(function ($query) use ($user_id) {
                $query->where('user_id', '=', $user_id);
            })
                ->select('id', 'lead_id', 'checkin_date', 'checkin_time', 'checkin_latitude', 'checkin_longitude', 'checkin_address', 'checkout_date', 'checkout_time', 'checkout_latitude', 'checkout_longitude', 'checkout_address', 'beatscheduleid')->orderBy('checkin_date', 'desc')->orderBy('checkin_time', 'desc');
            $db_data = (!empty($pageSize)) ? $query->paginate($pageSize) : $query->get();
            $data = collect([]);
            if ($db_data->isNotEmpty()) {
                foreach ($db_data as $key => $value) {
                    $data->push([
                        'checkin_id' => isset($value['id']) ? $value['id'] : 0,
                        'lead_id' => isset($value['lead_id']) ? $value['lead_id'] : null,
                        'customer_name' => isset($value['customers']['name']) ? $value['customers']['name'] : $value['leads']['name'],
                        'customer_type' => isset($value['customers']['customertypes']['customertype_name']) ? $value['customers']['customertypes']['customertype_name'] : '',
                        'checkin_date' => isset($value['checkin_date']) ? $value['checkin_date'] : '',
                        'checkin_time' => isset($value['checkin_time']) ? $value['checkin_time'] : '',
                        'checkin_latitude' => isset($value['checkin_latitude']) ? $value['checkin_latitude'] : '',
                        'checkin_longitude' => isset($value['checkin_longitude']) ? $value['checkin_longitude'] : '',
                        'checkin_address' => isset($value['checkin_address']) ? $value['checkin_address'] : '',
                        'checkout_date' => isset($value['checkout_date']) ? $value['checkout_date'] : '',
                        'checkout_time' => isset($value['checkout_time']) ? $value['checkout_time'] : '',
                        'checkout_latitude' => isset($value['checkout_latitude']) ? $value['checkout_latitude'] : '',
                        'checkout_longitude' => isset($value['checkout_longitude']) ? $value['checkout_longitude'] : '',
                        'checkout_address' => isset($value['checkout_address']) ? $value['checkout_address'] : '',
                        'is_lead' => isset($value['lead_id']) ? 'No' : 'Yes',
                        'beat_schedule_id' => isset($value['beatscheduleid']) ? $value['beatscheduleid'] : 0,
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], $this->successStatus);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function submitCheckin(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->active == 'N') {
                return response()->json(['status' => 'error', 'message' =>  'User Inactive'], 401);
            }
            $validator = Validator::make($request->all(), [
                'lead_id' => 'required|exists:leads,id',
                'checkin_latitude' => 'required',
                'checkin_longitude' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }
            $distance = '';

            // if (!empty($request['checkin_latitude']) && !empty($request['checkin_longitude'])) {
            //     $distance = distance($request['checkin_latitude'], $request['checkin_longitude'], $request['lead_id']);
            // }

            $request['checkin_address'] = getLatLongToAddress($request['checkin_latitude'], $request['checkin_longitude']);

            if ($checkin_id = $this->checkin->insertGetId([
                'active' => 'Y',
                'lead_id' => isset($request['lead_id']) ? $request['lead_id'] : null,
                'user_id' => $user->id,
                'checkin_date' => getcurentDate(),
                'checkin_time' => getcurentTime(),
                'checkin_latitude' => isset($request['checkin_latitude']) ? $request['checkin_latitude'] : '',
                'checkin_longitude' => isset($request['checkin_longitude']) ? $request['checkin_longitude'] : '',
                'checkin_address' => isset($request['checkin_address']) ? $request['checkin_address'] : '',
                'distance' => $distance
            ])) {
                return response()->json(['status' => 'success', 'message' => 'Check In successfully', 'checkin_id' => $checkin_id], $this->successStatus);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in Check In'], $this->badrequest);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function submitCheckout(Request $request)
    {
        try {

            $user = $request->user();
            if ($user->active == 'N') {
                return response()->json(['status' => 'error', 'message' =>  'User Inactive'], 401);
            }
            $validator = Validator::make($request->all(), [
                'checkin_id' => 'required|exists:lead_check_in,id',
                'checkout_latitude' => 'required',
                'checkout_longitude' => 'required',
                'description' => 'required|string|max:1540',
                'lead_id' => 'required|exists:leads,id',
                'visit_type_id' => 'nullable|exists:visit_types,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' =>  $validator->errors()], $this->badrequest);
            }

            $request['checkout_address'] = getLatLongToAddress($request['checkout_latitude'], $request['checkout_longitude']);
            $check_in = LeadCheckIn::where('id', $request['checkin_id'])->first();
            if ($this->checkin->where('id', '=', $request['checkin_id'])->update([
                'checkout_date' => getcurentDate(),
                'checkout_time' => getcurentTime(),
                'checkout_latitude' => !empty($request['checkout_latitude']) ? $request['checkout_latitude'] : '',
                'checkout_longitude' => !empty($request['checkout_longitude']) ? $request['checkout_longitude'] : '',
                'checkout_address' => !empty($request['checkout_address']) ? $request['checkout_address'] : '',
                'checkout_note' => !empty($request['description']) ? $request['description'] : '',
                'time_interval' => gmdate("H:i:s", strtotime(getcurentDateTime()) - strtotime($check_in->checkin_date . ' ' . $check_in->checkin_time))
            ])) {
                LeadNote::create([
                    'note' => $request['description'],
                    'lead_id' => $check_in->lead_id,
                    'created_by' => $request->user()->id
                ]);
                // VisitReport::insertGetId([
                //     'checkin_id' => isset($request['checkin_id']) ? $request['checkin_id'] : null,
                //     'user_id' => $user->id,
                //     'lead_id' => isset($request['lead_id']) ? $request['lead_id'] : null,
                //     'visit_type_id' => isset($request['visit_type_id']) ? $request['visit_type_id'] : null,
                //     'description' => isset($request['description']) ? $request['description'] : '',
                //     'visit_image' => '',
                //     'created_by' => $user->id,
                //     'next_visit' => isset($request['next_visit']) ? date('Y-m-d H:i:s', strtotime($request['next_visit'])) : null,
                //     'created_at' => getcurentDateTime()
                // ]);
                // $checkDraft = CheckInDraft::where('checkin_id', $request->checkin_id)->first();
                // if($checkDraft){
                //     $checkDraft->delete();
                // }
                return response()->json(['status' => 'success', 'message' => 'Check Out successfully'], $this->successStatus);
            }
            return response()->json(['status' => 'error', 'message' => 'Please submit report then checkout'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $this->internalError);
        }
    }

    public function getLeadTasks(Request $request)
    {
        try {
            if (!$request->user()->hasRole('superadmin')) {
                $lead_ids = Lead::where('assign_to', $request->user()->id)
                    ->orWhere('created_by', $request->user()->id);

                if ($request->input('search') != "") {
                    $search = $request->input('search');
                    $lead_ids = Lead::where(function ($query) use ($search) {
                        $query->where('company_name', 'like', "%{$search}%")
                            ->orWhereHas('contacts', function ($subQuery) use ($search) {
                                $subQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone_number', 'like', "%{$search}%");
                            });
                    });
                }
                $lead_ids = $lead_ids->pluck('id');

                $tasks = LeadTask::with('lead:id,company_name', 'assignUser:id,name')
                    ->whereIn('lead_id', $lead_ids);
                if ($request->user_id && !empty($request->user_id)) {
                    $tasks->where('assigned_to', $request->user_id);
                }
                if ($request->start_date && !empty($request->start_date) && $request->end_date && !empty($request->end_date)) {
                    $tasks->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date]);
                }
                if ($request->status_id && !empty($request->status_id)) {
                    $tasks->where('status', $request->status_id);
                }
                $tasks = $tasks->latest()->paginate($request->pageSize ?? 30);
            } else {
                $lead_ids = [];
                if ($request->input('search') != "") {
                    $search = $request->input('search');
                    $lead_ids = Lead::where(function ($query) use ($search) {
                        $query->where('company_name', 'like', "%{$search}%")
                            ->orWhereHas('contacts', function ($subQuery) use ($search) {
                                $subQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone_number', 'like', "%{$search}%");
                            });
                    })->pluck('id');
                }
                $tasks = LeadTask::with('lead:id,company_name', 'assignUser:id,name');
                if (isset($lead_ids) && count($lead_ids) > 0) {
                    $tasks->whereIn('lead_id', $lead_ids);
                }
                if ($request->user_id && !empty($request->user_id)) {
                    $tasks->where('assigned_to', $request->user_id);
                }
                if ($request->start_date && !empty($request->start_date) && $request->end_date && !empty($request->end_date)) {
                    $tasks->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date]);
                }
                if ($request->status_id && !empty($request->status_id)) {
                    $tasks->where('status', $request->status_id);
                }
                $tasks = $tasks->latest()
                    ->paginate($request->pageSize ?? 30);
            }

            $tasks->each(function ($task) {
                $task->date = date('d-m-Y', strtotime($task->date));
                $task->time = date('h:i A', strtotime($task->time)); // lowercase 'h' for 12-hour format
                $task->created_at_formatted = date('d-m-Y', strtotime($task->created_at));
                $task->contact = LeadContact::where('lead_id', $task->lead_id)->first();
                $task->status = ucwords(str_replace('_', ' ', $task->status));
            });
            $notification_count = LeadNotification::where(['user_id' => $request->user()->id, 'read' => 0])->count();
            return response()->json([
                'status' => 'success',
                'data' => $tasks->items(),
                'notification_count' => $notification_count,
                'message' => 'Tasks retrieved successfully'
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $this->internalError);
        }
    }

    public function getOtherTasks(Request $request)
    {
        try {
            if (!$request->user()->hasRole('superadmin')) {
                $all_task_ids = TaskAssignment::where('user_id', $request->user()->id)->pluck('task_id');
                $other_tasks = Tasks::with('users:id,name', 'task_department', 'task_priority', 'lead:id,company_name', 'project:id,name', 'customers:id,name')->where(function ($q) use ($request, $all_task_ids) {
                    $q->where('user_id', $request->user()->id)
                        ->orWhereIn('id', $all_task_ids);
                });
            } else {
                $other_tasks = Tasks::with('users:id,name', 'task_department', 'task_priority', 'lead:id,company_name', 'project:id,name', 'customers:id,name');
            }

            if ($request->input('search') != "") {
                $search = $request->input('search');
                $other_tasks = $other_tasks->where(function ($query) use ($search) {
                    $query->where('task_type', 'like', "%{$search}%")
                        ->orWhere('descriptions', 'like', "%{$search}%");
                });
            }

            if ($request->user_id && !empty($request->user_id)) {
                $other_tasks->whereHas('assigned_users', function ($query) use ($request) {
                    $query->where('user_id', $request->user_id);
                });
                // $task_ids = TaskAssignment::where('user_id', $request->user_id)->pluck('task_id');
                // $other_tasks->whereIn('id', $task_ids);
            }
            if ($request->start_date && !empty($request->start_date) && $request->end_date && !empty($request->end_date)) {
                $other_tasks->whereBetween(DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date]);
            }
            if ($request->status_id && !empty($request->status_id)) {
                $other_tasks->where('status', $request->status_id);
            }
            $other_tasks = $other_tasks->latest()->paginate($request->pageSize ?? 30);

            $other_tasks->each(function ($task) {
                $task->due_datetime = date('d M Y | h:i A', strtotime($task->due_datetime));
                $task->completed_at = $task->completed_at ? date('d M Y | h:i A', strtotime($task->completed_at)) : '';
            });
            $notification_count = LeadNotification::where(['user_id' => $request->user()->id, 'read' => 0])->count();
            return response()->json([
                'status' => 'success',
                'data' => $other_tasks->items(),
                'notification_count' => $notification_count,
                'message' => 'Tasks retrieved successfully'
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $this->internalError);
        }
    }

    public function change_task_status(Request $request)
    {
        $validate = validator($request->all(), [
            'task_id' => 'required|exists:lead_tasks,id',
            'status' => 'required|in:pending,open,in_progress,completed',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }

        $task_id = $request->task_id;
        $lead_task = LeadTask::find($task_id);
        if ($lead_task) {
            $lead_task->update(['status' => $request->status, 'remark' => $request->remark ?? null]);
            if ($request->status == 'open') {
                $lead_task->update(['open_date' => date('Y-m-d')]);
            }
            if ($request->status == 'completed') {
                $lead_task->update(['close_date' => date('Y-m-d')]);

                $msg = '📝 Your assigned task ' . $lead_task->description .
                    ' related to lead: ' . Str::limit($lead_task->lead->company_name, 10, '...') .
                    ' has been completed.';


                SendPushNotification($lead_task->created_by, $msg, 'task');
                StoreLeadNotification($lead_task->id, 'Assigned Task', $msg, $lead_task->created_by, 'task');
            }
            return response()->json(['status' => 'success', 'message' => 'Task status updated successfully.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Task not found.']);
        }
    }

    public function change_other_task_status(Request $request)
    {
        $validate = validator($request->all(), [
            'task_id' => 'required|exists:tasks,id',
            'task_status' => 'required|in:pending,open,in_progress,completed',
            'comment' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
        }

        $task_id = $request->task_id;
        $task = Tasks::find($task_id);
        $request['task_status'] = ucfirst(str_replace('_', ' ', $request->task_status));
        $task_status = ucfirst(str_replace('_', ' ', $request->task_status)) ?? null;
        $comment = $request->comment ?? null;

        if ($task) {
            $oldStatus = $task?->task_status ?? null;
            $taskCreatedBy = $task->user_id ?? null;
            if (isset($task->task_status) && $task->task_status != $task_status) {
                if ($task_status == 'Completed') {
                    $request['completed_at'] = date('Y-m-d H:i s');
                    SendPushNotification($task->created_by, '📝 Your assigned task ' . $task->title . ' has been completed. (By-  ' . $request->user()->name . ')', 'task_management');
                } elseif ($task_status == 'Open') {
                    $request['open_datetime'] = date('Y-m-d H:i s');
                } elseif ($task_status == 'In progress') {
                    $request['inprogress_datetime'] = date('Y-m-d H:i s');
                } elseif ($task_status == 'Reopen') {
                    $request['reopen_datetime'] = date('Y-m-d H:i s');
                }
            }
            // dd($request->except(['_token', 'assigned_to']));
            if ($task->update($request->except(['_token', 'assigned_to']))) {

                if ($comment) {
                    TaskComment::create([
                        'task_id' => $task->id,
                        'comment' => $comment,
                        'user_id' => $request->user()->id
                    ]);
                }

                if ($oldStatus !== $task_status) {

                    // Store log entry
                    TaskStatusLog::create([
                        'task_id'         => $task->id,
                        'previous_status' => $oldStatus,
                        'new_status'      => $task_status,
                        'changed_by'      => $request->user()->id,
                        'comments'        => auth()->user()->name . ' marked the task as ' . $task_status . ' on ' . date('d-m-Y') . ' at ' . date('h:i A'),
                    ]);
                }
                if ($comment) {
                    // Store log entry
                    $shortComment = strlen($comment) > 50 ? substr($comment, 0, 47) . '...' : $comment;
                    TaskStatusLog::create([
                        'task_id'         => $task->id,
                        'previous_status' => $oldStatus,
                        'new_status'      => $task_status,
                        'changed_by'      => $request->user()->id,
                        'comments'        => auth()->user()->name . ' commented on ' . date('d-m-Y') . ' at ' . date('h:i A') . ' : ' . '"' . $shortComment . '"',
                    ]);
                }

                // If new files are uploaded
                if (auth()->user()->id == $taskCreatedBy) {
                    if ($request->hasFile('files')) {
                        foreach ($request->file('files') as $file) {
                            $task->addMedia($file)->toMediaCollection('task_admin_files');
                        }
                    }
                } else {
                    if ($request->hasFile('files')) {
                        foreach ($request->file('files') as $file) {
                            $task->addMedia($file)->toMediaCollection('task_assigned_user_files');
                        }
                    }
                }
                return response()->json(['status' => 'success', 'message' => 'Task status updated successfully.']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Task not found.']);
        }
    }

    public function getAllLeadNotifications(Request $request)
    {
        try {
            $user = $request->user();
            $notifications = LeadNotification::where(['user_id' => $user->id, 'read' => 0])->latest()->paginate($request->pageSize ?? 30);
            return response()->json([
                'status' => 'success',
                'data' => $notifications->items(),
                'message' => 'Notifications retrieved successfully'
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $this->internalError);
        }
    }

    public function readNotification(Request $request)
    {
        try {
            $validate = validator($request->all(), [
                'id' => 'required|exists:lead_notifications,id',
            ]);

            if ($validate->fails()) {
                return response()->json(['status' => 'error', 'message' => $validate->errors()], 400);
            }
            $user = $request->user();
            LeadNotification::where(['user_id' => $user->id, 'id' => $request->id])->update(['read' => 1]);
            return response()->json([
                'status' => 'success',
                'message' => 'Notification marked as read successfully'
            ], $this->successStatus);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], $this->internalError);
        }
    }
}
