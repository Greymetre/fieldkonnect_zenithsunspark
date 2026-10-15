<?php

namespace Tests\Feature;

use App\Http\Controllers\LeadController;
use App\Models\{Lead, User};
use App\Services\LeadActivityFeed;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Gate};
use Tests\TestCase;

class LeadDetailsPerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'lead_details_test', 'database.connections.lead_details_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ], 'constants.customer_roles' => []]);
        foreach ([
            'users (id INTEGER PRIMARY KEY, name TEXT, active TEXT, reportingid INTEGER)',
            'roles (id INTEGER PRIMARY KEY, name TEXT, guard_name TEXT)',
            'model_has_roles (role_id INTEGER, model_id INTEGER, model_type TEXT)',
            'leads (id INTEGER PRIMARY KEY, company_name TEXT, assign_to INTEGER, created_by INTEGER, status INTEGER)',
            'lead_tasks (id INTEGER PRIMARY KEY, lead_id INTEGER, assigned_to INTEGER, created_by INTEGER, description TEXT, date TEXT, time TEXT, priority TEXT, created_at TEXT)',
            'lead_notes (id INTEGER PRIMARY KEY, lead_id INTEGER, note TEXT, created_by INTEGER, created_at TEXT)',
            'lead_logs (id INTEGER PRIMARY KEY, lead_id INTEGER, message TEXT, created_at TEXT)',
            'lead_opportunities (id INTEGER PRIMARY KEY, lead_id INTEGER, note TEXT, status INTEGER, created_by INTEGER, created_at TEXT)',
            'lead_contacts (id INTEGER PRIMARY KEY, lead_id INTEGER, name TEXT, email TEXT, phone_number TEXT, title TEXT, url TEXT)',
            'addresses (id INTEGER PRIMARY KEY, model_id INTEGER, model_type TEXT)',
            'statuses (id INTEGER PRIMARY KEY, module TEXT, active TEXT)',
            'opportunitie_statuses (id INTEGER PRIMARY KEY, status_name TEXT, ordering INTEGER)',
            'pincodes (id INTEGER PRIMARY KEY, pincode TEXT, active TEXT, city_id INTEGER)',
            'media (id INTEGER PRIMARY KEY, model_id INTEGER, model_type TEXT, collection_name TEXT, order_column INTEGER)',
        ] as $definition) DB::statement('CREATE TABLE ' . $definition);
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Owner', 'active' => 'Y', 'reportingid' => null],
            ['id' => 2, 'name' => 'Other', 'active' => 'Y', 'reportingid' => null],
        ]);
        DB::table('leads')->insert(['id' => 755, 'company_name' => 'Example Lead', 'assign_to' => 1, 'created_by' => 1, 'status' => 0]);
        DB::table('lead_contacts')->insert(['id' => 1, 'lead_id' => 755, 'name' => 'Primary']);
        Gate::shouldReceive('denies')->with('lead_access')->andReturn(false);
        $this->actingAs(User::find(1));
    }

    private function request(array $data = [], int $userId = 1): Request
    {
        $request = Request::create('/', 'GET', $data);
        $request->setUserResolver(fn () => User::find($userId));
        return $request;
    }

    public function test_first_response_only_loads_primary_contact_and_bounded_activity(): void
    {
        for ($i = 1; $i <= 50; $i++) {
            DB::table('lead_notes')->insert(['id' => $i, 'lead_id' => 755, 'note' => 'Note ' . $i, 'created_by' => 1, 'created_at' => '2026-10-15 10:00:00']);
        }
        DB::enableQueryLog();
        $view = (new LeadController)->show($this->request(), Lead::find(755));
        $queries = collect(DB::getQueryLog())->pluck('query')->implode("\n");
        $data = $view->getData();
        $this->assertCount(30, $data['combined']);
        $this->assertTrue($data['activityPage']->hasMorePages());
        $this->assertCount(1, $data['lead_contacts']);
        $this->assertTrue($data['users']->isEmpty());
        $this->assertStringNotContainsString('"pincodes"', $queries);
        $this->assertStringNotContainsString('"media"', $queries);
        $this->assertStringNotContainsString('"countries"', $queries);
    }

    public function test_activity_preserves_same_ids_across_types_and_paginates_without_duplicates(): void
    {
        for ($i = 1; $i <= 35; $i++) {
            foreach (['lead_notes' => 'note', 'lead_logs' => 'message'] as $table => $column) {
                DB::table($table)->insert(['id' => $i, 'lead_id' => 755, $column => 'Entry', 'created_at' => '2026-10-15 10:00:00']);
            }
        }
        $feed = new LeadActivityFeed;
        $keys = [];
        for ($page = 1; $page <= 3; $page++) {
            $result = $feed->page(Lead::find(755), User::find(1), $page);
            foreach ($result as $item) $keys[] = $item->type . ':' . $item->id;
        }
        $this->assertCount(70, $keys);
        $this->assertCount(70, array_unique($keys));
        $this->assertContains('note:1', $keys);
        $this->assertContains('log:1', $keys);
    }

    public function test_activity_does_not_expose_unrelated_tasks_on_an_accessible_lead(): void
    {
        DB::table('leads')->where('id', 755)->update(['assign_to' => 2, 'created_by' => 2]);
        foreach ([1, 2] as $id) {
            DB::table('lead_tasks')->insert(['id' => $id, 'lead_id' => 755, 'assigned_to' => $id, 'created_by' => 2, 'created_at' => '2026-10-15 10:00:00']);
        }
        $page = (new LeadActivityFeed)->page(Lead::find(755), User::find(1));
        $this->assertSame([1], $page->getCollection()->pluck('id')->all());
    }

    /** @dataProvider sections */
    public function test_background_sections_reject_unrelated_users(string $section): void
    {
        $this->expectException(ModelNotFoundException::class);
        (new LeadController)->detailSection($this->request([], 2), Lead::find(755), $section);
    }

    public static function sections(): array
    {
        return array_map(fn ($name) => [$name], ['activity', 'sidebar', 'notes', 'files', 'address', 'pincodes']);
    }

    public function test_pincode_search_is_paginated_and_scoped_to_selected_city(): void
    {
        for ($i = 1; $i <= 45; $i++) DB::table('pincodes')->insert(['id' => $i, 'pincode' => '110' . str_pad($i, 3, '0', STR_PAD_LEFT), 'active' => 'Y', 'city_id' => $i <= 30 ? 1 : 2]);
        $data = (new LeadController)->detailSection($this->request(['q' => '110', 'city_id' => 1]), Lead::find(755), 'pincodes')->getData(true);
        $this->assertCount(20, $data['results']);
        $this->assertTrue($data['pagination']['more']);
        $this->assertSame(1, $data['results'][0]['id']);
    }

    public function test_background_notes_and_activity_return_rendered_fragments(): void
    {
        DB::table('lead_notes')->insert(['id' => 1, 'lead_id' => 755, 'note' => 'Visible note', 'created_by' => 1, 'created_at' => '2026-10-15 10:00:00']);
        foreach (['notes', 'activity'] as $section) {
            $data = (new LeadController)->detailSection($this->request(), Lead::find(755), $section)->getData(true);
            $this->assertStringContainsString('Visible note', $data['html']);
        }
    }

    public function test_initial_page_renders_without_waiting_for_background_sections(): void
    {
        Gate::shouldReceive('forUser')->andReturnSelf();
        Gate::shouldReceive('check')->andReturn(false);
        Gate::shouldReceive('allows')->andReturn(false);
        $request = $this->request();
        $route = app('router')->getRoutes()->getByName('leads.show');
        $request->setRouteResolver(fn () => $route);
        $this->app->instance('request', $request);
        $request->setUserResolver(fn () => User::find(1));
        $media = new \Spatie\MediaLibrary\MediaCollections\Models\Media;
        $media->setRawAttributes(['id' => 1, 'collection_name' => 'profile_image', 'disk' => 's3',
            'file_name' => 'avatar.png', 'custom_properties' => '{}']);
        auth()->user()->setRelation('media', new \Illuminate\Database\Eloquent\Collection([$media]));
        $disk = \Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $disk->shouldReceive('url')->andReturn('https://images.example/avatar.png');
        $disk->shouldNotReceive('exists');
        \Illuminate\Support\Facades\Storage::shouldReceive('disk')->with('s3')->andReturn($disk);
        $view = (new LeadController)->show($request, Lead::find(755));
        $view->with('errors', new \Illuminate\Support\ViewErrorBag);
        $html = $view->render();
        $this->assertStringContainsString('Example Lead', $html);
        $this->assertStringContainsString('https://images.example/avatar.png', $html);
        $this->assertStringContainsString('lead-activity-items', $html);
        $this->assertStringContainsString('lead-notes-list', $html);
        $this->assertStringNotContainsString('document.querySelector(".content").style.display = "none"', $html);
        $this->assertStringNotContainsString('/http://', $html);
        $this->assertStringNotContainsString('/https://', $html);
    }

    public function test_background_sidebar_and_empty_files_render(): void
    {
        $controller = new LeadController;
        $data = $controller->detailSection($this->request(), Lead::find(755), 'sidebar')->getData(true);
        $this->assertStringContainsString('Primary', $data['contacts']);
        $this->assertSame([1], array_column($data['users'], 'id'));
        $this->assertSame('Primary', $data['contact_options'][0]['name']);
        $data = $controller->detailSection($this->request(), Lead::find(755), 'files')->getData(true);
        $this->assertStringNotContainsString('<img', $data['html']);
    }
}
