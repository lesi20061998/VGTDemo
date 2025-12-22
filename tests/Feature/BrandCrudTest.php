<?php

namespace Tests\Feature;

use App\Models\ProjectBrand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BrandCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up project context for testing
        config(['database.connections.project.database' => 'project_hd001']);
    }

    public function test_can_create_brand_with_only_name(): void
    {
        $brandData = [
            'name' => 'Test Brand',
        ];

        $response = $this->post(route('project.admin.brands.store', 'hd001'), $brandData);

        $response->assertRedirect(route('project.admin.brands.index', 'hd001'));
        $response->assertSessionHas('alert.type', 'success');

        $this->assertDatabaseHas('brands', [
            'name' => 'Test Brand',
            'slug' => 'test-brand', // Auto-generated
        ], 'project');
    }

    public function test_shows_warning_for_duplicate_brand_name(): void
    {
        // Create existing brand
        ProjectBrand::create([
            'name' => 'Existing Brand',
            'slug' => 'existing-brand',
        ]);

        $brandData = [
            'name' => 'Existing Brand',
        ];

        $response = $this->post(route('project.admin.brands.store', 'hd001'), $brandData);

        $response->assertRedirect();
        $response->assertSessionHas('alert.type', 'warning');
        $response->assertSessionHas('alert.message');
    }

    public function test_can_create_brand_with_custom_slug(): void
    {
        $brandData = [
            'name' => 'Custom Brand',
            'slug' => 'my-custom-slug',
        ];

        $response = $this->post(route('project.admin.brands.store', 'hd001'), $brandData);

        $response->assertRedirect(route('project.admin.brands.index', 'hd001'));

        $this->assertDatabaseHas('brands', [
            'name' => 'Custom Brand',
            'slug' => 'my-custom-slug',
        ], 'project');
    }

    public function test_can_create_brand_with_all_optional_fields(): void
    {
        $brandData = [
            'name' => 'Complete Brand',
            'slug' => 'complete-brand',
            'description' => 'This is a complete brand description',
            'logo' => 'https://example.com/logo.png',
            'meta_title' => 'Complete Brand SEO Title',
            'meta_description' => 'Complete brand SEO description',
            'is_active' => true,
        ];

        $response = $this->post(route('project.admin.brands.store', 'hd001'), $brandData);

        $response->assertRedirect(route('project.admin.brands.index', 'hd001'));

        $this->assertDatabaseHas('brands', $brandData, 'project');
    }

    public function test_can_update_brand(): void
    {
        $brand = ProjectBrand::create([
            'name' => 'Original Brand',
            'slug' => 'original-brand',
        ]);

        $updateData = [
            'name' => 'Updated Brand',
            'description' => 'Updated description',
        ];

        $response = $this->put(
            route('project.admin.brands.update', ['hd001', $brand->id]),
            $updateData
        );

        $response->assertRedirect(route('project.admin.brands.index', 'hd001'));
        $response->assertSessionHas('alert.type', 'success');

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Updated Brand',
            'slug' => 'updated-brand', // Auto-generated from new name
            'description' => 'Updated description',
        ], 'project');
    }

    public function test_shows_warning_when_updating_to_duplicate_name(): void
    {
        $existingBrand = ProjectBrand::create([
            'name' => 'Existing Brand',
            'slug' => 'existing-brand',
        ]);

        $brandToUpdate = ProjectBrand::create([
            'name' => 'Brand To Update',
            'slug' => 'brand-to-update',
        ]);

        $updateData = [
            'name' => 'Existing Brand', // Same as existing brand
        ];

        $response = $this->put(
            route('project.admin.brands.update', ['hd001', $brandToUpdate->id]),
            $updateData
        );

        $response->assertRedirect();
        $response->assertSessionHas('alert.type', 'warning');
    }

    public function test_can_delete_brand(): void
    {
        $brand = ProjectBrand::create([
            'name' => 'Brand To Delete',
            'slug' => 'brand-to-delete',
        ]);

        $response = $this->delete(route('project.admin.brands.destroy', ['hd001', $brand->id]));

        $response->assertRedirect(route('project.admin.brands.index', 'hd001'));
        $response->assertSessionHas('alert.type', 'success');

        $this->assertDatabaseMissing('brands', [
            'id' => $brand->id,
        ], 'project');
    }

    public function test_name_is_required(): void
    {
        $brandData = [
            'slug' => 'test-slug',
            'description' => 'Test description',
        ];

        $response = $this->post(route('project.admin.brands.store', 'hd001'), $brandData);

        $response->assertSessionHasErrors('name');
    }

    public function test_can_view_brand_index(): void
    {
        ProjectBrand::create([
            'name' => 'Test Brand 1',
            'slug' => 'test-brand-1',
        ]);

        ProjectBrand::create([
            'name' => 'Test Brand 2',
            'slug' => 'test-brand-2',
        ]);

        $response = $this->get(route('project.admin.brands.index', 'hd001'));

        $response->assertStatus(200);
        $response->assertSee('Test Brand 1');
        $response->assertSee('Test Brand 2');
    }
}
