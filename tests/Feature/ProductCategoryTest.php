<?php

namespace Tests\Feature;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_categories_list()
    {
        ProductCategory::factory(3)->create();

        $response = $this->actingAs($this->user)->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
    }

    public function test_can_create_root_category()
    {
        $response = $this->actingAs($this->user)->post(route('admin.categories.store'), [
            'name' => 'Root Category',
            'description' => 'Test Root Category',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Root Category',
            'level' => 0,
        ]);
    }

    public function test_can_create_subcategory()
    {
        $parent = ProductCategory::factory()->create();

        $response = $this->actingAs($this->user)->post(route('admin.categories.store'), [
            'name' => 'Sub Category',
            'parent_id' => $parent->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Sub Category',
            'parent_id' => $parent->id,
            'level' => 1,
        ]);
    }

    public function test_can_update_category()
    {
        $category = ProductCategory::factory()->create();

        $response = $this->actingAs($this->user)->put(route('admin.categories.update', $category), [
            'name' => 'Updated Category',
            'description' => 'Updated Description',
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
        ]);
    }

    public function test_cannot_delete_category_with_children()
    {
        $parent = ProductCategory::factory()->create();
        $child = ProductCategory::factory()->withParent($parent)->create();

        $response = $this->actingAs($this->user)->delete(route('admin.categories.destroy', $parent));

        $this->assertDatabaseHas('product_categories', ['id' => $parent->id]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_can_delete_empty_category()
    {
        $category = ProductCategory::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseMissing('product_categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.categories.index'));
    }
}
