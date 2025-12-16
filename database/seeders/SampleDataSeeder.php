<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\PostType;
use App\Models\Category;
use App\Models\PostMeta;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionItem;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Tạo categories từ @dist
        $categories = [
            ['name' => 'NIKE', 'slug' => 'nike', 'type' => 'product'],
            ['name' => 'ADIDAS', 'slug' => 'adidas', 'type' => 'product'],
            ['name' => 'MIZUNO', 'slug' => 'mizuno', 'type' => 'product'],
            ['name' => 'KAMITO', 'slug' => 'kamito', 'type' => 'product'],
            ['name' => 'PUMA', 'slug' => 'puma', 'type' => 'product'],
            ['name' => 'PAN', 'slug' => 'pan', 'type' => 'product'],
            ['name' => 'ZOCKER', 'slug' => 'zocker', 'type' => 'product'],
            ['name' => 'WIKA', 'slug' => 'wika', 'type' => 'product'],
            ['name' => 'GIÀY FUTSAL', 'slug' => 'giay-futsal', 'type' => 'product'],
            ['name' => 'GIÀY SÂN CỎ', 'slug' => 'giay-san-co', 'type' => 'product'],
            ['name' => 'GIÀY TRẺ EM', 'slug' => 'giay-tre-em', 'type' => 'product'],
            ['name' => 'PHỤ KIỆN', 'slug' => 'phu-kien', 'type' => 'product'],
            ['name' => 'TIN TỨC', 'slug' => 'tin-tuc', 'type' => 'article'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Tạo sản phẩm mẫu
        $productType = PostType::where('name', 'product')->first();
        $articleType = PostType::where('name', 'article')->first();

        // Sản phẩm
        for ($i = 1; $i <= 20; $i++) {
            $product = Post::create([
                'title' => "Giày đá banh Nike Phantom {$i}",
                'slug' => "giay-da-banh-nike-phantom-{$i}",
                'content' => "Mô tả chi tiết sản phẩm giày đá banh Nike Phantom {$i}. Chất liệu cao cấp, thiết kế hiện đại.",
                'excerpt' => "Giày đá banh Nike Phantom {$i} - Chất lượng cao",
                'status' => 'published',
                'post_type_id' => $productType->id,
                'featured_image' => '/assets/img/products/related-product.svg',
            ]);

            // Meta cho sản phẩm
            PostMeta::create(['post_id' => $product->id, 'meta_key' => 'price', 'meta_value' => rand(500000, 2000000)]);
            PostMeta::create(['post_id' => $product->id, 'meta_key' => 'old_price', 'meta_value' => rand(600000, 2500000)]);
            PostMeta::create(['post_id' => $product->id, 'meta_key' => 'sizes', 'meta_value' => json_encode(['35.5', '36', '36.5', '37', '38', '39', '40', '41'])]);
            PostMeta::create(['post_id' => $product->id, 'meta_key' => 'is_flash_sale', 'meta_value' => $i <= 6 ? '1' : '0']);

            // Gán category
            $categoryId = rand(1, 12);
            $product->categories()->attach($categoryId);
        }

        // Bài viết
        for ($i = 1; $i <= 10; $i++) {
            $article = Post::create([
                'title' => "Ra mắt bộ sưu tập giày cỏ nhân tạo {$i}",
                'slug' => "ra-mat-bo-suu-tap-giay-co-nhan-tao-{$i}",
                'content' => "Khám phá chương trình ưu đãi đặc biệt dành riêng cho tín đồ bóng đá tháng 9 này. Sở hữu đôi Phantom 6 Academy với mức giá ưu đãi và nhiều quà tặng kèm hấp dẫn.",
                'excerpt' => "Khám phá chương trình ưu đãi đặc biệt dành riêng cho tín đồ bóng đá",
                'status' => 'published',
                'post_type_id' => $articleType->id,
                'featured_image' => "/assets/img/products/product-article-{$i}.png",
            ]);

            $article->categories()->attach(13); // Tin tức category
        }

        // Tạo trang Home với sections
        $homePage = Page::create([
            'title' => 'Trang chủ',
            'slug' => 'home',
            'content' => 'Trang chủ website',
            'status' => 'published',
            'is_homepage' => true,
        ]);

        // Main Visual Section
        $mainVisualSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'mainvisual',
            'title' => 'Main Visual',
            'order' => 1,
            'is_active' => true,
        ]);

        PageSectionItem::create([
            'page_section_id' => $mainVisualSection->id,
            'type' => 'image',
            'content' => json_encode(['image' => '/assets/img/top/mainvisual.jpg', 'alt' => 'Main Visual']),
            'order' => 1,
        ]);

        // Flash Sale Section
        $flashSaleSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'flash_sale',
            'title' => 'Flash Sale',
            'order' => 2,
            'is_active' => true,
        ]);

        PageSectionItem::create([
            'page_section_id' => $flashSaleSection->id,
            'type' => 'countdown',
            'content' => json_encode([
                'days' => 10,
                'hours' => 9,
                'minutes' => 30,
                'seconds' => 59,
                'banner' => '/assets/img/top/fsale_banner.jpg'
            ]),
            'order' => 1,
        ]);

        // Brand Categories Section
        $brandSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'brand_categories',
            'title' => 'Danh mục thương hiệu',
            'order' => 3,
            'is_active' => true,
        ]);

        $brands = ['NIKE', 'PHỤ KIỆN', 'MIZUNO', 'ZOCKER', 'WIKA', 'PAN', 'KAMITO', 'GIÀY TRẺ EM'];
        foreach ($brands as $index => $brand) {
            PageSectionItem::create([
                'page_section_id' => $brandSection->id,
                'type' => 'brand',
                'content' => json_encode(['name' => $brand, 'link' => '#']),
                'order' => $index + 1,
            ]);
        }

        // Ad Banners Section
        $adSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'ad_banners',
            'title' => 'Quảng cáo',
            'order' => 4,
            'is_active' => true,
        ]);

        PageSectionItem::create([
            'page_section_id' => $adSection->id,
            'type' => 'banner',
            'content' => json_encode(['image' => '/assets/img/top/ad_banner1.jpg']),
            'order' => 1,
        ]);

        PageSectionItem::create([
            'page_section_id' => $adSection->id,
            'type' => 'banner',
            'content' => json_encode(['image' => '/assets/img/top/ad_banner2.jpg']),
            'order' => 2,
        ]);

        // Features Section
        $featuresSection = PageSection::create([
            'page_id' => $homePage->id,
            'section_type' => 'features',
            'title' => 'Tại sao chọn chúng tôi',
            'order' => 5,
            'is_active' => true,
        ]);

        $features = [
            ['icon' => '/assets/img/icon/delivery-man.svg', 'title' => 'SHIP TOÀN QUỐC', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor'],
            ['icon' => '/assets/img/icon/award.svg', 'title' => 'CAM KẾT CHẤT LƯỢNG', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor'],
            ['icon' => '/assets/img/icon/best-price.svg', 'title' => 'GIÁ CẢ CẠNH TRANH', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor'],
            ['icon' => '/assets/img/icon/handshake.svg', 'title' => 'TUYỂN ĐẠI LÝ - CTV', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor'],
        ];

        foreach ($features as $index => $feature) {
            PageSectionItem::create([
                'page_section_id' => $featuresSection->id,
                'type' => 'feature',
                'content' => json_encode($feature),
                'order' => $index + 1,
            ]);
        }
    }
}