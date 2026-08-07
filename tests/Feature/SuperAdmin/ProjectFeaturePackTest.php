<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\FeaturePack;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectFeaturePackTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'level' => 10,
        ]);

        $this->project = Project::factory()->create([
            'cms_features' => [],
        ]);
    }

    public function test_config_page_displays_feature_packs(): void
    {
        FeaturePack::create([
            'name' => 'Đặt lịch khám (Booking)',
            'code' => 'booking',
            'group_name' => 'Y tế & Phòng khám',
            'description' => 'Cho phép bệnh nhân đặt lịch hẹn khám online',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.projects.config', $this->project));

        $response->assertStatus(200);
        $response->assertSee('Feature Packs');
        $response->assertSee('Đặt lịch khám (Booking)');
        $response->assertSee('Y tế & Phòng khám');
    }

    public function test_config_page_shows_no_packs_message_when_empty(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.projects.config', $this->project));

        $response->assertStatus(200);
        $response->assertSee('Chưa có Feature Pack nào');
    }

    public function test_feature_packs_are_grouped_by_group_name(): void
    {
        FeaturePack::create(['name' => 'Booking', 'code' => 'booking', 'group_name' => 'Y tế & Phòng khám', 'is_active' => true]);
        FeaturePack::create(['name' => 'Commerce', 'code' => 'commerce', 'group_name' => 'Thương mại điện tử', 'is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.projects.config', $this->project));

        $response->assertStatus(200);
        $response->assertSee('Y tế & Phòng khám');
        $response->assertSee('Thương mại điện tử');
    }

    public function test_saving_cms_features_persists_to_project(): void
    {
        FeaturePack::create(['name' => 'Booking', 'code' => 'booking', 'group_name' => 'Y tế & Phòng khám', 'is_active' => true]);
        FeaturePack::create(['name' => 'Blog', 'code' => 'blog', 'group_name' => 'Tính năng chung', 'is_active' => true]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.projects.config', $this->project), [
                'cms_features' => ['booking', 'blog'],
            ]);

        $response->assertRedirect();

        $this->project->refresh();
        $this->assertContains('booking', $this->project->cms_features);
        $this->assertContains('blog', $this->project->cms_features);
    }

    public function test_clearing_cms_features_saves_empty_array(): void
    {
        $this->project->update(['cms_features' => ['booking', 'blog']]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.projects.config', $this->project), [
                'settings' => [],
            ]);

        $response->assertRedirect();

        $this->project->refresh();
        $this->assertEmpty($this->project->cms_features);
    }

    public function test_inactive_feature_packs_not_shown_on_config_page(): void
    {
        FeaturePack::create(['name' => 'Hidden Pack', 'code' => 'hidden', 'group_name' => 'Test', 'is_active' => false]);

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.projects.config', $this->project));

        $response->assertStatus(200);
        $response->assertDontSee('Hidden Pack');
    }

    public function test_project_has_feature_method_works_correctly(): void
    {
        $this->project->update(['cms_features' => ['booking', 'blog']]);
        $this->project->refresh();

        $this->assertTrue($this->project->hasFeature('booking'));
        $this->assertTrue($this->project->hasFeature('blog'));
        $this->assertFalse($this->project->hasFeature('commerce'));
    }
}
