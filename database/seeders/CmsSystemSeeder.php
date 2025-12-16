<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CmsSystemSeeder extends Seeder
{
    public function run()
    {
        // 1. Admin User
        DB::table('users')->insertOrIgnore([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Product Categories
        $categories = [
            ['name' => 'Điện thoại', 'slug' => 'dien-thoai', 'parent_id' => null],
            ['name' => 'Laptop', 'slug' => 'laptop', 'parent_id' => null],
            ['name' => 'Phụ kiện', 'slug' => 'phu-kien', 'parent_id' => null],
        ];

        foreach ($categories as $category) {
            DB::table('product_categories')->insertOrIgnore([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'parent_id' => $category['parent_id'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Brands
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Samsung', 'slug' => 'samsung'],
            ['name' => 'Sony', 'slug' => 'sony'],
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->insertOrIgnore([
                'name' => $brand['name'],
                'slug' => $brand['slug'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Products
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'description' => 'Điện thoại iPhone 15 Pro mới nhất',
                'sku' => 'IP15PRO001',
                'price' => 29990000,
                'category_id' => 1,
                'brand_id' => 1,
                'status' => 'published',
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'slug' => 'samsung-galaxy-s24',
                'description' => 'Điện thoại Samsung Galaxy S24',
                'sku' => 'SGS24001',
                'price' => 22990000,
                'category_id' => 1,
                'brand_id' => 2,
                'status' => 'published',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products_enhanced')->insertOrIgnore([
                'name' => $product['name'],
                'slug' => $product['slug'],
                'description' => $product['description'],
                'sku' => $product['sku'],
                'price' => $product['price'],
                'product_category_id' => $product['category_id'],
                'brand_id' => $product['brand_id'],
                'status' => $product['status'],
                'stock_quantity' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Posts
        $posts = [
            [
                'title' => 'Tin tức công nghệ mới nhất',
                'slug' => 'tin-tuc-cong-nghe-moi-nhat',
                'content' => 'Nội dung tin tức về công nghệ...',
                'status' => 'published',
                'author_id' => 1,
            ],
            [
                'title' => 'Xu hướng smartphone 2024',
                'slug' => 'xu-huong-smartphone-2024',
                'content' => 'Nội dung về xu hướng smartphone...',
                'status' => 'published',
                'author_id' => 1,
            ],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->insertOrIgnore([
                'title' => $post['title'],
                'slug' => $post['slug'],
                'content' => $post['content'],
                'status' => $post['status'],
                'author_id' => $post['author_id'],
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'CMS System', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Hệ thống quản lý nội dung', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'contact@example.com', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '0123456789', 'group' => 'contact'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insertOrIgnore([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'group' => $setting['group'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}