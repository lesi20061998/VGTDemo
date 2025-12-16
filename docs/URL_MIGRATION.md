# URL Migration Guide

## Thay đổi URL CMS Admin

### URL Cũ (DEPRECATED)
```
/{projectCode}/admin/*
```

### URL Mới (HIỆN TẠI)
```
/{projectCode}/cms/*
```

## Ví dụ với Project: SiVGT

| Chức năng | URL Cũ | URL Mới |
|-----------|---------|---------|
| Dashboard | `/SiVGT/admin` | `/SiVGT/cms` |
| Products | `/SiVGT/admin/products` | `/SiVGT/cms/products` |
| Orders | `/SiVGT/admin/orders` | `/SiVGT/cms/orders` |
| Widgets | `/SiVGT/admin/widgets` | `/SiVGT/cms/widgets` |
| Settings | `/SiVGT/admin/settings` | `/SiVGT/cms/settings` |

## Lý do thay đổi

1. **Phân biệt rõ ràng**: CMS (Content Management) vs SuperAdmin (System Management)
2. **Cấu trúc views**: `resources/views/cms/` thay vì `resources/views/admin/`
3. **Route naming**: `project.cms.*` thay vì `project.admin.*`
4. **Dễ export**: Khi export website, chỉ cần export `cms/` và `frontend/`

## Cập nhật Links

Nếu có hardcode links trong code, cần update:

```php
// Cũ
<a href="/{{ $projectCode }}/admin/products">Products</a>

// Mới
<a href="/{{ $projectCode }}/cms/products">Products</a>
```

Hoặc dùng route helper:
```php
<a href="{{ route('project.cms.products.index', $projectCode) }}">Products</a>
```

## Bookmarks

Nếu đã bookmark URL cũ, cần update:
- `http://localhost:8000/SiVGT/admin` → `http://localhost:8000/SiVGT/cms`

## Không có Redirect

URL cũ `/admin` KHÔNG tự động redirect sang `/cms` vì conflict với frontend routes.
Phải dùng URL mới `/cms` trực tiếp.
