<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Foundation\Bus\Dispatchable;

class SyncUserPermissionsJob implements ShouldQueue
{

    public $tries = 7;

    public $timeout = 2000;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $userIds;
    protected array $permissionIds;

    public function __construct(array $userIds, array $permissionIds)
    {
        $this->userIds = $userIds;
        $this->permissionIds = $permissionIds;
    }

    public function handle(): void
    {
        collect($this->userIds)->chunk(100)->each(function ($chunkedUserIds) {
            User::whereIn('id', $chunkedUserIds)->each(function ($user) {
                $user->syncPermissions($this->permissionIds);
            });
        });
    }
}
