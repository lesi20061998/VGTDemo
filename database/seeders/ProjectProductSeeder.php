<?php

namespace Database\Seeders;

use App\Models\ProjectBrand;
use App\Models\ProjectProduct;
use App\Models\ProjectProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get project code from environment
        $projectCode = env('CURRENT_PROJECT_CODE', 'hd001');

        // Disable foreign key checks
        \DB::connection('project')->statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        ProjectProduct::truncate();

        // Get categories and brands for relationships
        $categories = ProjectProductCategory::all();
        $brands = ProjectBrand::all();

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run ProjectProductCategorySeeder first.');

            return;
        }

        if ($brands->isEmpty()) {
            $this->command->warn('No brands found. Please run ProjectBrandSeeder first.');

            return;
        }

        // Get project-specific products based on project code
        $products = $this->getProductsForProject($projectCode);

        // Process and create products
        $this->processProducts($products);

        // Re-enable foreign key checks
        \DB::connection('project')->statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✓ Created '.ProjectProduct::count()." project products for {$projectCode}");
    }

    /**
     * Get products data based on project code
     */
    private function getProductsForProject($projectCode): array
    {
        // Different products for different projects
        switch ($projectCode) {
            case 'hd001':
                return $this->getHD001Products();
            case 'abc123':
                return $this->getABC123Products();
            case 'xyz789':
                return $this->getXYZ789Products();
            default:
                return $this->getDefaultProducts();
        }
    }

    /**
     * Products for HD001 project (Electronics & Tech)
     */
    private function getHD001Products(): array
    {
        return [
            [
                'name' => 'iPhone 15 Pro Max',
                'short_description' => 'Điện thoại thông minh cao cấp với chip A17 Pro',
                'description' => '<p>iPhone 15 Pro Max là flagship mới nhất của Apple với nhiều tính năng đột phá. Được trang bị chip A17 Pro mạnh mẽ, camera chuyên nghiệp và thiết kế titanium sang trọng.</p><p>Tính năng nổi bật:</p><ul><li>Chip A17 Pro 3nm</li><li>Camera chính 48MP với zoom quang học 5x</li><li>Màn hình Super Retina XDR 6.7 inch</li><li>Khung viền titanium</li></ul>',
                'price' => 34990000,
                'sale_price' => 32990000,
                'sku' => 'IPHONE-15-PRO-MAX',
                'stock_quantity' => 50,
                'status' => 'published',
                'is_featured' => true,
                'category_name' => 'Điện thoại',
                'brand_name' => 'Apple',
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'short_description' => 'Smartphone Android cao cấp với S Pen tích hợp',
                'description' => '<p>Galaxy S24 Ultra là chiếc smartphone Android hàng đầu của Samsung với S Pen tích hợp và camera zoom 100x Space Zoom.</p><p>Đặc điểm nổi bật:</p><ul><li>Snapdragon 8 Gen 3</li><li>Camera 200MP với zoom 100x</li><li>S Pen tích hợp</li><li>Màn hình Dynamic AMOLED 2X 6.8 inch</li></ul>',
                'price' => 33990000,
                'sale_price' => null,
                'sku' => 'GALAXY-S24-ULTRA',
                'stock_quantity' => 30,
                'status' => 'published',
                'is_featured' => true,
                'category_name' => 'Điện thoại',
                'brand_name' => 'Samsung',
            ],
            [
                'name' => 'MacBook Pro 14 inch M3',
                'short_description' => 'Laptop chuyên nghiệp với chip M3 mạnh mẽ',
                'description' => '<p>MacBook Pro 14 inch với chip M3 mang lại hiệu suất vượt trội cho công việc chuyên nghiệp và sáng tạo.</p><p>Thông số kỹ thuật:</p><ul><li>Chip Apple M3</li><li>RAM 8GB/16GB/32GB</li><li>SSD 512GB/1TB/2TB</li><li>Màn hình Liquid Retina XDR 14.2 inch</li></ul>',
                'price' => 52990000,
                'sale_price' => 49990000,
                'sku' => 'MACBOOK-PRO-14-M3',
                'stock_quantity' => 25,
                'status' => 'published',
                'is_featured' => true,
                'category_name' => 'Laptop',
                'brand_name' => 'Apple',
            ],
            [
                'name' => 'Nike Air Max 270',
                'short_description' => 'Giày thể thao với đệm khí Max Air lớn nhất',
                'description' => '<p>Nike Air Max 270 với đệm khí Max Air lớn nhất từ trước đến nay, mang lại cảm giác êm ái và thoải mái cả ngày.</p><p>Đặc điểm:</p><ul><li>Đệm khí Max Air 270 độ</li><li>Upper mesh thoáng khí</li><li>Đế ngoài cao su bền bỉ</li><li>Thiết kế thời trang</li></ul>',
                'price' => 3290000,
                'sale_price' => 2990000,
                'sku' => 'NIKE-AIR-MAX-270',
                'stock_quantity' => 100,
                'status' => 'published',
                'is_featured' => false,
                'category_name' => 'Giày thể thao',
                'brand_name' => 'Nike',
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'short_description' => 'Giày chạy bộ với công nghệ Boost',
                'description' => '<p>Adidas Ultraboost 22 với công nghệ đệm Boost mang lại năng lượng trả về tối đa cho mỗi bước chạy.</p><p>Công nghệ:</p><ul><li>Đệm Boost responsive</li><li>Upper Primeknit+ co giãn</li><li>Continental rubber outsole</li><li>Torsion System hỗ trợ</li></ul>',
                'price' => 4590000,
                'sale_price' => null,
                'sku' => 'ADIDAS-ULTRABOOST-22',
                'stock_quantity' => 75,
                'status' => 'published',
                'is_featured' => false,
                'category_name' => 'Giày thể thao',
                'brand_name' => 'Adidas',
            ],
            [
                'name' => 'Áo thun Uniqlo Heattech',
                'short_description' => 'Áo thun giữ nhiệt công nghệ Heattech',
                'description' => '<p>Áo thun Heattech của Uniqlo với công nghệ sợi đặc biệt giúp giữ ấm cơ thể trong thời tiết lạnh.</p><p>Tính năng:</p><ul><li>Công nghệ Heattech giữ nhiệt</li><li>Chất liệu mềm mại, co giãn</li><li>Thấm hút mồ hôi tốt</li><li>Thiết kế basic dễ phối đồ</li></ul>',
                'price' => 390000,
                'sale_price' => 290000,
                'sku' => 'UNIQLO-HEATTECH-TEE',
                'stock_quantity' => 200,
                'status' => 'published',
                'is_featured' => false,
                'category_name' => 'Quần áo nam',
                'brand_name' => 'Uniqlo',
            ],
            [
                'name' => 'Váy Zara midi',
                'short_description' => 'Váy midi thanh lịch phong cách Châu Âu',
                'description' => '<p>Váy midi Zara với thiết kế thanh lịch, phù hợp cho cả công sở và dạo phố.</p><p>Chi tiết:</p><ul><li>Chất liệu polyester cao cấp</li><li>Thiết kế A-line tôn dáng</li><li>Màu sắc trung tính dễ phối</li><li>Có thể giặt máy</li></ul>',
                'price' => 1290000,
                'sale_price' => null,
                'sku' => 'ZARA-MIDI-DRESS',
                'stock_quantity' => 60,
                'status' => 'published',
                'is_featured' => false,
                'category_name' => 'Quần áo nữ',
                'brand_name' => 'Zara',
            ],
            [
                'name' => 'Bàn làm việc IKEA BEKANT',
                'short_description' => 'Bàn làm việc hiện đại với chân thép chắc chắn',
                'description' => '<p>Bàn BEKANT của IKEA với thiết kế tối giản, phù hợp cho văn phòng và nhà ở.</p><p>Thông số:</p><ul><li>Kích thước: 160x80 cm</li><li>Chân thép sơn tĩnh điện</li><li>Mặt bàn melamine bền bỉ</li><li>Dễ lắp ráp và vệ sinh</li></ul>',
                'price' => 2990000,
                'sale_price' => 2490000,
                'sku' => 'IKEA-BEKANT-DESK',
                'stock_quantity' => 40,
                'status' => 'published',
                'is_featured' => false,
                'category_name' => 'Đồ nội thất',
                'brand_name' => 'IKEA',
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'short_description' => 'Tai nghe chống ồn cao cấp',
                'description' => '<p>Sony WH-1000XM5 với công nghệ chống ồn hàng đầu và chất lượng âm thanh Hi-Res.</p><p>Tính năng:</p><ul><li>Chống ồn chủ động thế hệ mới</li><li>Pin 30 giờ</li><li>Sạc nhanh 3 phút = 3 giờ nghe</li><li>Hỗ trợ LDAC và Hi-Res Audio</li></ul>',
                'price' => 8990000,
                'sale_price' => null,
                'sku' => 'SONY-WH1000XM5',
                'stock_quantity' => 35,
                'status' => 'published',
                'is_featured' => true,
                'category_name' => 'Phụ kiện điện tử',
                'brand_name' => 'Sony',
            ],
            [
                'name' => 'Sách "Atomic Habits"',
                'short_description' => 'Sách phát triển bản thân về xây dựng thói quen',
                'description' => '<p>"Atomic Habits" của James Clear - cuốn sách hướng dẫn cách xây dựng thói quen tốt và loại bỏ thói quen xấu.</p><p>Nội dung:</p><ul><li>4 quy luật thay đổi hành vi</li><li>Chiến lược xây dựng thói quen</li><li>Cách duy trì động lực</li><li>Ví dụ thực tế dễ áp dụng</li></ul>',
                'price' => 189000,
                'sale_price' => 149000,
                'sku' => 'BOOK-ATOMIC-HABITS',
                'stock_quantity' => 150,
                'status' => 'published',
                'is_featured' => false,
                'category_name' => 'Sách',
                'brand_name' => null,
            ],
            [
                'name' => 'Bút bi Pilot G2',
                'short_description' => 'Bút bi gel mực mịn, viết êm',
                'description' => '<p>Bút bi gel Pilot G2 với mực gel mịn, viết êm và không lem.</p><p>Đặc điểm:</p><ul><li>Mực gel không lem</li><li>Đầu bi 0.7mm</li><li>Thân bút ergonomic</li><li>Có thể thay ruột</li></ul>',
                'price' => 25000,
                'sale_price' => null,
                'sku' => 'PILOT-G2-PEN',
                'stock_quantity' => 500,
                'status' => 'published',
                'is_featured' => false,
                'category_name' => 'Văn phòng phẩm',
                'brand_name' => null,
            ],
            [
                'name' => 'Áo khoác thể thao Nike Dri-FIT',
                'short_description' => 'Áo khoác thể thao công nghệ Dri-FIT',
                'description' => '<p>Áo khoác Nike với công nghệ Dri-FIT giúp thấm hút mồ hôi và giữ cơ thể khô ráo.</p><p>Công nghệ:</p><ul><li>Dri-FIT moisture-wicking</li><li>Chất liệu polyester tái chế</li><li>Thiết kế athletic fit</li><li>Có túi zip an toàn</li></ul>',
                'price' => 1890000,
                'sale_price' => 1590000,
                'sku' => 'NIKE-DRIFIT-JACKET',
                'stock_quantity' => 80,
                'status' => 'published',
                'is_featured' => false,
                'category_name' => 'Quần áo thể thao',
                'brand_name' => 'Nike',
            ],
            [
                'name' => 'LG OLED C3 55 inch',
                'short_description' => 'Smart TV OLED 4K với AI ThinQ',
                'description' => '<p>LG OLED C3 55 inch với công nghệ OLED self-lit pixels mang lại chất lượng hình ảnh tuyệt vời.</p><p>Tính năng:</p><ul><li>OLED evo với Brightness Booster</li><li>α9 Gen6 AI Processor 4K</li><li>webOS 23 với ThinQ AI</li><li>HDMI 2.1 cho gaming 4K@120Hz</li></ul>',
                'price' => 32990000,
                'sale_price' => 29990000,
                'sku' => 'LG-OLED-C3-55',
                'stock_quantity' => 20,
                'status' => 'published',
                'is_featured' => true,
                'category_name' => 'Điện tử',
                'brand_name' => 'LG',
            ],
            [
                'name' => 'Bộ đồ chơi LEGO Creator',
                'short_description' => 'Bộ đồ chơi xếp hình sáng tạo cho trẻ em',
                'description' => '<p>Bộ LEGO Creator giúp trẻ em phát triển tư duy sáng tạo và kỹ năng xây dựng.</p><p>Nội dung:</p><ul><li>500+ chi tiết LEGO</li><li>Hướng dẫn xây dựng 3 mô hình</li><li>Phù hợp từ 8 tuổi trở lên</li><li>Chất liệu ABS an toàn</li></ul>',
                'price' => 1290000,
                'sale_price' => null,
                'sku' => 'LEGO-CREATOR-SET',
                'stock_quantity' => 90,
                'status' => 'draft',
                'is_featured' => false,
                'category_name' => 'Quần áo trẻ em',
                'brand_name' => null,
            ],
            [
                'name' => 'Nồi cơm điện Panasonic 1.8L',
                'short_description' => 'Nồi cơm điện cao cấp với lòng nồi chống dính',
                'description' => '<p>Nồi cơm điện Panasonic 1.8L với công nghệ nấu cơm thông minh và lòng nồi chống dính bền bỉ.</p><p>Tính năng:</p><ul><li>Dung tích 1.8L (8-10 người ăn)</li><li>Lòng nồi chống dính cao cấp</li><li>Chức năng hẹn giờ nấu</li><li>Giữ ấm tự động</li></ul>',
                'price' => 2190000,
                'sale_price' => 1890000,
                'sku' => 'PANASONIC-RICE-COOKER',
                'stock_quantity' => 45,
                'status' => 'published',
                'is_featured' => false,
                'category_name' => 'Đồ dùng nhà bếp',
                'brand_name' => null,
            ],
        ];
    }

    /**
     * Products for ABC123 project (Fashion & Lifestyle)
     */
    private function getABC123Products(): array
    {
        return [
            [
                'name' => 'Áo sơ mi nam công sở',
                'short_description' => 'Áo sơ mi nam chất liệu cotton cao cấp',
                'description' => '<p>Áo sơ mi nam công sở với chất liệu cotton 100% cao cấp, thiết kế thanh lịch phù hợp cho môi trường công sở.</p>',
                'price' => 450000,
                'sale_price' => 350000,
                'sku' => 'SHIRT-MEN-001',
                'stock_quantity' => 100,
                'status' => 'published',
                'is_featured' => true,
                'category_name' => 'Quần áo nam',
                'brand_name' => 'Uniqlo',
            ],
            [
                'name' => 'Váy công sở nữ',
                'short_description' => 'Váy công sở nữ thanh lịch',
                'description' => '<p>Váy công sở nữ với thiết kế thanh lịch, chất liệu polyester cao cấp.</p>',
                'price' => 650000,
                'sale_price' => null,
                'sku' => 'DRESS-WOMEN-001',
                'stock_quantity' => 80,
                'status' => 'published',
                'is_featured' => true,
                'category_name' => 'Quần áo nữ',
                'brand_name' => 'Zara',
            ],
            // Add more fashion products...
        ];
    }

    /**
     * Products for XYZ789 project (Home & Garden)
     */
    private function getXYZ789Products(): array
    {
        return [
            [
                'name' => 'Bộ bàn ăn gỗ sồi',
                'short_description' => 'Bộ bàn ăn 6 ghế gỗ sồi tự nhiên',
                'description' => '<p>Bộ bàn ăn cao cấp làm từ gỗ sồi tự nhiên, bao gồm 1 bàn và 6 ghế.</p>',
                'price' => 15000000,
                'sale_price' => 12000000,
                'sku' => 'DINING-SET-001',
                'stock_quantity' => 10,
                'status' => 'published',
                'is_featured' => true,
                'category_name' => 'Đồ nội thất',
                'brand_name' => 'IKEA',
            ],
            // Add more home products...
        ];
    }

    /**
     * Default products for unknown projects
     */
    private function getDefaultProducts(): array
    {
        return [
            [
                'name' => 'Sản phẩm mẫu',
                'short_description' => 'Đây là sản phẩm mẫu',
                'description' => '<p>Mô tả sản phẩm mẫu</p>',
                'price' => 100000,
                'sale_price' => null,
                'sku' => 'SAMPLE-001',
                'stock_quantity' => 50,
                'status' => 'published',
                'is_featured' => false,
                'category_name' => 'Điện tử',
                'brand_name' => null,
            ],
        ];
    }

    /**
     * Process products data and create records
     */
    private function processProducts(array $products): void
    {
        // Get categories and brands for relationships
        $categories = ProjectProductCategory::all();
        $brands = ProjectBrand::all();

        foreach ($products as $productData) {
            // Find category
            $category = null;
            if ($productData['category_name']) {
                $category = $categories->where('name', $productData['category_name'])->first();
            }

            // Find brand
            $brand = null;
            if ($productData['brand_name']) {
                $brand = $brands->where('name', $productData['brand_name'])->first();
            }

            // Get default language
            $defaultLanguage = \App\Models\Language::getDefault();

            // Create product
            ProjectProduct::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'short_description' => $productData['short_description'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'sale_price' => $productData['sale_price'],
                'sku' => $productData['sku'],
                'stock_quantity' => $productData['stock_quantity'],
                'stock_status' => $productData['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock',
                'manage_stock' => true,
                'status' => $productData['status'],
                'is_featured' => $productData['is_featured'],
                'has_price' => ! empty($productData['price']),
                'product_category_id' => $category?->id,
                'brand_id' => $brand?->id,
                'product_type' => 'simple',
                'language_id' => 1, // Default to Vietnamese
                'views' => rand(0, 1000),
                'rating_average' => rand(35, 50) / 10, // 3.5 - 5.0
                'rating_count' => rand(0, 100),
                'meta_title' => $productData['name'],
                'meta_description' => $productData['short_description'],
                'noindex' => false,
            ]);
        }
    }
}
