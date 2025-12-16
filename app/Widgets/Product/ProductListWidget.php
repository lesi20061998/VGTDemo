<?php

namespace App\Widgets\Product;

use App\Widgets\BaseWidget;

class ProductListWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Sản phẩm nổi bật');
        $limit = $this->get('limit', 6);
        
        $products = [
            ['name' => 'Laptop Dell XPS 15', 'slug' => 'laptop-dell-xps-15', 'image' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?w=400', 'description' => 'Laptop cao cấp cho doanh nhân', 'price' => 35000000],
            ['name' => 'iPhone 15 Pro Max', 'slug' => 'iphone-15-pro-max', 'image' => 'https://images.unsplash.com/photo-1592286927505-4fd4d3d4ef9f?w=400', 'description' => 'Smartphone flagship mới nhất', 'price' => 32000000],
            ['name' => 'MacBook Pro M3', 'slug' => 'macbook-pro-m3', 'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400', 'description' => 'Hiệu năng vượt trội', 'price' => 45000000],
            ['name' => 'Samsung Galaxy S24', 'slug' => 'samsung-galaxy-s24', 'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400', 'description' => 'Android flagship', 'price' => 25000000],
            ['name' => 'iPad Pro 2024', 'slug' => 'ipad-pro-2024', 'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400', 'description' => 'Máy tính bảng chuyên nghiệp', 'price' => 28000000],
            ['name' => 'AirPods Pro 2', 'slug' => 'airpods-pro-2', 'image' => 'https://images.unsplash.com/photo-1606841837239-c5a1a4a07af7?w=400', 'description' => 'Tai nghe chống ồn', 'price' => 6000000],
        ];
        
        $products = array_slice($products, 0, $limit);
        
        $html = "<section class=\"product-list-widget py-16 bg-white\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"grid md:grid-cols-3 gap-6\">";
        
        $projectCode = request()->route('projectCode');
        foreach ($products as $product) {
            $productUrl = $projectCode ? "/{$projectCode}/product/{$product['slug']}" : "/product/{$product['slug']}";
            $html .= "
                <div class=\"product-card bg-white rounded-lg shadow hover:shadow-xl transition\">
                    <img src=\"{$product['image']}\" alt=\"{$product['name']}\" class=\"w-full h-48 object-cover rounded-t-lg\">
                    <div class=\"p-6\">
                        <h3 class=\"text-xl font-bold mb-2\">{$product['name']}</h3>
                        <p class=\"text-gray-600 text-sm mb-4\">{$product['description']}</p>
                        <div class=\"flex justify-between items-center\">
                            <span class=\"text-2xl font-bold text-blue-600\">" . number_format($product['price']) . "đ</span>
                            <a href=\"{$productUrl}\" class=\"bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700\">Xem</a>
                        </div>
                    </div>
                </div>";
        }
        
        $html .= "</div></div></section>";
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .product-card { transition: all 0.3s ease; }
        .product-card:hover { transform: translateY(-8px); }
        .product-card img { transition: transform 0.3s; }
        .product-card:hover img { transform: scale(1.1); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        document.querySelectorAll(".product-card").forEach((card, i) => {
            card.style.opacity = "0";
            card.style.transform = "translateY(30px)";
            setTimeout(() => {
                card.style.transition = "all 0.6s ease";
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
            }, i * 150);
        });
        </script>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Product List',
            'description' => 'Display product grid',
            'category' => 'product',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Sản phẩm nổi bật'],
                ['name' => 'limit', 'label' => 'Number of Products', 'type' => 'number', 'default' => 6],
            ]
        ];
    }
}

