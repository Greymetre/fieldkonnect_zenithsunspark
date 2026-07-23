<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\EveryNight;
use App\Console\Commands\AutoPunchOut;
use App\Console\Commands\AddressUpdate;
use App\Console\Commands\MoveStorageToS3;
use App\Models\User;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \App\Console\Commands\SendPendingTaskNotification::class,
    ];

  
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command(EveryNight::class, ['--force'])->dailyAt('13:00');
        // $schedule->command(EveryNight::class)->timezone('Asia/Kolkata')->dailyAt('23:00');
        // $schedule->command(AutoPunchOut::class)->timezone('Asia/Kolkata')->dailyAt('22:30');
        // $schedule->command(AddressUpdate::class)->timezone('Asia/Kolkata')->everyFourHours();
        // $schedule->command('backup:run')->monthlyOn(1, '00:00');
        // $schedule->command('update:leave-balance')->monthlyOn(1, '00:00');
        // $schedule->command('sales:send-primary')->timezone('Asia/Kolkata')->dailyAt('00:00');
        // $schedule->command('send:all-users')->timezone('Asia/Kolkata')->dailyAt('01:00');
        // $schedule->command('send:all-users-goly')->timezone('Asia/Kolkata')->dailyAt('02:00');
        // $schedule->command('send:all-users-target')->timezone('Asia/Kolkata')->dailyAt('02:15');
        // $schedule->command('send:all-branch-goly')->timezone('Asia/Kolkata')->dailyAt('02:30');
        $schedule->command('tasks:send-pending-today')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
