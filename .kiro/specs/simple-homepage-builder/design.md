# Design Document

## Overview

The Simple Homepage Builder is a form-based widget management system that replaces the complex GrapesJS visual editor with an intuitive, ACF-like interface. The system leverages the existing widget architecture (BaseWidget, ProductsWidget, PostListWidget, etc.) and provides a simple administrative interface for configuring homepage content through form fields rather than drag-and-drop visual editing.

## Architecture

The system follows a layered architecture:

1. **Presentation Layer**: Simple form-based admin interface for widget management
2. **Controller Layer**: Handles widget CRUD operations and homepage rendering
3. **Service Layer**: Widget registry, configuration validation, and rendering services
4. **Data Layer**: Widget configurations stored in database, integrated with existing Page model
5. **Widget Layer**: Existing widget classes (ProductsWidget, PostListWidget, etc.) with enhanced configuration

### Key Components

- **SimpleHomepageController**: Manages widget configurations and homepage rendering
- **WidgetConfigurationService**: Handles widget instance management and validation
- **WidgetRegistryService**: Discovers and manages available widget types
- **HomepageWidgetModel**: Stores widget configurations with project context
- **Widget Configuration Forms**: Dynamic form generation based on widget field definitions

## Components and Interfaces

### SimpleHomepageController

```php
class SimpleHomepageController extends Controller
{
    public function index(): View                    // Show widget management interface
    public function store(Request $request): Response // Add new widget instance
    public function update(Request $request, int $id): Response // Update widget configuration
    public function destroy(int $id): Response       // Delete widget instance
    public function reorder(Request $request): Response // Reorder widgets
    public function preview(): View                  // Preview homepage with current widgets
    public function publish(): Response              // Publish widget changes to live homepage
}
```

### WidgetConfigurationService

```php
class WidgetConfigurationService
{
    public function getAvailableWidgets(): array     // Get all registered widget types
    public function createWidgetInstance(string $type, array $config): HomepageWidget
    public function updateWidgetInstance(int $id, array $config): HomepageWidget
    public function validateWidgetConfig(string $type, array $config): array
    public function renderWidget(HomepageWidget $widget): string
    public function getWidgetFormFields(string $type): array
}
```

### WidgetRegistryService

```php
class WidgetRegistryService
{
    public function discoverWidgets(): array         // Auto-discover widget classes
    public function getWidgetMetadata(string $class): array
    public function getWidgetCategories(): array     // Group widgets by category
    public function isWidgetAvailable(string $class): bool
}
```

## Data Models

### HomepageWidget Model

```php
class HomepageWidget extends Model
{
    protected $fillable = [
        'project_id',        // Project context for multi-tenancy
        'widget_type',       // Widget class name (e.g., 'ProductsWidget')
        'widget_config',     // JSON configuration data
        'order',            // Display order on homepage
        'is_active',        // Enable/disable widget
        'variant',          // Widget variant (if supported)
    ];
    
    protected $casts = [
        'widget_config' => 'array',
        'is_active' => 'boolean',
    ];
}
```

### Enhanced Page Model

The existing Page model will be extended to support widget-based homepage rendering:

```php
// Additional methods for Page model
public function getWidgetBasedContent(): string    // Render homepage using widgets
public function hasWidgetConfiguration(): bool     // Check if using widget system
public function migrateFromGrapesJS(): void       // Migration helper
```

### Database Schema

```sql
CREATE TABLE homepage_widgets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id BIGINT UNSIGNED NOT NULL,
    widget_type VARCHAR(255) NOT NULL,
    widget_config JSON NOT NULL,
    order INT NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    variant VARCHAR(100) DEFAULT 'default',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    INDEX idx_project_order (project_id, order),
    INDEX idx_project_active (project_id, is_active),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property Reflection

After analyzing all acceptance criteria, several properties can be consolidated to eliminate redundancy:

- Properties about data persistence (5.1, 5.3, 5.4) can be combined into a comprehensive persistence property
- Properties about project scoping (8.1, 8.2, 8.3) can be unified into a single project isolation property  
- Properties about widget rendering (4.1, 4.2, 4.3, 4.4) can be consolidated into widget data loading property
- Properties about form generation (2.1, 7.2) address the same core functionality

### Core Properties

**Property 1: Widget Configuration Persistence**
*For any* widget configuration changes (create, update, delete, reorder), the system should persist those changes to the database with correct project context and maintain data integrity
**Validates: Requirements 1.3, 1.4, 1.5, 5.1, 5.3, 5.4, 8.3**

**Property 2: Widget Form Generation**
*For any* widget type, the system should generate form fields that exactly match the widget's field definitions in terms of type, validation rules, and default values
**Validates: Requirements 1.2, 2.1, 2.5, 7.2**

**Property 3: Widget Configuration Validation**
*For any* widget configuration input, the system should validate it according to the widget's field requirements and reject invalid data with appropriate error messages
**Validates: Requirements 2.2, 2.4, 7.5**

**Property 4: Widget Rendering with Configuration**
*For any* widget with saved configuration, rendering the widget should produce output that reflects all configured settings and applies project-specific customizations
**Validates: Requirements 2.3, 6.3, 6.4**

**Property 5: Homepage Widget Ordering**
*For any* set of homepage widgets, the system should render them on the homepage in the exact order specified by their order values
**Validates: Requirements 1.4, 6.1**

**Property 6: Widget Data Loading with Project Context**
*For any* widget that loads database content, the system should fetch data from the appropriate tables filtered by the current project context
**Validates: Requirements 4.1, 4.2, 4.3, 4.4, 4.5, 8.2**

**Property 7: Project Isolation**
*For any* project context, all widget operations (access, configuration, data loading) should be scoped to that project and prevent cross-project data access
**Validates: Requirements 8.1, 8.2, 8.4, 8.5**

**Property 8: Widget Registry Discovery**
*For any* widget class that extends BaseWidget, the widget registry should automatically discover it and make it available for homepage configuration
**Validates: Requirements 7.1, 7.3**

**Property 9: Preview Rendering Consistency**
*For any* widget configuration changes, the preview should render the homepage with those changes applied without affecting the live homepage until published
**Validates: Requirements 3.2, 3.5**

**Property 10: Widget Asset Integration**
*For any* widget that defines CSS or JavaScript assets, those assets should be included in the rendered page when the widget is active
**Validates: Requirements 7.4**

## Error Handling

The system implements comprehensive error handling at multiple levels:

### Validation Errors
- Widget configuration validation against field definitions
- Database constraint validation for widget persistence
- Project context validation to ensure proper scoping
- File upload validation for widget assets

### Runtime Errors
- Widget rendering failures with graceful degradation
- Database connection issues with appropriate fallbacks
- Missing widget class handling with error logging
- Invalid widget configuration recovery

### User Experience Errors
- Clear error messages for form validation failures
- Confirmation dialogs for destructive operations (delete widgets)
- Loading states during widget operations
- Rollback capability for failed widget updates

## Testing Strategy

The system employs a dual testing approach combining unit tests and property-based tests:

### Unit Testing Approach
Unit tests will cover:
- Specific widget configuration scenarios
- Error handling edge cases
- Integration points between components
- Database operations and constraints
- Form generation for known widget types

### Property-Based Testing Approach
Property-based tests will verify universal properties using **PHPUnit with Eris** (PHP property-based testing library):
- Each property-based test will run a minimum of 100 iterations
- Tests will generate random widget configurations, project contexts, and user inputs
- Each test will be tagged with comments referencing the design document property

**Property Test Configuration:**
```php
// Example property test structure
/**
 * Feature: simple-homepage-builder, Property 1: Widget Configuration Persistence
 */
public function testWidgetConfigurationPersistence(): void
{
    $this->forAll(
        Generator\elements(['ProductsWidget', 'PostListWidget', 'CategoryWidget']),
        Generator\associative(['title' => Generator\string(), 'limit' => Generator\nat()]),
        Generator\nat()
    )->then(function ($widgetType, $config, $projectId) {
        // Test that widget configuration persists correctly
    });
}
```

### Test Coverage Requirements
- All correctness properties must be implemented as property-based tests
- Unit tests must cover error conditions and edge cases
- Integration tests must verify end-to-end widget workflows
- Performance tests for widget rendering with large datasets