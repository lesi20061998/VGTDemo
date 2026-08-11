<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiVGT2WebsiteDataSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo categories
        $categories = [
            ['name' => 'Điện thoại', 'slug' => 'dien-thoai', 'description' => 'Điện thoại thông minh', 'is_active' => 1],
            ['name' => 'Laptop', 'slug' => 'laptop', 'description' => 'Máy tính xách tay', 'is_active' => 1],
            ['name' => 'Phụ kiện', 'slug' => 'phu-kien', 'description' => 'Phụ kiện công nghệ', 'is_active' => 1],
            ['name' => 'Tablet', 'slug' => 'tablet', 'description' => 'Máy tính bảng', 'is_active' => 1],
        ];

        foreach ($categories as $category) {
            DB::table('product_categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Tạo brands
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple', 'description' => 'Thương hiệu Apple', 'is_active' => 1],
            ['name' => 'Samsung', 'slug' => 'samsung', 'description' => 'Thương hiệu Samsung', 'is_active' => 1],
            ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'description' => 'Thương hiệu Xiaomi', 'is_active' => 1],
            ['name' => 'Dell', 'slug' => 'dell', 'description' => 'Thương hiệu Dell', 'is_active' => 1],
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->insert(array_merge($brand, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Tạo products
        $products = [
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'description' => 'iPhone 15 Pro Max mới nhất từ Apple',
                'short_description' => 'Điện thoại cao cấp với chip A17 Pro',
                'price' => 29990000,
                'sale_price' => 27990000,
                'sku' => 'IP15PM001',
                'stock_quantity' => 50,
                'category_id' => 1,
                'brand_id' => 1,
                'is_active' => 1,
                'is_featured' => 1,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'description' => 'Samsung Galaxy S24 Ultra với S Pen',
                'short_description' => 'Flagship Android với camera 200MP',
                'price' => 26990000,
                'sale_price' => 24990000,
                'sku' => 'SGS24U001',
                'stock_quantity' => 30,
                'category_id' => 1,
                'brand_id' => 2,
                'is_active' => 1,
                'is_featured' => 1,
            ],
            [
                'name' => 'MacBook Pro M3',
                'slug' => 'macbook-pro-m3',
                'description' => 'MacBook Pro với chip M3 mạnh mẽ',
                'short_description' => 'Laptop chuyên nghiệp cho developer',
                'price' => 45990000,
                'sale_price' => 43990000,
                'sku' => 'MBP3001',
                'stock_quantity' => 20,
                'category_id' => 2,
                'brand_id' => 1,
                'is_active' => 1,
                'is_featured' => 1,
            ],
            [
                'name' => 'Dell XPS 13',
                'slug' => 'dell-xps-13',
                'description' => 'Dell XPS 13 ultrabook mỏng nhẹ',
                'short_description' => 'Laptop Windows cao cấp',
                'price' => 32990000,
                'sale_price' => 29990000,
                'sku' => 'DXPS13001',
                'stock_quantity' => 25,
                'category_id' => 2,
                'brand_id' => 4,
                'is_active' => 1,
                'is_featured' => 0,
            ],
            [
                'name' => 'iPad Pro M2',
                'slug' => 'ipad-pro-m2',
                'description' => 'iPad Pro với chip M2 và Apple Pencil',
                'short_description' => 'Tablet chuyên nghiệp cho sáng tạo',
                'price' => 24990000,
                'sale_price' => 22990000,
                'sku' => 'IPADM2001',
                'stock_quantity' => 35,
                'category_id' => 4,
                'brand_id' => 1,
                'is_active' => 1,
                'is_featured' => 1,
            ],
        ];

        foreach ($products as $product) {
            DB::table('products_enhanced')->insert(array_merge($product, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Tạo users (customers)
        $users = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'customer1@example.com',
                'password' => Hash::make('password123'),
                'phone' => '0901234567',
                'address' => '123 Đường ABC, Quận 1, TP.HCM',
                'role' => 'customer',
                'level' => 2,
                'is_active' => 1,
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'customer2@example.com',
                'password' => Hash::make('password123'),
                'phone' => '0907654321',
                'address' => '456 Đường XYZ, Quận 3, TP.HCM',
                'role' => 'customer',
                'level' => 2,
                'is_active' => 1,
            ],
            [
                'name' => 'Admin SiVGT2',
                'email' => 'admin@sivgt2.com',
                'password' => Hash::make('admin123'),
                'phone' => '0909999999',
                'address' => 'Văn phòng SiVGT2',
                'role' => 'admin',
                'level' => 1,
                'is_active' => 1,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Tạo sample orders
        $orders = [
            [
                'user_id' => 1,
                'order_number' => 'ORD001',
                'status' => 'completed',
                'total_amount' => 27990000,
                'shipping_address' => '123 Đường ABC, Quận 1, TP.HCM',
                'shipping_phone' => '0901234567',
                'shipping_name' => 'Nguyễn Văn A',
                'payment_method' => 'cod',
                'payment_status' => 'paid',
                'notes' => 'Giao hàng giờ hành chính',
            ],
            [
                'user_id' => 2,
                'order_number' => 'ORD002',
                'status' => 'processing',
                'total_amount' => 24990000,
                'shipping_address' => '456 Đường XYZ, Quận 3, TP.HCM',
                'shipping_phone' => '0907654321',
                'shipping_name' => 'Trần Thị B',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
                'notes' => 'Chuyển khoản trước khi giao',
            ],
        ];

        foreach ($orders as $order) {
            DB::table('orders')->insert(array_merge($order, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Tạo order items
        $orderItems = [
            [
                'order_id' => 1,
                'product_id' => 1,
                'quantity' => 1,
                'price' => 27990000,
                'total' => 27990000,
            ],
            [
                'order_id' => 2,
                'product_id' => 2,
                'quantity' => 1,
                'price' => 24990000,
                'total' => 24990000,
            ],
        ];

        foreach ($orderItems as $item) {
            DB::table('order_items')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Tạo settings
        $settings = [
            ['key' => 'site_name', 'value' => 'SiVGT2 Store'],
            ['key' => 'site_description', 'value' => 'Cửa hàng công nghệ hàng đầu'],
            ['key' => 'contact_email', 'value' => 'contact@sivgt2.com'],
            ['key' => 'contact_phone', 'value' => '1900-1234'],
            ['key' => 'contact_address', 'value' => '123 Đường Công Nghệ, TP.HCM'],
            ['key' => 'currency', 'value' => 'VND'],
            ['key' => 'timezone', 'value' => 'Asia/Ho_Chi_Minh'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        echo "✅ Dữ liệu website SiVGT2 đã được tạo thành công!\n";
        echo "📦 Đã tạo: 4 categories, 4 brands, 5 products\n";
        echo "👥 Đã tạo: 3 users (2 customers + 1 admin)\n";
        echo "🛒 Đã tạo: 2 orders mẫu\n";
        echo "⚙️ Đã tạo: 7 settings cơ bản\n";
        echo "\n🔐 Thông tin đăng nhập:\n";
        echo "Admin: admin@sivgt2.com / admin123\n";
        echo "Customer 1: customer1@example.com / password123\n";
        echo "Customer 2: customer2@example.com / password123\n";
    }
}
