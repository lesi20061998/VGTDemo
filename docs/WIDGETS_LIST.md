# Danh Sách Widgets Từ Core Template

Tất cả các component từ `resources/core-template/components` đã được chuyển đổi thành widgets cho Core CMS.

## Layout Widgets
- **HeaderWidget** (`header`) - Header với navigation và thông tin liên hệ
- **FooterWidget** (`footer`) - Footer với thông tin công ty

## Hero/Banner Widgets
- **HeroWidget** (`hero`) - Hero section với CTA
- **FeaturesWidget** (`features`) - Features showcase
- **BentoGridHomeWidget** (`bento_grid_home`) - Bento grid layout cho homepage
- **BannerTop01Widget** (`banner_top_01`) - Hero banner với breadcrumbs
- **BannerTop02Widget** (`banner_top_02`) - Alternative hero banner

## About Widgets
- **AboutBanner01Widget** (`about_banner_01`) - About section với images
- **AboutBanner02Widget** (`about_banner_02`) - About section style 2
- **AboutBanner03Widget** (`about_banner_03`) - About section style 3 (văn hóa)
- **AboutProductWidget** (`about_product`) - Product showcase trong about
- **AboutAchievementsWidget** (`about_achievements`) - Thành tựu và chứng chỉ
- **CoreValuesWidget** (`core_values`) - Giá trị cốt lõi
- **VisionMissionWidget** (`vision_mission`) - Tầm nhìn & sứ mệnh
- **HistoryWidget** (`history`) - Lịch sử hình thành

## Product Widgets
- **ProductListWidget** (`product_list`) - Danh sách sản phẩm (có sẵn)
- **ProductsWidget** (`products`) - Product grid display
- **ProductCateWidget** (`product_cate`) - Product category list

## Category Widgets
- **CategoryWidget** (`category`) - Product categories grid
- **HomeCateWidget** (`home_cate`) - Home page category showcase

## Brand/Partner Widgets
- **Brand01Widget** (`brand_01`) - Brand/Partner logos showcase
- **Brand02Widget** (`brand_02`) - Alternative brand showcase

## News/Post Widgets
- **PostListWidget** (`post_list`) - Danh sách bài viết (có sẵn)
- **PostSliderWidget** (`post_slider`) - Post slider (có sẵn)
- **NewsArticleWidget** (`news_article`) - News articles list
- **NewsFeaturedWidget** (`news_featured`) - Featured news posts
- **RelatedPostsWidget** (`related_posts`) - Bài viết liên quan

## Marketing Widgets
- **CtaWidget** (`cta`) - Call to action (có sẵn)
- **NewsletterWidget** (`newsletter`) - Newsletter signup (có sẵn)
- **TestimonialWidget** (`testimonial`) - Testimonials (có sẵn)

## Cách Sử Dụng

### 1. Trong Page Builder
Các widget này sẽ tự động xuất hiện trong page builder của CMS.

### 2. Trong Code
```php
use App\Widgets\WidgetRegistry;

// Render widget
echo WidgetRegistry::render('header', [
    'logo' => '/img/logo.png',
    'email' => 'contact@example.com',
    'phone' => '0123456789'
]);
```

### 3. Trong Database
```php
use App\Models\Widget;

Widget::create([
    'name' => 'Homepage Header',
    'type' => 'header',
    'area' => 'header',
    'settings' => json_encode([
        'logo' => '/img/logo.png',
        'email' => 'contact@example.com',
        'phone' => '0123456789'
    ]),
    'sort_order' => 1,
    'is_active' => true,
]);
```

## Lưu Ý
- Mỗi widget cần có view tương ứng trong `resources/views/components/widgets/`
- HTML từ core-template components cần được chuyển đổi sang Blade templates
- CSS và JS từ components cần được tích hợp vào widget hoặc assets chung
