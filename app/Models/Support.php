<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $table = 'supports';

    protected $fillable = [ 'active', 'subject', 'description', 'full_name', 'department_id', 'user_id', 'status_id', 'customer_id', 'priority', 'assigned_to', 'isoverdue', 'reopened', 'is_transferred','assigned_at' ,'transferred_at', 'reopened_at', 'duedate', 'closed_at', 'last_message_at', 'last_response_at', 'lock_at', 'deleted_at', 'created_at', 'updated_at' ];
    
    public function statusname()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name');
    }
    public function priorities()
    {
        return $this->belongsTo('App\Models\Priority', 'priority', 'id')->select('id','priority_name');
    }

    public function associatedUsers()
    {
        return $this->hasMany('App\Models\SupportAssign','support_id','id')->select('support_id','user_id');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Notes', 'support_id', 'id')->select('id','support_id', 'note','status_id','is_replay','created_at')->latest();
    }
    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')->select('id','name','email');
    }
}
