<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = ProductCategory::factory()->create();
        $this->brand = Brand::factory()->create();
    }

    public function test_can_view_products_list()
    {
        Product::factory(5)->create();

        $response = $this->actingAs($this->user)->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
    }

    public function test_can_create_product()
    {
        $response = $this->actingAs($this->user)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'sku' => 'TEST-SKU-001',
            'price' => 100000,
            'stock_quantity' => 10,
            'product_category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'status' => 'published',
            'stock_status' => 'in_stock',
        ]);

        $this->assertDatabaseHas('products_enhanced', [
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-001',
        ]);
    }

    public function test_cannot_create_product_with_duplicate_sku()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->post(route('admin.products.store'), [
            'name' => 'Another Product',
            'description' => 'Description',
            'sku' => $product->sku,
            'price' => 100000,
            'stock_quantity' => 10,
            'status' => 'published',
            'stock_status' => 'in_stock',
        ]);

        $response->assertSessionHasErrors('sku');
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->put(route('admin.products.update', $product), [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'sku' => $product->sku,
            'price' => 150000,
            'stock_quantity' => 20,
            'status' => 'draft',
            'stock_status' => 'in_stock',
        ]);

        $this->assertDatabaseHas('products_enhanced', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 150000,
        ]);
    }

    public function test_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('admin.products.destroy', $product));

        $this->assertDatabaseMissing('products_enhanced', ['id' => $product->id]);
    }

    public function test_sale_price_cannot_be_greater_than_price()
    {
        $response = $this->actingAs($this->user)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'description' => 'Description',
            'sku' => 'TEST-SKU-002',
            'price' => 100000,
            'sale_price' => 150000,
            'stock_quantity' => 10,
            'status' => 'published',
            'stock_status' => 'in_stock',
        ]);

        $response->assertSessionHasErrors('sale_price');
    }
}
