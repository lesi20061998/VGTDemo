<?php

namespace Database\Seeders;

use App\Models\ProjectBrand;
use App\Models\ProjectProduct;
use App\Models\ProjectProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ProjectSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds for a specific project.
     */
    public function run($projectCode = 'hd001')
    {
        // Set up project database connection
        Config::set('database.connections.project', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => 'project_'.strtolower($projectCode),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        DB::purge('project');
        DB::setDefaultConnection('project');

        $this->command->info("Seeding sample data for project: {$projectCode}");
        $this->command->info('Database: project_'.strtolower($projectCode));

        // Seed categories first
        $this->seedCategories();

        // Seed brands
        $this->seedBrands();

        // Seed products
        $this->seedProducts();

        $this->command->info('Sample data seeded successfully!');
    }

    private function seedCategories()
    {
        $this->command->info('Checking existing categories...');

        $existingCount = ProjectProductCategory::count();
        if ($existingCount > 0) {
            $this->command->info("Categories already exist: {$existingCount} categories found. Skipping category seeding.");

            return;
        }

        $this->command->info('Seeding categories...');

        $categories = [
            [
                'name' => 'Điện tử',
                'slug' => 'dien-tu',
                'description' => 'Các sản phẩm điện tử tiêu dùng',
                'parent_id' => null,
                'level' => 0,
                'sort_order' => 1,
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Điện thoại',
                        'slug' => 'dien-thoai',
                        'description' => 'Smartphone và điện thoại di động',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Laptop',
                        'slug' => 'laptop',
                        'description' => 'Máy tính xách tay',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Tai nghe',
                        'slug' => 'tai-nghe',
                        'description' => 'Tai nghe và thiết bị âm thanh',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Thời trang',
                'slug' => 'thoi-trang',
                'description' => 'Quần áo và phụ kiện thời trang',
                'parent_id' => null,
                'level' => 0,
                'sort_order' => 2,
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Áo nam',
                        'slug' => 'ao-nam',
                        'description' => 'Áo sơ mi, áo thun nam',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Quần nam',
                        'slug' => 'quan-nam',
                        'description' => 'Quần jeans, quần kaki nam',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Giày dép',
                        'slug' => 'giay-dep',
                        'description' => 'Giày thể thao, giày da',
                        'sort_order' => 3,
                    ],
                ],
            ],
            [
                'name' => 'Gia dụng',
                'slug' => 'gia-dung',
                'description' => 'Đồ gia dụng và nội thất',
                'parent_id' => null,
                'level' => 0,
                'sort_order' => 3,
                'is_active' => true,
                'children' => [
                    [
                        'name' => 'Nồi cơm điện',
                        'slug' => 'noi-com-dien',
                        'description' => 'Nồi cơm điện các loại',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Máy lạnh',
                        'slug' => 'may-lanh',
                        'description' => 'Điều hòa không khí',
                        'sort_order' => 2,
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = ProjectProductCategory::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                $childData['level'] = 1;
                $childData['path'] = $parent->id;
                $childData['is_active'] = true;

                ProjectProductCategory::create($childData);
            }
        }

        $this->command->info('Categories seeded: '.ProjectProductCategory::count());
    }

    private function seedBrands()
    {
        $this->command->info('Seeding brands...');

        $brands = [
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'description' => 'Thương hiệu điện tử hàng đầu Hàn Quốc',
                'is_active' => true,
            ],
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'description' => 'Thương hiệu công nghệ cao cấp',
                'is_active' => true,
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'Thương hiệu thể thao nổi tiếng',
                'is_active' => true,
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'Thương hiệu thể thao Đức',
                'is_active' => true,
            ],
            [
                'name' => 'Uniqlo',
                'slug' => 'uniqlo',
                'description' => 'Thương hiệu thời trang Nhật Bản',
                'is_active' => true,
            ],
            [
                'name' => 'Panasonic',
                'slug' => 'panasonic',
                'description' => 'Thương hiệu gia dụng Nhật Bản',
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            ProjectBrand::create($brand);
        }

        $this->command->info('Brands seeded: '.ProjectBrand::count());
    }

    private function seedProducts()
    {
        $this->command->info('Seeding products...');

        $categories = ProjectProductCategory::all();
        $brands = ProjectBrand::all();

        $products = [
            // Điện tử - Điện thoại
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'short_description' => 'Flagship smartphone cao cấp với camera 200MP',
                'description' => '<p>Samsung Galaxy S24 Ultra là chiếc smartphone flagship cao cấp nhất của Samsung với nhiều tính năng vượt trội:</p><ul><li>Camera chính 200MP với zoom quang học 10x</li><li>Màn hình Dynamic AMOLED 6.8 inch</li><li>Chip Snapdragon 8 Gen 3</li><li>RAM 12GB, bộ nhớ 256GB</li><li>Pin 5000mAh với sạc nhanh 45W</li></ul>',
                'sku' => 'SAM-S24U-256',
                'price' => 29990000,
                'sale_price' => 27990000,
                'stock_quantity' => 50,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'product_category_id' => $categories->where('slug', 'dien-thoai')->first()?->id,
                'brand_id' => $brands->where('slug', 'samsung')->first()?->id,
                'status' => 'published',
                'is_featured' => true,
                'has_price' => true,
                'product_type' => 'simple',
                'views' => 1250,
                'rating_average' => 4.8,
                'rating_count' => 156,
                'language_id' => 1, // Vietnamese
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'short_description' => 'iPhone cao cấp với chip A17 Pro và camera tiên tiến',
                'description' => '<p>iPhone 15 Pro Max - đỉnh cao công nghệ từ Apple:</p><ul><li>Chip A17 Pro 3nm mạnh mẽ</li><li>Camera chính 48MP với zoom quang học 5x</li><li>Màn hình Super Retina XDR 6.7 inch</li><li>Khung titan cao cấp</li><li>Cổng USB-C</li></ul>',
                'sku' => 'APL-IP15PM-256',
                'price' => 34990000,
                'sale_price' => null,
                'stock_quantity' => 30,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'product_category_id' => $categories->where('slug', 'dien-thoai')->first()?->id,
                'brand_id' => $brands->where('slug', 'apple')->first()?->id,
                'status' => 'published',
                'is_featured' => true,
                'has_price' => true,
                'product_type' => 'simple',
                'views' => 2100,
                'rating_average' => 4.9,
                'rating_count' => 89,
                'language_id' => 1, // Vietnamese
            ],

            // Điện tử - Laptop
            [
                'name' => 'MacBook Pro 14 inch M3',
                'slug' => 'macbook-pro-14-m3',
                'short_description' => 'Laptop chuyên nghiệp với chip M3 mạnh mẽ',
                'description' => '<p>MacBook Pro 14 inch với chip M3 - sức mạnh cho chuyên gia:</p><ul><li>Chip Apple M3 8-core CPU, 10-core GPU</li><li>RAM 16GB, SSD 512GB</li><li>Màn hình Liquid Retina XDR 14.2 inch</li><li>Pin lên đến 18 giờ</li><li>Cổng Thunderbolt 4, HDMI, SD card</li></ul>',
                'sku' => 'APL-MBP14-M3-512',
                'price' => 52990000,
                'sale_price' => 49990000,
                'stock_quantity' => 15,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'product_category_id' => $categories->where('slug', 'laptop')->first()?->id,
                'brand_id' => $brands->where('slug', 'apple')->first()?->id,
                'status' => 'published',
                'is_featured' => true,
                'has_price' => true,
                'product_type' => 'simple',
                'views' => 890,
                'rating_average' => 4.7,
                'rating_count' => 45,
                'language_id' => 1, // Vietnamese
            ],

            // Thời trang - Áo nam
            [
                'name' => 'Áo thun nam Uniqlo Dry-EX',
                'slug' => 'ao-thun-nam-uniqlo-dry-ex',
                'short_description' => 'Áo thun thể thao nam với công nghệ thấm hút mồ hôi',
                'description' => '<p>Áo thun nam Uniqlo Dry-EX với công nghệ tiên tiến:</p><ul><li>Chất liệu polyester thoáng khí</li><li>Công nghệ Dry-EX thấm hút mồ hôi nhanh</li><li>Form dáng regular fit thoải mái</li><li>Nhiều màu sắc lựa chọn</li><li>Dễ dàng bảo quản và giặt ủi</li></ul>',
                'sku' => 'UNI-DRYEX-M',
                'price' => 390000,
                'sale_price' => 290000,
                'stock_quantity' => 200,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'product_category_id' => $categories->where('slug', 'ao-nam')->first()?->id,
                'brand_id' => $brands->where('slug', 'uniqlo')->first()?->id,
                'status' => 'published',
                'is_featured' => false,
                'has_price' => true,
                'product_type' => 'simple',
                'views' => 567,
                'rating_average' => 4.3,
                'rating_count' => 234,
                'language_id' => 1, // Vietnamese
            ],

            // Thời trang - Giày dép
            [
                'name' => 'Nike Air Max 270',
                'slug' => 'nike-air-max-270',
                'short_description' => 'Giày thể thao nam với đệm khí Max Air lớn nhất',
                'description' => '<p>Nike Air Max 270 - đỉnh cao của công nghệ đệm khí:</p><ul><li>Đệm khí Max Air 270 độ lớn nhất từ trước đến nay</li><li>Upper mesh thoáng khí</li><li>Đế ngoài cao su bền bỉ</li><li>Thiết kế hiện đại, năng động</li><li>Phù hợp cho chạy bộ và hoạt động thể thao</li></ul>',
                'sku' => 'NIK-AM270-42',
                'price' => 3200000,
                'sale_price' => 2800000,
                'stock_quantity' => 80,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'product_category_id' => $categories->where('slug', 'giay-dep')->first()?->id,
                'brand_id' => $brands->where('slug', 'nike')->first()?->id,
                'status' => 'published',
                'is_featured' => true,
                'has_price' => true,
                'product_type' => 'simple',
                'views' => 1456,
                'rating_average' => 4.6,
                'rating_count' => 178,
                'language_id' => 1, // Vietnamese
            ],

            // Gia dụng - Nồi cơm điện
            [
                'name' => 'Nồi cơm điện Panasonic 1.8L',
                'slug' => 'noi-com-dien-panasonic-18l',
                'short_description' => 'Nồi cơm điện cao cấp với công nghệ nấu 3D',
                'description' => '<p>Nồi cơm điện Panasonic 1.8L với công nghệ tiên tiến:</p><ul><li>Dung tích 1.8L phù hợp cho 4-6 người</li><li>Công nghệ nấu 3D đều nhiệt</li><li>Lòng nồi chống dính cao cấp</li><li>Chức năng hẹn giờ và giữ ấm</li><li>Thiết kế sang trọng, dễ sử dụng</li></ul>',
                'sku' => 'PAN-RC18-BK',
                'price' => 2890000,
                'sale_price' => 2490000,
                'stock_quantity' => 45,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'product_category_id' => $categories->where('slug', 'noi-com-dien')->first()?->id,
                'brand_id' => $brands->where('slug', 'panasonic')->first()?->id,
                'status' => 'published',
                'is_featured' => false,
                'has_price' => true,
                'product_type' => 'simple',
                'views' => 678,
                'rating_average' => 4.4,
                'rating_count' => 92,
            ],

            // Sản phẩm nháp
            [
                'name' => 'Adidas Ultraboost 22',
                'slug' => 'adidas-ultraboost-22',
                'short_description' => 'Giày chạy bộ cao cấp với công nghệ Boost',
                'description' => '<p>Adidas Ultraboost 22 - giày chạy bộ hàng đầu:</p><ul><li>Công nghệ đệm Boost trả năng lượng</li><li>Upper Primeknit+ co giãn 4 chiều</li><li>Đế Continental Rubber bám đường tốt</li><li>Thiết kế thời trang, hiện đại</li></ul>',
                'sku' => 'ADI-UB22-42',
                'price' => 4200000,
                'sale_price' => null,
                'stock_quantity' => 25,
                'manage_stock' => true,
                'stock_status' => 'in_stock',
                'product_category_id' => $categories->where('slug', 'giay-dep')->first()?->id,
                'brand_id' => $brands->where('slug', 'adidas')->first()?->id,
                'status' => 'draft',
                'is_featured' => false,
                'has_price' => true,
                'product_type' => 'simple',
                'views' => 234,
                'rating_average' => 0,
                'rating_count' => 0,
            ],

            // Sản phẩm hết hàng
            [
                'name' => 'Samsung Galaxy Buds2 Pro',
                'slug' => 'samsung-galaxy-buds2-pro',
                'short_description' => 'Tai nghe không dây cao cấp với chống ồn chủ động',
                'description' => '<p>Samsung Galaxy Buds2 Pro - tai nghe không dây đỉnh cao:</p><ul><li>Chống ồn chủ động ANC tiên tiến</li><li>Âm thanh Hi-Fi 24bit</li><li>Pin 8 giờ + 20 giờ với case</li><li>Chống nước IPX7</li><li>Kết nối Bluetooth 5.3</li></ul>',
                'sku' => 'SAM-GB2P-BK',
                'price' => 4990000,
                'sale_price' => 4490000,
                'stock_quantity' => 0,
                'manage_stock' => true,
                'stock_status' => 'out_of_stock',
                'product_category_id' => $categories->where('slug', 'tai-nghe')->first()?->id,
                'brand_id' => $brands->where('slug', 'samsung')->first()?->id,
                'status' => 'published',
                'is_featured' => false,
                'has_price' => true,
                'product_type' => 'simple',
                'views' => 892,
                'rating_average' => 4.5,
                'rating_count' => 67,
            ],
        ];

        foreach ($products as $product) {
            ProjectProduct::create($product);
        }

        $this->command->info('Products seeded: '.ProjectProduct::count());
    }
}
