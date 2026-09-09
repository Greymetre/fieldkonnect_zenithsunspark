<?php

namespace App\Console\Commands;

use App\Services\LeadDataPurger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeLeadData extends Command
{
    protected $signature = 'leads:purge-all
        {--dry-run : Preview affected row counts without deleting anything}
        {--force : Permanently delete all leads and related database records}';

    protected $description = 'Preview or permanently delete all lead data, including linked tasks and CRM history';

    public function handle(LeadDataPurger $purger): int
    {
        try {
            $this->line('Database: ' . DB::connection()->getDatabaseName());
            $this->table(['Table', 'Rows to delete'], collect($purger->preview())
                ->map(fn ($count, $table) => [$table, $count])->values()->all());

            if ($this->option('dry-run') || !$this->option('force')) {
                $this->info('Preview only. No data deleted. Use --force to execute.');
                return self::SUCCESS;
            }

            $deleted = $purger->purge();
            $this->table(['Table', 'Deleted rows'], collect($deleted)
                ->map(fn ($count, $table) => [$table, $count])->values()->all());
            $this->info('All leads and related database records deleted.');
            $this->line('Uploaded files on disk/S3 are retained; their lead media database records are removed.');
            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error('Lead purge failed: ' . $exception->getMessage());
            return self::FAILURE;
        }
    }
}
