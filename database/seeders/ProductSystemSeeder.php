<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductEnhanced;
use App\Models\ProductReview;
use Illuminate\Support\Str;

class ProductSystemSeeder extends Seeder
{
    public function run()
    {
        // Xóa dữ liệu cũ nếu bảng tồn tại
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if (\Schema::hasTable('products_enhanced')) {
            \DB::table('products_enhanced')->truncate();
        }
        if (\Schema::hasTable('product_reviews')) {
            \DB::table('product_reviews')->truncate();
        }
        if (\Schema::hasTable('product_attribute_values')) {
            \DB::table('product_attribute_values')->truncate();
        }
        if (\Schema::hasTable('product_attributes')) {
            \DB::table('product_attributes')->truncate();
        }
        if (\Schema::hasTable('product_categories')) {
            \DB::table('product_categories')->truncate();
        }
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Tạo danh mục sản phẩm
        $categories = [
            ['name' => 'Giày đá banh', 'description' => 'Giày đá banh chuyên nghiệp'],
            ['name' => 'Phụ kiện thể thao', 'description' => 'Các phụ kiện thể thao'],
            ['name' => 'Quần áo thể thao', 'description' => 'Quần áo thể thao nam nữ'],
        ];

        foreach ($categories as $categoryData) {
            ProductCategory::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'is_active' => true,
                'sort_order' => 0,
            ]);
        }

        // Tạo thuộc tính sản phẩm
        $attributes = [
            [
                'name' => 'Màu sắc',
                'type' => 'color',
                'values' => [
                    ['value' => 'red', 'display_value' => 'Đỏ', 'color_code' => '#FF0000'],
                    ['value' => 'blue', 'display_value' => 'Xanh dương', 'color_code' => '#0000FF'],
                    ['value' => 'black', 'display_value' => 'Đen', 'color_code' => '#000000'],
                    ['value' => 'white', 'display_value' => 'Trắng', 'color_code' => '#FFFFFF'],
                    ['value' => 'green', 'display_value' => 'Xanh lá', 'color_code' => '#00FF00'],
                ]
            ],
            [
                'name' => 'Kích thước',
                'type' => 'select',
                'values' => [
                    ['value' => '35', 'display_value' => '35'],
                    ['value' => '36', 'display_value' => '36'],
                    ['value' => '37', 'display_value' => '37'],
                    ['value' => '38', 'display_value' => '38'],
                    ['value' => '39', 'display_value' => '39'],
                    ['value' => '40', 'display_value' => '40'],
                    ['value' => '41', 'display_value' => '41'],
                    ['value' => '42', 'display_value' => '42'],
                ]
            ],
            [
                'name' => 'Thương hiệu',
                'type' => 'select',
                'values' => [
                    ['value' => 'nike', 'display_value' => 'Nike'],
                    ['value' => 'adidas', 'display_value' => 'Adidas'],
                    ['value' => 'puma', 'display_value' => 'Puma'],
                    ['value' => 'mizuno', 'display_value' => 'Mizuno'],
                ]
            ],
            [
                'name' => 'Chất liệu',
                'type' => 'multiselect',
                'values' => [
                    ['value' => 'leather', 'display_value' => 'Da thật'],
                    ['value' => 'synthetic', 'display_value' => 'Da tổng hợp'],
                    ['value' => 'canvas', 'display_value' => 'Vải canvas'],
                    ['value' => 'mesh', 'display_value' => 'Lưới thoáng khí'],
                ]
            ],
            [
                'name' => 'Loại sân',
                'type' => 'select',
                'values' => [
                    ['value' => 'natural_grass', 'display_value' => 'Sân cỏ tự nhiên'],
                    ['value' => 'artificial_grass', 'display_value' => 'Sân cỏ nhân tạo'],
                    ['value' => 'indoor', 'display_value' => 'Sân trong nhà'],
                ]
            ]
        ];

        foreach ($attributes as $attrData) {
            $attribute = ProductAttribute::create([
                'name' => $attrData['name'],
                'slug' => Str::slug($attrData['name']),
                'type' => $attrData['type'],
                'is_filterable' => true,
                'is_required' => false,
                'sort_order' => 0,
            ]);

            foreach ($attrData['values'] as $index => $valueData) {
                ProductAttributeValue::create([
                    'product_attribute_id' => $attribute->id,
                    'value' => $valueData['value'],
                    'display_value' => $valueData['display_value'],
                    'color_code' => $valueData['color_code'] ?? null,
                    'sort_order' => $index,
                ]);
            }
        }

        // Tạo sản phẩm mẫu
        $products = [
            [
                'name' => 'Giày đá banh Nike Mercurial Vapor',
                'short_description' => 'Giày đá banh chuyên nghiệp Nike Mercurial Vapor với công nghệ tiên tiến',
                'description' => 'Giày đá banh Nike Mercurial Vapor được thiết kế dành cho những cầu thủ tốc độ. Với upper Vaporposite+ và đế ngoài được tối ưu hóa, đôi giày này mang lại cảm giác bóng tuyệt vời và tốc độ bùng nổ trên sân.',
                'sku' => 'NIKE-MV-001',
                'price' => 2500000,
                'sale_price' => 2200000,
                'stock_quantity' => 50,
                'category_id' => 1,
                'badges' => ['hot', 'sale'],
                'attributes' => [
                    1 => [1, 2], // Màu đỏ, xanh dương
                    2 => [3, 4, 5], // Size 37, 38, 39
                    3 => [1], // Nike
                    4 => [2], // Da tổng hợp
                    5 => [1], // Sân cỏ tự nhiên
                ]
            ],
            [
                'name' => 'Giày đá banh Adidas Predator',
                'short_description' => 'Giày đá banh Adidas Predator với công nghệ Control Frame',
                'description' => 'Adidas Predator là dòng giày đá banh huyền thoại được thiết kế để tối ưu hóa khả năng kiểm soát bóng. Với công nghệ Control Frame và đế ngoài Controlskin, đôi giày này giúp bạn có những cú sút chính xác nhất.',
                'sku' => 'ADIDAS-PRD-001',
                'price' => 2800000,
                'sale_price' => null,
                'stock_quantity' => 30,
                'category_id' => 1,
                'badges' => ['new'],
                'attributes' => [
                    1 => [3, 4], // Đen, trắng
                    2 => [4, 5, 6], // Size 38, 39, 40
                    3 => [2], // Adidas
                    4 => [1], // Da thật
                    5 => [2], // Sân cỏ nhân tạo
                ]
            ],
            [
                'name' => 'Giày đá banh Puma Future',
                'short_description' => 'Giày đá banh Puma Future với thiết kế không dây giày',
                'description' => 'Puma Future mang đến sự tự do tuyệt đối với thiết kế không dây giày độc đáo. Công nghệ NETFIT cho phép tùy chỉnh độ ôm chân theo ý muốn, mang lại sự thoải mái và hiệu suất tối ưu.',
                'sku' => 'PUMA-FUT-001',
                'price' => 2300000,
                'sale_price' => 1950000,
                'stock_quantity' => 25,
                'category_id' => 1,
                'badges' => ['sale', 'bestseller'],
                'attributes' => [
                    1 => [2, 5], // Xanh dương, xanh lá
                    2 => [2, 3, 4], // Size 36, 37, 38
                    3 => [3], // Puma
                    4 => [2, 4], // Da tổng hợp, lưới
                    5 => [3], // Sân trong nhà
                ]
            ],
            [
                'name' => 'Giày đá banh Mizuno Morelia',
                'short_description' => 'Giày đá banh Mizuno Morelia da kangaroo cao cấp',
                'description' => 'Mizuno Morelia là biểu tượng của sự hoàn hảo trong thiết kế giày đá banh. Được làm từ da kangaroo cao cấp, đôi giày này mang lại cảm giác bóng tự nhiên nhất và độ bền vượt trội.',
                'sku' => 'MIZUNO-MOR-001',
                'price' => 3200000,
                'sale_price' => null,
                'stock_quantity' => 15,
                'category_id' => 1,
                'badges' => ['premium'],
                'attributes' => [
                    1 => [3, 4], // Đen, trắng
                    2 => [5, 6, 7], // Size 39, 40, 41
                    3 => [4], // Mizuno
                    4 => [1], // Da thật
                    5 => [1], // Sân cỏ tự nhiên
                ]
            ],
            [
                'name' => 'Bóng đá FIFA Quality Pro',
                'short_description' => 'Bóng đá chính thức FIFA Quality Pro cho thi đấu chuyên nghiệp',
                'description' => 'Bóng đá FIFA Quality Pro được sản xuất theo tiêu chuẩn quốc tế, phù hợp cho các giải đấu chuyên nghiệp. Với cấu trúc 32 miếng da và công nghệ chống thấm nước.',
                'sku' => 'BALL-FIFA-001',
                'price' => 850000,
                'sale_price' => 750000,
                'stock_quantity' => 100,
                'category_id' => 2,
                'badges' => ['sale'],
                'attributes' => [
                    1 => [4, 1], // Trắng, đỏ
                ]
            ]
        ];

        foreach ($products as $productData) {
            $product = ProductEnhanced::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'short_description' => $productData['short_description'],
                'description' => $productData['description'],
                'sku' => $productData['sku'],
                'price' => $productData['price'],
                'sale_price' => $productData['sale_price'],
                'stock_quantity' => $productData['stock_quantity'],
                'product_category_id' => $productData['category_id'],
                'status' => 'published',
                'is_featured' => rand(0, 1),
                'badges' => $productData['badges'],
                'featured_image' => '/assets/img/products/product-' . rand(1, 5) . '.jpg',
                'gallery' => [
                    '/assets/img/products/gallery-1.jpg',
                    '/assets/img/products/gallery-2.jpg',
                    '/assets/img/products/gallery-3.jpg',
                ],
                'meta_title' => $productData['name'],
                'meta_description' => $productData['short_description'],
            ]);

            // Gán thuộc tính cho sản phẩm
            if (isset($productData['attributes'])) {
                foreach ($productData['attributes'] as $attributeId => $valueIds) {
                    foreach ($valueIds as $valueId) {
                        $product->attributeValues()->attach($valueId, [
                            'product_attribute_id' => $attributeId
                        ]);
                    }
                }
            }

            // Tạo đánh giá mẫu
            for ($i = 0; $i < rand(3, 8); $i++) {
                ProductReview::create([
                    'product_id' => $product->id,
                    'reviewer_name' => 'Khách hàng ' . ($i + 1),
                    'reviewer_email' => 'customer' . ($i + 1) . '@example.com',
                    'rating' => rand(3, 5),
                    'title' => 'Sản phẩm tốt',
                    'comment' => 'Sản phẩm chất lượng, giao hàng nhanh, đóng gói cẩn thận.',
                    'status' => 'approved',
                    'is_verified_purchase' => rand(0, 1),
                ]);
            }

            // Cập nhật rating
            $product->updateRating();
        }
    }
}