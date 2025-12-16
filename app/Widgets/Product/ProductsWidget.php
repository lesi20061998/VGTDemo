<?php

namespace App\Widgets\Product;

use App\Widgets\BaseWidget;
use App\Models\Product;

class ProductsWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Sản Phẩm');
        $limit = $this->get('limit', 8);
        $products = collect([
            (object)['name' => 'Laptop Dell XPS 15', 'slug' => 'laptop-dell-xps-15', 'image' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=400', 'price' => 35000000],
            (object)['name' => 'iPhone 15 Pro Max', 'slug' => 'iphone-15-pro-max', 'image' => 'https://images.unsplash.com/photo-1592286927505-4fd4d3d4ef9f?w=400', 'price' => 32000000],
            (object)['name' => 'MacBook Pro M3', 'slug' => 'macbook-pro-m3', 'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400', 'price' => 45000000],
            (object)['name' => 'Samsung Galaxy S24', 'slug' => 'samsung-galaxy-s24', 'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400', 'price' => 25000000],
        ])->take($limit);
        
        $projectCode = request()->route('projectCode');
        $html = "<section class=\"products-widget py-16 bg-white\">";
        $html .= "<div class=\"container mx-auto px-4\">";
        $html .= "<h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>";
        $html .= "<div class=\"grid md:grid-cols-4 gap-6\">";
        
        foreach ($products as $product) {
            $productUrl = $projectCode ? "/{$projectCode}/product/{$product->slug}" : "/product/{$product->slug}";
            $html .= "<div class=\"product-card bg-white rounded-lg shadow hover:shadow-xl transition\">";
            $html .= "<img src=\"{$product->image}\" alt=\"{$product->name}\" class=\"w-full h-48 object-cover rounded-t-lg\">";
            $html .= "<div class=\"p-4\">";
            $html .= "<h3 class=\"font-bold mb-2\">{$product->name}</h3>";
            $html .= "<div class=\"flex justify-between items-center\">";
            $html .= "<span class=\"text-lg font-bold text-blue-600\">" . number_format($product->price) . "đ</span>";
            $html .= "<a href=\"{$productUrl}\" class=\"bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm\">Xem</a>";
            $html .= "</div></div></div>";
        }
        
        $html .= "</div></div></section>";
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .product-card { transition: all 0.3s ease; }
        .product-card:hover { transform: translateY(-5px); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".product-card").forEach((card, i) => {
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
            'name' => 'Products',
            'description' => 'Product grid display',
            'category' => 'product',
            'icon' => '<path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Sản phẩm nổi bật'],
                ['name' => 'limit', 'label' => 'Number of Products', 'type' => 'number', 'default' => 8],
            ]
        ];
    }
}
