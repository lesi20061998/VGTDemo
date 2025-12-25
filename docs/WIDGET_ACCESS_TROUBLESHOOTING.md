# Widget Access Troubleshooting Guide

## Vấn đề: Widget không hiển thị trên giao diện

### Nguyên nhân chính:
1. **Permission Service quá nghiêm ngặt**: `WidgetPermissionService` kiểm tra quyền user quá chặt
2. **User chưa đăng nhập**: Cần đăng nhập để truy cập widget builder
3. **Local environment bypass chưa hoạt động**: Permission check vẫn chạy trong local

### Giải pháp đã thực hiện:

#### 1. Bypass Permission trong Local Environment
```php
// app/Services/WidgetPermissionService.php
public function getAccessibleWidgets($user = null): array
{
    // In local environment, return all widgets
    if (config('app.env') === 'local') {
        return WidgetRegistry::all();
    }
    // ... rest of the code
}
```

#### 2. Skip Permission Check trong Controller
```php
// app/Http/Controllers/Admin/WidgetController.php
public function index()
{
    // Skip permission check entirely in local environment
    if (config('app.env') !== 'local') {
        $permissionService = new WidgetPermissionService();
        
        if (!$permissionService->canManageWidgets()) {
            abort(403, 'You do not have permission to manage widgets');
        }
    }
    // ... rest of the code
}
```

#### 3. Tạo User Test
```bash
# User đã tạo:
Email: admin@test.com
Password: password
Role: admin
Level: 100
```

### Cách kiểm tra:

#### 1. Kiểm tra Widget Registry
```bash
php artisan widget:list
# Kết quả: 18 widgets đã đăng ký thành công
```

#### 2. Debug Permission
Truy cập: `/SiVGT/admin/widgets/debug-permission`

#### 3. Test Access
Truy cập: `/SiVGT/admin/widgets/test-access`

#### 4. Login Test
Sử dụng file `test_login.php` để đăng nhập nhanh

### Các bước troubleshooting:

1. **Kiểm tra Environment**
   ```bash
   php artisan tinker --execute="dd(config('app.env'), config('app.debug'));"
   ```

2. **Kiểm tra User Authentication**
   ```bash
   php artisan tinker --execute="dd(auth()->check(), auth()->user());"
   ```

3. **Kiểm tra Widget Registry**
   ```bash
   php artisan widget:list
   ```

4. **Test Permission Service**
   ```php
   $service = new \App\Services\WidgetPermissionService();
   dd($service->getAccessibleWidgets());
   ```

### Các route debug có sẵn:

- `/SiVGT/admin/widgets/debug-permission` - Kiểm tra permission chi tiết
- `/SiVGT/admin/widgets/test-access` - Test access cơ bản
- `/SiVGT/admin/widgets/dev-access` - Grant development access
- `/SiVGT/admin/debug/auth` - Debug authentication

### Lưu ý quan trọng:

1. **Environment phải là 'local'** để bypass permission
2. **User phải đăng nhập** để truy cập widget builder
3. **Widget Registry phải có widget** để hiển thị
4. **View phải tồn tại** tại `resources/views/cms/widgets/builder.blade.php`

### Nếu vẫn không hoạt động:

1. Clear cache: `php artisan cache:clear`
2. Clear config: `php artisan config:clear`
3. Clear route: `php artisan route:clear`
4. Restart server
5. Kiểm tra log: `storage/logs/laravel.log`

### Widget đã đăng ký thành công:

- ✅ `simple_text` - Widget văn bản đơn giản (18 widgets total)
- ✅ `test_widget` - Widget test
- ✅ Tất cả widget khác từ registry

### Truy cập Widget Builder:

URL: `/SiVGT/admin/widgets`

Yêu cầu:
- Đăng nhập với user có quyền admin
- Environment = 'local' (để bypass permission)
- Widget registry có ít nhất 1 widget