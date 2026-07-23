<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeleteOldUserLiveLocations extends Command
{
    protected $signature = 'locations:delete-old';
    protected $description = 'Delete user live location records older than 2 days';

    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(1);

        $deleted = DB::table('user_live_locations')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        $this->info("Deleted $deleted records older than 2 days from user_live_locations table.");
    }
}
