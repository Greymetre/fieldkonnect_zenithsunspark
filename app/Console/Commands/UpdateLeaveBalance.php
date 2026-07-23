<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UpdateLeaveBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:leave-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increment leave balance by 1.25 for all users on the 1st of each month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
        // Update leave_balance for all users
        User::query()
        ->where('active', 'Y')
        ->increment('leave_balance', 1.25);
        Log::channel('sapstock')->error("All Leave balance update on " . now());
        $this->info('Leave balance updated for all users.');
        } catch (\Exception $e) {
            Log::channel('sapstock')->error("Error in the update monthly leave balance: " . $e->getMessage());
        }
    }
}
