# Hệ thống Shortcode & Archive Templates

## Mục lục
1. [Giới thiệu](#giới-thiệu)
2. [Shortcode System](#shortcode-system)
3. [Archive Templates](#archive-templates)
4. [Hướng dẫn sử dụng](#hướng-dẫn-sử-dụng)
5. [Tạo Shortcode mới](#tạo-shortcode-mới)
6. [API Reference](#api-reference)
7. [Roadmap phát triển](#roadmap-phát-triển)

---

## Giới thiệu

Hệ thống Shortcode cho phép nhúng nội dung động vào bất kỳ đâu trong ứng dụng thông qua cú pháp đơn giản `[shortcode attr="value"]`. Tương tự như WordPress shortcodes.

### Các file chính:
```
app/
├── Services/
│   └── ShortcodeService.php          # Core shortcode engine
├── Models/
│   └── ArchiveTemplate.php           # Model cho archive templates
├── Helpers/
│   └── shortcode_helper.php          # Helper functions
├── Providers/
│   └── ShortcodeServiceProvider.php  # Service provider & Blade directives
├── Http/Controllers/Frontend/
│   └── ArchiveController.php         # Controller cho archive pages

resources/views/
├── components/shortcodes/            # Shortcode templates
│   ├── products-grid.blade.php
│   ├── products-list.blade.php
│   ├── posts-grid.blade.php
│   ├── categories-grid.blade.php
│   └── gallery.blade.php
├── frontend/archives/                # Archive page templates
│   ├── product.blade.php
│   └── post.blade.php

database/migrations/
└── 2025_12_29_000001_create_archive_templates_table.php
```

---

## Shortcode System

### Shortcodes có sẵn

#### 1. Products Shortcode
Hiển thị danh sách sản phẩm.

```
[products limit="6" columns="3" category="1" template="grid" orderby="created_at" order="desc"]
```

| Attribute | Mô tả | Mặc định | Giá trị |
|-----------|-------|----------|---------|
| `limit` | Số sản phẩm | 6 | số nguyên |
| `columns` | Số cột | 3 | 1-6 |
| `category` | ID danh mục | null | số nguyên |
| `template` | Kiểu hiển thị | grid | grid, list |
| `orderby` | Sắp xếp theo | created_at | created_at, price, name, sold_count |
| `order` | Thứ tự | desc | asc, desc |
| `show_price` | Hiện giá | true | true, false |
| `show_button` | Hiện nút | true | true, false |

#### 2. Posts Shortcode
Hiển thị danh sách bài viết.

```
[posts limit="4" columns="2" category="news" type="post" template="grid"]
```

| Attribute | Mô tả | Mặc định | Giá trị |
|-----------|-------|----------|---------|
| `limit` | Số bài viết | 4 | số nguyên |
| `columns` | Số cột | 2 | 1-4 |
| `category` | Slug danh mục | null | string |
| `type` | Loại bài viết | post | post, news, blog, page |
| `template` | Kiểu hiển thị | grid | grid, list |
| `show_excerpt` | Hiện tóm tắt | true | true, false |
| `show_date` | Hiện ngày | true | true, false |

#### 3. Categories Shortcode
Hiển thị danh mục sản phẩm.

```
[categories type="product" limit="6" columns="3" show_count="true"]
```

| Attribute | Mô tả | Mặc định | Giá trị |
|-----------|-------|----------|---------|
| `type` | Loại | product | product, post |
| `limit` | Số danh mục | 6 | số nguyên |
| `columns` | Số cột | 3 | 2-6 |
| `show_count` | Hiện số lượng | true | true, false |

#### 4. Widget Shortcode
Nhúng widget vào content.

```
[widget type="hero" title="Tiêu đề" subtitle="Mô tả" button_text="Xem thêm"]
```

| Attribute | Mô tả | Mặc định |
|-----------|-------|----------|
| `type` | Loại widget (bắt buộc) | - |
| `...` | Các settings của widget | - |

#### 5. Button Shortcode
Tạo nút bấm.

```
[button url="/contact" text="Liên hệ ngay" style="primary" target="_blank"]
```

| Attribute | Mô tả | Mặc định | Giá trị |
|-----------|-------|----------|---------|
| `url` | Đường dẫn | # | URL |
| `text` | Nội dung | Click here | string |
| `style` | Kiểu dáng | primary | primary, secondary, outline, danger |
| `target` | Target | _self | _self, _blank |
| `icon` | Icon HTML | - | HTML string |

#### 6. Image Shortcode
Chèn hình ảnh.

```
[image src="/images/banner.jpg" alt="Banner" class="rounded-lg" link="/products"]
```

| Attribute | Mô tả | Mặc định |
|-----------|-------|----------|
| `src` | Đường dẫn ảnh | - |
| `alt` | Alt text | - |
| `class` | CSS classes | w-full h-auto rounded-lg |
| `link` | Link khi click | - |

#### 7. Gallery Shortcode
Hiển thị gallery ảnh.

```
[gallery ids="1,2,3,4,5" columns="3"]
```

| Attribute | Mô tả | Mặc định |
|-----------|-------|----------|
| `ids` | IDs của media (phân cách bởi dấu phẩy) | - |
| `columns` | Số cột | 3 |

#### 8. Map Shortcode
Nhúng Google Maps.

```
[map lat="10.762622" lng="106.660172" zoom="15" height="400"]
```

| Attribute | Mô tả | Mặc định |
|-----------|-------|----------|
| `lat` | Vĩ độ | 10.762622 |
| `lng` | Kinh độ | 106.660172 |
| `zoom` | Mức zoom | 15 |
| `height` | Chiều cao (px) | 400 |

#### 9. Archive Shortcode
Render archive template.

```
[archive type="product" template="default"]
```

| Attribute | Mô tả | Mặc định |
|-----------|-------|----------|
| `type` | Loại archive | product |
| `template` | Template slug | default |

#### 10. Contact Form Shortcode
Hiển thị form liên hệ.

```
[contact_form]
```

---

## Archive Templates

Archive Templates cho phép tùy chỉnh giao diện trang danh sách (products, posts, categories).

### Database Schema

```sql
CREATE TABLE archive_templates (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    name VARCHAR(255),
    type VARCHAR(255),           -- product, post, category
    slug VARCHAR(255) UNIQUE,
    description TEXT,
    template_code LONGTEXT,      -- Blade template code
    template_css LONGTEXT,
    template_js LONGTEXT,
    config JSON,                 -- Cấu hình: pagination, columns, filters...
    is_default BOOLEAN,
    is_active BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Cấu trúc Config JSON

```json
{
    "pagination": {
        "per_page": 12,
        "style": "simple"
    },
    "layout": {
        "columns": 3,
        "gap": "6"
    },
    "filters": {
        "show_category": true,
        "show_price": true,
        "show_brand": true,
        "show_sort": true
    },
    "display": {
        "show_sidebar": true,
        "sidebar_position": "left"
    }
}
```

### Sử dụng Archive Template

```php
// Trong Controller
$template = ArchiveTemplate::getDefault('product');
return $template->render([
    'products' => $products,
    'categories' => $categories,
]);

// Hoặc dùng helper
return render_archive('product', $data);
```

---

## Hướng dẫn sử dụng

### 1. Trong Blade Template

```blade
{{-- Parse shortcodes trong content từ database --}}
{!! parse_shortcodes($post->content) !!}

{{-- Hoặc dùng Blade directive --}}
@shortcodes($post->content)

{{-- Gọi shortcode trực tiếp --}}
@shortcode('products', ['limit' => 6, 'columns' => 3])

{{-- Render archive --}}
@archive('product', ['products' => $products])
```

### 2. Trong PHP Code

```php
// Parse shortcodes
$html = parse_shortcodes($content);

// Render single shortcode
$html = shortcode('products', ['limit' => 6]);

// Render archive
$html = render_archive('product', $data);

// Register custom shortcode
register_shortcode('my_shortcode', function($attrs) {
    return view('shortcodes.my-shortcode', $attrs)->render();
});
```

### 3. Trong WYSIWYG Editor / Content

```html
<div class="my-section">
    <h2>Sản phẩm nổi bật</h2>
    [products limit="6" columns="3" category="1"]
</div>

<div class="blog-section">
    <h2>Tin tức mới nhất</h2>
    [posts limit="4" columns="2"]
</div>
```

---

## Tạo Shortcode mới

### Cách 1: Trong ShortcodeServiceProvider

```php
// app/Providers/ShortcodeServiceProvider.php

protected function registerCustomShortcodes(): void
{
    $shortcodeService = $this->app->make(ShortcodeService::class);

    // Shortcode đơn giản
    $shortcodeService->register('hello', function($attrs) {
        $name = $attrs['name'] ?? 'World';
        return "<p>Hello, {$name}!</p>";
    });

    // Shortcode với view
    $shortcodeService->register('testimonials', function($attrs) {
        $limit = $attrs['limit'] ?? 3;
        $testimonials = \App\Models\Testimonial::limit($limit)->get();
        return view('components.shortcodes.testimonials', [
            'testimonials' => $testimonials,
            'attrs' => $attrs,
        ])->render();
    });
}
```

### Cách 2: Trong ShortcodeService

```php
// app/Services/ShortcodeService.php

protected function registerDefaultShortcodes(): void
{
    // ... existing shortcodes ...

    // Thêm shortcode mới
    $this->register('my_shortcode', function ($attrs) {
        return $this->renderMyShortcode($attrs);
    });
}

protected function renderMyShortcode(array $attrs): string
{
    // Logic xử lý
    return view('components.shortcodes.my-shortcode', $attrs)->render();
}
```

### Cách 3: Runtime Registration

```php
// Trong AppServiceProvider hoặc bất kỳ đâu
register_shortcode('promo_banner', function($attrs) {
    return view('shortcodes.promo-banner', $attrs)->render();
});
```

### Tạo View cho Shortcode

```blade
{{-- resources/views/components/shortcodes/testimonials.blade.php --}}
@props(['testimonials', 'attrs' => []])

<div class="testimonials-grid grid grid-cols-{{ $attrs['columns'] ?? 3 }} gap-6">
    @foreach($testimonials as $item)
        <div class="testimonial-card bg-white p-6 rounded-xl shadow">
            <p class="text-gray-600 italic mb-4">"{{ $item->content }}"</p>
            <div class="flex items-center gap-3">
                <img src="{{ $item->avatar }}" class="w-12 h-12 rounded-full">
                <div>
                    <p class="font-semibold">{{ $item->name }}</p>
                    <p class="text-sm text-gray-500">{{ $item->position }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
```

---

## API Reference

### ShortcodeService

```php
// Đăng ký shortcode
$service->register(string $tag, callable $callback): void

// Parse content
$service->parse(string $content): string

// Kiểm tra shortcode tồn tại
$service->exists(string $tag): bool

// Lấy danh sách shortcodes
$service->getAll(): array
```

### Helper Functions

```php
// Parse shortcodes trong content
parse_shortcodes(string $content): string

// Render single shortcode
shortcode(string $tag, array $attrs = []): string

// Đăng ký shortcode mới
register_shortcode(string $tag, callable $callback): void

// Render archive template
render_archive(string $type, array $data = [], ?string $templateSlug = null): string

// Lấy archive templates theo type
get_archive_templates(string $type): Collection
```

### Blade Directives

```blade
{{-- Parse shortcodes --}}
@shortcodes($content)

{{-- Render shortcode --}}
@shortcode('tag', ['attr' => 'value'])

{{-- Render archive --}}
@archive('type', $data)
```

### ArchiveTemplate Model

```php
// Lấy template mặc định
ArchiveTemplate::getDefault(string $type): ?ArchiveTemplate

// Render template
$template->render(array $data = []): string

// Lấy CSS/JS từ file
$template->getCss(): string
$template->getJs(): string
```

---

## Roadmap phát triển

### Phase 1: Core System ✅
- [x] ShortcodeService với các shortcodes cơ bản
- [x] Archive Templates model và migration
- [x] Helper functions và Blade directives
- [x] Views mẫu cho products, posts, categories

### Phase 2: Admin UI (TODO)
- [ ] Trang quản lý Shortcodes
  - [ ] Danh sách shortcodes có sẵn
  - [ ] Preview shortcode
  - [ ] Copy shortcode syntax
- [ ] Trang quản lý Archive Templates
  - [ ] CRUD Archive Templates
  - [ ] Code editor cho template_code, CSS, JS
  - [ ] Preview template
  - [ ] Set default template

### Phase 3: Advanced Features (TODO)
- [ ] Shortcode Builder UI (drag & drop)
- [ ] Nested shortcodes support
- [ ] Shortcode caching
- [ ] Shortcode analytics (usage tracking)
- [ ] Import/Export templates
- [ ] Template marketplace

### Phase 4: Integration (TODO)
- [ ] Tích hợp với Page Builder
- [ ] Tích hợp với WYSIWYG Editor (TinyMCE, CKEditor)
- [ ] Shortcode button trong editor toolbar
- [ ] Live preview trong editor

---

## Ví dụ thực tế

### Trang chủ với Shortcodes

```blade
@extends('frontend.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Hero Section --}}
    [widget type="hero" title="Chào mừng" subtitle="Khám phá sản phẩm tuyệt vời"]
    
    {{-- Featured Products --}}
    <section class="my-12">
        <h2 class="text-2xl font-bold mb-6">Sản phẩm nổi bật</h2>
        [products limit="8" columns="4" orderby="sold_count" order="desc"]
    </section>
    
    {{-- Categories --}}
    <section class="my-12">
        <h2 class="text-2xl font-bold mb-6">Danh mục sản phẩm</h2>
        [categories limit="6" columns="3"]
    </section>
    
    {{-- Latest Posts --}}
    <section class="my-12">
        <h2 class="text-2xl font-bold mb-6">Tin tức mới nhất</h2>
        [posts limit="3" columns="3"]
    </section>
    
    {{-- CTA --}}
    <section class="my-12 text-center bg-blue-50 py-12 rounded-xl">
        <h2 class="text-2xl font-bold mb-4">Liên hệ với chúng tôi</h2>
        <p class="text-gray-600 mb-6">Chúng tôi luôn sẵn sàng hỗ trợ bạn</p>
        [button url="/contact" text="Liên hệ ngay" style="primary"]
    </section>
</div>
@endsection
```

### Widget Template với Shortcode

```blade
{{-- Trong Widget Template Builder --}}
<div class="widget-featured-products">
    @if(!empty($settings['title']))
        <h2 class="text-2xl font-bold mb-6">{{ $settings['title'] }}</h2>
    @endif
    
    {{-- Sử dụng shortcode để hiển thị products --}}
    [products 
        limit="{{ $settings['limit'] ?? 6 }}" 
        columns="{{ $settings['columns'] ?? 3 }}"
        category="{{ $settings['category_id'] ?? '' }}"
    ]
</div>
```

---

## Troubleshooting

### Shortcode không hoạt động
1. Kiểm tra đã đăng ký `ShortcodeServiceProvider` trong `bootstrap/providers.php`
2. Chạy `composer dump-autoload`
3. Clear cache: `php artisan cache:clear && php artisan view:clear`

### Archive Template không render
1. Kiểm tra `is_active = true` và `is_default = true`
2. Kiểm tra view file tồn tại
3. Kiểm tra data truyền vào đúng format

### Lỗi "Class not found"
1. Chạy `composer dump-autoload`
2. Kiểm tra namespace đúng
3. Kiểm tra file helper đã được thêm vào `composer.json` autoload

---

## Liên hệ & Hỗ trợ

Nếu có câu hỏi hoặc cần hỗ trợ, vui lòng tạo issue hoặc liên hệ team phát triển.
