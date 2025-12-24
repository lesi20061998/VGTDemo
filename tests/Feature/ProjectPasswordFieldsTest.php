<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectPasswordAudit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPasswordFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_has_new_password_fields(): void
    {
        $project = Project::factory()->create([
            'project_admin_password_plain' => encrypt('test-password'),
            'password_updated_at' => now(),
        ]);

        $this->assertNotNull($project->project_admin_password_plain);
        $this->assertNotNull($project->password_updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $project->password_updated_at);
    }

    public function test_project_can_decrypt_password(): void
    {
        $plainPassword = 'test-password-123';
        $project = Project::factory()->create();
        
        $project->setEncryptedPassword($plainPassword);
        $project->save();

        $decryptedPassword = $project->getDecryptedPassword();
        
        $this->assertEquals($plainPassword, $decryptedPassword);
    }

    public function test_project_password_audit_can_be_created(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $audit = ProjectPasswordAudit::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => 'viewed',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'performed_at' => now(),
        ]);

        $this->assertDatabaseHas('project_password_audits', [
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => 'viewed',
        ]);

        $this->assertEquals($project->id, $audit->project->id);
        $this->assertEquals($user->id, $audit->user->id);
    }

    public function test_project_has_password_audit_relationship(): void
    {
        $project = Project::factory()->create();
        $user = User::factory()->create();

        ProjectPasswordAudit::factory()->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(1, $project->passwordAudits);
        $this->assertInstanceOf(ProjectPasswordAudit::class, $project->passwordAudits->first());
    }
}
