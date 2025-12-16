<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SystemInitSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Roles
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access',
                'permissions' => json_encode(['*']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'editor',
                'display_name' => 'Editor',
                'description' => 'Content management access',
                'permissions' => json_encode(['posts.*', 'pages.*', 'media.*']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'support',
                'display_name' => 'Support Staff',
                'description' => 'Customer support access',
                'permissions' => json_encode(['orders.view', 'feedbacks.*', 'contacts.*']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);

        // 2. Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Assign admin role
        DB::table('user_roles')->insert([
            'user_id' => $admin->id,
            'role_id' => 1, // admin role
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. System Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'Agency CMS', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Professional CMS for agencies', 'type' => 'string', 'group' => 'general'],
            ['key' => 'admin_per_page', 'value' => '20', 'type' => 'integer', 'group' => 'admin'],
            ['key' => 'currency', 'value' => 'VND', 'type' => 'string', 'group' => 'ecommerce'],
            ['key' => 'price_placeholder', 'value' => 'Liên hệ', 'type' => 'string', 'group' => 'ecommerce'],
            ['key' => 'media_max_size', 'value' => '10240', 'type' => 'integer', 'group' => 'media'],
            ['key' => 'allowed_file_types', 'value' => 'jpg,jpeg,png,gif,pdf,doc,docx', 'type' => 'string', 'group' => 'media'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 4. Sample Brands
        $brands = [
            ['name' => 'Nike', 'slug' => 'nike', 'description' => 'Just Do It'],
            ['name' => 'Adidas', 'slug' => 'adidas', 'description' => 'Impossible is Nothing'],
            ['name' => 'Puma', 'slug' => 'puma', 'description' => 'Forever Faster'],
        ];

        foreach ($brands as $brand) {
            DB::table('brands')->insert(array_merge($brand, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 5. Sample Categories
        $categories = [
            ['name' => 'Giày thể thao', 'slug' => 'giay-the-thao', 'level' => 0, 'path' => '1'],
            ['name' => 'Giày chạy bộ', 'slug' => 'giay-chay-bo', 'parent_id' => 1, 'level' => 1, 'path' => '1/2'],
            ['name' => 'Giày bóng đá', 'slug' => 'giay-bong-da', 'parent_id' => 1, 'level' => 1, 'path' => '1/3'],
            ['name' => 'Quần áo', 'slug' => 'quan-ao', 'level' => 0, 'path' => '4'],
        ];

        foreach ($categories as $category) {
            DB::table('product_categories')->insert(array_merge($category, [
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 6. Attribute Groups
        $attributeGroups = [
            ['name' => 'Kích thước', 'slug' => 'kich-thuoc'],
            ['name' => 'Màu sắc', 'slug' => 'mau-sac'],
            ['name' => 'Chất liệu', 'slug' => 'chat-lieu'],
        ];

        foreach ($attributeGroups as $group) {
            DB::table('attribute_groups')->insert(array_merge($group, [
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 7. Product Attributes
        $attributes = [
            ['name' => 'Size', 'slug' => 'size', 'type' => 'select', 'attribute_group_id' => 1],
            ['name' => 'Màu', 'slug' => 'mau', 'type' => 'color', 'attribute_group_id' => 2],
            ['name' => 'Chất liệu', 'slug' => 'chat-lieu', 'type' => 'select', 'attribute_group_id' => 3],
        ];

        foreach ($attributes as $attr) {
            DB::table('product_attributes')->insert(array_merge($attr, [
                'is_filterable' => true,
                'is_required' => false,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 8. Attribute Values
        $attributeValues = [
            // Sizes
            ['product_attribute_id' => 1, 'value' => '38', 'slug' => '38'],
            ['product_attribute_id' => 1, 'value' => '39', 'slug' => '39'],
            ['product_attribute_id' => 1, 'value' => '40', 'slug' => '40'],
            ['product_attribute_id' => 1, 'value' => '41', 'slug' => '41'],
            ['product_attribute_id' => 1, 'value' => '42', 'slug' => '42'],
            
            // Colors
            ['product_attribute_id' => 2, 'value' => 'Đỏ', 'slug' => 'do', 'color_code' => '#FF0000'],
            ['product_attribute_id' => 2, 'value' => 'Xanh', 'slug' => 'xanh', 'color_code' => '#0000FF'],
            ['product_attribute_id' => 2, 'value' => 'Đen', 'slug' => 'den', 'color_code' => '#000000'],
            
            // Materials
            ['product_attribute_id' => 3, 'value' => 'Da thật', 'slug' => 'da-that'],
            ['product_attribute_id' => 3, 'value' => 'Vải canvas', 'slug' => 'vai-canvas'],
        ];

        foreach ($attributeValues as $value) {
            DB::table('product_attribute_values')->insert(array_merge($value, [
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}