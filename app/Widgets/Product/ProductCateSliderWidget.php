<?php

namespace App\Widgets\Product;

use App\Models\Taxonomy;
use App\Widgets\BaseWidget;

class ProductCateSliderWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Danh Mục Sản Phẩm');
        $limit = $this->get('limit', 12);
        $showCount = $this->get('show_count', true);
        $onlyParent = $this->get('only_parent', true);
        $showNavigation = $this->get('show_navigation', true);
        $showPagination = $this->get('show_pagination', true);
        $autoplay = $this->get('autoplay', false);
        $autoplayDelay = (int) $this->get('autoplay_delay', 3000);

        $query = Taxonomy::where('taxonomy', 'product_cat')->orderBy('order', 'asc');

        if ($onlyParent) {
            $query->whereNull('parent_id');
        }

        $categories = $query->limit($limit)->get();

        if ($categories->isEmpty()) {
            return $this->renderEmptyState($title);
        }

        $projectCode = request()->route('projectCode');
        $widgetId = 'product-cate-slider-' . uniqid();

        $styleAttr = $this->buildWrapperStyleAttribute();
        $bgClasses = implode(' ', $this->getWrapperBgClasses());

        $html  = '<section class="product-cate-slider-widget py-16 bg-white ' . $bgClasses . '"' . $styleAttr . '>';
        $html .= '<div class="container mx-auto px-4">';
        if ($title) {
            $html .= "<h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>";
        }
        $html .= "<div class=\"swiper product-cate-slider\" id=\"{$widgetId}\">";
        $html .= '<div class="swiper-wrapper">';

        foreach ($categories as $category) {
            $categoryUrl = $projectCode ? "/{$projectCode}/danh-muc/{$category->slug}" : "/danh-muc/{$category->slug}";

            $image = isset($category->meta_data['image']) && $category->meta_data['image']
                ? $category->meta_data['image']
                : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='200' viewBox='0 0 300 200'%3E%3Crect width='300' height='200' fill='%23e5e7eb'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='sans-serif' font-size='14' fill='%239ca3af'%3E" . urlencode($category->name) . "%3C/text%3E%3C/svg%3E";

            $productCount = $showCount ? $category->posts()->where('status', 'published')->count() : 0;
            $name = htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8');

            $html .= '<div class="swiper-slide h-auto">';
            $html .= '<a href="' . $categoryUrl . '" class="category-slide-card block group h-full flex flex-col">';
            $html .= '<div class="relative overflow-hidden rounded-xl shadow hover:shadow-xl transition-all duration-300">';
            $html .= "<img src=\"{$image}\" alt=\"{$name}\" class=\"w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300\">";
            $html .= '<div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent rounded-xl flex flex-col justify-end p-4">';
            $html .= "<h3 class=\"text-white font-bold text-base mb-1\">{$name}</h3>";
            if ($showCount) {
                $html .= "<p class=\"text-white/80 text-xs\">{$productCount} sản phẩm</p>";
            }
            $html .= '</div></div></a>';
            $html .= '</div>'; // End swiper-slide
        }

        $html .= '</div>'; // End swiper-wrapper

        if ($showPagination) {
            $html .= '<div class="swiper-pagination mt-4"></div>';
        }
        if ($showNavigation) {
            $html .= '<div class="swiper-button-prev product-cate-nav-prev"></div>';
            $html .= '<div class="swiper-button-next product-cate-nav-next"></div>';
        }

        $html .= '</div>'; // End swiper
        $html .= '</div></section>';

        return $this->wrapWithCodeInjections($html);
    }

    protected function renderEmptyState(string $title): string
    {
        return "<section class=\"product-cate-slider-widget py-16 bg-white\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"text-center text-gray-400 py-12\">
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
        return '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <style>
        .product-cate-slider-widget { position: relative; }
        .product-cate-slider-widget .swiper-slide { height: auto; }
        .product-cate-slider-widget .swiper { padding-bottom: 48px; padding-left: 40px; padding-right: 40px; }
        .product-cate-slider-widget .swiper-pagination { bottom: 0; }
        .product-cate-slider-widget .swiper-pagination-bullet-active { background: #2563eb; }
        .category-slide-card { display: block; }
        .category-slide-card:hover { text-decoration: none; }

        /* Custom nav buttons */
        .product-cate-nav-prev,
        .product-cate-nav-next {
            position: absolute;
            top: 50%;
            transform: translateY(-60%);
            z-index: 10;
            width: 40px !important;
            height: 40px !important;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #e5e7eb;
        }
        .product-cate-nav-prev:hover,
        .product-cate-nav-next:hover {
            background: #2563eb;
            box-shadow: 0 4px 16px rgba(37,99,235,0.3);
            border-color: #2563eb;
        }
        .product-cate-nav-prev { left: 0; }
        .product-cate-nav-next { right: 0; }
        .product-cate-nav-prev::after,
        .product-cate-nav-next::after {
            font-size: 14px !important;
            font-weight: 900 !important;
            color: #374151;
            transition: color 0.2s;
        }
        .product-cate-nav-prev:hover::after,
        .product-cate-nav-next:hover::after { color: #fff; }
        .product-cate-nav-prev.swiper-button-disabled,
        .product-cate-nav-next.swiper-button-disabled { opacity: 0.3; pointer-events: none; }
        </style>';
    }

    public function js(): string
    {
        $slidesPerView = (int) $this->get('slides_per_view', 5);
        $autoplay = $this->get('autoplay', false);
        $autoplayDelay = (int) $this->get('autoplay_delay', 3000);
        $showNavigation = $this->get('show_navigation', true);
        $showPagination = $this->get('show_pagination', true);

        $autoplayConfig = $autoplay ? "autoplay: { delay: {$autoplayDelay}, disableOnInteraction: false }," : '';
        $navConfig = $showNavigation ? "navigation: { nextEl: swiperEl.querySelector('.swiper-button-next'), prevEl: swiperEl.querySelector('.swiper-button-prev') }," : '';
        $paginationConfig = $showPagination ? "pagination: { el: swiperEl.querySelector('.swiper-pagination'), clickable: true }," : '';

        return "<script src=\"https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js\"></script>
        <script>
        (function() {
            function initCateSliders() {
                if (typeof Swiper === 'undefined') {
                    setTimeout(initCateSliders, 50);
                    return;
                }
                document.querySelectorAll('.product-cate-slider').forEach(function(swiperEl) {
                    if (swiperEl.swiper) return;
                    new Swiper(swiperEl, {
                        slidesPerView: 2,
                        spaceBetween: 16,
                        {$autoplayConfig}
                        {$navConfig}
                        {$paginationConfig}
                        breakpoints: {
                            480: { slidesPerView: 3, spaceBetween: 16 },
                            768: { slidesPerView: 4, spaceBetween: 20 },
                            1024: { slidesPerView: {$slidesPerView}, spaceBetween: 24 }
                        }
                    });
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCateSliders);
            } else {
                setTimeout(initCateSliders, 0);
            }
        })();
        </script>";
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Product Categories Slider',
            'description' => 'Hiển thị danh mục sản phẩm dạng Swiper slider',
            'category' => 'product',
            'icon' => '<path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'Danh Mục Sản Phẩm'],
                ['name' => 'limit', 'label' => 'Số danh mục', 'type' => 'number', 'default' => 12],
                ['name' => 'slides_per_view', 'label' => 'Số slide/màn hình (desktop)', 'type' => 'select', 'default' => '5', 'options' => ['3' => '3', '4' => '4', '5' => '5', '6' => '6']],
                ['name' => 'show_count', 'label' => 'Hiện số sản phẩm', 'type' => 'checkbox', 'default' => true],
                ['name' => 'only_parent', 'label' => 'Chỉ hiện danh mục cha', 'type' => 'checkbox', 'default' => true],
                ['name' => 'show_navigation', 'label' => 'Hiện nút Previous/Next', 'type' => 'checkbox', 'default' => true],
                ['name' => 'show_pagination', 'label' => 'Hiện phân trang (dots)', 'type' => 'checkbox', 'default' => true],
                ['name' => 'autoplay', 'label' => 'Tự động chạy', 'type' => 'checkbox', 'default' => false],
                ['name' => 'autoplay_delay', 'label' => 'Thời gian chuyển (ms)', 'type' => 'number', 'default' => 3000],
            ],
            'settings' => [
                'cacheable' => false,
            ],
        ];
    }
}
