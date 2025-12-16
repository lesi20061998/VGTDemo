# Routes Structure Documentation

## Tổng quan Routes

Hệ thống có 4 file routes chính:

```
routes/
├── web.php          # Routes tổng hợp
├── frontend.php     # DEPRECATED - Đã chuyển sang project.php
├── backend.php      # CMS Admin (Global - không dùng multisite)
├── project.php      # Project Routes (Multisite)
└── superadmin.php   # SuperAdmin Routes
```

## 1. Project Routes (Multisite)

File: `routes/project.php`

### Frontend Routes
```
URL Pattern: /{projectCode}/*
Middleware: ProjectSubdomainMiddleware
Route Name: project.*
```

**Danh sách routes:**
- `GET /{projectCode}` - Trang chủ
- `GET /{projectCode}/products` - Danh sách sản phẩm
- `GET /{projectCode}/product/{slug}` - Chi tiết sản phẩm
- `GET /{projectCode}/blog` - Danh sách blog
- `GET /{projectCode}/blog/{slug}` - Chi tiết blog
- `GET /{projectCode}/contact` - Liên hệ
- `GET /{projectCode}/cart` - Giỏ hàng
- `GET /{projectCode}/checkout` - Thanh toán
- `GET /{projectCode}/{slug}` - Trang động

### CMS Admin Routes
```
URL Pattern: /{projectCode}/cms/*
Middleware: ProjectSubdomainMiddleware + auth + CheckCmsRole
Route Name: project.cms.*
```

**Danh sách routes:**
- `GET /{projectCode}/cms` - Dashboard
- `/{projectCode}/cms/products` - Quản lý sản phẩm
- `/{projectCode}/cms/categories` - Quản lý danh mục
- `/{projectCode}/cms/orders` - Quản lý đơn hàng
- `/{projectCode}/cms/widgets` - Page builder
- `/{projectCode}/cms/settings` - Cài đặt

### Auth Routes
```
URL Pattern: /{projectCode}/login
Middleware: ProjectSubdomainMiddleware
```

## 2. Backend Routes (Global CMS)

File: `routes/backend.php`

```
URL Pattern: /cms/admin/*
Middleware: auth + cms
Route Name: cms.*
```

**Chức năng:** CMS Admin không dùng multisite (legacy)

## 3. SuperAdmin Routes

File: `routes/superadmin.php`

```
URL Pattern: /superadmin/*
Middleware: auth + superadmin
Route Name: superadmin.*
```

**Danh sách routes:**
- `/superadmin` - Dashboard
- `/superadmin/projects` - Quản lý projects
- `/superadmin/employees` - Quản lý nhân viên
- `/superadmin/contracts` - Quản lý hợp đồng
- `/superadmin/tasks` - Quản lý tasks
- `/superadmin/tickets` - Quản lý tickets

## Route Naming Convention

### Frontend
```php
Route::name('project.{action}')
// Example: project.home, project.products.index
```

### CMS Admin
```php
Route::name('project.cms.{module}.{action}')
// Example: project.cms.products.index, project.cms.widgets.store
```

### SuperAdmin
```php
Route::name('superadmin.{module}.{action}')
// Example: superadmin.projects.index, superadmin.employees.create
```

## Middleware Stack

### Frontend (Public)
```php
[ProjectSubdomainMiddleware]
```

### CMS Admin (Protected)
```php
[ProjectSubdomainMiddleware, auth, CheckCmsRole]
```

### SuperAdmin (Protected)
```php
[auth, superadmin]
```

## URL Examples

### Project: SiVGT

**Frontend:**
- `http://localhost:8000/SiVGT/` - Trang chủ
- `http://localhost:8000/SiVGT/products` - Sản phẩm
- `http://localhost:8000/SiVGT/cart` - Giỏ hàng

**CMS Admin:**
- `http://localhost:8000/SiVGT/cms` - Dashboard
- `http://localhost:8000/SiVGT/cms/products` - Quản lý sản phẩm
- `http://localhost:8000/SiVGT/cms/widgets` - Page builder

**Auth:**
- `http://localhost:8000/SiVGT/login` - Đăng nhập CMS

**SuperAdmin:**
- `http://localhost:8000/superadmin` - Dashboard
- `http://localhost:8000/superadmin/projects` - Quản lý projects

## Migration Notes

### Thay đổi từ `/admin` sang `/cms`

**Cũ:**
```
/{projectCode}/admin/* → project.admin.*
```

**Mới:**
```
/{projectCode}/cms/* → project.cms.*
```

**Lý do:** Phân biệt rõ CMS (Content Management) vs SuperAdmin (System Management)
