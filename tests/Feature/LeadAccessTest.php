<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\LeadTasksController;
use App\Models\Lead;
use App\Models\LeadTask;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class LeadAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'lead_access_test', 'database.connections.lead_access_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ], 'constants.customer_roles' => []]);
        DB::statement('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, active TEXT, reportingid INTEGER)');
        DB::statement('CREATE TABLE roles (id INTEGER PRIMARY KEY, name TEXT, guard_name TEXT)');
        DB::statement('CREATE TABLE model_has_roles (role_id INTEGER, model_id INTEGER, model_type TEXT)');
        DB::statement('CREATE TABLE leads (id INTEGER PRIMARY KEY, company_name TEXT, assign_to INTEGER, created_by INTEGER)');
        DB::statement('CREATE TABLE lead_tasks (id INTEGER PRIMARY KEY, lead_id INTEGER, assigned_to INTEGER, created_by INTEGER, description TEXT, date TEXT, time TEXT, status TEXT, created_at TEXT)');
        DB::statement('CREATE TABLE lead_contacts (id INTEGER PRIMARY KEY, lead_id INTEGER, name TEXT, phone_number TEXT)');
        DB::statement('CREATE TABLE lead_notifications (id INTEGER PRIMARY KEY, user_id INTEGER, read INTEGER, deleted_at TEXT)');
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Manager', 'active' => 'Y', 'reportingid' => null],
            ['id' => 2, 'name' => 'Team', 'active' => 'Y', 'reportingid' => 1],
            ['id' => 3, 'name' => 'Other', 'active' => 'Y', 'reportingid' => null],
            ['id' => 4, 'name' => 'Admin', 'active' => 'Y', 'reportingid' => null],
        ]);
        DB::table('leads')->insert([
            ['id' => 10, 'company_name' => 'Team Lead', 'assign_to' => 2, 'created_by' => 3],
            ['id' => 20, 'company_name' => 'Other Lead', 'assign_to' => 3, 'created_by' => 3],
        ]);
        foreach ([
            [1, 10, 3, 3], // Lead belongs to team; its tasks are visible to the manager.
            [2, 20, 2, 3], // Task assigned to team on an otherwise unrelated lead.
            [3, 20, 3, 2], // Task created by team.
            [4, 20, 3, 3], // Unrelated task on the same lead must stay hidden.
        ] as [$id, $leadId, $assigned, $created]) {
            DB::table('lead_tasks')->insert([
                'id' => $id, 'lead_id' => $leadId, 'assigned_to' => $assigned, 'created_by' => $created,
                'description' => 'Task ' . $id, 'date' => '2026-09-09', 'time' => '10:00:00',
                'status' => 'pending', 'created_at' => '2026-09-09 10:00:00',
            ]);
        }
    }

    public function test_manager_and_assignee_see_owned_created_and_team_tasks(): void
    {
        foreach ([1, 2] as $userId) {
            $this->assertSame([1, 2, 3], LeadTask::visibleTo(User::find($userId))->orderBy('id')->pluck('id')->all());
            $this->assertSame([10, 20], Lead::visibleTo(User::find($userId))->orderBy('id')->pluck('id')->all());
        }
    }

    /** @dataProvider globalRoles */
    public function test_global_roles_see_all_tasks(string $role): void
    {
        DB::table('roles')->insert(['id' => 1, 'name' => $role, 'guard_name' => 'users']);
        DB::table('model_has_roles')->insert(['role_id' => 1, 'model_id' => 4, 'model_type' => User::class]);
        $this->assertSame(4, LeadTask::visibleTo(User::find(4))->count());
    }

    public static function globalRoles(): array
    {
        return [['superadmin'], ['Admin'], ['BACKOFFICE1'], ['BACKOFFICE2'], ['BACKOFFICE3']];
    }

    public function test_search_and_assignee_filters_cannot_expand_api_access(): void
    {
        $controller = new LeadController;
        $response = $controller->getLeadTasks($this->request(1, ['search' => 'Other Lead']));
        $this->assertSame(200, $response->getStatusCode());
        $ids = array_column($response->getData(true)['data'], 'id');
        sort($ids);
        $this->assertSame([2, 3], $ids);
        $response = $controller->getLeadTasks($this->request(1, ['search' => 'Other Lead', 'user_id' => 3]));
        $this->assertSame([3], array_column($response->getData(true)['data'], 'id'));
        $response = $controller->getLeadTasks($this->request(1, ['search' => 'No match']));
        $this->assertSame([], $response->getData(true)['data']);
    }

    public function test_unrelated_user_cannot_open_lead_details(): void
    {
        $this->expectException(ModelNotFoundException::class);
        (new LeadController)->leadDetails($this->request(4, ['lead_id' => 20]));
    }

    public function test_unrelated_task_cannot_be_updated_by_id(): void
    {
        $this->expectException(ModelNotFoundException::class);
        (new LeadController)->change_task_status($this->request(1, ['task_id' => 4, 'status' => 'open']));
    }

    public function test_web_task_list_uses_same_scope_as_api(): void
    {
        Gate::shouldReceive('denies')->with('lead_access')->andReturn(false);
        $controller = (new \ReflectionClass(LeadTasksController::class))->newInstanceWithoutConstructor();
        $request = $this->request(1, []);
        $this->app->instance('request', $request);
        $request->setUserResolver(fn () => User::find(1));
        $response = $controller->getLeadTasks($request);
        $ids = array_column($response->getData(true)['data'], 'id');
        sort($ids);
        $this->assertSame([1, 2, 3], $ids);
    }

    public function test_web_lead_details_reject_unrelated_user(): void
    {
        Gate::shouldReceive('denies')->with('lead_access')->andReturn(false);
        $controller = (new \ReflectionClass(\App\Http\Controllers\LeadController::class))->newInstanceWithoutConstructor();
        $this->expectException(ModelNotFoundException::class);
        $controller->show($this->request(4, []), Lead::find(20));
    }

    private function request(int $userId, array $data): Request
    {
        $request = Request::create('/', 'GET', $data);
        $request->setUserResolver(fn () => User::find($userId));
        return $request;
    }
}
