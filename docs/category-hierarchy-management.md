# Quản lý Cấp độ Danh mục Sản phẩm

## Tổng quan

Hệ thống quản lý danh mục sản phẩm hỗ trợ cấu trúc phân cấp với khả năng tự động cập nhật cấp độ của các danh mục con khi danh mục cha thay đổi.

## Tính năng chính

### 1. Cập nhật tự động cấp độ danh mục con

Khi một danh mục thay đổi cấp độ (ví dụ: từ cấp 1 lên cấp 0), tất cả các danh mục con sẽ được tự động cập nhật:

- **Cấp độ (level)**: Được tính lại dựa trên danh mục cha mới
- **Đường dẫn (path)**: Được xây dựng lại theo cấu trúc mới

### 2. Ví dụ thực tế

**Trước khi thay đổi:**
```
Cấp 0: Thời trang
├── Cấp 1: Phụ kiện thời trang
    ├── Cấp 2: Túi xách
    └── Cấp 2: Trang sức
```

**Sau khi chuyển "Phụ kiện thời trang" lên cấp 0:**
```
Cấp 0: Thời trang
Cấp 0: Phụ kiện thời trang
├── Cấp 1: Túi xách
└── Cấp 1: Trang sức
```

## Cách thức hoạt động

### 1. Controller Logic

Trong `CategoryController::update()`:

```php
// Lưu trữ cấp độ cũ để so sánh
$oldLevel = $category->level;
$oldParentId = $category->parent_id;

// Cập nhật danh mục
$category->update($data);

// Nếu có thay đổi về parent hoặc level, cập nhật tất cả danh mục con
if ($oldParentId != $data['parent_id'] || $oldLevel != $data['level']) {
    $category->updateDescendantsHierarchy();
}
```

### 2. Model Method

Trong `ProductCategory` model:

```php
public function updateDescendantsHierarchy(): void
{
    foreach ($this->children as $child) {
        // Cập nhật level và path của danh mục con
        $child->level = $this->level + 1;
        $child->path = $this->path.'/'.$child->slug;
        $child->save();

        // Đệ quy cập nhật các danh mục con của nó
        $child->updateDescendantsHierarchy();
    }
}
```

## Cấu trúc Database

### Bảng `product_categories`

| Trường | Kiểu | Mô tả |
|--------|------|-------|
| `id` | bigint | ID chính |
| `name` | string | Tên danh mục |
| `slug` | string | Slug URL |
| `parent_id` | bigint nullable | ID danh mục cha |
| `level` | integer | Cấp độ (0 = root) |
| `path` | string | Đường dẫn đầy đủ |
| `sort_order` | integer | Thứ tự sắp xếp |
| `is_active` | boolean | Trạng thái hoạt động |

### Indexes

- `parent_id, sort_order`: Tối ưu truy vấn danh mục con
- `is_active, level`: Tối ưu truy vấn theo trạng thái và cấp độ

## Testing

Hệ thống được test đầy đủ với các trường hợp:

1. **Chuyển danh mục từ cấp 1 lên cấp 0**
2. **Di chuyển danh mục sang danh mục cha khác**
3. **Cập nhật cấu trúc phân cấp sâu (4+ cấp)**

Chạy test:
```bash
php artisan test tests/Feature/CategoryHierarchyTest.php
```

## Lưu ý quan trọng

- Chức năng hoạt động cho cả `ProductCategory` và `ProjectProductCategory`
- Tự động cập nhật đệ quy cho tất cả các cấp con
- Đảm bảo tính nhất quán của dữ liệu trong toàn bộ cây danh mục
- Tối ưu hiệu suất với việc chỉ cập nhật khi cần thiết