<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'previous_status',
        'new_status',
        'changed_by',
        'comments',
    ];

    public function task()
    {
        return $this->belongsTo(Tasks::class ,'task_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by','id');
    }
}
