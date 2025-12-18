<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $testUser;

    protected Role $adminRole;

    protected Tenant $tenant;

    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a tenant first
        $this->tenant = Tenant::create([
            'name' => 'Test Tenant',
            'code' => 'test',
            'domain' => 'test.example.com',
            'database_name' => 'test_db',
            'status' => 'active',
        ]);

        // Create a project
        $this->project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test project description',
            'status' => 'active',
            'tenant_id' => $this->tenant->id,
        ]);

        // Set default tenant ID for testing
        config(['app.default_tenant_id' => $this->tenant->id]);
        session(['current_tenant_id' => $this->tenant->id]);

        // Create admin role
        $this->adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrator role',
            'is_default' => false,
            'level' => 1,
        ]);

        // Create permissions
        $viewLogsPermission = Permission::create([
            'name' => 'view_activity_logs',
            'display_name' => 'View Activity Logs',
            'description' => 'View system activity logs',
            'group' => 'System',
        ]);

        // Assign permissions to admin role
        $this->adminRole->permissions()->attach($viewLogsPermission->id);

        // Create admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'level' => 1,
            'status' => true,
            'tenant_id' => $this->tenant->id,
        ]);

        // Create test user
        $this->testUser = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'level' => 2,
            'status' => true,
            'tenant_id' => $this->tenant->id,
        ]);

        // Assign admin role
        $this->admin->roles()->attach($this->adminRole);
    }

    /**
     * Test activity log index page displays logs correctly.
     */
    public function test_index_displays_activity_logs(): void
    {
        // Create test activity logs
        $logs = ActivityLog::factory()->count(5)->create([
            'user_id' => $this->testUser->id,
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cms.activity-logs.index');
        $response->assertViewHas('logs');
        $response->assertViewHas('users');
        $response->assertViewHas('actions');
        $response->assertViewHas('models');

        // Check that logs are displayed
        foreach ($logs as $log) {
            $response->assertSee($log->action);
            $response->assertSee($log->description);
        }
    }

    /**
     * Test activity log index with search functionality.
     */
    public function test_index_with_search(): void
    {
        $searchLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'user_created',
            'description' => 'Created user John Doe',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $otherLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'product_updated',
            'description' => 'Updated product ABC',
            'model' => 'Product',
            'model_id' => 2,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        // Search by action
        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.index', ['search' => 'user_created']));

        $response->assertStatus(200);
        $response->assertSee($searchLog->description);
        $response->assertDontSee($otherLog->description);

        // Search by description
        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.index', ['search' => 'John Doe']));

        $response->assertStatus(200);
        $response->assertSee($searchLog->description);
        $response->assertDontSee($otherLog->description);
    }

    /**
     * Test activity log index with user filter.
     */
    public function test_index_with_user_filter(): void
    {
        $anotherUser = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $userLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'test_action',
            'description' => 'Test user action',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $otherUserLog = ActivityLog::create([
            'user_id' => $anotherUser->id,
            'action' => 'test_action',
            'description' => 'Another user action',
            'model' => 'User',
            'model_id' => 2,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.index', ['user_id' => $this->testUser->id]));

        $response->assertStatus(200);
        $response->assertSee($userLog->description);
        $response->assertDontSee($otherUserLog->description);
    }

    /**
     * Test activity log index with action filter.
     */
    public function test_index_with_action_filter(): void
    {
        $createLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'user_created',
            'description' => 'Created user',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $updateLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'user_updated',
            'description' => 'Updated user',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.index', ['action' => 'user_created']));

        $response->assertStatus(200);
        $response->assertSee($createLog->description);
        $response->assertDontSee($updateLog->description);
    }

    /**
     * Test activity log index with model filter.
     */
    public function test_index_with_model_filter(): void
    {
        $userLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'created',
            'description' => 'Created user',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $productLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'created',
            'description' => 'Created product',
            'model' => 'Product',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.index', ['model' => 'User']));

        $response->assertStatus(200);
        $response->assertSee($userLog->description);
        $response->assertDontSee($productLog->description);
    }

    /**
     * Test activity log index with date range filter.
     */
    public function test_index_with_date_range_filter(): void
    {
        $oldLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'old_action',
            'description' => 'Old action',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
            'created_at' => now()->subDays(10),
        ]);

        $recentLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'recent_action',
            'description' => 'Recent action',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.index', [
                'date_from' => now()->subDays(5)->format('Y-m-d'),
                'date_to' => now()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $response->assertSee($recentLog->description);
        $response->assertDontSee($oldLog->description);
    }

    /**
     * Test activity log show page displays log details.
     */
    public function test_show_displays_log_details(): void
    {
        $log = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'test_action',
            'description' => 'Test action description',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Mozilla/5.0 Test Browser',
            'project_id' => $this->project->id,
            'properties' => ['key' => 'value'],
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.show', $log));

        $response->assertStatus(200);
        $response->assertViewIs('cms.activity-logs.show');
        $response->assertViewHas('activityLog');
        $response->assertSee($log->action);
        $response->assertSee($log->description);
        $response->assertSee($log->ip_address);
        $response->assertSee($log->user_agent);
    }

    /**
     * Test user logs page displays logs for specific user.
     */
    public function test_user_logs_displays_user_specific_logs(): void
    {
        $anotherUser = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $userLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'user_action',
            'description' => 'User specific action',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $otherUserLog = ActivityLog::create([
            'user_id' => $anotherUser->id,
            'action' => 'other_action',
            'description' => 'Other user action',
            'model' => 'User',
            'model_id' => 2,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.user-logs', $this->testUser));

        $response->assertStatus(200);
        $response->assertViewIs('cms.activity-logs.user-logs');
        $response->assertViewHas('logs');
        $response->assertViewHas('user');
        $response->assertSee($userLog->description);
        $response->assertDontSee($otherUserLog->description);
    }

    /**
     * Test activity log export functionality.
     */
    public function test_export_generates_csv_file(): void
    {
        $log = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'export_test',
            'description' => 'Export test action',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition');

        // Check CSV content contains the log data
        $content = $response->getContent();
        $this->assertStringContainsString('export_test', $content);
        $this->assertStringContainsString('Export test action', $content);
        $this->assertStringContainsString($this->testUser->name, $content);
    }

    /**
     * Test activity log export with filters.
     */
    public function test_export_with_filters(): void
    {
        $includedLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'included_action',
            'description' => 'Included action',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $excludedLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'excluded_action',
            'description' => 'Excluded action',
            'model' => 'Product',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.export', ['model' => 'User']));

        $response->assertStatus(200);

        // Check CSV content includes filtered log and excludes others
        $content = $response->getContent();
        $this->assertStringContainsString('included_action', $content);
        $this->assertStringNotContainsString('excluded_action', $content);
    }

    /**
     * Test clearing old activity logs.
     */
    public function test_clear_old_removes_old_logs(): void
    {
        // Create old logs
        $oldLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'old_action',
            'description' => 'Old action',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
            'created_at' => now()->subDays(40),
        ]);

        // Create recent logs
        $recentLog = ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'recent_action',
            'description' => 'Recent action',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
            'created_at' => now()->subDays(10),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.activity-logs.clear-old'), ['days' => 30]);

        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['deleted_count']);

        // Assert old log was deleted
        $this->assertDatabaseMissing('activity_logs', ['id' => $oldLog->id]);

        // Assert recent log was not deleted
        $this->assertDatabaseHas('activity_logs', ['id' => $recentLog->id]);
    }

    /**
     * Test clearing old logs validation.
     */
    public function test_clear_old_validates_days_parameter(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('cms.activity-logs.clear-old'), ['days' => 0]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['days']);
    }

    /**
     * Test activity statistics endpoint.
     */
    public function test_statistics_returns_activity_data(): void
    {
        // Create test logs
        ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'user_created',
            'description' => 'Created user',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
            'created_at' => now()->subDays(5),
        ]);

        ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'user_updated',
            'description' => 'Updated user',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
            'created_at' => now()->subDays(3),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.statistics'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_logs',
            'unique_users',
            'top_actions',
            'top_users',
            'daily_activity',
        ]);

        $data = $response->json();
        $this->assertGreaterThan(0, $data['total_logs']);
        $this->assertGreaterThan(0, $data['unique_users']);
    }

    /**
     * Test activity statistics with custom days parameter.
     */
    public function test_statistics_with_custom_days(): void
    {
        // Create logs within and outside the range
        ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'recent_action',
            'description' => 'Recent action',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
            'created_at' => now()->subDays(5),
        ]);

        ActivityLog::create([
            'user_id' => $this->testUser->id,
            'action' => 'old_action',
            'description' => 'Old action',
            'model' => 'User',
            'model_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'project_id' => $this->project->id,
            'created_at' => now()->subDays(15),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('cms.activity-logs.statistics', ['days' => 7]));

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertEquals(1, $data['total_logs']); // Only the recent log should be counted
    }
}
