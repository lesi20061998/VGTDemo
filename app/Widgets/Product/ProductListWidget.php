<?php

namespace App\Widgets\Product;

use App\Widgets\BaseWidget;
use App\Models\ProjectProduct;

class ProductListWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Sản phẩm nổi bật');
        $limit = $this->get('limit', 6);
        $categoryId = $this->get('category_id', null);
        $showFeatured = $this->get('show_featured', false);

        $query = ProjectProduct::where('status', 'published')
            ->orderBy('created_at', 'desc');

        if ($categoryId) {
            $query->where('product_category_id', $categoryId);
        }

        if ($showFeatured) {
            $query->where('is_featured', true);
        }

        $products = $query->limit($limit)->get();

        if ($products->isEmpty()) {
            return $this->renderEmptyState($title);
        }

        $projectCode = request()->route('projectCode');
        
        // Kiểm tra watermark có được bật không
        $watermark = setting('watermark', []);
        $watermarkEnabled = $watermark['enabled'] ?? false;

        $html = "<section class=\"product-list-widget py-16 bg-white\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"grid md:grid-cols-3 gap-6\">";

        foreach ($products as $product) {
            $productUrl = $projectCode ? "/{$projectCode}/san-pham/{$product->slug}" : "/san-pham/{$product->slug}";
            $image = $this->getProductImage($product->featured_image, $watermarkEnabled);
            $price = $product->price ? number_format($product->price) . 'đ' : 'Liên hệ';
            $salePrice = $product->sale_price ? number_format($product->sale_price) . 'đ' : null;

            $html .= "<div class=\"product-card bg-white rounded-lg shadow hover:shadow-xl transition overflow-hidden\">";
            $html .= "<div class=\"relative\">";
            $html .= "<img src=\"{$image}\" alt=\"{$product->name}\" class=\"w-full h-48 object-cover\">";

            // Badges
            $html .= "<div class=\"absolute top-2 left-2 flex flex-col gap-1\">";
            if ($product->is_featured) {
                $html .= "<span class=\"bg-yellow-400 text-yellow-900 text-xs px-2 py-1 rounded\">Nổi bật</span>";
            }
            if ($product->is_bestseller) {
                $html .= "<span class=\"bg-green-500 text-white text-xs px-2 py-1 rounded\">Bán chạy</span>";
            }
            if ($product->is_favorite) {
                $html .= "<span class=\"bg-red-500 text-white text-xs px-2 py-1 rounded\">Yêu thích</span>";
            }
            $html .= "</div>";

            if ($salePrice) {
                $html .= "<span class=\"absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded\">Sale</span>";
            }
            $html .= "</div>";

            $html .= "<div class=\"p-6\">";
            $html .= "<h3 class=\"text-xl font-bold mb-2 line-clamp-2\">{$product->name}</h3>";
            $desc = \Str::limit(strip_tags($product->short_description ?: $product->description), 80);
            $html .= "<p class=\"text-gray-600 text-sm mb-4 line-clamp-2\">{$desc}</p>";
            $html .= "<div class=\"flex justify-between items-center\">";
            $html .= "<div>";
            if ($salePrice) {
                $html .= "<span class=\"text-gray-400 line-through text-sm\">{$price}</span><br>";
            }
            $displayPrice = $salePrice ?: $price;
            $html .= "<span class=\"text-2xl font-bold text-blue-600\">{$displayPrice}</span>";
            $html .= "</div>";
            $html .= "<a href=\"{$productUrl}\" class=\"bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700\">Xem</a>";
            $html .= "</div></div></div>";
        }

        $html .= "</div></div></section>";

        return $html;
    }

    /**
     * Xử lý URL hình ảnh với watermark
     */
    protected function getProductImage(?string $imagePath, bool $watermarkEnabled): string
    {
        if (empty($imagePath)) {
            return '/assets/img/placeholder-images-image_large.webp';
        }

        // Nếu watermark được bật, chuyển URL từ /storage/media/* sang /media/*
        if ($watermarkEnabled && str_contains($imagePath, '/storage/media/')) {
            return str_replace('/storage/media/', '/media/', $imagePath);
        }

        return $imagePath;
    }

    protected function renderEmptyState(string $title): string
    {
        return "<section class=\"product-list-widget py-16 bg-white\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"text-center text-gray-500 py-12\">
                    <p>Chưa có sản phẩm nào</p>
                </div>
            </div>
        </section>";
    }

    public function css(): string
    {
        return '<style>
        .product-card { transition: all 0.3s ease; }
        .product-card:hover { transform: translateY(-8px); }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        </style>';
    }

    public function js(): string
    {
        return '';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Product List',
            'description' => 'Hiển thị danh sách sản phẩm từ database',
            'category' => 'product',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'Sản phẩm nổi bật'],
                ['name' => 'limit', 'label' => 'Số lượng sản phẩm', 'type' => 'number', 'default' => 6],
                ['name' => 'category_id', 'label' => 'Danh mục (ID)', 'type' => 'number', 'default' => ''],
                ['name' => 'show_featured', 'label' => 'Chỉ hiện sản phẩm nổi bật', 'type' => 'checkbox', 'default' => false],
            ],
            'settings' => [
                'cacheable' => false,
            ],
        ];
    }
}
