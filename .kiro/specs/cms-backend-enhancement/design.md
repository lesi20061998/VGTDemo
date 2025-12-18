# Design Document - CMS Backend Enhancement

## Overview

Thiết kế này mô tả việc nâng cấp toàn diện backend CMS hiện tại để trở thành một hệ thống quản lý nội dung đầy đủ tính năng, hỗ trợ multi-tenant, với các chức năng quản lý người dùng, sản phẩm, đơn hàng, nội dung, báo cáo và tích hợp API hoàn chỉnh.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend Layer                           │
├─────────────────────────────────────────────────────────────┤
│                    API Gateway                              │
├─────────────────────────────────────────────────────────────┤
│                 Controller Layer                            │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐           │
│  │   Admin     │ │ SuperAdmin  │ │   Employee  │           │
│  │ Controllers │ │ Controllers │ │ Controllers │           │
│  └─────────────┘ └─────────────┘ └─────────────┘           │
├─────────────────────────────────────────────────────────────┤
│                  Service Layer                              │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐           │
│  │   Business  │ │   Report    │ │    Media    │           │
│  │   Logic     │ │   Service   │ │   Service   │           │
│  └─────────────┘ └─────────────┘ └─────────────┘           │
├─────────────────────────────────────────────────────────────┤
│                   Data Layer                                │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐           │
│  │   Models    │ │ Repositories│ │    Cache    │           │
│  │             │ │             │ │             │           │
│  └─────────────┘ └─────────────┘ └─────────────┘           │
├─────────────────────────────────────────────────────────────┤
│                 Infrastructure                              │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐           │
│  │  Database   │ │   Storage   │ │   Queue     │           │
│  │             │ │             │ │             │           │
│  └─────────────┘ └─────────────┘ └─────────────┘           │
└─────────────────────────────────────────────────────────────┘
```

### Multi-Tenant Architecture

Hệ thống sử dụng mô hình multi-tenant với database separation:
- Mỗi tenant có database riêng biệt
- Shared application code
- Tenant-specific configurations
- Isolated data và settings

## Components and Interfaces

### 1. User Management System

#### Models
- `User`: Quản lý thông tin người dùng cơ bản
- `Role`: Định nghĩa các vai trò trong hệ thống
- `Permission`: Định nghĩa các quyền cụ thể
- `UserRole`: Liên kết người dùng với vai trò
- `RolePermission`: Liên kết vai trò với quyền
- `ActivityLog`: Ghi log hoạt động người dùng

#### Controllers
- `UserController`: CRUD operations cho người dùng
- `RoleController`: Quản lý vai trò và phân quyền
- `ActivityLogController`: Xem log hoạt động

#### Services
- `UserService`: Business logic cho quản lý người dùng
- `RolePermissionService`: Logic phân quyền
- `ActivityLogService`: Ghi và truy vấn log

### 2. Product Management System

#### Models
- `Product`: Sản phẩm chính (đã có, cần mở rộng)
- `ProductCategory`: Danh mục sản phẩm
- `ProductAttribute`: Thuộc tính sản phẩm
- `ProductVariation`: Biến thể sản phẩm
- `ProductInventory`: Quản lý kho
- `ProductSEO`: Thông tin SEO cho sản phẩm

#### Controllers
- `ProductController`: Đã có, cần mở rộng
- `ProductInventoryController`: Quản lý kho
- `ProductImportExportController`: Import/Export sản phẩm

#### Services
- `ProductService`: Business logic sản phẩm
- `InventoryService`: Quản lý kho
- `ProductImportService`: Import sản phẩm
- `ProductExportService`: Export sản phẩm

### 3. Order Management System

#### Models
- `Order`: Đơn hàng (đã có, cần mở rộng)
- `OrderItem`: Chi tiết đơn hàng
- `OrderStatus`: Trạng thái đơn hàng
- `OrderPayment`: Thanh toán đơn hàng
- `OrderShipping`: Vận chuyển đơn hàng
- `OrderNote`: Ghi chú đơn hàng

#### Controllers
- `OrderController`: Đã có, cần mở rộng
- `OrderPaymentController`: Quản lý thanh toán
- `OrderShippingController`: Quản lý vận chuyển
- `OrderReportController`: Báo cáo đơn hàng

#### Services
- `OrderService`: Business logic đơn hàng
- `PaymentService`: Xử lý thanh toán
- `ShippingService`: Xử lý vận chuyển
- `OrderNotificationService`: Thông báo đơn hàng

### 4. Content Management System

#### Models
- `Post`: Bài viết (đã có, cần mở rộng)
- `Page`: Trang tĩnh
- `Category`: Danh mục nội dung
- `Tag`: Thẻ nội dung
- `Translation`: Bản dịch đa ngôn ngữ
- `ContentSEO`: SEO cho nội dung

#### Controllers
- `PostController`: Đã có, cần mở rộng
- `PageController`: Quản lý trang
- `CategoryController`: Quản lý danh mục
- `TranslationController`: Quản lý bản dịch

#### Services
- `ContentService`: Business logic nội dung
- `TranslationService`: Dịch thuật
- `SEOService`: Tối ưu SEO

### 5. Reporting System

#### Models
- `Report`: Định nghĩa báo cáo
- `ReportData`: Dữ liệu báo cáo
- `Dashboard`: Cấu hình dashboard
- `KPI`: Chỉ số hiệu suất

#### Controllers
- `ReportController`: Tạo và xem báo cáo
- `DashboardController`: Đã có, cần mở rộng
- `AnalyticsController`: Phân tích dữ liệu

#### Services
- `ReportService`: Tạo báo cáo
- `AnalyticsService`: Phân tích dữ liệu
- `DashboardService`: Quản lý dashboard

### 6. API System

#### Controllers
- `ApiAuthController`: Xác thực API
- `ApiProductController`: API sản phẩm
- `ApiOrderController`: API đơn hàng
- `ApiContentController`: API nội dung
- `ApiUserController`: API người dùng

#### Middleware
- `ApiAuthMiddleware`: Xác thực API
- `ApiRateLimitMiddleware`: Giới hạn tần suất
- `ApiLoggingMiddleware`: Ghi log API

#### Services
- `ApiService`: Business logic API
- `ApiDocumentationService`: Tài liệu API

### 7. Security & Backup System

#### Models
- `BackupLog`: Log backup
- `SecurityLog`: Log bảo mật
- `SystemConfig`: Cấu hình hệ thống

#### Controllers
- `BackupController`: Quản lý backup
- `SecurityController`: Quản lý bảo mật
- `SystemController`: Đã có, cần mở rộng

#### Services
- `BackupService`: Thực hiện backup
- `SecurityService`: Kiểm tra bảo mật
- `EncryptionService`: Mã hóa dữ liệu

### 8. Media Management System

#### Models
- `Media`: File media (có thể sử dụng Spatie Media Library)
- `MediaFolder`: Thư mục media
- `MediaTag`: Thẻ media

#### Controllers
- `MediaController`: Đã có, cần mở rộng
- `MediaFolderController`: Quản lý thư mục

#### Services
- `MediaService`: Xử lý media
- `ImageProcessingService`: Xử lý hình ảnh
- `CDNService`: Tích hợp CDN

## Data Models

### Enhanced User Model
```php
class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'username', 'password', 'avatar',
        'phone', 'address', 'status', 'email_verified_at',
        'last_login_at', 'tenant_id', 'preferences'
    ];

    protected $casts = [
        'preferences' => 'array',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime'
    ];

    // Relationships
    public function roles(): BelongsToMany;
    public function permissions(): BelongsToMany;
    public function activityLogs(): HasMany;
    public function orders(): HasMany;
}
```

### Enhanced Product Model
```php
class Product extends Model implements HasMedia
{
    protected $fillable = [
        'name', 'slug', 'description', 'short_description',
        'sku', 'price', 'sale_price', 'cost_price',
        'stock_quantity', 'min_stock_level', 'max_stock_level',
        'weight', 'dimensions', 'status', 'visibility',
        'featured', 'digital', 'downloadable',
        'category_id', 'brand_id', 'tenant_id'
    ];

    // Relationships
    public function category(): BelongsTo;
    public function brand(): BelongsTo;
    public function variations(): HasMany;
    public function inventory(): HasOne;
    public function seo(): HasOne;
    public function reviews(): HasMany;
}
```

### New Order Model Enhancement
```php
class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'status', 'payment_status',
        'shipping_status', 'total_amount', 'tax_amount',
        'shipping_amount', 'discount_amount', 'currency',
        'billing_address', 'shipping_address', 'notes',
        'tenant_id'
    ];

    protected $casts = [
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'total_amount' => 'decimal:2'
    ];

    // Relationships
    public function customer(): BelongsTo;
    public function items(): HasMany;
    public function payments(): HasMany;
    public function shipments(): HasMany;
    public function statusHistory(): HasMany;
}
```

## Error Handling

### Exception Hierarchy
```php
// Base exception
abstract class CmsException extends Exception
{
    protected $errorCode;
    protected $context = [];
    
    public function getErrorCode(): string;
    public function getContext(): array;
}

// Specific exceptions
class UserNotFoundException extends CmsException;
class ProductOutOfStockException extends CmsException;
class OrderProcessingException extends CmsException;
class PaymentFailedException extends CmsException;
class MediaUploadException extends CmsException;
class ApiAuthenticationException extends CmsException;
```

### Error Response Format
```json
{
    "success": false,
    "error": {
        "code": "PRODUCT_NOT_FOUND",
        "message": "Product with ID 123 not found",
        "details": {
            "product_id": 123,
            "tenant_id": 1
        }
    },
    "timestamp": "2025-01-18T10:30:00Z"
}
```

## Testing Strategy

### Unit Testing
- Model tests: Relationships, scopes, accessors/mutators
- Service tests: Business logic, calculations
- Helper tests: Utility functions
- Validation tests: Form requests, rules

### Feature Testing
- Controller tests: HTTP responses, middleware
- API tests: Endpoints, authentication, rate limiting
- Integration tests: Database interactions, external services
- Authentication tests: Login, permissions, roles

### Property-Based Testing
- User management: Role assignments, permission checks
- Product management: Inventory calculations, pricing
- Order processing: Status transitions, payment flows
- Content management: Translation consistency, SEO validation

### Performance Testing
- Database query optimization
- API response times
- File upload/processing
- Report generation speed

## Security Considerations

### Authentication & Authorization
- Multi-factor authentication support
- JWT tokens for API access
- Role-based access control (RBAC)
- Permission-based route protection

### Data Protection
- Encryption at rest for sensitive data
- HTTPS enforcement
- SQL injection prevention
- XSS protection
- CSRF protection

### API Security
- Rate limiting per user/IP
- API key management
- Request/response logging
- Input validation and sanitization

### Backup & Recovery
- Automated daily backups
- Encrypted backup storage
- Point-in-time recovery
- Disaster recovery procedures

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### User Management Properties

Property 1: User creation completeness
*For any* valid user data, when creating a new user account, the system should store all required information and trigger email activation
**Validates: Requirements 1.1**

Property 2: Role assignment consistency
*For any* user and valid role, when assigning a role to the user, the user's permissions should reflect exactly the permissions defined for that role
**Validates: Requirements 1.2**

Property 3: Menu visibility based on permissions
*For any* authenticated user, the displayed menu items should contain only those items the user has permission to access
**Validates: Requirements 1.3**

Property 4: Account deactivation enforcement
*For any* deactivated user account, all authentication attempts should be rejected and access should be denied
**Validates: Requirements 1.4**

Property 5: Activity logging completeness
*For any* user action in the system, a corresponding activity log entry should be created with complete action details
**Validates: Requirements 1.5**

### Product Management Properties

Property 6: Product data integrity
*For any* product creation with attributes, variations, and media, all provided data should be stored and retrievable without loss
**Validates: Requirements 2.1**

Property 7: Inventory tracking accuracy
*For any* product with stock management enabled, the system should accurately track quantity changes and alert when stock falls below minimum levels
**Validates: Requirements 2.2**

Property 8: Attribute system flexibility
*For any* custom attribute definition, the system should allow creation of attribute groups and individual attributes with proper relationships
**Validates: Requirements 2.3**

Property 9: Product import/export round trip
*For any* valid product data exported to CSV/Excel, importing that data should recreate equivalent product records
**Validates: Requirements 2.4**

Property 10: SEO field completeness
*For any* product, all standard SEO fields (meta title, description, keywords, canonical URL) should be available and storable
**Validates: Requirements 2.5**

### Order Management Properties

Property 11: Order creation workflow
*For any* new order, the system should automatically assign appropriate status and send notifications to relevant parties
**Validates: Requirements 3.1**

Property 12: Order status history tracking
*For any* order status change, the system should create a history record and send customer notifications
**Validates: Requirements 3.2**

Property 13: Order reporting accuracy
*For any* time period and status filter, order reports should accurately reflect all orders matching the criteria
**Validates: Requirements 3.3**

Property 14: Payment status synchronization
*For any* payment status change, the system should update order status accordingly and maintain payment history
**Validates: Requirements 3.4**

Property 15: Invoice generation completeness
*For any* completed order, the system should generate a complete invoice with all required details and delivery confirmation
**Validates: Requirements 3.5**

### Content Management Properties

Property 16: Multi-language content creation
*For any* content item, the system should allow creation and storage of translations for all enabled languages
**Validates: Requirements 4.1**

Property 17: Language management operations
*For any* language addition, modification, or removal, the system should update language configurations and maintain content integrity
**Validates: Requirements 4.2**

Property 18: Language-specific content display
*For any* user with language preference, the system should display content in the preferred language when available
**Validates: Requirements 4.3**

Property 19: Content format consistency
*For any* content exported in multiple languages, the formatting and structure should remain consistent across all language versions
**Validates: Requirements 4.5**

### Reporting System Properties

Property 20: Real-time KPI calculation
*For any* dashboard view request, the displayed KPI values should reflect the current state of the underlying data
**Validates: Requirements 5.1**

Property 21: Custom report generation
*For any* valid report criteria and time range, the system should generate a report containing only data matching those specifications
**Validates: Requirements 5.2**

Property 22: Multi-format report export
*For any* generated report, the system should support export to multiple formats while preserving data integrity
**Validates: Requirements 5.3**

Property 23: Data collection accuracy
*For any* system event that should be tracked, the collected data should accurately represent the actual event details
**Validates: Requirements 5.4**

### API System Properties

Property 24: API response format consistency
*For any* valid API request, the response should follow the defined format specification and include proper status codes
**Validates: Requirements 6.1**

Property 25: API usage logging
*For any* API request, the system should create a log entry with request details, response status, and timing information
**Validates: Requirements 6.2**

Property 26: API error handling
*For any* API request that results in an error, the response should include appropriate error codes and descriptive messages
**Validates: Requirements 6.3**

Property 27: API authentication security
*For any* protected API endpoint, access should be granted only to properly authenticated and authorized requests
**Validates: Requirements 6.4**

### Security & Backup Properties

Property 28: Backup completeness and integrity
*For any* backup operation, the created backup should contain all system data and pass integrity verification
**Validates: Requirements 7.1**

Property 29: Backup restoration round trip
*For any* valid backup file, restoring from that backup should recreate the system state as it was at backup time
**Validates: Requirements 7.2**

Property 30: Security event logging
*For any* suspicious access attempt or security event, the system should create detailed log entries and trigger appropriate alerts
**Validates: Requirements 7.3**

Property 31: Data encryption compliance
*For any* sensitive data stored in the system, the data should be encrypted using approved encryption standards
**Validates: Requirements 7.4**

Property 32: Security measure effectiveness
*For any* common attack vector, the system should implement and maintain appropriate defensive measures
**Validates: Requirements 7.5**

### Media Management Properties

Property 33: File upload validation
*For any* file upload attempt, the system should validate format, size, and content according to defined rules
**Validates: Requirements 8.1**

Property 34: File organization operations
*For any* file management operation (create folder, move file, categorize), the system should maintain proper file structure and relationships
**Validates: Requirements 8.2**

Property 35: Image processing automation
*For any* uploaded image, the system should automatically generate required sizes and apply watermarks as configured
**Validates: Requirements 8.3**

Property 36: Media search functionality
*For any* search query on media files, the results should include all files matching the search criteria and applied filters
**Validates: Requirements 8.4**

### Marketing & SEO Properties

Property 37: SEO analysis accuracy
*For any* content item, SEO analysis should provide accurate scores and actionable recommendations based on current best practices
**Validates: Requirements 9.1**

Property 38: Sitemap synchronization
*For any* content change that affects public pages, the sitemap should be automatically updated to reflect the changes
**Validates: Requirements 9.2**

Property 39: Newsletter delivery reliability
*For any* newsletter campaign, the system should track delivery status and provide accurate delivery reports
**Validates: Requirements 9.3**

Property 40: Page builder template consistency
*For any* page created using templates, the resulting page should maintain template structure while allowing customization
**Validates: Requirements 9.5**

### Configuration Management Properties

Property 41: Configuration change application
*For any* configuration modification, the new settings should be applied immediately and affect subsequent system behavior
**Validates: Requirements 10.1**

Property 42: Configuration backup round trip
*For any* system configuration, exporting and then importing the configuration should restore the exact same settings
**Validates: Requirements 10.3**

Property 43: Environment-specific configuration
*For any* multi-environment setup, configuration changes should apply only to the target environment without affecting others
**Validates: Requirements 10.4**

Property 44: Configuration validation
*For any* invalid configuration input, the system should reject the change and provide specific error messages
**Validates: Requirements 10.5**