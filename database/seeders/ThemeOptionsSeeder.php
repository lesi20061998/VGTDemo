<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class ThemeOptionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create main navigation menu
        $navMenu = Menu::firstOrCreate(
            ['slug' => 'main-menu'],
            ['name' => 'Menu chính', 'location' => 'header']
        );

        // Create menu items
        $menuItems = [
            ['title' => 'Trang chủ', 'url' => '/', 'order' => 1],
            ['title' => 'Sản phẩm', 'url' => '/products', 'order' => 2],
            ['title' => 'Tin tức', 'url' => '/blog', 'order' => 3],
            ['title' => 'Giới thiệu', 'url' => '/about', 'order' => 4],
            ['title' => 'Liên hệ', 'url' => '/contact', 'order' => 5],
        ];

        foreach ($menuItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $navMenu->id, 'title' => $item['title']],
                ['url' => $item['url'], 'order' => $item['order']]
            );
        }

        // Create topbar menu
        $topbarMenu = Menu::firstOrCreate(
            ['slug' => 'topbar-menu'],
            ['name' => 'Menu topbar', 'location' => 'topbar']
        );

        $topbarItems = [
            ['title' => 'Hỗ trợ', 'url' => '/support', 'order' => 1],
            ['title' => 'Theo dõi đơn hàng', 'url' => '/track-order', 'order' => 2],
        ];

        foreach ($topbarItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $topbarMenu->id, 'title' => $item['title']],
                ['url' => $item['url'], 'order' => $item['order']]
            );
        }

        // Settings
        $settings = [
            // General
            ['key' => 'site_name', 'payload' => ['value' => 'HLM Shop'], 'group' => 'general'],
            ['key' => 'site_logo', 'payload' => ['value' => 'https://via.placeholder.com/200x60?text=LOGO'], 'group' => 'general'],
            ['key' => 'hotline', 'payload' => ['value' => '1900 1234'], 'group' => 'general'],

            // Topbar
            ['key' => 'topbar_enabled', 'payload' => ['value' => 1], 'group' => 'theme'],
            ['key' => 'topbar_background_color', 'payload' => ['value' => '#1a1a1a'], 'group' => 'theme'],
            ['key' => 'topbar_text_color', 'payload' => ['value' => '#ffffff'], 'group' => 'theme'],
            ['key' => 'topbar_menu_id', 'payload' => ['value' => $topbarMenu->id], 'group' => 'theme'],

            // Header
            ['key' => 'header_background_color', 'payload' => ['value' => '#ffffff'], 'group' => 'theme'],
            ['key' => 'header_text_color', 'payload' => ['value' => '#333333'], 'group' => 'theme'],
            ['key' => 'navigation_menu_id', 'payload' => ['value' => $navMenu->id], 'group' => 'theme'],

            // Theme Options
            ['key' => 'theme_option_layout', 'payload' => [
                'page_layout' => 'sidebar-right',
                'post_layout' => 'sidebar-right',
                'product_layout' => 'sidebar-left',
            ], 'group' => 'theme'],

            ['key' => 'theme_option_header', 'payload' => [
                'header_style' => 'style-4',
            ], 'group' => 'theme'],

            ['key' => 'theme_option_topbar', 'payload' => [
                'topbar_style' => 'style-1',
            ], 'group' => 'theme'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['payload' => $setting['payload'], 'group' => $setting['group'] ?? null]
            );
        }

        $this->command->info('Theme options seeded successfully!');
    }
}
