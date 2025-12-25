# Widget Engine Design Document

## Overview

The Widget Engine is a flexible, extensible component system that allows developers to create reusable widgets with configurable fields while providing administrators with an intuitive interface to manage content. The system builds upon the existing widget infrastructure and enhances it with automatic discovery, metadata-driven configuration, and improved developer experience.

The engine follows a plugin-like architecture where each widget is self-contained with its own metadata, Blade components, assets, and configuration. This approach ensures maintainability, testability, and scalability as the CMS grows.

## Architecture

### Core Components

```
Widget Engine Architecture
├── Widget Registry (Discovery & Management)
├── Metadata Parser (Configuration Processing)  
├── Field Type System (Form Generation)
├── Rendering Engine (Component Resolution)
├── Admin Interface (Content Management)
└── Artisan Commands (Developer Tools)
```

### Widget Structure

Each widget follows a standardized directory structure:

```
app/Widgets/[Category]/[WidgetName]/
├── [WidgetName]Widget.php          # Main widget class
├── widget.json                     # Metadata configuration
├── views/
│   ├── default.blade.php          # Default template
│   └── variants/                  # Alternative layouts
│       ├── compact.blade.php
│       └── featured.blade.php
├── assets/
│   ├── styles.css                 # Widget-specific CSS
│   └── scripts.js                 # Widget-specific JS
└── tests/
    └── [WidgetName]WidgetTest.php # Widget tests
```

### Service Layer

The engine introduces several key services:

- **WidgetDiscoveryService**: Automatically scans and registers widgets
- **MetadataValidationService**: Validates widget configurations
- **FieldTypeService**: Manages form field types and rendering
- **WidgetRenderingService**: Handles widget output generation
- **WidgetCacheService**: Optimizes widget loading and rendering

## Components and Interfaces

### Enhanced Base Widget Class

```php
abstract class BaseWidget
{
    protected array $settings;
    protected array $metadata;
    protected string $variant = 'default';
    
    public function __construct(array $settings = [], string $variant = 'default')
    {
        $this->settings = $settings;
        $this->variant = $variant;
        $this->metadata = $this->loadMetadata();
    }
    
    abstract public function render(): string;
    abstract public static function getMetadataPath(): string;
    
    public function getMetadata(): array;
    public function getVariants(): array;
    public function validateSettings(): bool;
    public function getPreview(): string;
}
```

### Widget Registry Interface

```php
interface WidgetRegistryInterface
{
    public function discover(): array;
    public function register(string $type, string $class): void;
    public function get(string $type): ?string;
    public function getByCategory(): array;
    public function render(string $type, array $settings, string $variant = 'default'): string;
}
```

### Field Type System

```php
interface FieldTypeInterface
{
    public function render(array $config, mixed $value = null): string;
    public function validate(mixed $value, array $rules): bool;
    public function transform(mixed $value): mixed;
}
```

## Data Models

### Widget Metadata Schema

```json
{
  "name": "Hero Section",
  "description": "Large banner with call-to-action",
  "category": "hero",
  "version": "1.0.0",
  "author": "Development Team",
  "icon": "heroicon-outline-photograph",
  "preview_image": "preview.jpg",
  "variants": {
    "default": "Default Layout",
    "compact": "Compact Layout", 
    "featured": "Featured Layout"
  },
  "fields": [
    {
      "name": "title",
      "label": "Title",
      "type": "text",
      "required": true,
      "default": "Welcome to Our Website",
      "validation": "required|string|max:100",
      "help": "Main heading text"
    },
    {
      "name": "subtitle", 
      "label": "Subtitle",
      "type": "textarea",
      "required": false,
      "default": "Build amazing things",
      "validation": "string|max:255"
    },
    {
      "name": "background_image",
      "label": "Background Image",
      "type": "image",
      "required": false,
      "validation": "image|max:2048"
    },
    {
      "name": "buttons",
      "label": "Action Buttons",
      "type": "repeatable",
      "max_items": 3,
      "fields": [
        {
          "name": "text",
          "label": "Button Text", 
          "type": "text",
          "required": true
        },
        {
          "name": "url",
          "label": "Button URL",
          "type": "url", 
          "required": true
        },
        {
          "name": "style",
          "label": "Button Style",
          "type": "select",
          "options": {
            "primary": "Primary",
            "secondary": "Secondary",
            "outline": "Outline"
          },
          "default": "primary"
        }
      ]
    }
  ],
  "settings": {
    "cacheable": true,
    "cache_duration": 3600,
    "permissions": ["admin", "editor"],
    "dependencies": []
  }
}
```

### Enhanced Widget Model

```php
class Widget extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'type', 'area', 'settings', 
        'variant', 'sort_order', 'is_active', 'metadata'
    ];
    
    protected $casts = [
        'settings' => 'array',
        'metadata' => 'array', 
        'is_active' => 'boolean'
    ];
    
    public function getRenderedContent(): string;
    public function validateSettings(): bool;
    public function getPreview(): string;
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

After analyzing all acceptance criteria, several properties can be consolidated to eliminate redundancy:

- Widget discovery and registration properties (1.1, 1.3) can be combined into a single comprehensive discovery property
- Validation properties (1.2, 2.2, 2.3, 3.3) share common validation logic and can be unified
- Rendering properties (4.2, 4.5, 7.3) all test widget output generation and can be consolidated
- Field type properties (2.1, 2.5) both test field type handling and can be combined

### Core Properties

**Property 1: Widget Discovery and Registration**
*For any* valid widget directory with proper metadata file, the Widget_Engine should automatically discover, register, and make the widget available in the admin interface
**Validates: Requirements 1.1, 1.3**

**Property 2: Metadata Validation**
*For any* widget metadata configuration, the Widget_Engine should validate the structure against the schema and accept valid configurations while rejecting invalid ones
**Validates: Requirements 1.2**

**Property 3: Naming Convention Mapping**
*For any* widget following the established naming convention, the Widget_Engine should automatically map it to the corresponding Blade component without manual configuration
**Validates: Requirements 1.5**

**Property 4: Field Type Support**
*For any* supported field type (text, textarea, image, gallery, select, checkbox, repeatable, nested), the Widget_Engine should process the field definition and render appropriate form controls
**Validates: Requirements 2.1, 2.5**

**Property 5: Validation Rule Enforcement**
*For any* widget field with validation rules or required status, the Widget_Engine should prevent saving invalid data and enforce all defined validation constraints
**Validates: Requirements 1.4, 2.2, 2.3, 3.3**

**Property 6: Repeatable Field Management**
*For any* repeatable field configuration, the Widget_Engine should allow adding and removing multiple instances while maintaining proper data structure
**Validates: Requirements 2.4**

**Property 7: Form Generation from Metadata**
*For any* widget metadata, the admin interface should generate form fields that exactly match the metadata definition
**Validates: Requirements 3.2**

**Property 8: Preview Functionality**
*For any* widget with configured data, the admin interface should provide real-time preview that updates immediately when data changes and renders using actual Blade components
**Validates: Requirements 3.4, 7.1, 7.2, 7.3**

**Property 9: Data Storage Format**
*For any* saved widget configuration, the system should store the data in a structured format that preserves all field values and relationships
**Validates: Requirements 3.5**

**Property 10: Widget Rendering with Data**
*For any* widget added to a page section, the system should render the widget with its configured data and maintain proper order when multiple widgets exist
**Validates: Requirements 4.2, 4.4, 4.5**

**Property 11: Section Reordering Integrity**
*For any* page section reordering operation, the system should maintain all widget configurations and relationships without data loss
**Validates: Requirements 4.3**

**Property 12: Widget Isolation**
*For any* widget creation or modification, the system should ensure the widget operates independently without interfering with other widgets
**Validates: Requirements 5.1, 5.2**

**Property 13: Namespace Conflict Prevention**
*For any* set of widgets being loaded, the system should prevent naming conflicts through proper namespacing
**Validates: Requirements 5.3**

**Property 14: Cleanup Isolation**
*For any* widget removal operation, the system should handle cleanup without affecting other system components
**Validates: Requirements 5.5**

**Property 15: Schema Compliance**
*For any* widget metadata definition, it should follow the standardized schema format and pass validation
**Validates: Requirements 6.2**

**Property 16: Directory Structure Enforcement**
*For any* widget creation, the system should enforce consistent directory structure and naming conventions
**Validates: Requirements 6.3**

**Property 17: Template Variant Support**
*For any* widget with multiple template variants, the system should support all defined layouts and render them correctly
**Validates: Requirements 6.4**

**Property 18: Widget State Management**
*For any* widget enable/disable operation, the system should respect the state change and apply it consistently across the interface
**Validates: Requirements 8.1**

**Property 19: Permission Enforcement**
*For any* user role and widget access attempt, the system should respect permission settings and control access appropriately
**Validates: Requirements 8.2**

**Property 20: Widget Categorization**
*For any* set of widgets with categories, the admin interface should organize and display them logically by category
**Validates: Requirements 8.3**

**Property 21: Dependency Validation**
*For any* widget with dependencies, the system should validate and enforce requirement relationships
**Validates: Requirements 8.4**

**Property 22: Import/Export Integrity**
*For any* widget configuration export and subsequent import, the system should maintain data integrity and preserve all settings
**Validates: Requirements 8.5**

<function_calls>
<invoke name="prework">
<parameter name="featureName">widget-engine

## Error Handling

### Widget Discovery Errors
- **Missing Metadata**: When widget.json is missing or malformed, log error and skip widget registration
- **Invalid Schema**: When metadata doesn't match schema, provide detailed validation errors
- **Class Not Found**: When widget class doesn't exist, log error and mark widget as unavailable

### Runtime Errors
- **Rendering Failures**: Catch exceptions during widget rendering and display fallback content
- **Asset Loading**: Handle missing CSS/JS files gracefully with warnings
- **Data Validation**: Provide clear error messages for invalid field data

### Admin Interface Errors
- **Preview Failures**: Show error messages in preview area with debugging information
- **Save Failures**: Display validation errors inline with form fields
- **Permission Errors**: Show appropriate access denied messages

### Error Recovery
- **Graceful Degradation**: System continues functioning when individual widgets fail
- **Error Logging**: All errors logged with context for debugging
- **User Feedback**: Clear, actionable error messages for administrators

## Testing Strategy

### Dual Testing Approach

The Widget Engine requires both unit testing and property-based testing to ensure comprehensive coverage:

**Unit Testing Focus:**
- Specific widget configurations and edge cases
- Integration points between components
- Error conditions and boundary cases
- Admin interface interactions

**Property-Based Testing Focus:**
- Universal properties that should hold across all widget types
- Validation rules across different field configurations
- Data integrity during operations
- System behavior with randomly generated widget metadata

### Property-Based Testing Configuration

- **Testing Library**: PHPUnit with Faker for property-based testing
- **Minimum Iterations**: 100 iterations per property test
- **Test Tagging**: Each property-based test tagged with format: `**Feature: widget-engine, Property {number}: {property_text}**`
- **Coverage**: Each correctness property implemented by a single property-based test

### Testing Infrastructure

**Widget Test Base Classes:**
```php
abstract class WidgetTestCase extends TestCase
{
    protected function createTestWidget(array $metadata = []): string;
    protected function assertWidgetRenders(string $type, array $settings): void;
    protected function assertMetadataValid(array $metadata): void;
}
```

**Property Test Generators:**
```php
class WidgetMetadataGenerator
{
    public static function validMetadata(): array;
    public static function invalidMetadata(): array;
    public static function fieldConfiguration(): array;
}
```

### Test Organization

- Unit tests: `tests/Unit/Widgets/`
- Property tests: `tests/Property/Widgets/`
- Integration tests: `tests/Feature/Widgets/`
- Widget-specific tests: `tests/Unit/Widgets/[Category]/[WidgetName]Test.php`

### Testing Best Practices

- Test widget isolation and independence
- Verify metadata validation thoroughly
- Test all supported field types
- Validate rendering output
- Test error conditions and recovery
- Verify permission enforcement
- Test caching behavior
- Validate import/export functionality