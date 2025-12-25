<?php

namespace App\Widgets\Product;

use App\Widgets\BaseWidget;
use App\Models\ProjectProductCategory;

class ProductCateWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Danh mục sản phẩm');
        $limit = $this->get('limit', 8);
        $showCount = $this->get('show_count', true);
        $onlyParent = $this->get('only_parent', true);
        
        // Lấy danh mục từ database
        $query = ProjectProductCategory::orderBy('sort_order', 'asc');
        
        // Chỉ lấy danh mục cha nếu được chọn
        if ($onlyParent) {
            $query->whereNull('parent_id');
        }
        
        $categories = $query->limit($limit)->get();
        
        // Fallback nếu không có danh mục
        if ($categories->isEmpty()) {
            return $this->renderEmptyState($title);
        }
        
        $projectCode = request()->route('projectCode');
        
        $html = "<section class=\"product-cate-widget py-16 bg-white\">";
        $html .= "<div class=\"container mx-auto px-4\">";
        $html .= "<h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>";
        $html .= "<div class=\"grid md:grid-cols-2 lg:grid-cols-4 gap-6\">";
        
        foreach ($categories as $category) {
            $categoryUrl = $projectCode ? "/{$projectCode}/danh-muc/{$category->slug}" : "/danh-muc/{$category->slug}";
            $image = $category->image ?: 'https://via.placeholder.com/300x200?text=' . urlencode($category->name);
            
            // Đếm số sản phẩm trong danh mục
            $productCount = $showCount ? $category->products()->where('status', 'published')->count() : 0;
            
            $html .= "<div class=\"category-item bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition\">";
            $html .= "<img src=\"{$image}\" alt=\"{$category->name}\" class=\"w-full h-40 object-cover\">";
            $html .= "<div class=\"p-4 text-center\">";
            $html .= "<h3 class=\"font-bold mb-2\">{$category->name}</h3>";
            if ($showCount) {
                $html .= "<p class=\"text-gray-500 text-sm mb-3\">{$productCount} sản phẩm</p>";
            }
            $html .= "<a href=\"{$categoryUrl}\" class=\"inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm\">Xem tất cả</a>";
            $html .= "</div></div>";
        }
        
        $html .= "</div></div></section>";
        return $html;
    }
    
    protected function renderEmptyState(string $title): string
    {
        return "<section class=\"product-cate-widget py-16 bg-white\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"text-center text-gray-500 py-12\">
                    <svg class=\"w-16 h-16 mx-auto mb-4 text-gray-300\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
                        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z\"></path>
                    </svg>
                    <p>Chưa có danh mục nào</p>
                </div>
            </div>
        </section>";
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
            'name' => 'Product Categories',
            'description' => 'Hiển thị danh mục sản phẩm từ database',
            'category' => 'product',
            'icon' => '<path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'Danh mục sản phẩm'],
                ['name' => 'limit', 'label' => 'Số lượng', 'type' => 'number', 'default' => 8],
                ['name' => 'show_count', 'label' => 'Hiện số lượng sản phẩm', 'type' => 'checkbox', 'default' => true],
                ['name' => 'only_parent', 'label' => 'Chỉ hiện danh mục cha', 'type' => 'checkbox', 'default' => true],
            ]
        ];
    }
}
