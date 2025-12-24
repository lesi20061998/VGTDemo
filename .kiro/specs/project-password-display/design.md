# Design Document

## Overview

Thiết kế hệ thống hiển thị và quản lý mật khẩu project admin trong SuperAdmin interface. Hệ thống sẽ cho phép xem mật khẩu gốc, tái tạo mật khẩu mới, và quản lý bảo mật cho các thao tác nhạy cảm.

## Architecture

### Current System Analysis
- Mật khẩu project admin hiện được lưu hash trong database
- Không có cách nào để reverse hash thành plain text
- Cần lưu trữ plain password riêng biệt hoặc generate mới

### Proposed Solution
- Thêm trường `project_admin_password_plain` vào bảng `projects`
- Encrypt plain password thay vì lưu trực tiếp
- Tạo service để quản lý password operations
- Implement audit logging cho security

## Components and Interfaces

### 1. Database Schema Changes

```sql
ALTER TABLE projects ADD COLUMN project_admin_password_plain TEXT NULL;
ALTER TABLE projects ADD COLUMN password_updated_at TIMESTAMP NULL;
ALTER TABLE projects ADD COLUMN password_updated_by INT NULL;
```

### 2. ProjectPasswordService

```php
class ProjectPasswordService
{
    public function getPlainPassword(Project $project): ?string
    public function setPassword(Project $project, string $password, User $updatedBy): bool
    public function generatePassword(Project $project, User $updatedBy): string
    public function logPasswordAccess(Project $project, User $user, string $action): void
}
```

### 3. Controller Methods

```php
// ProjectController additions
public function showPassword(Project $project): JsonResponse
public function regeneratePassword(Project $project): JsonResponse  
public function setCustomPassword(Request $request, Project $project): JsonResponse
```

### 4. Frontend Components

- Password display component với show/hide toggle
- Copy to clipboard functionality
- Password regeneration modal
- Custom password setting form

## Data Models

### Project Model Extensions

```php
// Add to Project model
protected $fillable = [
    // existing fields...
    'project_admin_password_plain',
    'password_updated_at', 
    'password_updated_by'
];

protected $casts = [
    'password_updated_at' => 'datetime'
];

// Relationships
public function passwordUpdatedBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'password_updated_by');
}

// Methods
public function getDecryptedPassword(): ?string
public function setEncryptedPassword(string $password): void
```

### Password Audit Log Model

```php
class ProjectPasswordAudit extends Model
{
    protected $fillable = [
        'project_id',
        'user_id', 
        'action', // 'viewed', 'generated', 'updated'
        'ip_address',
        'user_agent',
        'performed_at'
    ];
}
```

## Error Handling

### Password Decryption Errors
- Graceful handling khi không thể decrypt
- Fallback message "Password not available"
- Log decryption failures

### Database Errors  
- Transaction rollback cho password updates
- Proper error messages cho users
- System notification cho critical failures

### Security Errors
- Rate limiting cho password operations
- Authentication verification
- Audit trail cho failed attempts

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Password Display Consistency
*For any* project with a stored plain password, when the SuperAdmin views the config page, the displayed plain password should match the stored encrypted value when decrypted
**Validates: Requirements 1.1**

### Property 2: Fallback Message Display  
*For any* project without a stored plain password, when the SuperAdmin views the config page, the system should display "Password not available" message
**Validates: Requirements 1.2**

### Property 3: Audit Logging Completeness
*For any* password viewing operation, when a SuperAdmin accesses a plain password, an audit record should be created with user ID, project ID, and timestamp
**Validates: Requirements 1.4, 4.1**

### Property 4: Username and Password Pairing
*For any* project with an active admin account, when displaying password information, both username and plain password should be shown together
**Validates: Requirements 1.5**

### Property 5: Password Generation Uniqueness
*For any* password regeneration operation, when a SuperAdmin regenerates a password, the new password should be different from the previous password
**Validates: Requirements 2.1**

### Property 6: Database Update Consistency
*For any* password generation or update operation, when a new password is set, both the hashed version in the project database and the encrypted plain version should be updated
**Validates: Requirements 2.2, 3.2, 3.3**

### Property 7: Audit Trail Completeness
*For any* password operation (view, generate, update), when the operation occurs, an audit record should be created with the correct action type and metadata
**Validates: Requirements 2.3, 4.1**

### Property 8: Email Notification Trigger
*For any* password regeneration operation, when a password is regenerated, an email notification should be queued for the project admin
**Validates: Requirements 2.4**

### Property 9: Error Message Specificity
*For any* password operation that fails validation, when validation fails, specific error messages should be displayed indicating the exact validation failure
**Validates: Requirements 2.5, 3.4**

### Property 10: Password Validation Enforcement
*For any* custom password input, when a SuperAdmin sets a custom password, the password should only be accepted if it meets all strength requirements
**Validates: Requirements 3.1**

### Property 11: Success Confirmation Display
*For any* successful password update operation, when the password is successfully updated, a success confirmation message should be displayed
**Validates: Requirements 3.5**

### Property 12: Authentication Requirement
*For any* password operation, when a SuperAdmin attempts to perform the operation, current user authentication should be verified before proceeding
**Validates: Requirements 4.2**

### Property 13: Rate Limiting Enforcement
*For any* sequence of failed password operations from the same user, when the failure count exceeds the threshold, subsequent operations should be rate limited
**Validates: Requirements 4.3**

## Testing Strategy

### Unit Tests
- ProjectPasswordService methods
- Password encryption/decryption
- Audit logging functionality
- Validation rules

### Property-Based Tests
- Password generation and validation across random inputs
- Audit logging consistency across different operations
- Rate limiting behavior under various failure scenarios
- Database consistency during concurrent password operations

### Feature Tests  
- SuperAdmin password viewing workflow
- Password regeneration process
- Custom password setting
- Security and rate limiting

### Security Tests
- Password encryption strength
- Audit log integrity
- Access control verification
- Rate limiting effectiveness