<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->user = User::factory()->create([
            'email' => 'admin@test.com',
            'level' => 0, // Super admin
        ]);
    }

    public function test_can_view_categories_list(): void
    {
        $this->actingAs($this->user);

        // Create some categories
        ProductCategory::factory()->count(3)->create();

        $response = $this->get(route('cms.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cms.categories.index');
        $response->assertViewHas('categories');
    }

    public function test_can_create_root_category(): void
    {
        $this->actingAs($this->user);

        $categoryData = [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic products',
            'is_active' => true,
            'sort_order' => 0,
        ];

        $response = $this->post(route('cms.categories.store'), $categoryData);

        $response->assertRedirect(route('cms.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'level' => 0,
            'path' => 'electronics',
        ]);
    }

    public function test_can_create_subcategory(): void
    {
        $this->actingAs($this->user);

        // Create parent category
        $parent = ProductCategory::factory()->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'level' => 0,
            'path' => 'electronics',
        ]);

        $categoryData = [
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'parent_id' => $parent->id,
            'description' => 'Mobile phones',
            'is_active' => true,
            'sort_order' => 0,
        ];

        $response = $this->post(route('cms.categories.store'), $categoryData);

        $response->assertRedirect(route('cms.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'parent_id' => $parent->id,
            'level' => 1,
            'path' => 'electronics/smartphones',
        ]);
    }

    public function test_can_update_category(): void
    {
        $this->actingAs($this->user);

        $category = ProductCategory::factory()->create([
            'name' => 'Old Name',
            'slug' => 'old-name',
        ]);

        $updateData = [
            'name' => 'New Name',
            'slug' => 'new-name',
            'description' => 'Updated description',
            'is_active' => true,
            'sort_order' => 0,
        ];

        $response = $this->put(route('cms.categories.update', $category->id), $updateData);

        $response->assertRedirect(route('cms.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'slug' => 'new-name',
        ]);
    }

    public function test_cannot_delete_category_with_children(): void
    {
        $this->actingAs($this->user);

        $parent = ProductCategory::factory()->create();
        $child = ProductCategory::factory()->create(['parent_id' => $parent->id]);

        $response = $this->delete(route('cms.categories.destroy', $parent->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('product_categories', ['id' => $parent->id]);
    }

    public function test_can_delete_empty_category(): void
    {
        $this->actingAs($this->user);

        $category = ProductCategory::factory()->create();

        $response = $this->delete(route('cms.categories.destroy', $category->id));

        $response->assertRedirect(route('cms.categories.index'));
        $this->assertDatabaseMissing('product_categories', ['id' => $category->id]);
    }

    public function test_slug_is_auto_generated_from_name(): void
    {
        $this->actingAs($this->user);

        $categoryData = [
            'name' => 'Test Category Name',
            'description' => 'Test description',
            'is_active' => true,
            'sort_order' => 0,
        ];

        $response = $this->post(route('cms.categories.store'), $categoryData);

        $response->assertRedirect(route('cms.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'name' => 'Test Category Name',
            'slug' => 'test-category-name',
        ]);
    }

    public function test_duplicate_slug_gets_incremented(): void
    {
        $this->actingAs($this->user);

        // Create first category
        ProductCategory::factory()->create([
            'name' => 'Test',
            'slug' => 'test',
        ]);

        // Create second category with same name
        $categoryData = [
            'name' => 'Test',
            'description' => 'Test description',
            'is_active' => true,
            'sort_order' => 0,
        ];

        $response = $this->post(route('cms.categories.store'), $categoryData);

        $response->assertRedirect(route('cms.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'name' => 'Test',
            'slug' => 'test-2',
        ]);
    }
}