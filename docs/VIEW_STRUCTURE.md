# View Structure Documentation

## Cấu trúc thư mục Views

```
resources/views/
├── cms/                    # CMS Admin Panel (Content Management System)
│   ├── layouts/           # Layout cho CMS admin
│   ├── dashboard/         # Dashboard CMS
│   ├── products/          # Quản lý sản phẩm
│   ├── categories/        # Quản lý danh mục
│   ├── brands/            # Quản lý thương hiệu
│   ├── orders/            # Quản lý đơn hàng
│   ├── posts/             # Quản lý bài viết
│   ├── pages/             # Quản lý trang
│   ├── widgets/           # Page builder widgets
│   ├── menus/             # Quản lý menu
│   ├── settings/          # Cài đặt hệ thống
│   └── components/        # Components tái sử dụng
│
├── frontend/              # Frontend Website (Khách hàng)
│   ├── home.blade.php    # Trang chủ
│   ├── products/         # Danh sách & chi tiết sản phẩm
│   ├── posts/            # Blog/Tin tức
│   ├── cart/             # Giỏ hàng & checkout
│   ├── contact.blade.php # Liên hệ
│   └── page.blade.php    # Template trang động
│
├── superadmin/            # SuperAdmin Panel (Quản lý Projects)
│   ├── layouts/          # Layout cho superadmin
│   ├── dashboard/        # Dashboard superadmin
│   ├── projects/         # Quản lý projects
│   ├── employees/        # Quản lý nhân viên
│   ├── contracts/        # Quản lý hợp đồng
│   ├── tasks/            # Quản lý tasks
│   ├── tickets/          # Quản lý tickets
│   └── settings/         # Cài đặt hệ thống
│
├── crm/                   # CRM (Customer Relationship Management - Tương lai)
│   ├── layouts/          # Layout cho CRM
│   ├── dashboard/        # Dashboard CRM
│   ├── customers/        # Quản lý khách hàng
│   ├── leads/            # Quản lý leads
│   ├── deals/            # Quản lý deals
│   └── reports/          # Báo cáo
│
├── auth/                  # Authentication views
│   ├── login.blade.php
│   └── project-login.blade.php
│
├── errors/                # Error pages
│   ├── 404.blade.php
│   ├── 403.blade.php
│   └── 500.blade.php
│
└── components/            # Global components
    └── language-switcher.blade.php
```

## Phân biệt các module

### 1. CMS (Content Management System)
- **Mục đích**: Quản lý nội dung website cho từng project
- **URL**: `/{projectCode}/admin/*`
- **Users**: CMS Admin, Content Manager
- **Chức năng**: 
  - Quản lý sản phẩm, đơn hàng
  - Quản lý bài viết, trang
  - Page builder (widgets)
  - Cài đặt website

### 2. Frontend
- **Mục đích**: Website hiển thị cho khách hàng
- **URL**: `/{projectCode}/*`
- **Users**: Khách hàng, Visitors
- **Chức năng**:
  - Xem sản phẩm, blog
  - Giỏ hàng, checkout
  - Liên hệ

### 3. SuperAdmin
- **Mục đích**: Quản lý toàn bộ hệ thống, projects
- **URL**: `/superadmin/*`
- **Users**: SuperAdmin, System Admin
- **Chức năng**:
  - Quản lý projects
  - Quản lý employees, contracts
  - Multi-tenancy management
  - System settings

### 4. CRM (Tương lai)
- **Mục đích**: Quản lý quan hệ khách hàng
- **URL**: `/crm/*`
- **Users**: Sales, Marketing team
- **Chức năng**:
  - Quản lý khách hàng, leads
  - Quản lý deals, opportunities
  - Email marketing
  - Reports & Analytics

## Quy tắc đặt tên

1. **CMS views**: `resources/views/cms/{module}/{action}.blade.php`
2. **Frontend views**: `resources/views/frontend/{page}.blade.php`
3. **SuperAdmin views**: `resources/views/superadmin/{module}/{action}.blade.php`
4. **CRM views**: `resources/views/crm/{module}/{action}.blade.php`

## Export & Deployment

Khi export website cho khách hàng:
- Export: `frontend/` + `cms/` folders
- Không export: `superadmin/`, `crm/` folders
- Config: Tạo `.env` riêng cho từng project
