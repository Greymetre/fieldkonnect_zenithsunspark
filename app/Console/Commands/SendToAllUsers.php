<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class SendToAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:all-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a message to all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertUsers";

        $users = User::all();

        if ($users->isEmpty()) {
            $this->info('No users found.');
            return;
        }

        $users->chunk(200)->each(function ($chunk) use ($url) {
            $payload = ['users' => $chunk->toArray()];
            $response = Http::timeout(240)->post($url, $payload);

            if ($response->successful()) {
                $this->info(count($chunk) . ' records sent successfully.');
            } else {
                $this->error('Failed to send sales data: ' . $response->body());
            }
        });

        $this->info('Message sent to all users.');
    }
}
