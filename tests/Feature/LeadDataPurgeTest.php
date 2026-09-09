<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Tasks;
use App\Services\LeadDataPurger;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LeadDataPurgeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'lead_purge_test', 'database.connections.lead_purge_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ]]);
        DB::statement('CREATE TABLE leads (id INTEGER PRIMARY KEY)');
        DB::table('leads')->insert(['id' => 1]);
        foreach (['lead_contacts', 'lead_notes', 'lead_tasks', 'lead_opportunities', 'lead_logs', 'lead_check_in'] as $table) {
            DB::statement("CREATE TABLE {$table} (id INTEGER PRIMARY KEY, lead_id INTEGER REFERENCES leads(id))");
            DB::table($table)->insert(['id' => 1, 'lead_id' => 1]);
        }
        DB::statement('CREATE TABLE tasks (id INTEGER PRIMARY KEY, lead_id INTEGER REFERENCES leads(id))');
        DB::table('tasks')->insert([['id' => 1, 'lead_id' => 1], ['id' => 2, 'lead_id' => null]]);
        foreach (['task_assignments', 'task_comments', 'task_status_logs'] as $table) {
            DB::statement("CREATE TABLE {$table} (id INTEGER PRIMARY KEY, task_id INTEGER REFERENCES tasks(id))");
            DB::table($table)->insert([['id' => 1, 'task_id' => 1], ['id' => 2, 'task_id' => 2]]);
        }
        DB::statement('CREATE TABLE visit_reports (id INTEGER PRIMARY KEY, lead_id INTEGER REFERENCES leads(id), checkin_id INTEGER)');
        DB::table('visit_reports')->insert([
            ['id' => 1, 'lead_id' => 1, 'checkin_id' => 1], ['id' => 2, 'lead_id' => null, 'checkin_id' => 1],
        ]);
        foreach (['media', 'addresses'] as $table) {
            DB::statement("CREATE TABLE {$table} (id INTEGER PRIMARY KEY, model_type TEXT, model_id INTEGER)");
            DB::table($table)->insert([
                ['id' => 1, 'model_type' => Lead::class, 'model_id' => 1],
                ['id' => 2, 'model_type' => Lead::class, 'model_id' => 999], // Orphan.
                ['id' => 3, 'model_type' => Tasks::class, 'model_id' => 1],
                ['id' => 4, 'model_type' => Tasks::class, 'model_id' => 2],
                ['id' => 5, 'model_type' => 'App\\Models\\Customers', 'model_id' => 1],
            ]);
        }
        DB::statement('CREATE TABLE lead_notifications (id INTEGER PRIMARY KEY, model TEXT, model_id INTEGER, deleted_at TEXT)');
        foreach (['lead', 'task', 'opportunity', 'task_management', 'unrelated'] as $index => $model) {
            DB::table('lead_notifications')->insert(['id' => $index + 1, 'model' => $model, 'model_id' => 1, 'deleted_at' => '2026-09-09']);
        }
        DB::table('lead_notifications')->insert(['id' => 6, 'model' => 'task_management', 'model_id' => 2]);
        DB::table('lead_notifications')->insert(['id' => 7, 'model' => 'lead', 'model_id' => null]);
        foreach (['customers', 'users', 'purchase_orders', 'sales_orders', 'statuses'] as $table) {
            DB::statement("CREATE TABLE {$table} (id INTEGER PRIMARY KEY)");
            DB::table($table)->insert(['id' => 1]);
        }
    }

    public function test_default_and_dry_run_never_delete(): void
    {
        $this->artisan('leads:purge-all')->assertSuccessful();
        $this->artisan('leads:purge-all', ['--dry-run' => true, '--force' => true])->assertSuccessful();
        $this->assertSame(1, DB::table('leads')->count());
        $this->assertSame(2, DB::table('tasks')->count());
    }

    public function test_force_deletes_related_data_and_preserves_other_modules(): void
    {
        $this->artisan('leads:purge-all', ['--force' => true])->assertSuccessful();
        foreach (['leads', 'lead_contacts', 'lead_notes', 'lead_tasks', 'lead_opportunities', 'lead_logs', 'lead_check_in'] as $table) {
            $this->assertSame(0, DB::table($table)->count(), $table);
        }
        foreach (['tasks', 'task_assignments', 'task_comments', 'task_status_logs', 'visit_reports'] as $table) {
            $this->assertSame([2], DB::table($table)->pluck('id')->all(), $table);
        }
        foreach (['media', 'addresses'] as $table) {
            $this->assertSame([4, 5], DB::table($table)->orderBy('id')->pluck('id')->all(), $table);
        }
        $this->assertSame([5, 6], DB::table('lead_notifications')->orderBy('id')->pluck('id')->all());
        foreach (['customers', 'users', 'purchase_orders', 'sales_orders', 'statuses'] as $table) {
            $this->assertSame(1, DB::table($table)->count(), $table);
        }
        $this->assertSame(0, array_sum((new LeadDataPurger)->preview()));
        $this->artisan('leads:purge-all', ['--force' => true])->assertSuccessful();
    }

    public function test_foreign_key_failure_rolls_back_every_deletion(): void
    {
        DB::statement('CREATE TABLE custom_lead_reference (id INTEGER PRIMARY KEY, owner_id INTEGER REFERENCES leads(id))');
        DB::table('custom_lead_reference')->insert(['id' => 1, 'owner_id' => 1]);
        $before = (new LeadDataPurger)->preview();
        $this->artisan('leads:purge-all', ['--force' => true])->assertFailed();
        $this->assertSame($before, (new LeadDataPurger)->preview());
    }

    public function test_unknown_lead_relation_without_a_foreign_key_blocks_deletion(): void
    {
        DB::statement('CREATE TABLE custom_lead_data (id INTEGER PRIMARY KEY, lead_id INTEGER)');
        DB::table('custom_lead_data')->insert(['id' => 1, 'lead_id' => 1]);
        $this->artisan('leads:purge-all', ['--force' => true])->assertFailed();
        $this->assertSame(1, DB::table('leads')->count());
        $this->assertSame(1, DB::table('lead_contacts')->count());
    }

    public function test_missing_optional_tables_are_supported(): void
    {
        DB::statement('DROP TABLE visit_reports');
        DB::statement('DROP TABLE lead_logs');
        DB::statement('DROP TABLE media');
        $this->artisan('leads:purge-all', ['--force' => true])->assertSuccessful();
        $this->assertSame(0, DB::table('leads')->count());
    }
}
