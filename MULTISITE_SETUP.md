# Hướng dẫn cấu hình Multisite Database (Fixed Configuration)

## Tổng quan

Hệ thống đã được cấu hình để **luôn sử dụng multisite mode** với database cố định:
- **Database**: `u712054581_Database_01`
- **Username**: `u712054581_Database_01`
- **Password**: Được cấu hình trong `.env`

Tất cả projects sẽ chia sẻ cùng một database với `project_id` để phân biệt dữ liệu.

## Cấu hình bắt buộc trong `.env`

```env
# Multisite Database Configuration (Fixed)
MULTISITE_ENABLED=true
MULTISITE_DB_HOST=127.0.0.1
MULTISITE_DB_PORT=3306
MULTISITE_DB_DATABASE=u712054581_Database_01
MULTISITE_DB_USERNAME=u712054581_Database_01
MULTISITE_DB_PASSWORD=your_actual_password_here
```

## Cách hoạt động

### Khi tạo website mới
1. **Không tạo database mới** - Luôn sử dụng `u712054581_Database_01`
2. **Đổ dữ liệu** vào database chung với `project_id = {project->id}`
3. **Tạo admin user** với `project_id = {project->id}`
4. **Middleware tự động** filter dữ liệu theo `project_id`

### Cấu trúc dữ liệu
```sql
-- Tất cả bảng có thêm cột project_id
users: id, name, email, project_id, ...
settings: id, key, value, project_id, ...
menus: id, name, project_id, ...
posts: id, title, content, project_id, ...
```

### Truy cập dữ liệu
- **Middleware** tự động set `current_project_id` trong session
- **Models** tự động filter theo `project_id`
- **Mỗi project** chỉ thấy dữ liệu của mình

## Commands quản lý

```bash
# Kiểm tra trạng thái cấu hình
php artisan multisite:manage status

# Test kết nối database
php artisan multisite:manage test

# Setup database (tạo tables nếu chưa có)
php artisan multisite:manage setup
```

## Lợi ích của cấu hình này

### Ưu điểm
- **Tiết kiệm database**: Chỉ cần 1 database cho tất cả projects
- **Phù hợp shared hosting**: Không bị giới hạn số database
- **Dễ backup**: Chỉ cần backup 1 database
- **Dễ quản lý**: Tất cả dữ liệu ở một nơi
- **Không cần tạo database**: Chỉ cần đổ dữ liệu

### Cơ chế bảo mật
- **Project isolation**: Mỗi project chỉ thấy dữ liệu của mình
- **Middleware protection**: Tự động filter theo project_id
- **Session-based**: Current project được lưu trong session

## Cấu trúc database

### Bảng chính cần có cột `project_id`
```sql
ALTER TABLE users ADD COLUMN project_id INT NULL;
ALTER TABLE settings ADD COLUMN project_id INT NULL;
ALTER TABLE menus ADD COLUMN project_id INT NULL;
ALTER TABLE menu_items ADD COLUMN project_id INT NULL;
ALTER TABLE widgets ADD COLUMN project_id INT NULL;
ALTER TABLE posts ADD COLUMN project_id INT NULL;
ALTER TABLE products ADD COLUMN project_id INT NULL;
ALTER TABLE categories ADD COLUMN project_id INT NULL;
```

### Index để tối ưu performance
```sql
CREATE INDEX idx_users_project_id ON users(project_id);
CREATE INDEX idx_settings_project_id ON settings(project_id);
CREATE INDEX idx_posts_project_id ON posts(project_id);
```

## Troubleshooting

### Lỗi kết nối database
```bash
php artisan multisite:manage test
```

### Kiểm tra cấu hình
```bash
php artisan multisite:manage status
```

### Kiểm tra logs
```bash
tail -f storage/logs/laravel.log
```

### Lỗi thiếu cột project_id
```sql
-- Thêm cột project_id vào bảng bị thiếu
ALTER TABLE table_name ADD COLUMN project_id INT NULL;
CREATE INDEX idx_table_name_project_id ON table_name(project_id);
```

### Reset dữ liệu project
```sql
-- Xóa tất cả dữ liệu của một project
DELETE FROM users WHERE project_id = 123;
DELETE FROM settings WHERE project_id = 123;
DELETE FROM posts WHERE project_id = 123;
```

## Lưu ý quan trọng

1. **Password bảo mật**: Đặt password mạnh cho `MULTISITE_DB_PASSWORD`
2. **Backup thường xuyên**: Database chứa dữ liệu của tất cả projects
3. **Monitor performance**: Theo dõi hiệu suất khi có nhiều projects
4. **Project isolation**: Đảm bảo middleware hoạt động đúng
5. **Index optimization**: Tạo index cho `project_id` trên các bảng lớn