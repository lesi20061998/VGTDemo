<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebsiteConfigSeeder extends Seeder
{
    /**
     * Seed website configuration settings.
     *
     * - Global settings (project_id = NULL): fallback defaults for all projects
     * - Project settings (project_id = N): project-specific overrides
     *
     * Only inserts if key does not exist - never overwrites existing config.
     */
    public function run(): void
    {
        $this->command->info('Seeding Website Configuration...');
        $this->seedGlobalSettings();

        $projects = DB::table('projects')->get(['id', 'code', 'name']);
        foreach ($projects as $project) {
            $this->seedProjectSettings($project);
        }

        $this->command->info('Website Config Seeder completed!');
    }

    /**
     * Insert a setting only if it doesn't exist yet.
     */
    private function upsertSetting(string $key, string $value, ?int $projectId): bool
    {
        $exists = DB::table('settings')
            ->where('key', $key)
            ->where(function ($q) use ($projectId) {
                if ($projectId === null) {
                    $q->whereNull('project_id');
                } else {
                    $q->where('project_id', $projectId);
                }
            })
            ->exists();

        if ($exists) {
            return false;
        }

        DB::table('settings')->insert([
            'key' => $key,
            'payload' => json_encode(['value' => $value]),
            'group' => 'website_config',
            'project_id' => $projectId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    /**
     * Seed global fallback settings (project_id = NULL).
     */
    private function seedGlobalSettings(): void
    {
        $year = date('Y');

        $settings = [
            // General
            'site_name' => 'VGT Demo',
            'site_logo' => '',
            'theme_color' => '#98191F',
            'bg_type' => 'color',
            'bg_color' => '#f9fafb',
            'bg_gradient_start' => '#4F46E5',
            'bg_gradient_end' => '#7C3AED',
            'bg_gradient_direction' => 'to right',
            'bg_image' => '',
            'bg_image_size' => 'cover',
            'bg_image_position' => 'center',
            'bg_image_repeat' => 'no-repeat',
            // Top Bar
            'topbar_enabled' => '1',
            'topbar_menu_id' => '',
            'topbar_text' => 'Mien phi giao hang toan quoc cho don tu 500.000d | Hotline: 1900 1234',
            'topbar_bg_color' => '#98191F',
            'topbar_text_color' => '#ffffff',
            // Header
            'header_layout' => 'default',
            'header_sticky' => '1',
            'header_bg_color' => '#ffffff',
            'header_text_color' => '#111827',
            'show_search' => '1',
            'show_cart' => '1',
            'show_account' => '1',
            // Mobile
            'mobile_menu_style' => 'sidebar',
            'mobile_show_search' => '1',
            'mobile_show_cart' => '1',
            // Navigation
            'navigation_menu_id' => '',
            'nav_style' => 'horizontal',
            'nav_bg_color' => '#98191F',
            'nav_text_color' => '#ffffff',
            'nav_hover_color' => '#c0392b',
            // Map
            'map_enabled' => '0',
            'map_iframe' => '',
            // Footer
            'footer_layout' => '4-columns',
            'footer_bg_color' => '#1a1a1a',
            'footer_text_color' => '#d1d5db',
            'footer_copyright' => "Copyright {$year} VGT Demo. All rights reserved.",
            'footer_about' => 'We provide high quality products with excellent service.',
            'footer_col1_title' => 'About Us',
            'footer_col1_content' => '<p>Trusted brand with quality products nationwide.</p>',
            'footer_col2_title' => 'Quick Links',
            'footer_col2_content' => '<ul><li><a href="#">Home</a></li><li><a href="#">Products</a></li><li><a href="#">Contact</a></li></ul>',
            'footer_col3_title' => 'Contact',
            'footer_col3_content' => '<ul><li>123 Street, City</li><li>1900 1234</li><li>info@vgtdemo.com</li></ul>',
            'footer_col4_title' => 'Social',
            'footer_col4_content' => '<ul><li><a href="#">Facebook</a></li><li><a href="#">Instagram</a></li></ul>',
            // Branches
            'show_branches' => '0',
            'branches_title' => 'Our Branches',
            // Posts
            'posts_per_page' => '12',
            'show_author' => '1',
            'show_date' => '1',
            'show_excerpt' => '1',
            // Products
            'products_per_page' => '16',
            'show_quick_view' => '1',
            'show_compare' => '0',
            'show_wishlist' => '1',
            // Floating cart
            'floating_cart_enabled' => '1',
            'floating_cart_position' => 'bottom-right',
            'floating_cart_color' => '#98191F',
            // Contact form
            'form_enabled' => '1',
            'form_title' => 'Free Consultation',
            'form_position' => 'sidebar',
            'form_fields' => '[{"name":"name","label":"Full Name","type":"text","required":true},{"name":"phone","label":"Phone","type":"tel","required":true}]',
        ];

        $count = 0;
        foreach ($settings as $key => $value) {
            if ($this->upsertSetting($key, $value, null)) {
                $count++;
            }
        }

        $this->command->info("  Global: {$count} new settings inserted");
    }

    /**
     * Seed project-specific settings that override global.
     */
    private function seedProjectSettings(object $project): void
    {
        $year = date('Y');

        $projectConfigs = [
            'HD001' => [
                'site_name' => 'Dong Y 1',
                'theme_color' => '#98191F',
                'bg_type' => 'color',
                'bg_color' => '#fafafa',
                'topbar_enabled' => '1',
                'topbar_text' => 'Dong Y chinh hang - Giao hang toan quoc | Hotline: 1900 6789',
                'topbar_bg_color' => '#98191F',
                'topbar_text_color' => '#ffffff',
                'header_sticky' => '1',
                'header_bg_color' => '#ffffff',
                'header_text_color' => '#1a1a1a',
                'show_search' => '1',
                'show_cart' => '1',
                'show_account' => '1',
                'nav_bg_color' => '#98191F',
                'nav_text_color' => '#ffffff',
                'nav_hover_color' => '#7a1419',
                'map_enabled' => '1',
                'footer_bg_color' => '#2d1212',
                'footer_text_color' => '#e5c5c5',
                'footer_layout' => '3-columns',
                'footer_copyright' => "Copyright {$year} Dong Y 1. All rights reserved.",
                'footer_col1_title' => 'Dong Y 1',
                'footer_col1_content' => '<p>Thuong hieu dong y uy tin voi hon 20 nam kinh nghiem.</p><p>Hotline: 1900 6789</p>',
                'footer_col2_title' => 'Danh muc',
                'footer_col2_content' => '<ul><li><a href="#">Thuoc bo</a></li><li><a href="#">Thao duoc</a></li><li><a href="#">My pham thien nhien</a></li></ul>',
                'footer_col3_title' => 'Lien he',
                'footer_col3_content' => '<ul><li>12 Pho Thuoc Nam, Ha Noi</li><li>1900 6789</li><li>info@dongY1.vn</li></ul>',
                'floating_cart_enabled' => '1',
                'floating_cart_color' => '#98191F',
                'form_enabled' => '1',
                'form_title' => 'Tu van mien phi',
                'products_per_page' => '16',
                'show_quick_view' => '1',
                'show_wishlist' => '1',
            ],
        ];

        $data = $projectConfigs[$project->code] ?? [
            'site_name' => $project->name,
            'theme_color' => '#2563eb',
            'topbar_enabled' => '1',
            'topbar_text' => "Welcome to {$project->name}!",
            'topbar_bg_color' => '#2563eb',
            'topbar_text_color' => '#ffffff',
            'footer_bg_color' => '#1f2937',
            'footer_text_color' => '#d1d5db',
            'footer_copyright' => "Copyright {$year} {$project->name}.",
        ];

        $count = 0;
        foreach ($data as $key => $value) {
            if ($this->upsertSetting($key, $value, $project->id)) {
                $count++;
            }
        }

        $this->command->info("  Project [{$project->code}] {$project->name}: {$count} new settings inserted");
    }
}
