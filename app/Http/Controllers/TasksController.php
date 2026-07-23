<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Models\Customers;
use App\Models\User;
use App\Models\TaskDepartment;
use App\Models\TaskProject;
use App\Models\TaskPriority;
use App\Models\Lead;
use App\Models\TaskAssignment;
use App\Models\TaskComment;
use App\Models\TaskStatusLog;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;
use DataTables;
use Validator;
use Gate;
use App\DataTables\TasksDataTable;
use App\Imports\TasksImport;
use App\Exports\TasksExport;
use App\Exports\TasksTemplate;
use App\Models\Media as ModelsMedia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TasksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->tasks = new Tasks();
    }

    public function index(TasksDataTable $dataTable)
    {
        //abort_if(Gate::denies('tasks_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::where(function ($query) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                // $query->whereIn('id',$userids);
            }
            $query->where('active', 'Y');
        })->select('id', 'name')->get();
        $statuses = ['Pending', 'Open', 'In progress', 'Completed', 'Reopen'];

        return $dataTable->render('tasks.index', [
            'users' => $users,
            'statuses' => $statuses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //abort_if(Gate::denies('tasks_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();

        $users = User::where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                // $query->whereIn('id',$userids);
            }
            $query->where('active', 'Y');
        })->select('id', 'name')->get();

        $customers = Customers::where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                // $query->whereIn('executive_id',$userids);
            }
        })
            ->select('id', 'name', 'mobile')
            ->get();
        $departments = TaskDepartment::select('id', 'name')->get();
        $projects = TaskProject::select('id', 'name')->get();
        $priorities = TaskPriority::select('id', 'name')->get();
        $leads = Lead::select('id', 'company_name')->get();



        return view('tasks.create', compact('users', 'customers', 'departments', 'projects', 'priorities', 'leads'))->with('tasks', $this->tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        $assignedTos  = $request->assigned_to ?? [];
        $userId = Auth::user()->id;
        //abort_if(Gate::denies('tasks_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request['active'] = 'Y';
        $request['created_by'] = $userId;
        $request['user_id'] = $userId;
        $taskLogs = [];
        if ($task = Tasks::create($request->except(['_token']))) {
            // Store log entry
            $taskLogs[] = [
                'task_id'         => $task->id,
                'new_status'      => 'Pending',
                'changed_by'      => $userId,
                'comments'        =>  'Task #T' . $task->id . ' was created by ' . auth()->user()->name . ' on ' . date('d-m-Y') . ' at ' . date('h:i A'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (count($assignedTos)) {
                foreach ($assignedTos as $assignedUserId) {
                    TaskAssignment::create([
                        'task_id' => $task->id,
                        'user_id' => $assignedUserId,
                    ]);
                    $user = User::find($assignedUserId);
                    $taskLogs[] = [
                        'task_id'         => $task->id,
                        'new_status'      => 'Pending',
                        'changed_by'      => $userId,
                        'comments'        =>  'Task #T' . $task->id . ' was assigned to ' . $user->name . ' on ' . date('d-m-Y') . ' at ' . date('h:i A'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    SendPushNotification($assignedUserId, '📝 You have been assigned new tasks "' . $task->title . '".', 'task_management');
                    StoreLeadNotification($task->id, 'Assigned Task', '📝 A new task has been assigned to you.', $user->id, 'task_management');
                }
            }
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $task->addMedia($file)->toMediaCollection('task_admin_files');
                }
            }



            TaskStatusLog::insert($taskLogs);

            // $toemail = User::where('id',$request['user_id'])->pluck('email')->first();
            // Mail::send('emails.tasks.assign', ['task' => $task ], function ($message) use($task, $toemail) {
            //       $message->to($toemail)->subject($task['task_title']);
            // });

            return Redirect::to('tasks')->with('message_success', 'Tasks Store Successfully');
        }


        return redirect()->back()->with('message_danger', 'Error in Tasks Store')->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     //abort_if(Gate::denies('tasks_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    //     $id = decrypt($id);
    //     $task = Tasks::find($id);
    //     $task['user_name'] = isset($task['users']['name']) ? $task['users']['name'] :'';
    //     return response()->json($task);
    //     //return view('tasks.show')->with('tasks',$task);
    // }

    public function show($id)
    {
        //abort_if(Gate::denies('tasks_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $id = decrypt($id);
        $task = Tasks::find($id);

        $users = User::where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                // $query->whereIn('id',$userids);
            }
            $query->where('active', 'Y');
        })->select('id', 'name')->get();
        $customers = Customers::where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                // $query->whereIn('executive_id',$userids);
            }
        })
            ->select('id', 'name', 'mobile')
            ->get();
        $departments = TaskDepartment::select('id', 'name')->get();
        $projects = TaskProject::select('id', 'name')->get();
        $priorities = TaskPriority::select('id', 'name')->get();
        $leads = Lead::select('id', 'company_name')->get();
        $assignedUserIds = TaskAssignment::where('task_id', $id)->pluck('user_id')->toArray();
        $roleName = auth()->user()->roles->pluck('name')->first();
        $taskStatuses = ['Pending', 'Open', 'In progress', 'Completed'];
        $isRestricted = true;
        if ($roleName === 'superadmin') {
            $isRestricted = true;
            $taskStatuses[] = 'Reopen';
        } else {
            $isRestricted = true;
            if ($task->task_status == 'Pending') {
                $taskStatuses = ['Pending', 'Open'];
            } elseif ($task->task_status == 'Open') {
                $taskStatuses = ['Open', 'In progress'];
            } elseif ($task->task_status == 'In progress') {
                $taskStatuses = ['In progress', 'Completed'];
            } elseif ($task->task_status == 'Reopen') {
                $taskStatuses = ['Pending', 'Open'];
            }
        }
        $taskStatusLogs = TaskStatusLog::with('task', 'user')->where('task_id', $task->id)->get();
        $taskComments = TaskComment::where('task_id', $id)->get();
        return view('tasks.show', compact('task', 'users', 'customers', 'departments', 'projects', 'priorities', 'leads', 'assignedUserIds', 'isRestricted', 'taskStatuses', 'taskComments', 'taskStatusLogs'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //abort_if(Gate::denies('tasks_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $userids = getUsersReportingToAuth();
        $id = decrypt($id);
        $task = Tasks::find($id);

        if (!((auth()->user()->id == $task->created_by) || (auth()->user()->roles->pluck('name')->first() == 'superadmin'))) {
            return Redirect::back()->with('message_error', 'You are not allowed to access this page');
        }


        $users = User::where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                // $query->whereIn('id',$userids);
            }
            $query->where('active', 'Y');
        })->select('id', 'name')->get();
        $customers = Customers::where('active', '=', 'Y')->where(function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                // $query->whereIn('executive_id',$userids);
            }
        })
            ->select('id', 'name', 'mobile')
            ->get();
        $departments = TaskDepartment::select('id', 'name')->get();
        $projects = TaskProject::select('id', 'name')->get();
        $priorities = TaskPriority::select('id', 'name')->get();
        $leads = Lead::select('id', 'company_name')->get();
        $assignedUserIds = TaskAssignment::where('task_id', $id)->pluck('user_id')->toArray();
        $roleName = auth()->user()->roles->pluck('name')->first();
        $isRestricted = true;
        if ($roleName === 'superadmin') {
            $isRestricted = false;
        } else {
            $isRestricted = true;
        }
        $taskStatuses = ['Pending', 'Open', 'In progress', 'Completed', 'Reopen'];
        $taskComments = TaskComment::where('task_id', $id)->get();
        return view('tasks.edit', compact('task', 'users', 'customers', 'departments', 'projects', 'priorities', 'leads', 'assignedUserIds', 'isRestricted', 'taskStatuses', 'taskComments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, $id)
    {
        $assignedTos = $request->assigned_to ?? [];
        $taskType = $request->task_type ?? null;
        $comment = $request->comment ?? null;
        $userId = Auth::user()->id;
        $task_status = $request->task_status ?? null;

        $task = Tasks::findOrFail($id);
        $taskCreatedBy = $task->user_id ?? null;
        $oldStatus = $task->task_status ?? null;
        // Optional: restrict access
        // abort_if(Gate::denies('tasks_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request['updated_by'] = $userId;

        if ($taskType == 'adhoc') {
            $request['customer_id'] = null;
            $request['project_id'] = null;
            $request['lead_id'] = null;
        } elseif ($taskType == 'customer') {
            $request['project_id'] = null;
            $request['lead_id'] = null;
        } elseif ($taskType == 'project') {
            $request['customer_id'] = null;
            $request['lead_id'] = null;
        } elseif ($taskType == 'lead') {
            $request['customer_id'] = null;
            $request['project_id'] = null;
        }
        // when task status is update
        if (isset($task->task_status) && $task->task_status != $task_status) {
            // if status is completed
            if ($task_status == 'Completed') {
                $request['completed_at'] = date('Y-m-d H:i s');
                SendPushNotification($task->created_by, '📝 Your assigned task '. $task->title .' has been completed. (By-  '. $request->user()->name .')', 'task_management');
                StoreLeadNotification($task->id, 'Completed Task', '📝 Your assigned task '. $task->title .' has been completed. (By-  '. $request->user()->name .')', $task->created_by, 'task_management');
            } elseif ($task_status == 'Open') {
                $request['open_datetime'] = date('Y-m-d H:i s');
            } elseif ($task_status == 'In progress') {
                $request['inprogress_datetime'] = date('Y-m-d H:i s');
            } elseif ($task_status == 'Reopen') {
                $request['reopen_datetime'] = date('Y-m-d H:i s');
            }
        }
        if ($task->update($request->except(['_token', 'assigned_to']))) {

            // Delete existing assignments and reassign
            // TaskAssignment::where('task_id', $task->id)->delete();

            if (count($assignedTos)) {
                $taskLogs = [];
                foreach ($assignedTos as $assignedUserId) {
                    $user = User::find($assignedUserId);
                    $isExist = TaskAssignment::where(['task_id' => $task->id, 'user_id' => $assignedUserId])->exists();
                    if ($isExist) {
                        continue;
                    }
                    TaskAssignment::create([
                        'task_id' => $task->id,
                        'user_id' => $assignedUserId,
                    ]);

                    $taskLogs[] = [
                        'task_id'         => $task->id,
                        'changed_by'      => $userId,
                        'comments'        =>  'Task #T' . $task->id . ' was assigned to ' . $user->name . ' on ' . date('d-m-Y') . ' at ' . date('h:i A'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $deletedAssignments = TaskAssignment::where(['task_id' => $task->id])->whereNotIn('user_id', $assignedTos)->get();
                if (count($deletedAssignments)) {
                    foreach ($deletedAssignments as $deletedAssignment) {
                        $userDeleted = User::find($deletedAssignment->user_id);
                        $isDeleted = TaskAssignment::where('id', $deletedAssignment->id)->delete();
                        if ($isDeleted) {
                            $taskLogs[] = [
                                'task_id'         => $task->id,
                                'changed_by'      => $userId,
                                'comments'        =>  'Task #T' . $task->id . ' was removed from ' . $userDeleted->name . ' on ' . date('d-m-Y') . ' at ' . date('h:i A'),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }

                if (count($taskLogs)) {
                    TaskStatusLog::insert($taskLogs);
                }
            }
            if ($comment) {
                TaskComment::create([
                    'task_id' => $task->id,
                    'comment' => $comment,
                    'user_id' => auth()->user()->id
                ]);
            }

            // Only log if status is actually changed
            if ($oldStatus !== $task_status) {

                // Store log entry
                TaskStatusLog::create([
                    'task_id'         => $task->id,
                    'previous_status' => $oldStatus,
                    'new_status'      => $task_status,
                    'changed_by'      => $userId,
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
                    'changed_by'      => $userId,
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


            return Redirect::to('tasks')->with('message_success', 'Task Updated Successfully');
        }

        return Redirect::back()->with('message_error', 'Failed to update task');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //abort_if(Gate::denies('tasks_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // TaskUsers::where('task_id',$id)->delete();
        TaskAssignment::where('task_id', $id)->delete();
        TaskComment::where('task_id', $id)->delete();
        TaskStatusLog::where('task_id', $id)->delete();
        ModelsMedia::where('model_id', $id)->where('model_type', 'App\Models\Tasks')->delete();

        if (Tasks::where('id', $id)->delete()) {
            return response()->json(['status' => 'success', 'message' => 'Task deleted successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Task Delete!']);
    }

    public function active(Request $request)
    {
        if (Tasks::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y'])) {
            $message = ($request['active'] == 'Y') ? 'Inactive' : 'Active';
            return response()->json(['status' => 'success', 'message' => 'Task ' . $message . ' Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }

    public function completed(Request $request)
    {
        if (Tasks::where('id', $request['id'])->update(['completed' => '1', 'completed_at' => getcurentDateTime()])) {
            $task = Tasks::find($request['id']);
            SendPushNotification($task->created_by, '📝 Your assigned task '. $task->title .' has been completed. (By-  '. $request->user()->name .')', 'task_management');
            StoreLeadNotification($task->id, 'Completed Task', '📝 Your assigned task '. $task->title .' has been completed. (By-  '. $request->user()->name .')', $task->created_by, 'task_management');
            return response()->json(['status' => 'success', 'message' => 'Task Completed  Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }
    public function done(Request $request)
    {
        if (Tasks::where('id', $request['id'])->update(['is_done' => '1'])) {
            return response()->json(['status' => 'success', 'message' => 'Task Done  Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }
    public function reopen(Request $request)
    {
        if (Tasks::where('id', $request['id'])->update(['is_done' => '0', 'done_by' => null])) {
            return response()->json(['status' => 'success', 'message' => 'Task Done  Successfully!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
    }

    public function upload(Request $request)
    {
        abort_if(Gate::denies('tasks_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new TasksImport, request()->file('import_file'));
        return back();
    }
    public function download()
    {
        abort_if(Gate::denies('tasks_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TasksExport, 'tasks.xlsx');
    }
    public function template()
    {
        abort_if(Gate::denies('tasks_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TasksTemplate, 'tasks.xlsx');
    }

    public function tasksInfo(Request $request)
    {
        if ($request->ajax()) {
            $data = Tasks::with('priorities', 'statusname')
                ->orWhere(function ($query) use ($request) {
                    $query->where('user_id', $request['user_id'])
                        ->where(function ($query) use ($request) {
                            if ($request['due_at'] == 'Today') {
                                $query->whereDate('datetime', date('Y-m-d'));
                            }
                            if ($request['due_at'] == 'Week') {
                                $query->whereDate('datetime', '>', date('Y-m-d'));

                                $query->whereDate('datetime', '<=', Carbon::now()->endOfWeek()->format('Y-m-d'));
                            }
                            if ($request['due_at'] == 'overdue') {
                                $query->whereDate('datetime', '>', date('Y-m-d'));
                                $query->where('completed', 0);
                            }
                            if ($request['due_at'] == 'Completed') {
                                $query->where('completed', 1);
                            }
                        });
                })
                ->latest();
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('start_date', function ($data) {
                    return showdateformat($data->start_date) . ' ' . $data->start_day . ' ' . showdateformat($data->start_time);
                })
                ->editColumn('datetime', function ($data) {
                    return showdateformat($data->datetime) . ' ' . $data->due_day . ' ' . showdateformat($data->due_time);
                })
                ->editColumn('status_id', function ($data) {
                    $status = '';
                    if ($data['is_done'] == 1) {
                        $status = 'Done';
                    } elseif ($data['completed'] == 1) {
                        $status = 'Completed';
                    } else {
                        $status = 'Open';
                    }
                    return $status;
                })
                ->make(true);
        }
    }

    public function deleteMedia(Media $media, Request $request)
    {
        try {
            $media->delete();
            return response()->json(['status' => true, 'message' => 'File deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error deleting file.']);
        }
    }
}
