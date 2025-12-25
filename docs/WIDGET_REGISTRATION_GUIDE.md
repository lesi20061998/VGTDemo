# Hướng dẫn đăng ký Widget

## Các cách đăng ký Widget

### 1. Sử dụng Artisan Command (Khuyến nghị)

Cách dễ nhất để tạo widget mới:

```bash
php artisan make:widget TenWidget --category=ten_category
```

Ví dụ:
```bash
php artisan make:widget NewsWidget --category=content
php artisan make:widget ProductShowcase --category=product
php artisan make:widget ContactForm --category=marketing
```

Command này sẽ tạo:
- File widget class: `app/Widgets/{Category}/{WidgetName}/{WidgetName}Widget.php`
- File metadata: `app/Widgets/{Category}/{WidgetName}/widget.json`
- File view mặc định: `app/Widgets/{Category}/{WidgetName}/views/default.blade.php`
- Thư mục assets: `app/Widgets/{Category}/{WidgetName}/assets/`

### 2. Tạo Widget thủ công

#### Bước 1: Tạo cấu trúc thư mục
```
app/Widgets/
└── {Category}/
    └── {WidgetName}/
        ├── {WidgetName}Widget.php
        ├── widget.json
        ├── views/
        │   └── default.blade.php
        └── assets/
            ├── style.css
            └── script.js
```

#### Bước 2: Tạo Widget Class

```php
<?php

namespace App\Widgets\{Category}\{WidgetName};

use App\Widgets\BaseWidget;

class {WidgetName}Widget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->getSetting('title', 'Tiêu đề mặc định');
        
        return "
        <div class='my-widget'>
            <h3>{$title}</h3>
        </div>
        ";
    }

    public function getPreview(): string
    {
        return $this->render();
    }

    public function validateSettings(): bool
    {
        // Validation logic
        return true;
    }

    public static function getMetadataPath(): string
    {
        return __DIR__ . '/widget.json';
    }
}
```

#### Bước 3: Tạo file metadata (widget.json)

```json
{
    "name": "Tên Widget",
    "description": "Mô tả widget",
    "category": "category_name",
    "icon": "fas fa-icon",
    "version": "1.0.0",
    "author": "Tên tác giả",
    "tags": ["tag1", "tag2"],
    "variants": {
        "default": "Mặc định",
        "style2": "Kiểu 2"
    },
    "fields": [
        {
            "name": "title",
            "type": "text",
            "label": "Tiêu đề",
            "required": true,
            "default": "Tiêu đề mặc định"
        }
    ],
    "settings": {
        "cacheable": true,
        "cache_duration": 3600,
        "permissions": [],
        "dependencies": []
    }
}
```

### 3. Đăng ký Widget trong Registry

Sau khi tạo widget, bạn cần đăng ký nó trong `app/Widgets/WidgetRegistry.php`:

#### Bước 1: Import class
```php
use App\Widgets\{Category}\{WidgetName}\{WidgetName}Widget;
```

#### Bước 2: Thêm vào mảng $widgets
```php
protected static array $widgets = [
    // ... các widget khác
    'widget_type' => {WidgetName}Widget::class,
];
```

### 4. Kiểm tra Widget đã đăng ký

```bash
# Xem danh sách tất cả widget
php artisan widget:list

# Phát hiện widget mới (auto-discovery)
php artisan widget:discover

# Validate widget
php artisan widget:validate widget_type
```

## Các loại Field hỗ trợ

- `text` - Trường văn bản
- `textarea` - Trường văn bản nhiều dòng
- `select` - Dropdown
- `checkbox` - Checkbox
- `image` - Upload ảnh
- `gallery` - Upload nhiều ảnh
- `url` - URL
- `email` - Email
- `number` - Số
- `date` - Ngày
- `color` - Màu sắc
- `range` - Thanh trượt
- `repeatable` - Trường lặp lại

## Ví dụ Widget hoàn chỉnh

Xem các widget mẫu tại:
- `app/Widgets/Custom/SimpleText/` - Widget văn bản đơn giản
- `app/Widgets/Custom/TestWidget/` - Widget test
- `app/Widgets/Hero/HeroWidget.php` - Widget hero section
- `app/Widgets/Marketing/ContactFormWidget.php` - Widget form liên hệ

## Lưu ý quan trọng

1. **Namespace**: Đảm bảo namespace đúng theo cấu trúc thư mục
2. **Metadata**: File `widget.json` phải có cấu trúc đúng
3. **BaseWidget**: Widget class phải extend từ `BaseWidget`
4. **Registry**: Phải đăng ký trong `WidgetRegistry.php` để sử dụng
5. **Cache**: Chạy `php artisan widget:discover` sau khi tạo widget mới

## Troubleshooting

### Widget không hiển thị trong danh sách
- Kiểm tra namespace
- Kiểm tra file `widget.json`
- Chạy `php artisan widget:discover`
- Kiểm tra đã đăng ký trong Registry chưa

### Lỗi Class not found
- Kiểm tra namespace trong file PHP
- Kiểm tra import trong `WidgetRegistry.php`
- Chạy `composer dump-autoload`

### Widget không render
- Kiểm tra method `render()`
- Kiểm tra settings và validation
- Xem log lỗi trong `storage/logs/`