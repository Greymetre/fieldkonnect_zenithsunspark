<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

use DataTables;
use Auth;

use App\Exports\ExcelExport;
use Excel;

use App\Models\Lead;
use Illuminate\Support\Str;

use App\Models\LeadTask;

class LeadTasksController extends Controller
{



    public function index(Request $request)
    {
        abort_if(Gate::denies('lead_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('lead-tasks.index');
    }

    public function getLeadTasks(Request $request)
    {
        $lead_tasks = LeadTask::with(['lead', 'assignUser']);
        if (!auth()->user()->hasRole('superadmin')) {
            $user_ids = getUsersReportingToAuth();
            $lead_ids = Lead::where('assign_to', $user_ids)->pluck('id');
            $lead_tasks->where('assigned_to', $user_ids);
        }
        if ($request->search && !empty($request->search)) {
            $lead_tasks->where(function ($query) use ($request) {
                $query->where('description', 'like', '%' . $request->search . '%')
                    ->orWhere('priority', 'like', '%' . $request->search . '%')
                    ->orWhereHas('lead', function ($subQuery) use ($request) {
                        $subQuery->where('company_name', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('assignUser', function ($subQuery) use ($request) {
                        $subQuery->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }
        if ($request->status && !empty($request->status) && $request->status != '') {
            if ($request->status == 'overdue') {
                $lead_tasks->where('lead_tasks.status', 'pending')
                    ->where('lead_tasks.date', '<', now()->toDateString());
            } else {
                $lead_tasks->where('lead_tasks.status', $request->status);
            }
        }
        if($request->start_date && $request->end_date && !empty($request->start_date) && !empty($request->end_date)){
            $lead_tasks->whereBetween('lead_tasks.date', [$request->start_date, $request->end_date]);
        }
        $lead_tasks = $lead_tasks->orderByRaw("
                CASE 
                    WHEN lead_tasks.status = 'pending' THEN 0
                    WHEN lead_tasks.status = 'open' THEN 1
                    WHEN lead_tasks.status = 'in_progress' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')->orderBy('lead_tasks.date', 'asc')->orderBy('lead_tasks.time', 'asc')->select(\DB::raw(with(new LeadTask)->getTable() . '.*'))->groupBy('id');
        return DataTables::of($lead_tasks)
            ->editColumn('lead.company_name', function ($lead_task) {
                return $lead_task->lead ? '<a href="' . route('leads.show', $lead_task->lead->id) . '">' . Str::limit($lead_task->lead->company_name, 25, '...') . '</a>' : '';
            })
            ->editColumn('description', function ($lead_task) {
                return $lead_task->description;
            })
            ->editColumn('date', function ($lead_task) {
                return date("M d,Y", strtotime($lead_task->date)) . ' ' . date("h:i A", strtotime($lead_task->time));
            })
            ->addColumn('action', function ($lead_task) {
                return "";
            })
            ->addColumn('assignUser.name', function ($lead_task) {
                return $lead_task->assignUser->name ?? '';
            })
            ->addColumn('checkbox', function ($lead_task) {
                $lead_task_id = "'" . $lead_task->id . "'";
                return '<input type="checkbox" class="lead_task-checkbox checkbox_cls" value="' . $lead_task->id . '" name="lead_task_ids[]" onclick="checkboxDelete(' . $lead_task_id . ')">';
            })
            ->editColumn('status', function ($lead_task) {
                if ($lead_task->status == 'pending') {
                    return '<button title="Change Status" class="btn btn-sm btn-danger change_status" data-status="pending" data-id="' . $lead_task->id . '">Pending</button>';
                } else if ($lead_task->status == 'open') {
                    return '<button title="Change Status" class="btn btn-sm btn-info change_status" data-status="open" data-id="' . $lead_task->id . '">Open</button>'; //<span class="badge badge-info">Open</span>';
                } else if ($lead_task->status == 'completed') {
                    return '<span class="badge badge-success">Completed</span>';
                } else if ($lead_task->status == 'in_progress') {
                    return '<button title="Change Status" class="btn btn-sm btn-warning change_status" data-status="in_progress" data-id="' . $lead_task->id . '">In Progress</button>';
                }
            })
            ->editColumn('priority', function ($lead_task) {
                if ($lead_task->priority == 'high') {
                    return '<span class="badge badge-danger">High</span>';
                } else if ($lead_task->priority == 'medium') {
                    return '<span class="badge badge-warning">Medium</span>';
                } else if ($lead_task->priority == 'low') {
                    return '<span class="badge badge-info">Low</span>';
                }
            })
            ->with('records_filtered_count', $lead_tasks->get()->count())
            ->rawColumns(['action', 'checkbox', 'status', 'priority', 'lead.company_name', 'assignUser.name'])
            ->make(true);
    }

    function exportTasks(Request $request)
    {
        $filename = 'tasks.xlsx';

        $results_per_page = 8000;
        $page_number = intval($request->input('page_number'));
        $page_result = ($page_number - 1) * $results_per_page;

        $lead_tasks = LeadTask::with(['lead']);
        if (!auth()->user()->hasRole('superadmin')) {
            $user_ids = getUsersReportingToAuth();
            $lead_ids = Lead::where('assign_to', $user_ids)->pluck('id');
            $lead_tasks->where('assigned_to', $user_ids);
        }
        if ($request->status && !empty($request->status) && $request->status != '') {
            if ($request->status == 'overdue') {
                $lead_tasks->where('lead_tasks.status', 'pending')
                    ->where('lead_tasks.date', '<', now()->toDateString());
            } else {
                $lead_tasks->where('lead_tasks.status', $request->status);
            }
        }
        $lead_tasks = $lead_tasks = $lead_tasks->orderByRaw("
                CASE 
                    WHEN lead_tasks.status = 'pending' THEN 0
                    WHEN lead_tasks.status = 'open' THEN 1
                    WHEN lead_tasks.status = 'in_progress' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')->orderBy('lead_tasks.date', 'asc')->orderBy('lead_tasks.time', 'asc')->get();
        $data = $lead_tasks->map(function ($item, $key) {

            return [
                $item->id,
                $item->lead->company_name ?? '',
                $item->description,
                $item->priority,
                ucwords(str_replace('_', ' ', $item->status)),
                date("M d,Y", strtotime($item->created_at)),
                date("M d,Y", strtotime($item->date)),
                $item->close_date ? date("M d,Y", strtotime($item->close_date)) : '',
                $item->remark,
                $item->createdby->name ?? '',
                $item->assignUser->name ?? '',
            ];
        })->toArray();

        $export = new ExcelExport([
            'Id',
            'Name',
            'Description',
            'Priority',
            'Task Status',
            'Open Date',
            'Due Date',
            'Close Date',
            'Remarks',
            'Created By',
            'Assign to',
        ], $data);

        return Excel::download($export, $filename);
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
            'lead_id' => 'required',
            'assigned_to' => 'required',
            'description' => 'required',
            'date' => 'required',
            'priority' => 'required',
            'status' => 'required',
        ];

        $request->validate($rules);
        $created_by = Auth::id();
        $task_id = $request->task_id;
        $lead_task = LeadTask::where(['id' => $task_id])->first();
        if (!$request->status && empty($request->status)) {
            $request->status = 'open';
        }
        if ($lead_task) {
            if ($request->status == 'completed' && $lead_task->status != 'completed') {
                $lead_task->update(['close_date' => date('Y-m-d')]);

                $msg = '📝 Your assigned task ' . $lead_task->description .
                    ' related to lead: ' . Str::limit($lead_task->lead->company_name, 10, '...') .
                    ' has been completed.';


                SendPushNotification($lead_task->created_by, $msg, 'task');
                StoreLeadNotification($lead_task->id, 'Assigned Task', $msg, $lead_task->created_by, 'task');
            }

            $lead_task->update(['assigned_to' => $request->assigned_to, 'lead_id' => $request->lead_id, 'created_by' => $created_by, 'description' => $request->description, 'date' => $request->date, 'time' => $request->time, 'priority' => $request->priority, 'status' => $request->status]);
            $request->session()->flash('message_success', __('Lead Task Update successfully.'));
        } else {
            $lead_task = LeadTask::create(['assigned_to' => $request->assigned_to, 'lead_id' => $request->lead_id, 'created_by' => $created_by, 'description' => $request->description, 'date' => $request->date, 'time' => $request->time, 'priority' => $request->priority, 'status' => $request->status]);
            $request->session()->flash('message_success', __('Lead Task Added successfully.'));

            SendPushNotification($request->assigned_to, '📝 A new task has been assigned to you.', 'task');
            StoreLeadNotification($lead_task->id, 'Assigned Task', '📝 A new task has been assigned to you.', $request->assigned_to, 'task');
        }
        if ($request->status == 'open') {
            $lead_task->update(['open_date' => date('Y-m-d')]);
        }
        if ($request->status == 'completed') {
            $lead_task->update(['close_date' => date('Y-m-d')]);
        }

        return redirect()->back();
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, LeadTask $leadTask)
    // {
    //     $rules = [
    //         'lead_id'=>'required',
    //         'assigned_to'=>'required',
    //         'description'=>'required',
    //         'date'=>'required',
    //         'time'=>'required',

    //     ];

    //     $request->validate($rules);
    //     $data = $request->all();
    //     $created_by = Auth::id(); 
    //     $lead_task = LeadTask::where(['id'=>$leadTask->id])->first();
    //     if($lead_task){
    //         $lead_task->update(['assigned_to'=>$request->assigned_to,'lead_id'=>$request->lead_id,'created_by'=>$created_by,'description'=>$request->description,'date'=>$request->date,'time'=>$request->time]);
    //          $request->session()->flash('message_success',__('Lead Task Added successfully.'));
    //     }else{
    //          $request->session()->flash('message_info',__('something went wrong.'));

    //     }

    //     return redirect()->back();
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, LeadTask $leadTask)
    {
        $leadTask->delete();
        $request->session()->flash('message_success', __('Lead Task deleted successfully.'));
        return redirect()->back();
    }

    public function checkboxAction(Request $request)
    {
        $lead_ids = $request->lead_ids;
        $lead_id_arr = explode(",", $lead_ids);
        if (count($lead_id_arr) > 0) {
            LeadTask::whereIn('id', $lead_id_arr)->delete();
            $request->session()->flash('message_success', __('Lead Task deleted successfully.'));
            return redirect()->back();
        }
    }

    public function change_status(Request $request)
    {
        $task_id = $request->task_id;
        $lead_task = LeadTask::find($task_id);
        if ($lead_task) {
            $lead_task->update(['status' => $request->status, 'remark' => $request->remark]);
            $request->session()->flash('message_success', __('Lead Task status changed successfully.'));
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
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }
}
