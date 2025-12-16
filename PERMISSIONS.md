# HỆ THỐNG PHÂN QUYỀN

## 📊 CẤU TRÚC PHÂN QUYỀN

### 1. LEVEL (Cấp độ hệ thống)
```
level = 0: SuperAdmin (Quản trị viên tối cao)
level = 1: Administrator (Quản trị viên)
level = 2: User (Người dùng thường)
```

### 2. ROLE (Vai trò)
```
role = 'admin': Quản trị viên
role = 'cms': Người quản lý nội dung
role = 'employee': Nhân viên
```

## 🔐 PHÂN QUYỀN CHI TIẾT

### A. SUPERADMIN ROUTES (`/superadmin/*`)
**Middleware:** `SuperAdminMiddleware`
**Quyền truy cập:** 
- ✅ level = 0 (SuperAdmin)
- ✅ level = 1 (Administrator)
- ❌ level = 2 (User)

**Chức năng:**
- Quản lý dự án (Projects)
- Quản lý nhân viên (Employees)
- Quản lý hợp đồng (Contracts)
- Quản lý công việc (Tasks)
- Quản lý tickets (Tickets)
- Quản lý tenants (Multi-tenancy)
- Tạo và cấu hình website

**Routes:**
```php
/superadmin/                    # Dashboard
/superadmin/projects            # Quản lý dự án
/superadmin/employees           # Quản lý nhân viên
/superadmin/contracts           # Quản lý hợp đồng
/superadmin/tasks               # Quản lý công việc
/superadmin/tickets             # Quản lý tickets
/superadmin/tenants             # Quản lý tenants
```

---

### B. CMS ROUTES (`/cms/admin/*`)
**Middleware:** `CMSMiddleware`
**Quyền truy cập:**
- ✅ role = 'cms'
- ✅ role = 'admin'
- ✅ level <= 1 (SuperAdmin/Administrator)
- ❌ role = 'employee' (trừ khi level <= 1)

**Chức năng:**
- Quản lý sản phẩm (Products)
- Quản lý danh mục (Categories)
- Quản lý thương hiệu (Brands)
- Quản lý thuộc tính (Attributes)
- Quản lý đơn hàng (Orders)
- Quản lý nội dung (Posts, Pages, FAQs)
- Quản lý media
- Quản lý người dùng
- Cài đặt hệ thống

**Routes:**
```php
/cms/admin/                     # Dashboard
/cms/admin/products             # Quản lý sản phẩm
/cms/admin/categories           # Quản lý danh mục
/cms/admin/brands               # Quản lý thương hiệu
/cms/admin/attributes           # Quản lý thuộc tính
/cms/admin/orders               # Quản lý đơn hàng
/cms/admin/posts                # Quản lý bài viết
/cms/admin/pages                # Quản lý trang
/cms/admin/users                # Quản lý người dùng
/cms/admin/settings             # Cài đặt
```

---

### C. ADMIN ROUTES (`/admin/*`)
**Middleware:** `AdminMiddleware`
**Quyền truy cập:**
- ✅ role = 'admin'
- ✅ level <= 1 (SuperAdmin/Administrator)
- ❌ role = 'cms' (trừ khi level <= 1)
- ❌ role = 'employee'

**Chức năng:**
- Dashboard cơ bản
- Quản lý media

**Routes:**
```php
/admin/                         # Dashboard
/admin/media/list               # Danh sách media
/admin/media/upload             # Upload media
```

---

### D. EMPLOYEE ROUTES (`/employee/*`)
**Middleware:** `EmployeeMiddleware`
**Quyền truy cập:**
- ✅ role = 'employee'
- ✅ level <= 1 (có thể truy cập)

**Chức năng:**
- Dashboard nhân viên
- Xem công việc được giao
- Xem hợp đồng
- Báo cáo tiến độ

---

### E. PROJECT ROUTES (`/project/*`)
**Middleware:** `ProjectMiddleware`
**Quyền truy cập:**
- ✅ User có project_ids chứa project đang truy cập
- ✅ level <= 1 (có thể truy cập tất cả)

**Chức năng:**
- Quản lý dự án cụ thể
- Cài đặt dự án
- Quản lý thành viên dự án

---

## 📋 MA TRẬN PHÂN QUYỀN

| Chức năng | SuperAdmin (L0) | Administrator (L1) | CMS User (L2) | Employee | User (L2) |
|-----------|----------------|-------------------|---------------|----------|-----------|
| SuperAdmin Panel | ✅ | ✅ | ❌ | ❌ | ❌ |
| CMS Panel | ✅ | ✅ | ✅ | ❌ | ❌ |
| Admin Panel | ✅ | ✅ | ❌ | ❌ | ❌ |
| Employee Panel | ✅ | ✅ | ❌ | ✅ | ❌ |
| Project Panel | ✅ | ✅ | Theo project_ids | Theo project_ids | ❌ |
| Quản lý Projects | ✅ | ✅ | ❌ | ❌ | ❌ |
| Quản lý Employees | ✅ | ✅ | ❌ | ❌ | ❌ |
| Quản lý Contracts | ✅ | ✅ | ❌ | ❌ | ❌ |
| Quản lý Products | ✅ | ✅ | ✅ | ❌ | ❌ |
| Quản lý Orders | ✅ | ✅ | ✅ | ❌ | ❌ |
| Quản lý Content | ✅ | ✅ | ✅ | ❌ | ❌ |
| Quản lý Users | ✅ | ✅ | ✅ | ❌ | ❌ |
| System Settings | ✅ | ✅ | ✅ | ❌ | ❌ |

---

## 🎯 HƯỚNG DẪN SỬ DỤNG

### 1. Tạo User với quyền SuperAdmin:
```php
User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'level' => 0,  // SuperAdmin
]);
```

### 2. Tạo User với quyền Administrator:
```php
User::create([
    'name' => 'Administrator',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'level' => 1,  // Administrator
]);
```

### 3. Tạo User với quyền CMS:
```php
User::create([
    'name' => 'CMS User',
    'email' => 'cms@example.com',
    'password' => bcrypt('password'),
    'role' => 'cms',
    'level' => 2,  // User
]);
```

### 4. Tạo Employee:
```php
User::create([
    'name' => 'Employee',
    'email' => 'employee@example.com',
    'password' => bcrypt('password'),
    'role' => 'employee',
    'level' => 2,  // User
]);
```

### 5. Gán User vào Project:
```php
$user = User::find(1);
$user->assignToProject($projectId);
// hoặc
$user->update(['project_ids' => [1, 2, 3]]);
```

---

## 🔧 KIỂM TRA QUYỀN TRONG CODE

### Trong Controller:
```php
// Kiểm tra SuperAdmin
if (auth()->user()->isSuperAdmin()) {
    // Code cho SuperAdmin
}

// Kiểm tra Administrator
if (auth()->user()->isAdministrator()) {
    // Code cho Administrator
}

// Kiểm tra có thể truy cập SuperAdmin
if (auth()->user()->canAccessSuperAdmin()) {
    // Code cho SuperAdmin hoặc Administrator
}

// Kiểm tra có quyền truy cập project
if (auth()->user()->hasAccessToProject($projectId)) {
    // Code cho user có quyền
}
```

### Trong Blade:
```blade
@if(auth()->user()->isSuperAdmin())
    <!-- Nội dung cho SuperAdmin -->
@endif

@if(auth()->user()->canAccessSuperAdmin())
    <!-- Nội dung cho SuperAdmin/Administrator -->
@endif

@if(auth()->user()->role === 'cms')
    <!-- Nội dung cho CMS User -->
@endif
```

---

## 🚨 LƯU Ý QUAN TRỌNG

1. **Level có ưu tiên cao hơn Role:**
   - User có level = 0 hoặc 1 có thể truy cập mọi khu vực
   - Role chỉ áp dụng cho user có level = 2

2. **Project Access:**
   - User phải có project_id trong mảng project_ids
   - SuperAdmin và Administrator có thể truy cập tất cả projects

3. **Middleware Order:**
   - Luôn kiểm tra auth trước
   - Sau đó kiểm tra level
   - Cuối cùng kiểm tra role

4. **Security:**
   - Không bao giờ tin tưởng input từ client
   - Luôn kiểm tra quyền ở cả middleware và controller
   - Log tất cả các hành động quan trọng

---

## 📝 DANH SÁCH TÀI KHOẢN MẪU

Sau khi chạy seeder:

| Email | Password | Role | Level | Quyền |
|-------|----------|------|-------|-------|
| admin@example.com | password | admin | 1 | Administrator - Full access |
| admin@gmail.com | 1234 | admin | 1 | Administrator - Full access |
| user@example.com | password | cms | 2 | CMS User - CMS only |

---

## 🔄 CẬP NHẬT QUYỀN

Để thay đổi quyền của user:

```php
// Nâng cấp lên Administrator
$user->update(['level' => 1, 'role' => 'admin']);

// Hạ xuống CMS User
$user->update(['level' => 2, 'role' => 'cms']);

// Chuyển sang Employee
$user->update(['role' => 'employee']);
```
