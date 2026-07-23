<?php

namespace App\DataTables;

use App\Models\Tasks;
use App\Models\TaskAssignment;
use App\Models\TaskComment;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;



class TasksDataTable extends DataTable
{
    
    
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            // ->editColumn('created_at', function($data)
            // {
            //     return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            // })
            ->addColumn('action', function ($query) {
                  $btn = '';
                //   if(auth()->user()->can(['tasks_edit']))
                //   {

                    if(((auth()->user()->id==$query->created_by) || (auth()->user()->roles->pluck('name')->first()=='superadmin'))){   
                        $btn = $btn.'<a href="'.url("tasks/".encrypt($query->id).'/edit') .'" class="btn btn-info btn-just-icon btn-sm" title="Edit Task">
                                        <i class="material-icons">edit</i>
                                    </a>';
                    }                
                    // if($query->completed == 0)
                    // {
                    //   $btn = $btn.' <a href="javascript:void(0)" class="btn btn-sm btn-warning btn-just-icon taskcomplete" value="'.$query->id.'" title="'.trans('panel.global.complete').' '.trans('panel.task.title_singular').'">
                    //             <i class="material-icons">add_task</i>
                    //           </a>';
                    // }
                    // if($query->completed == 1 && $query->is_done == 0)
                    // {
                    //   $btn = $btn.' <a href="javascript:void(0)" class="btn btn-sm btn-success btn-just-icon taskdone" value="'.$query->id.'" title="'.trans('panel.global.done').' '.trans('panel.task.title_singular').'">
                    //             <i class="material-icons">verified</i>
                    //           </a>';
                    // }
                //   }
                //   if(auth()->user()->can(['tasks_show']))
                //   {
                    $btn = $btn.'<a href="'.url("tasks/".encrypt($query->id).'/show') .'" class="btn btn-just-icon btn-sm show" value="'.encrypt($query->id).'" title="'.trans('panel.global.show').' '.trans('panel.task.title_singular').'">
                                    <i class="material-icons">visibility</i>
                                </a>';
                //   }
                //   if(auth()->user()->can(['tasks_delete']))
                //   {
                        if((auth()->user()->roles->pluck('id')->first()==$query->created_by) || (auth()->user()->roles->pluck('name')->first()=='superadmin')){        
                            $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm taskdelete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.task.title_singular').'">
                                        <i class="material-icons">clear</i>
                                      </a>';
                        }              
                //   }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>';
            })
            ->editColumn('created_at', function($data)
            {
                return isset($data->created_at) ? date('d-m-y H:i',strtotime($data->created_at)) : '';
            }) 
            ->editColumn('descriptions', function($data)
            {
                return '<div title="'.strip_tags($data->descriptions).'">'.Str::limit($data->descriptions, 50).'</div>';
            })   
            ->addColumn('assigned_users', function($data)
            {
                $assignedUsers = TaskAssignment::with('users')->where('task_id',$data->id)->get();
                $userNames = $assignedUsers->pluck('users.name')->filter()->implode(', ');
                return $userNames;
            })   
            ->editColumn('task_department.name', function($data)
            {
                return isset($data->task_department->name) ? $data->task_department->name : '';
            })   
            ->editColumn('task_type', function($data)
            {
                return isset($data->task_type) ? $data->task_type : '';
            })  
            ->editColumn('title', function($data)
            {
                return isset($data->title) ? $data->title : '';
            }) 
            ->editColumn('task_priority.name', function($data)
            {
                return isset($data->task_priority->name) ? $data->task_priority->name : '';
            })  
            ->editColumn('task_status', function($data)
            {
                return isset($data->task_status) ? $data->task_status : '';
            })  
            ->editColumn('users.name', function($data)
            {
                return isset($data->users->name) ? $data->users->name : '';
            })
            ->editColumn('due_datetime', function($data)
            {
                return isset($data->due_datetime) ? date('d-m-y H:i',strtotime($data->due_datetime)) : '';
            }) 
            ->editColumn('completed_at', function($data)
            {
                return isset($data->completed_at) ? date('d-m-y H:i',strtotime($data->completed_at)) : '';
            }) 
            ->editColumn('open_datetime', function($data)
            {
                return isset($data->open_datetime) ? date('d-m-y H:i',strtotime($data->open_datetime)) : '';
            }) 
            ->addColumn('comment1', function ($data) {
                $comment = $data->latest_comments[0]->comment ?? '';
                return '<div title="'.e(strip_tags($comment)).'">'.Str::limit(strip_tags($comment), 50).'</div>';
            })
            ->addColumn('comment2', function ($data) {
                $comment = $data->latest_comments[1]->comment ?? '';
                return '<div title="'.e(strip_tags($comment)).'">'.Str::limit(strip_tags($comment), 50).'</div>';
            })
            ->addColumn('comment3', function ($data) {
                $comment = $data->latest_comments[2]->comment ?? '';
                return '<div title="'.e(strip_tags($comment)).'">'.Str::limit(strip_tags($comment), 50).'</div>';
            })
            ->addColumn('comment4', function ($data) {
                $comment = $data->latest_comments[3]->comment ?? '';
                return '<div title="'.e(strip_tags($comment)).'">'.Str::limit(strip_tags($comment), 50).'</div>';
            })
            ->addColumn('comment5', function ($data) {
                $comment = $data->latest_comments[4]->comment ?? '';
                return '<div title="'.e(strip_tags($comment)).'">'.Str::limit(strip_tags($comment), 50).'</div>';
            })
            ->rawColumns(['action','descriptions','assigned_users','comment1','comment2','comment3','comment4','comment5']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Task $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public function query(Tasks $model)
    // {
    //     // return $model->with('users','statusname','task_priority','task_department');
    //     $assignedTaskIds = TaskAssignment::where('user_id',auth()->user()->id)->pluck('task_id');
    //     $userids = getUsersReportingToAuth();
    //     $finalQuery= $model->with('users','statusname','task_priority','task_department','latest_comments')->whereHas('users', function($query) use($userids){
    //                             if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
    //                             {
    //                                 // $query->whereIn('id',$userids);
    //                             }
    //                         });
    //     if(auth()->user()->roles->pluck('name')->first()!='superadmin'){
    //         $finalQuery->whereIn('id',$assignedTaskIds);
    //     }

    //     return $finalQuery->latest()->newQuery();
    // }
    public function query(Tasks $model,Request $request)
    {
        $status = $request->status??null;
        $user_id = $request->user_id??null;
        $start_date = $request->start_date??null;
        $end_date = $request->end_date??null;
        $userids = getUsersReportingToAuth();
        $assignedTaskIds = TaskAssignment::whereIn('user_id', $userids)->pluck('task_id');

        $finalQuery = $model->with([
                'users',
                'statusname',
                'task_priority',
                'task_department',
                'latest_comments'
            ])
            ->whereHas('users', function($query) use ($userids) {
                if (!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('Admin')) {
                    // Optional filter for users under reporting hierarchy
                    // $query->whereIn('id', $userids);
                }
            });

        if (auth()->user()->roles->pluck('name')->first() !== 'superadmin') {
            $finalQuery->where(function ($q) use ($assignedTaskIds, $userids) {
                $q->whereIn('id', $assignedTaskIds)
                  ->orWhereIn('created_by', $userids);
            });
        }

        if($status){
            $finalQuery->where('task_status',$status);
        }
        if($user_id){
            $assignedTaskIds = TaskAssignment::where('user_id',$user_id)->pluck('task_id');
            $finalQuery->whereIn('id',$assignedTaskIds);
        }

        if ($start_date && $end_date) {
            $assignedTaskNewIds = TaskAssignment::whereBetween('created_at', [
                $start_date . " 00:00:00",
                $end_date . " 23:59:59"
            ])->pluck('task_id')->unique();

            $finalQuery->whereIn('id', $assignedTaskNewIds);
            
        }




        return $finalQuery->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('tasks-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  // ->width(60)
                  ->addClass('text-center'),
            Column::make('id'),
            Column::make('add your columns'),
            Column::make('created_at'),
            Column::make('updated_at'),
            Column::make('descriptions')
                ->title('Description')
                ->addClass('text-left')
                ->addClass('description-column')
                ->orderable(false),

        ];
    }
}
