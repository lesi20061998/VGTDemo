<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminProductMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_product_with_featured_image_and_gallery(): void
    {
        Storage::fake(config('admin.media.storage_disk', 'public'));

        $this->withoutMiddleware(); // routes use 'admin' middleware — tests bypass for simplicity

        $user = User::factory()->create();

        $payload = [
            'name' => 'Sản phẩm test',
            'description' => 'Mô tả',
            'sku' => 'SKU-TEST-001',
            'price' => 100000,
            'product_category_id' => 1,
        ];

        $featured = UploadedFile::fake()->image('featured.jpg');
        $gallery = [
            UploadedFile::fake()->image('g1.jpg'),
            UploadedFile::fake()->image('g2.jpg'),
        ];

        $response = $this->actingAs($user)->post(route('admin.products.store'), array_merge($payload, [
            'featured_image' => $featured,
            'images' => $gallery,
        ]));

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'SKU-TEST-001')->first();
        $this->assertNotNull($product);

        // featured
        $this->assertNotNull($product->getFirstMedia('featured_image'));

        // gallery
        $this->assertCount(2, $product->getMedia('images'));
    }
}
