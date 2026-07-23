<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Tasks extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $table = 'tasks';

    protected $fillable = [ 'active', 'user_id', 'title', 'descriptions', 'datetime', 'reminder','open_datetime','inprogress_datetime','reopen_datetime', 'completed_at', 'completed', 'is_done', 'customer_id', 'status_id', 'created_by', 'created_at', 'updated_at','task_department_id','task_type','task_project_id','task_priority_id','lead_id','due_datetime','task_status'];
    
    public function statusname()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name');
    }
    public function customers()
    {
        return $this->belongsTo('App\Models\Customers', 'customer_id', 'id')->select('id','profile_image','active','name','mobile');
    }
    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name');
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
    public function task_department()
    {
        return $this->belongsTo('App\Models\TaskDepartment', 'task_department_id', 'id')->select('id','name');
    }
    public function task_priority()
    {
        return $this->belongsTo('App\Models\TaskPriority', 'task_priority_id', 'id')->select('id','name');
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('task_assigned_user_files'); 
        $this->addMediaCollection('task_admin_files'); 
    }
    public function latest_comments()
    {
        return $this->hasMany(TaskComment::class,'task_id','id')->latest()->limit(5);
    }
    public function lead()
    {
        return $this->belongsTo('App\Models\Lead', 'lead_id', 'id');
    }
    public function project()
    {
        return $this->belongsTo('App\Models\TaskProject', 'task_project_id', 'id');
    }

    public function assigned_users()
    {
        return $this->hasMany('App\Models\TaskAssignment', 'task_id', 'id');
    }


}
