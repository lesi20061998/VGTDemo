# Widget Engine Documentation

## Overview

The Widget Engine is a flexible, extensible component system that allows developers to create reusable widgets with configurable fields while providing administrators with an intuitive interface to manage content.

## Table of Contents

1. [Getting Started](#getting-started)
2. [Creating Widgets](#creating-widgets)
3. [Widget Metadata](#widget-metadata)
4. [Field Types](#field-types)
5. [Widget Variants](#widget-variants)
6. [Permissions & Access Control](#permissions--access-control)
7. [Import/Export](#importexport)
8. [Best Practices](#best-practices)
9. [Troubleshooting](#troubleshooting)

## Getting Started

### Prerequisites

- Laravel 12+
- PHP 8.2+
- Composer

### Installation

The Widget Engine is already integrated into the CMS. To start using it:

1. Access the widget builder at `/admin/widgets`
2. Discover existing widgets: `php artisan widget:discover`
3. Create your first widget: `php artisan make:widget MyWidget --category=general`

## Creating Widgets

### Using Artisan Command

The easiest way to create a new widget is using the artisan command:

```bash
php artisan make:widget HeroSection --category=hero
```

This creates:
- Widget class: `app/Widgets/Hero/HeroSection/HeroSectionWidget.php`
- Metadata file: `app/Widgets/Hero/HeroSection/widget.json`
- Default view: `app/Widgets/Hero/HeroSection/views/default.blade.php`
- Asset files: `app/Widgets/Hero/HeroSection/assets/`

### Manual Creation

1. **Create Directory Structure**
```
app/Widgets/[Category]/[WidgetName]/
├── [WidgetName]Widget.php
├── widget.json
├── views/
│   ├── default.blade.php
│   └── variants/
└── assets/
    ├── styles.css
    └── scripts.js
```

2. **Create Widget Class**
```php
<?php

namespace App\Widgets\Hero;

use App\Widgets\BaseWidget;

class HeroSectionWidget extends BaseWidget
{
    public function render(): string
    {
        $title = $this->get('title', 'Default Title');
        $subtitle = $this->get('subtitle', 'Default Subtitle');
        
        return "
        <section class=\"hero-section py-20\">
            <div class=\"container mx-auto px-4 text-center\">
                <h1 class=\"text-5xl font-bold mb-4\">{$title}</h1>
                <p class=\"text-xl mb-8\">{$subtitle}</p>
            </div>
        </section>";
    }

    public function css(): string
    {
        return '<style>
        .hero-section { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        </style>';
    }

    public function js(): string
    {
        return '<script>
        console.log("Hero widget loaded");
        </script>';
    }
}
```

3. **Create Metadata File**
```json
{
  "name": "Hero Section",
  "description": "Large banner with customizable content",
  "category": "hero",
  "version": "1.0.0",
  "fields": [
    {
      "name": "title",
      "label": "Title",
      "type": "text",
      "required": true,
      "default": "Welcome"
    }
  ]
}
```

## Widget Metadata

The `widget.json` file defines the widget's configuration, fields, and behavior.

### Required Fields

```json
{
  "name": "Widget Name",
  "description": "Widget description",
  "category": "category_name",
  "version": "1.0.0",
  "fields": []
}
```

### Optional Fields

```json
{
  "author": "Developer Name",
  "icon": "heroicon-outline-cube",
  "preview_image": "preview.jpg",
  "variants": {
    "default": "Default Layout",
    "compact": "Compact Layout"
  },
  "settings": {
    "cacheable": true,
    "cache_duration": 3600,
    "permissions": ["admin", "editor"],
    "dependencies": []
  }
}
```

## Field Types

The Widget Engine supports various field types for configuration:

### Text Field
```json
{
  "name": "title",
  "label": "Title",
  "type": "text",
  "required": true,
  "default": "Default Title",
  "validation": "required|string|max:100",
  "help": "The main title text"
}
```

### Textarea Field
```json
{
  "name": "description",
  "label": "Description",
  "type": "textarea",
  "rows": 4,
  "validation": "string|max:500"
}
```

### Select Field
```json
{
  "name": "style",
  "label": "Style",
  "type": "select",
  "options": {
    "modern": "Modern Style",
    "classic": "Classic Style",
    "minimal": "Minimal Style"
  },
  "default": "modern"
}
```

### Checkbox Field
```json
{
  "name": "is_active",
  "label": "Active",
  "type": "checkbox",
  "default": true
}
```

### Image Field
```json
{
  "name": "background_image",
  "label": "Background Image",
  "type": "image",
  "validation": "image|max:2048"
}
```

### Gallery Field
```json
{
  "name": "images",
  "label": "Image Gallery",
  "type": "gallery",
  "max_items": 5
}
```

### Repeatable Field
```json
{
  "name": "features",
  "label": "Features",
  "type": "repeatable",
  "max_items": 10,
  "fields": [
    {
      "name": "title",
      "label": "Feature Title",
      "type": "text",
      "required": true
    },
    {
      "name": "description",
      "label": "Feature Description",
      "type": "textarea"
    }
  ]
}
```

### Other Field Types
- `url`: URL input with validation
- `email`: Email input with validation
- `number`: Numeric input with min/max
- `date`: Date picker
- `color`: Color picker
- `range`: Range slider

## Widget Variants

Widgets can have multiple layout variants:

### Defining Variants
```json
{
  "variants": {
    "default": "Default Layout",
    "compact": "Compact Layout",
    "featured": "Featured Layout"
  }
}
```

### Using Variants in Widget Class
```php
public function render(): string
{
    $variant = $this->getVariant();
    
    switch ($variant) {
        case 'compact':
            return $this->renderCompact();
        case 'featured':
            return $this->renderFeatured();
        default:
            return $this->renderDefault();
    }
}
```

### Variant-Specific Views
Create separate view files for each variant:
```
views/
├── default.blade.php
├── compact.blade.php
└── featured.blade.php
```

## Permissions & Access Control

### Widget-Level Permissions
```json
{
  "settings": {
    "permissions": ["admin", "editor", "widget_manager"]
  }
}
```

### Checking Permissions in Code
```php
use App\Services\WidgetPermissionService;

$permissionService = new WidgetPermissionService();

// Check if user can access widget
if ($permissionService->canAccessWidget('hero_section')) {
    // User can use this widget
}

// Check if user can manage widgets
if ($permissionService->canManageWidgets()) {
    // User can create/edit/delete widgets
}
```

### Enabling/Disabling Widgets
```php
// Disable a widget
$permissionService->disableWidget('hero_section');

// Enable a widget
$permissionService->enableWidget('hero_section');

// Check if widget is enabled
if ($permissionService->isWidgetEnabled('hero_section')) {
    // Widget is enabled
}
```

## Import/Export

### Exporting Widgets
```php
use App\Services\WidgetImportExportService;

$service = new WidgetImportExportService();

// Export all widgets
$exportData = $service->exportWidgets();

// Export specific areas
$exportData = $service->exportWidgets([
    'areas' => ['homepage-main', 'sidebar']
]);

// Export to file
$filePath = $service->exportToFile('my_widgets.json');
```

### Importing Widgets
```php
// Import from array
$result = $service->importWidgets($importData, [
    'overwrite_existing' => true
]);

// Import from file
$result = $service->importFromFile('/path/to/widgets.json');

// Validate only (don't actually import)
$result = $service->importWidgets($importData, [
    'validate_only' => true
]);
```

### Creating Backups
```php
// Create backup
$backupPath = $service->createBackup('my_backup');

// Restore from backup
$result = $service->restoreBackup($backupPath, [
    'clear_existing' => true
]);
```

## Best Practices

### Widget Development

1. **Keep widgets focused**: Each widget should have a single, clear purpose
2. **Use meaningful names**: Widget and field names should be descriptive
3. **Validate input**: Always validate user input in your widgets
4. **Handle errors gracefully**: Provide fallbacks for missing data
5. **Cache when appropriate**: Use caching for expensive operations

### Performance

1. **Optimize rendering**: Avoid heavy computations in render methods
2. **Use lazy loading**: Load assets only when needed
3. **Minimize database queries**: Use eager loading and caching
4. **Compress assets**: Minify CSS and JavaScript

### Security

1. **Sanitize output**: Always escape user input in HTML
2. **Validate permissions**: Check user permissions before rendering
3. **Secure file uploads**: Validate and sanitize uploaded files
4. **Use CSRF protection**: Protect forms with CSRF tokens

### Code Organization

```php
class MyWidget extends BaseWidget
{
    public function render(): string
    {
        // Validate required data
        if (!$this->hasRequiredData()) {
            return $this->renderError('Missing required data');
        }
        
        // Get and sanitize data
        $data = $this->prepareData();
        
        // Render based on variant
        return $this->renderVariant($data);
    }
    
    protected function hasRequiredData(): bool
    {
        return !empty($this->get('title'));
    }
    
    protected function prepareData(): array
    {
        return [
            'title' => htmlspecialchars($this->get('title')),
            'description' => htmlspecialchars($this->get('description')),
        ];
    }
    
    protected function renderVariant(array $data): string
    {
        $variant = $this->getVariant();
        $method = 'render' . ucfirst($variant);
        
        if (method_exists($this, $method)) {
            return $this->$method($data);
        }
        
        return $this->renderDefault($data);
    }
    
    protected function renderError(string $message): string
    {
        if (app()->environment('local')) {
            return "<div class=\"widget-error\">{$message}</div>";
        }
        
        return ''; // Hide errors in production
    }
}
```

## Troubleshooting

### Common Issues

**Widget not appearing in admin**
- Run `php artisan widget:discover`
- Check widget metadata syntax
- Verify widget class extends BaseWidget

**Widget rendering errors**
- Check widget settings validation
- Verify all required fields have values
- Check for PHP syntax errors

**Permission denied errors**
- Verify user has required permissions
- Check widget permission settings
- Ensure widget is enabled

**Import/export failures**
- Validate JSON syntax
- Check file permissions
- Verify widget types exist

### Debugging

Enable debug mode in your widget:
```php
public function render(): string
{
    if (app()->environment('local')) {
        \Log::info('Widget data:', $this->getSettings());
    }
    
    // Your render logic
}
```

### Artisan Commands

```bash
# Discover widgets
php artisan widget:discover

# Validate widgets
php artisan widget:validate

# Validate specific widget
php artisan widget:validate hero_section

# Create new widget
php artisan make:widget MyWidget --category=general

# Clear widget cache
php artisan cache:clear
```

## API Reference

### BaseWidget Methods

- `get(string $key, mixed $default = null)`: Get field value
- `getSettings()`: Get all settings
- `getMetadata()`: Get widget metadata
- `getVariant()`: Get current variant
- `setVariant(string $variant)`: Set variant
- `validateSettings()`: Validate settings
- `getPreview()`: Get preview HTML

### WidgetRegistry Methods

- `WidgetRegistry::all()`: Get all widgets
- `WidgetRegistry::getByCategory()`: Get widgets by category
- `WidgetRegistry::get(string $type)`: Get widget class
- `WidgetRegistry::exists(string $type)`: Check if widget exists
- `WidgetRegistry::render(string $type, array $settings, string $variant)`: Render widget

### Helper Functions

- `render_widgets(string $area)`: Render widgets in area
- `render_widget_area(string $area)`: Render with caching
- `widget_exists(string $type)`: Check if widget exists
- `get_widget_preview(string $type, array $settings)`: Get preview
- `clear_widget_cache(string $area = null)`: Clear cache

## Examples

See the following example widgets in the codebase:
- `HeroWidget`: Basic text and button widget
- `ContactFormWidget`: Complex form with multiple field types
- `AnalyticsWidget`: Simple configuration widget

For more examples and advanced usage, check the `/docs/examples/` directory.