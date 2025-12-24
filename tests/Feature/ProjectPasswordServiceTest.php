<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectPasswordAudit;
use App\Models\User;
use App\Services\ProjectPasswordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProjectPasswordServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProjectPasswordService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProjectPasswordService();
    }

    public function test_can_get_plain_password(): void
    {
        $plainPassword = 'test-password-123';
        $project = Project::factory()->create([
            'project_admin_password_plain' => encrypt($plainPassword),
        ]);

        $result = $this->service->getPlainPassword($project);

        $this->assertEquals($plainPassword, $result);
    }

    public function test_returns_null_for_missing_plain_password(): void
    {
        $project = Project::factory()->create([
            'project_admin_password_plain' => null,
        ]);

        $result = $this->service->getPlainPassword($project);

        $this->assertNull($result);
    }

    public function test_can_set_password(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $newPassword = 'NewPassword123!';

        $result = $this->service->setPassword($project, $newPassword, $user);

        $this->assertTrue($result);
        $project->refresh();
        
        $this->assertTrue(Hash::check($newPassword, $project->project_admin_password));
        $this->assertEquals($newPassword, $this->service->getPlainPassword($project));
        $this->assertNotNull($project->password_updated_at);
        $this->assertEquals($user->id, $project->password_updated_by);
    }

    public function test_can_generate_password(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        $generatedPassword = $this->service->generatePassword($project, $user);

        $this->assertNotEmpty($generatedPassword);
        $this->assertGreaterThanOrEqual(8, strlen($generatedPassword));
        
        $project->refresh();
        $this->assertTrue(Hash::check($generatedPassword, $project->project_admin_password));
        $this->assertEquals($generatedPassword, $this->service->getPlainPassword($project));
    }

    public function test_logs_password_access(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        $this->service->logPasswordAccess($project, $user, 'viewed');

        $this->assertDatabaseHas('project_password_audits', [
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => 'viewed',
        ]);
    }

    public function test_validates_password_strength(): void
    {
        // Test weak password
        $weakPassword = '123';
        $errors = $this->service->validatePassword($weakPassword);
        $this->assertNotEmpty($errors);
        $this->assertContains('Password must be at least 8 characters long', $errors);

        // Test strong password
        $strongPassword = 'StrongPass123!';
        $errors = $this->service->validatePassword($strongPassword);
        $this->assertEmpty($errors);
    }

    public function test_has_viewable_password(): void
    {
        $projectWithPassword = Project::factory()->create([
            'project_admin_password_plain' => encrypt('test-password'),
        ]);
        
        $projectWithoutPassword = Project::factory()->create([
            'project_admin_password_plain' => null,
        ]);

        $this->assertTrue($this->service->hasViewablePassword($projectWithPassword));
        $this->assertFalse($this->service->hasViewablePassword($projectWithoutPassword));
    }

    public function test_can_get_password_audit_history(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        // Create some audit records
        ProjectPasswordAudit::factory()->count(3)->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $history = $this->service->getPasswordAuditHistory($project);

        $this->assertCount(3, $history);
        $this->assertInstanceOf(ProjectPasswordAudit::class, $history->first());
    }

    public function test_can_migrate_existing_passwords(): void
    {
        // Create projects with hashed passwords but no plain passwords
        $projects = Project::factory()->count(2)->create([
            'project_admin_password' => Hash::make('old-password'),
            'project_admin_password_plain' => null,
        ]);

        $migrated = $this->service->migrateExistingPasswords();

        $this->assertEquals(2, $migrated);
        
        foreach ($projects as $project) {
            $project->refresh();
            $this->assertNotNull($project->project_admin_password_plain);
            $this->assertNotNull($this->service->getPlainPassword($project));
        }
    }
}
