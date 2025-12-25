<?php

namespace App\Widgets\Product;

use App\Widgets\BaseWidget;
use App\Models\ProjectProduct;

class ProductsWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Sản Phẩm');
        $limit = $this->get('limit', 8);
        $columns = $this->get('columns', 4);
        $orderBy = $this->get('order_by', 'created_at');
        $orderDir = $this->get('order_dir', 'desc');
        
        // Lấy sản phẩm từ database
        $products = ProjectProduct::where('status', 'published')
            ->orderBy($orderBy, $orderDir)
            ->limit($limit)
            ->get();
        
        // Fallback nếu không có sản phẩm
        if ($products->isEmpty()) {
            return $this->renderEmptyState($title);
        }
        
        $projectCode = request()->route('projectCode');
        $gridCols = "md:grid-cols-{$columns}";
        
        // Kiểm tra watermark có được bật không từ settings
        $watermark = setting('watermark', []);
        $watermarkEnabled = $watermark['enabled'] ?? false;
        
        $html = "<section class=\"products-widget py-16 bg-white\">";
        $html .= "<div class=\"container mx-auto px-4\">";
        $html .= "<h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>";
        $html .= "<div class=\"grid {$gridCols} gap-6\">";
        
        foreach ($products as $product) {
            $productUrl = $projectCode ? "/{$projectCode}/san-pham/{$product->slug}" : "/san-pham/{$product->slug}";
            
            // Xử lý hình ảnh với watermark
            $image = $this->getProductImage($product->featured_image, $watermarkEnabled);
            
            $price = $product->price ? number_format($product->price) . 'đ' : 'Liên hệ';
            $salePrice = $product->sale_price ? number_format($product->sale_price) . 'đ' : null;
            
            $html .= "<div class=\"product-card bg-white rounded-lg shadow hover:shadow-xl transition overflow-hidden\">";
            $html .= "<div class=\"relative\">";
            $html .= "<img src=\"{$image}\" alt=\"{$product->name}\" class=\"w-full h-48 object-cover\">";
            
            // Badges container - góc trái trên
            $html .= "<div class=\"absolute top-2 left-2 flex flex-col gap-1\">";
            if ($product->is_featured) {
                $html .= "<span class=\"bg-yellow-400 text-yellow-900 text-xs px-2 py-1 rounded flex items-center gap-1\"><svg class=\"w-3 h-3\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path d=\"M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z\"></path></svg>Nổi bật</span>";
            }
            if ($product->is_bestseller) {
                $html .= "<span class=\"bg-green-500 text-white text-xs px-2 py-1 rounded flex items-center gap-1\"><svg class=\"w-3 h-3\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z\" clip-rule=\"evenodd\"></path></svg>Bán chạy</span>";
            }
            if ($product->is_favorite) {
                $html .= "<span class=\"bg-red-500 text-white text-xs px-2 py-1 rounded flex items-center gap-1\"><svg class=\"w-3 h-3\" fill=\"currentColor\" viewBox=\"0 0 20 20\"><path fill-rule=\"evenodd\" d=\"M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z\" clip-rule=\"evenodd\"></path></svg>Yêu thích</span>";
            }
            $html .= "</div>";
            
            // Sale badge - góc phải trên
            if ($salePrice) {
                $html .= "<span class=\"absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded\">Sale</span>";
            }
            $html .= "</div>";
            $html .= "<div class=\"p-4\">";
            $html .= "<h3 class=\"font-bold mb-2 line-clamp-2\">{$product->name}</h3>";
            $html .= "<div class=\"flex justify-between items-center\">";
            if ($salePrice) {
                $html .= "<div><span class=\"text-gray-400 line-through text-sm\">{$price}</span><br><span class=\"text-lg font-bold text-blue-600\">{$salePrice}</span></div>";
            } else {
                $html .= "<span class=\"text-lg font-bold text-blue-600\">{$price}</span>";
            }
            $html .= "<a href=\"{$productUrl}\" class=\"bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm\">Xem</a>";
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
        // Controller sẽ tự extract project code từ path (project-XXX/...)
        if ($watermarkEnabled && str_contains($imagePath, '/storage/media/')) {
            return str_replace('/storage/media/', '/media/', $imagePath);
        }

        return $imagePath;
    }
    
    protected function renderEmptyState(string $title): string
    {
        return "<section class=\"products-widget py-16 bg-white\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"text-center text-gray-500 py-12\">
                    <svg class=\"w-16 h-16 mx-auto mb-4 text-gray-300\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z\"></path>
                    </svg>
                    <p>Chưa có sản phẩm nào</p>
                </div>
            </div>
        </section>";
    }

    public function css(): string
    {
        return '<style>
        .product-card { transition: all 0.3s ease; }
        .product-card:hover { transform: translateY(-5px); }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
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
            'name' => 'Products Grid',
            'description' => 'Hiển thị lưới sản phẩm từ database',
            'category' => 'product',
            'icon' => '<path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'Sản phẩm'],
                ['name' => 'limit', 'label' => 'Số lượng', 'type' => 'number', 'default' => 8],
                ['name' => 'columns', 'label' => 'Số cột', 'type' => 'select', 'default' => '4', 'options' => ['2' => '2 cột', '3' => '3 cột', '4' => '4 cột']],
                ['name' => 'order_by', 'label' => 'Sắp xếp theo', 'type' => 'select', 'default' => 'created_at', 'options' => ['created_at' => 'Ngày tạo', 'name' => 'Tên', 'price' => 'Giá', 'views' => 'Lượt xem']],
                ['name' => 'order_dir', 'label' => 'Thứ tự', 'type' => 'select', 'default' => 'desc', 'options' => ['desc' => 'Giảm dần', 'asc' => 'Tăng dần']],
            ],
            'settings' => [
                'cacheable' => false, // Tắt cache để luôn lấy dữ liệu mới nhất từ database
            ]
        ];
    }
}
