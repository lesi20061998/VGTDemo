<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_brands_list()
    {
        Brand::factory(3)->create();

        $response = $this->actingAs($this->user)->get(route('admin.brands.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.brands.index');
        $response->assertViewHas('brands');
    }

    public function test_can_create_brand()
    {
        $response = $this->actingAs($this->user)->post(route('admin.brands.store'), [
            'name' => 'Test Brand',
            'description' => 'Test Brand Description',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('brands', [
            'name' => 'Test Brand',
            'description' => 'Test Brand Description',
        ]);

        $response->assertRedirect(route('admin.brands.index'));
    }

    public function test_can_update_brand()
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->user)->put(route('admin.brands.update', $brand), [
            'name' => 'Updated Brand',
            'description' => 'Updated Description',
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Updated Brand',
            'is_active' => false,
        ]);

        $response->assertRedirect(route('admin.brands.index'));
    }

    public function test_can_delete_brand()
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('admin.brands.destroy', $brand));

        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);

        $response->assertRedirect(route('admin.brands.index'));
    }

    public function test_brand_slug_is_auto_generated()
    {
        $response = $this->actingAs($this->user)->post(route('admin.brands.store'), [
            'name' => 'Auto Slug Brand',
            'slug' => null,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('brands', [
            'slug' => 'auto-slug-brand',
        ]);
    }
}
