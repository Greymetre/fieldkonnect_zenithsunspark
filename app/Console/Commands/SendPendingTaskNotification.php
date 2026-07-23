<?php

namespace App\Console\Commands;

use App\Models\LeadTask;
use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Tasks;
use Carbon\Carbon;

class SendPendingTaskNotification extends Command
{
    protected $signature = 'tasks:send-pending-today';
    protected $description = 'Send notification for tasks with today\'s due date and still pending';


    public function handle()
    {
        $now = Carbon::now();

        $tasks = LeadTask::whereDate('date', $now->toDateString())
            ->where('status', 'pending')
            ->get();

        foreach ($tasks as $task) {
            // Combine date + time into full datetime
            $taskDateTime = Carbon::parse($task->date . ' ' . $task->time);

            // Calculate minutes remaining
            $minutesRemaining = $now->diffInMinutes($taskDateTime, false);

            // Send only if remaining time is between 10 and 9 minutes
            if ($minutesRemaining <= 10 && $minutesRemaining >= 9) {
                SendPushNotification(
                    $task->assigned_to,
                    '⏰ Reminder: The task "' . $task->description . '" for ' . $task->lead->company_name . ' is due in ' . $minutesRemaining . ' minutes.'
                );
            }
        }

        $this->info("Pending lead task notifications checked for {$tasks->count()} tasks.");

        $tasks = Tasks::whereDate('due_datetime', $now->toDateString())
            ->where('task_status', 'Pending')
            ->get();


        foreach ($tasks as $task) {
            // Combine date + time into full datetime
            $taskDateTime = Carbon::parse($task->due_datetime);

            // Calculate minutes remaining
            $minutesRemaining = $now->diffInMinutes($taskDateTime, false);
            // if($task->id == 44){
            //     dd($minutesRemaining, $taskDateTime, $task->due_datetime, $now);
            // }
            // Send only if remaining time is between 10 and 9 minutes
            if ($minutesRemaining <= 10 && $minutesRemaining >= 9) {
                if ($task->assigned_users->count() > 0) {
                    foreach ($task->assigned_users as $assigned_user) {
                        SendPushNotification(
                            $assigned_user->user_id,
                            '⏰ Reminder: The task "' . $task->title . '" is due in ' . $minutesRemaining . ' minutes.'
                        );
                    }
                }
            }
        }

        $this->info("Pending task notifications checked for {$tasks->count()} tasks.");
    }
}
