<?php

namespace App\Widgets\Category;

use App\Widgets\BaseWidget;

class HomeCateWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Danh mục sản phẩm');
        
        $categories = [
            ['name' => 'Laptop & Máy tính', 'icon' => '💻', 'count' => 45, 'slug' => 'laptop-may-tinh'],
            ['name' => 'Điện thoại', 'icon' => '📱', 'count' => 32, 'slug' => 'dien-thoai'],
            ['name' => 'Tablet & iPad', 'icon' => '📱', 'count' => 18, 'slug' => 'tablet-ipad'],
            ['name' => 'Phụ kiện', 'icon' => '🎧', 'count' => 67, 'slug' => 'phu-kien'],
        ];
        
        $projectCode = request()->route('projectCode');
        $html = "<section class=\"home-cate-widget py-16 bg-gray-50\">";
        $html .= "<div class=\"container mx-auto px-4\">";
        $html .= "<h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>";
        $html .= "<div class=\"grid md:grid-cols-2 lg:grid-cols-4 gap-6\">";
        
        foreach ($categories as $category) {
            $categoryUrl = $projectCode ? "/{$projectCode}/category/{$category['slug']}" : "/category/{$category['slug']}";
            $html .= "<div class=\"category-card bg-white rounded-lg p-6 text-center hover:shadow-lg transition cursor-pointer\" onclick=\"location.href='{$categoryUrl}'\">";
            $html .= "<div class=\"text-4xl mb-4\">{$category['icon']}</div>";
            $html .= "<h3 class=\"font-bold mb-2\">{$category['name']}</h3>";
            $html .= "<p class=\"text-gray-500 text-sm\">{$category['count']} sản phẩm</p>";
            $html .= "</div>";
        }
        
        $html .= "</div></div></section>";
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .category-card { transition: all 0.3s ease; }
        .category-card:hover { transform: translateY(-5px); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".category-card").forEach((card, i) => {
            card.style.opacity = "0";
            card.style.transform = "translateY(20px)";
            setTimeout(() => {
                card.style.transition = "all 0.5s ease";
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
            }, i * 100);
        });
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Home Category',
            'description' => 'Home page category showcase',
            'category' => 'category',
            'icon' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Danh mục sản phẩm'],
            ]
        ];
    }
}
