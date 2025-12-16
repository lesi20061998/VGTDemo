# Widget Development Guide

## Cấu trúc Widget System

```
app/Widgets/
├── BaseWidget.php          # Base class cho tất cả widgets
├── WidgetRegistry.php      # Quản lý & đăng ký widgets
├── HeroWidget.php          # Example: Hero section
├── FeaturesWidget.php      # Example: Features grid
└── CtaWidget.php           # Example: Call-to-action
```

## Tạo Widget Mới

### Bước 1: Tạo Widget Class

Tạo file `app/Widgets/YourWidget.php`:

```php
<?php

namespace App\Widgets;

class YourWidget extends BaseWidget
{
    // 1. Render HTML
    public function render(): string
    {
        $title = $this->get('title', 'Default Title');
        
        return "
        <section class=\"your-widget\">
            <h2>{$title}</h2>
        </section>";
    }

    // 2. CSS riêng cho widget (optional)
    public function css(): string
    {
        return '<style>
        .your-widget { 
            padding: 2rem; 
            background: #f3f4f6; 
        }
        </style>';
    }

    // 3. JavaScript riêng cho widget (optional)
    public function js(): string
    {
        return '<script>
        console.log("Your widget loaded");
        </script>';
    }

    // 4. Config cho widget builder
    public static function getConfig(): array
    {
        return [
            'name' => 'Your Widget Name',
            'description' => 'Widget description',
            'icon' => '<path d="M12 2L2 7l10 5 10-5-10-5z"/>',
            'fields' => [
                [
                    'name' => 'title',
                    'label' => 'Title',
                    'type' => 'text',
                    'default' => 'Default Title'
                ],
            ]
        ];
    }
}
```

### Bước 2: Đăng ký Widget

Mở `app/Widgets/WidgetRegistry.php` và thêm:

```php
protected static $widgets = [
    'hero' => HeroWidget::class,
    'features' => FeaturesWidget::class,
    'cta' => CtaWidget::class,
    'your_widget' => YourWidget::class,  // ← Thêm dòng này
];
```

### Bước 3: Thêm vào Builder UI

Mở `resources/views/admin/widgets/builder.blade.php` và thêm template:

```html
<div class="widget-template border-2 border-dashed border-gray-300 rounded-lg p-4 cursor-move hover:border-blue-500 hover:bg-blue-50 transition" draggable="true" data-type="your_widget">
    <div class="flex items-center gap-3">
        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L2 7l10 5 10-5-10-5z"/>
        </svg>
        <div>
            <h4 class="font-semibold">Your Widget</h4>
            <p class="text-xs text-gray-500">Widget description</p>
        </div>
    </div>
</div>
```

Thêm default settings trong JavaScript:

```javascript
function getDefaultSettings(type) {
    const defaults = {
        hero: { /* ... */ },
        features: { /* ... */ },
        cta: { /* ... */ },
        your_widget: {  // ← Thêm config này
            title: 'Default Title'
        }
    };
    return defaults[type] || {};
}
```

### Bước 4: Thêm Config Form

Trong function `renderConfigForm()`, thêm:

```javascript
function renderConfigForm(widget) {
    if (widget.type === 'your_widget') {
        return `
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" id="cfg_title" value="${widget.settings.title}" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
        `;
    }
    // ... other widgets
}
```

Và trong `saveConfig()`:

```javascript
function saveConfig() {
    const widget = widgets[currentEditIndex];
    
    if (widget.type === 'your_widget') {
        widget.settings.title = document.getElementById('cfg_title').value;
    }
    // ... other widgets
}
```

## API Reference

### BaseWidget Methods

```php
// Lấy giá trị setting
$this->get('key', 'default_value')

// Required methods
public function render(): string          // Return HTML
public static function getConfig(): array // Return config

// Optional methods
public function css(): string             // Return CSS
public function js(): string              // Return JavaScript
```

### Field Types

```php
'fields' => [
    // Text input
    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Text'],
    
    // Textarea
    ['name' => 'content', 'label' => 'Content', 'type' => 'textarea', 'default' => ''],
    
    // Repeater (array of items)
    ['name' => 'items', 'label' => 'Items', 'type' => 'repeater', 'default' => []],
]
```

## Examples

### Simple Text Widget

```php
class TextWidget extends BaseWidget
{
    public function render(): string
    {
        return "<div class='text-widget'>" . $this->get('content') . "</div>";
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Text Block',
            'description' => 'Simple text content',
            'icon' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
            'fields' => [
                ['name' => 'content', 'label' => 'Content', 'type' => 'textarea', 'default' => '']
            ]
        ];
    }
}
```

### Image Gallery Widget

```php
class GalleryWidget extends BaseWidget
{
    public function render(): string
    {
        $images = $this->get('images', []);
        $html = '<div class="gallery grid grid-cols-3 gap-4">';
        
        foreach ($images as $img) {
            $html .= "<img src='{$img}' class='rounded-lg'>";
        }
        
        $html .= '</div>';
        return $html;
    }

    public function css(): string
    {
        return '<style>
        .gallery img { transition: transform 0.3s; }
        .gallery img:hover { transform: scale(1.05); }
        </style>';
    }

    public static function getConfig(): array
    {
        return [
            'name' => 'Image Gallery',
            'description' => 'Grid of images',
            'icon' => '<path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
            'fields' => [
                ['name' => 'images', 'label' => 'Images', 'type' => 'repeater', 'default' => []]
            ]
        ];
    }
}
```

## Best Practices

1. **Unique CSS Classes**: Dùng prefix cho class names để tránh conflict
2. **Scoped JavaScript**: Wrap JS trong IIFE hoặc dùng event delegation
3. **Default Values**: Luôn có default values cho tất cả settings
4. **Responsive**: Dùng Tailwind responsive classes (md:, lg:)
5. **Performance**: Cache widget output nếu cần

## Testing

Test widget tại:
- Builder: `http://localhost:8000/SiVGT/admin/widgets`
- Frontend: `http://localhost:8000/SiVGT/`

## Troubleshooting

**Widget không hiển thị?**
- Kiểm tra đã đăng ký trong `WidgetRegistry.php`
- Kiểm tra `render()` method return HTML string

**CSS/JS không hoạt động?**
- Kiểm tra syntax trong `css()` và `js()` methods
- Kiểm tra class names match giữa HTML và CSS

**Config form không lưu?**
- Thêm logic trong `renderConfigForm()` và `saveConfig()`
- Kiểm tra field names match với settings keys
