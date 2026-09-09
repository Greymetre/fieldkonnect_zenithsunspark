<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadCheckIn;
use App\Models\LeadContact;
use App\Models\LeadLog;
use App\Models\LeadNote;
use App\Models\LeadOpportunity;
use App\Models\LeadTask;
use App\Models\Tasks;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LeadDataPurger
{
    /** Queries are ordered child-first and use subqueries to avoid loading IDs into memory. */
    public function queries(): array
    {
        $queries = [];
        $taskIds = Schema::hasColumn('tasks', 'lead_id')
            ? DB::table('tasks')->where('lead_id', '>', 0)->select('id') : null;

        if (Schema::hasTable('lead_notifications')) {
            $queries['lead_notifications'] = DB::table('lead_notifications')->where(function ($query) use ($taskIds) {
                // Includes soft-deleted, orphaned and bulk-assignment lead notifications.
                $query->whereIn('model', ['lead', 'task', 'opportunity']);
                if ($taskIds) {
                    $query->orWhere(function ($query) use ($taskIds) {
                        $query->where('model', 'task_management')->whereIn('model_id', clone $taskIds);
                    });
                }
            });
        }

        $modelTypes = [];
        foreach ([Lead::class, LeadTask::class, LeadContact::class, LeadNote::class,
            LeadOpportunity::class, LeadLog::class, LeadCheckIn::class] as $model) {
            $modelTypes[] = $model;
            $modelTypes[] = (new $model)->getMorphClass();
        }
        foreach (['media', 'addresses'] as $table) {
            if (Schema::hasColumn($table, 'model_type') && Schema::hasColumn($table, 'model_id')) {
                $queries[$table] = DB::table($table)->where(function ($query) use ($modelTypes, $taskIds) {
                    $query->whereIn('model_type', array_unique($modelTypes));
                    if ($taskIds) {
                        $query->orWhere(function ($query) use ($taskIds) {
                            $query->whereIn('model_type', [Tasks::class, (new Tasks)->getMorphClass()])
                                ->whereIn('model_id', clone $taskIds);
                        });
                    }
                });
            }
        }

        if ($taskIds) {
            foreach (['task_assignments', 'task_comments', 'task_status_logs'] as $table) {
                if (Schema::hasColumn($table, 'task_id')) {
                    $queries[$table] = DB::table($table)->whereIn('task_id', clone $taskIds);
                }
            }
            $queries['tasks'] = DB::table('tasks')->where('lead_id', '>', 0);
        }

        // checkin_id alone is ambiguous: customer and lead check-ins use separate ID sequences.
        if (Schema::hasColumn('visit_reports', 'lead_id')) {
            $queries['visit_reports'] = DB::table('visit_reports')->where('lead_id', '>', 0);
        }
        foreach (['lead_opportunities', 'lead_tasks', 'lead_notes', 'lead_contacts', 'lead_logs', 'lead_check_in', 'leads'] as $table) {
            if (Schema::hasTable($table)) {
                $queries[$table] = DB::table($table);
            }
        }

        $this->assertNoUnplannedLeadRecords(array_keys($queries));
        return $queries;
    }

    private function assertNoUnplannedLeadRecords(array $plannedTables): void
    {
        $referenceColumns = ['lead_id', 'lead_task_id', 'lead_contact_id', 'lead_opportunity_id'];
        $prefix = DB::connection()->getTablePrefix();
        foreach (Schema::getAllTables() as $row) {
            $physicalTable = $row->name ?? array_values((array) $row)[0];
            if ($prefix !== '' && !str_starts_with($physicalTable, $prefix)) {
                continue;
            }
            $table = substr($physicalTable, strlen($prefix));
            if (in_array($table, $plannedTables, true)) {
                continue;
            }
            foreach (array_intersect($referenceColumns, Schema::getColumnListing($table)) as $column) {
                if (DB::table($table)->where($column, '>', 0)->exists()) {
                    throw new \RuntimeException("Unplanned lead records in {$table}.{$column}. Add this relation to LeadDataPurger before deleting.");
                }
            }
        }
    }

    public function preview(): array
    {
        return array_map(fn ($query) => (clone $query)->count(), $this->queries());
    }

    public function purge(): array
    {
        $queries = $this->queries();
        if (DB::getDriverName() === 'mysql') {
            $engines = DB::table('information_schema.TABLES')
                ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
                ->whereIn('TABLE_NAME', array_map(fn ($table) => DB::connection()->getTablePrefix() . $table, array_keys($queries)))
                ->pluck('ENGINE', 'TABLE_NAME');
            foreach ($engines as $table => $engine) {
                if (strtolower((string) $engine) !== 'innodb') {
                    throw new \RuntimeException("{$table} must use InnoDB before transactional deletion.");
                }
            }
        }

        return DB::transaction(function () use ($queries) {
            $deleted = [];
            foreach ($queries as $table => $query) {
                $deleted[$table] = $query->delete();
            }
            return $deleted;
        });
    }
}
