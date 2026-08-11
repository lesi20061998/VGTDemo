<?php

namespace App\Widgets\Product;

use App\Models\Post;
use App\Widgets\BaseWidget;

class ProductSliderWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Sản Phẩm Nổi Bật');
        $limit = $this->get('limit', 10);
        $showNavigation = $this->get('show_navigation', true);
        $showPagination = $this->get('show_pagination', true);
        $autoplay = $this->get('autoplay', false);
        $autoplayDelay = (int) $this->get('autoplay_delay', 3000);
        $slidesPerView = (int) $this->get('slides_per_view', 4);

        $query = Post::where('post_type', 'product')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc');

        $products = $query->limit($limit)->get();

        if ($products->isEmpty()) {
            return $this->renderEmptyState($title);
        }

        $projectCode = request()->route('projectCode');
        $widgetId = 'product-slider-' . uniqid();

        $watermark = setting('watermark', []);
        $watermarkEnabled = $watermark['enabled'] ?? false;

        $styleAttr = $this->buildWrapperStyleAttribute();
        $bgClasses = implode(' ', $this->getWrapperBgClasses());

        $html  = '<section class="product-slider-widget py-16 bg-white ' . $bgClasses . '"' . $styleAttr . '>';
        $html .= '<div class="container mx-auto px-4">';
        if ($title) {
            $html .= "<h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>";
        }
        $html .= "<div class=\"swiper product-slider\" id=\"{$widgetId}\">";
        $html .= '<div class="swiper-wrapper">';

        foreach ($products as $product) {
            $productUrl = $projectCode ? "/{$projectCode}/san-pham/{$product->slug}" : "/san-pham/{$product->slug}";
            $image = $this->getProductImage($product->featured_image, $watermarkEnabled);

            $priceValue = $product->meta_data['price'] ?? 0;
            $salePriceValue = $product->meta_data['sale_price'] ?? null;
            $price = $priceValue ? number_format($priceValue) . 'đ' : 'Liên hệ';
            $salePrice = $salePriceValue ? number_format($salePriceValue) . 'đ' : null;

            $isFeatured = ! empty($product->meta_data['is_featured']);
            $isBestseller = ! empty($product->meta_data['is_bestseller']);
            $name = htmlspecialchars($product->title, ENT_QUOTES, 'UTF-8');

            $html .= '<div class="swiper-slide">';
            $html .= '<div class="product-slide-card bg-white rounded-xl shadow hover:shadow-2xl transition-all duration-300 overflow-hidden group">';
            $html .= '<div class="relative overflow-hidden">';
            $html .= "<img src=\"{$image}\" alt=\"{$name}\" class=\"w-full h-52 object-cover group-hover:scale-105 transition-transform duration-300\">";

            // Badges
            $html .= '<div class="absolute top-2 left-2 flex flex-col gap-1">';
            if ($isFeatured) {
                $html .= '<span class="bg-yellow-400 text-yellow-900 text-xs px-2 py-0.5 rounded-full font-semibold">Nổi bật</span>';
            }
            if ($isBestseller) {
                $html .= '<span class="bg-green-500 text-white text-xs px-2 py-0.5 rounded-full font-semibold">Bán chạy</span>';
            }
            $html .= '</div>';

            if ($salePriceValue) {
                $html .= '<span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-semibold">Sale</span>';
            }
            $html .= '</div>';

            $html .= '<div class="p-4">';
            $html .= "<h3 class=\"font-bold text-gray-800 mb-2 line-clamp-2 text-sm\">{$name}</h3>";
            $html .= '<div class="flex items-center justify-between mt-auto">';
            if ($salePriceValue) {
                $html .= "<div><span class=\"text-gray-400 line-through text-xs\">{$price}</span><br><span class=\"text-base font-bold text-blue-600\">{$salePrice}</span></div>";
            } else {
                $html .= "<span class=\"text-base font-bold text-blue-600\">{$price}</span>";
            }
            $html .= "<a href=\"{$productUrl}\" class=\"bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 text-xs font-medium transition\">Xem</a>";
            $html .= '</div></div></div>';
            $html .= '</div>'; // End swiper-slide
        }

        $html .= '</div>'; // End swiper-wrapper

        if ($showPagination) {
            $html .= '<div class="swiper-pagination mt-4"></div>';
        }
        if ($showNavigation) {
            $html .= '<div class="swiper-button-prev product-slider-nav-prev"></div>';
            $html .= '<div class="swiper-button-next product-slider-nav-next"></div>';
        }

        $html .= '</div>'; // End swiper
        $html .= '</div></section>';

        return $this->wrapWithCodeInjections($html);
    }

    protected function getProductImage(?string $imagePath, bool $watermarkEnabled): string
    {
        if (empty($imagePath)) {
            return '/assets/img/placeholder-images-image_large.webp';
        }

        if ($watermarkEnabled && str_contains($imagePath, '/storage/media/')) {
            return str_replace('/storage/media/', '/media/', $imagePath);
        }

        return $imagePath;
    }

    protected function renderEmptyState(string $title): string
    {
        return "<section class=\"product-slider-widget py-16 bg-white\">
            <div class=\"container mx-auto px-4\">
                <h2 class=\"text-4xl font-bold text-center mb-12\">{$title}</h2>
                <div class=\"text-center text-gray-400 py-12\">
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
        return '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <style>
        .product-slider-widget { position: relative; }
        .product-slider-widget .swiper-slide { height: auto; }
        .product-slider-widget .swiper { padding-bottom: 48px; padding-left: 44px; padding-right: 44px; }
        .product-slider-widget .swiper-pagination { bottom: 0; }
        .product-slider-widget .swiper-pagination-bullet-active { background: #2563eb; }
        .product-slide-card { height: 100%; display: flex; flex-direction: column; }
        .product-slide-card .p-4 { flex: 1; display: flex; flex-direction: column; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

        /* Custom nav buttons */
        .product-slider-nav-prev,
        .product-slider-nav-next {
            position: absolute;
            top: 40%;
            transform: translateY(-50%);
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
        .product-slider-nav-prev:hover,
        .product-slider-nav-next:hover {
            background: #2563eb;
            box-shadow: 0 4px 16px rgba(37,99,235,0.3);
            border-color: #2563eb;
        }
        .product-slider-nav-prev { left: 0; }
        .product-slider-nav-next { right: 0; }
        .product-slider-nav-prev::after,
        .product-slider-nav-next::after {
            font-size: 14px !important;
            font-weight: 900 !important;
            color: #374151;
            transition: color 0.2s;
        }
        .product-slider-nav-prev:hover::after,
        .product-slider-nav-next:hover::after { color: #fff; }
        .product-slider-nav-prev.swiper-button-disabled,
        .product-slider-nav-next.swiper-button-disabled { opacity: 0.3; pointer-events: none; }
        </style>';
    }

    public function js(): string
    {
        $slidesPerView = (int) $this->get('slides_per_view', 4);
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
            function initProductSliders() {
                if (typeof Swiper === 'undefined') {
                    setTimeout(initProductSliders, 50);
                    return;
                }
                document.querySelectorAll('.product-slider').forEach(function(swiperEl) {
                    if (swiperEl.swiper) return;
                    new Swiper(swiperEl, {
                        slidesPerView: 1,
                        spaceBetween: 20,
                        {$autoplayConfig}
                        {$navConfig}
                        {$paginationConfig}
                        breakpoints: {
                            480: { slidesPerView: 2, spaceBetween: 16 },
                            768: { slidesPerView: 3, spaceBetween: 20 },
                            1024: { slidesPerView: {$slidesPerView}, spaceBetween: 24 }
                        }
                    });
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initProductSliders);
            } else {
                setTimeout(initProductSliders, 0);
            }
        })();
        </script>";
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Product Slider',
            'description' => 'Hiển thị sản phẩm dạng Swiper slider',
            'category' => 'product',
            'icon' => '<path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>',
            'fields' => [
                ['name' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => 'Sản Phẩm Nổi Bật'],
                ['name' => 'limit', 'label' => 'Số sản phẩm', 'type' => 'number', 'default' => 10],
                ['name' => 'slides_per_view', 'label' => 'Số slide/màn hình (desktop)', 'type' => 'select', 'default' => '4', 'options' => ['2' => '2', '3' => '3', '4' => '4', '5' => '5']],
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
