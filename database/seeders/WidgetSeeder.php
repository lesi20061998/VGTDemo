<?php

namespace Database\Seeders;

use App\Models\Widget;
use Illuminate\Database\Seeder;

class WidgetSeeder extends Seeder
{
    public function run(): void
    {
        Widget::create([
            'name' => 'Welcome Banner',
            'type' => 'html',
            'area' => 'homepage-top',
            'settings' => json_encode([
                'content' => '<h1>Welcome to Our Store</h1>',
                'padding' => 20,
                'margin' => 10,
            ]),
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Widget::create([
            'name' => 'Featured Products',
            'type' => 'product_list',
            'area' => 'homepage-top',
            'settings' => json_encode([
                'title' => 'Featured Products',
                'limit' => 8,
                'padding' => 15,
            ]),
            'sort_order' => 2,
            'is_active' => true,
        ]);

        Widget::create([
            'name' => 'Sidebar Info',
            'type' => 'html',
            'area' => 'sidebar',
            'settings' => json_encode([
                'content' => '<div class="sidebar-info"><h3>About Us</h3><p>We are the best store!</p></div>',
                'padding' => 10,
            ]),
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }
}
