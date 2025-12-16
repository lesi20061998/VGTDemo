<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\ProductCategory;
use App\Models\AttributeGroup;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ProductAttributeService;
use Illuminate\Support\Str;

class EcommerceSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        $this->truncateTable('order_status_histories');
        $this->truncateTable('order_items');
        $this->truncateTable('orders');
        $this->truncateTable('product_reviews');
        $this->truncateTable('product_attribute_value_mappings');
        $this->truncateTable('product_variations');
        $this->truncateTable('products_enhanced');
        $this->truncateTable('product_attribute_values');
        $this->truncateTable('product_attributes');
        $this->truncateTable('attribute_groups');
        $this->truncateTable('product_categories');
        $this->truncateTable('brands');

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create brands
        $brands = collect([
            ['name' => 'Nike', 'description' => 'Nike - Thương hiệu giày thể thao hàng đầu thế giới'],
            ['name' => 'Adidas', 'description' => 'Adidas - Thiết bị thể thao chất lượng cao'],
            ['name' => 'Puma', 'description' => 'Puma - Giày thể thao phong cách và hiệu năng'],
            ['name' => 'Mizuno', 'description' => 'Mizuno - Thương hiệu Nhật Bản huyền thoại'],
        ])->map(fn($brand) => Brand::factory()->create($brand));

        // Create product categories
        $mainCategories = collect([
            ['name' => 'Giày đá banh', 'description' => 'Giày đá banh chuyên nghiệp'],
            ['name' => 'Phụ kiện thể thao', 'description' => 'Các phụ kiện thể thao chất lượng cao'],
            ['name' => 'Quần áo thể thao', 'description' => 'Quần áo thể thao nam nữ'],
        ])->map(fn($cat) => ProductCategory::factory()->create($cat));

        // Create subcategories
        $mainCategories->each(function($parent) {
            ProductCategory::factory(2)->withParent($parent)->create();
        });

        // Create attribute groups
        $colorGroup = AttributeGroup::factory()->create(['name' => 'Màu sắc', 'slug' => 'mau-sac']);
        $sizeGroup = AttributeGroup::factory()->create(['name' => 'Kích thước', 'slug' => 'kich-thuoc']);
        $materialGroup = AttributeGroup::factory()->create(['name' => 'Chất liệu', 'slug' => 'chat-lieu']);

        // Create color attribute and values
        $colorAttr = ProductAttribute::factory()->create([
            'attribute_group_id' => $colorGroup->id,
            'name' => 'Màu sắc',
            'slug' => 'mau-sac',
            'type' => 'color'
        ]);

        collect(['Red' => '#FF0000', 'Blue' => '#0000FF', 'Black' => '#000000', 'White' => '#FFFFFF', 'Green' => '#00FF00'])
            ->each(fn($code, $name) => ProductAttributeValue::factory()->create([
                'product_attribute_id' => $colorAttr->id,
                'value' => Str::lower($name),
                'display_value' => $name,
                'color_code' => $code,
            ]));

        // Create size attribute and values
        $sizeAttr = ProductAttribute::factory()->create([
            'attribute_group_id' => $sizeGroup->id,
            'name' => 'Kích thước',
            'slug' => 'kich-thuoc',
            'type' => 'select'
        ]);

        collect(['35', '36', '37', '38', '39', '40', '41', '42'])
            ->each(fn($size, $idx) => ProductAttributeValue::factory()->create([
                'product_attribute_id' => $sizeAttr->id,
                'value' => $size,
                'display_value' => 'Size ' . $size,
                'sort_order' => $idx,
            ]));

        // Create material attribute and values
        $materialAttr = ProductAttribute::factory()->create([
            'attribute_group_id' => $materialGroup->id,
            'name' => 'Chất liệu',
            'slug' => 'chat-lieu',
            'type' => 'multiselect'
        ]);

        collect(['Leather' => 'Da thật', 'Synthetic' => 'Da tổng hợp', 'Canvas' => 'Vải canvas', 'Mesh' => 'Lưới thoáng khí'])
            ->each(fn($display, $value) => ProductAttributeValue::factory()->create([
                'product_attribute_id' => $materialAttr->id,
                'value' => Str::lower($value),
                'display_value' => $display,
            ]));

        // Create products with variations and attributes
        Product::factory(10)
            ->published()
            ->state(fn() => [
                'brand_id' => $brands->random()->id,
                'product_category_id' => $mainCategories->random()->id,
            ])
            ->create()
            ->each(function($product) use ($colorAttr, $sizeAttr, $materialAttr) {
                // Create 3-5 variations per product
                ProductVariation::factory(rand(3, 5))->create([
                    'product_id' => $product->id,
                ]);

                // Create reviews
                ProductReview::factory(rand(3, 8))->approved()->create(['product_id' => $product->id]);

                // Assign attributes to product using the 3-layer system
                $service = app(ProductAttributeService::class);

                // Get random colors and sizes
                $colorValues = ProductAttributeValue::where('product_attribute_id', $colorAttr->id)
                    ->inRandomOrder()
                    ->limit(rand(1, 2))
                    ->pluck('id')
                    ->toArray();

                $sizeValues = ProductAttributeValue::where('product_attribute_id', $sizeAttr->id)
                    ->inRandomOrder()
                    ->limit(rand(2, 4))
                    ->pluck('id')
                    ->toArray();

                $materialValues = ProductAttributeValue::where('product_attribute_id', $materialAttr->id)
                    ->inRandomOrder()
                    ->limit(rand(1, 2))
                    ->pluck('id')
                    ->toArray();

                // Assign using service - the 3-layer system is used internally
                $service->assignAttributes($product, [
                    $colorAttr->id => $colorValues,
                    $sizeAttr->id => $sizeValues,
                    $materialAttr->id => $materialValues,
                ]);
            });

        // Create orders with items
        Order::factory(20)
            ->create()
            ->each(function($order) {
                // Create 2-5 order items per order
                $itemCount = rand(2, 5);
                foreach (range(1, $itemCount) as $i) {
                    $product = Product::inRandomOrder()->first();
                    $quantity = rand(1, 5);
                    $unitPrice = $product->sale_price ?: $product->price;

                    OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'total_price' => $unitPrice * $quantity,
                    ]);
                }

                // Update order totals
                $items = $order->items;
                $subtotal = $items->sum('total_price');
                $taxAmount = $subtotal * 0.1;
                $shippingAmount = rand(30000, 200000);
                $discountAmount = rand(0, 1) ? $subtotal * 0.05 : 0;

                $order->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'shipping_amount' => $shippingAmount,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $subtotal + $taxAmount + $shippingAmount - $discountAmount,
                ]);
            });

        $this->command->info('Ecommerce data seeded successfully!');
    }

    private function truncateTable(string $table): void
    {
        if (\Schema::hasTable($table)) {
            \DB::table($table)->truncate();
        }
    }
}
