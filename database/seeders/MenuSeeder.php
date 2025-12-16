<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\MenuItem;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo Main Menu
        $mainMenu = Menu::create([
            'name' => 'Main Menu',
            'slug' => 'main-menu',
            'location' => 'header',
            'is_active' => true
        ]);
        
        // Thêm các mục menu
        MenuItem::create([
            'menu_id' => $mainMenu->id,
            'title' => 'Trang chủ',
            'url' => '/',
            'target' => '_self',
            'order' => 0
        ]);
        
        MenuItem::create([
            'menu_id' => $mainMenu->id,
            'title' => 'Giới thiệu',
            'url' => '/about',
            'target' => '_self',
            'order' => 1
        ]);
        
        MenuItem::create([
            'menu_id' => $mainMenu->id,
            'title' => 'Sản phẩm',
            'url' => '/products',
            'target' => '_self',
            'order' => 2
        ]);
        
        MenuItem::create([
            'menu_id' => $mainMenu->id,
            'title' => 'Liên hệ',
            'url' => '/contact',
            'target' => '_self',
            'order' => 3
        ]);
        
        // Tạo Footer Menu
        $footerMenu = Menu::create([
            'name' => 'Footer Menu',
            'slug' => 'footer-menu',
            'location' => 'footer',
            'is_active' => true
        ]);
        
        MenuItem::create([
            'menu_id' => $footerMenu->id,
            'title' => 'Về chúng tôi',
            'url' => '/about',
            'target' => '_self',
            'order' => 0
        ]);
        
        MenuItem::create([
            'menu_id' => $footerMenu->id,
            'title' => 'Liên hệ',
            'url' => '/contact',
            'target' => '_self',
            'order' => 1
        ]);
    }
}
