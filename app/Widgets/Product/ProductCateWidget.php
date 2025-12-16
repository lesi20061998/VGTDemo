<?php

namespace App\Widgets\Product;

use App\Widgets\BaseWidget;
use App\Models\ProductCategory;

class ProductCateWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Danh mục sản phẩm');
        
        $categories = [
            ['name' => 'Laptop Gaming', 'image' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=300', 'count' => 25, 'slug' => 'laptop-gaming'],
            ['name' => 'Smartphone', 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300', 'count' => 40, 'slug' => 'smartphone'],
            ['name' => 'Tablet', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=300', 'count' => 15, 'slug' => 'tablet'],
            ['name' => 'Accessories', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300', 'count' => 60, 'slug' => 'accessories'],
        ];
        
        $projectCode = request()->route('projectCode');
        $html = "<section class=\"product-cate-widget py-16 bg-white\">";
        $html .= "<div class=\"container mx-auto px-4\">";
        $html .= "<h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>";
        $html .= "<div class=\"grid md:grid-cols-2 lg:grid-cols-4 gap-6\">";
        
        foreach ($categories as $category) {
            $categoryUrl = $projectCode ? "/{$projectCode}/category/{$category['slug']}" : "/category/{$category['slug']}";
            $html .= "<div class=\"category-item bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition\">";
            $html .= "<img src=\"{$category['image']}\" alt=\"{$category['name']}\" class=\"w-full h-40 object-cover\">";
            $html .= "<div class=\"p-4 text-center\">";
            $html .= "<h3 class=\"font-bold mb-2\">{$category['name']}</h3>";
            $html .= "<p class=\"text-gray-500 text-sm mb-3\">{$category['count']} sản phẩm</p>";
            $html .= "<a href=\"{$categoryUrl}\" class=\"bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm\">Xem tất cả</a>";
            $html .= "</div></div>";
        }
        
        $html .= "</div></div></section>";
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .category-item { transition: all 0.3s ease; }
        .category-item:hover { transform: translateY(-5px); }
        .category-item img { transition: transform 0.3s; }
        .category-item:hover img { transform: scale(1.05); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".category-item").forEach((item, i) => {
            item.style.opacity = "0";
            item.style.transform = "translateY(20px)";
            setTimeout(() => {
                item.style.transition = "all 0.5s ease";
                item.style.opacity = "1";
                item.style.transform = "translateY(0)";
            }, i * 150);
        });
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Product Category',
            'description' => 'Product category list',
            'category' => 'product',
            'icon' => '<path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Danh mục sản phẩm'],
            ]
        ];
    }
}
