# Hướng dẫn cấu hình Multisite Database

## Tổng quan

Hệ thống hiện tại hỗ trợ 2 chế độ hoạt động:

1. **Legacy Mode** (mặc định): Mỗi project có database riêng biệt
2. **Multisite Mode**: Tất cả projects dùng chung 1 database với project_id để phân biệt

## Cấu hình Multisite Mode

### 1. Thêm vào file `.env`

```env
# Multisite Database Configuration
MULTISITE_ENABLED=true
MULTISITE_DB_HOST=127.0.0.1
MULTISITE_DB_PORT=3306
MULTISITE_DB_DATABASE=multisite_db
MULTISITE_DB_USERNAME=root
MULTISITE_DB_PASSWORD=your_password
```

### 2. Tạo database multisite

```sql
CREATE DATABASE multisite_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Chạy migrations cho database multisite

```bash
# Test kết nối trước
php artisan multisite:manage test

# Kiểm tra trạng thái
php artisan multisite:manage status

# Setup database
php artisan multisite:manage setup
```

## Cách hoạt động

### Khi MULTISITE_ENABLED=false (Legacy Mode)
- Mỗi project sẽ kết nối đến database riêng: `project_{code}`
- Trên Hostinger: `{user_prefix}_{code}`

### Khi MULTISITE_ENABLED=true (Multisite Mode)
- Tất cả projects kết nối đến database multisite chung
- Middleware tự động set `current_project_id` trong session
- Các model cần thêm scope để filter theo project_id

## Migration từ Legacy sang Multisite

### Bước 1: Backup dữ liệu
```bash
# Backup tất cả databases hiện tại
mysqldump -u root -p project_code1 > project_code1_backup.sql
mysqldump -u root -p project_code2 > project_code2_backup.sql
```

### Bước 2: Tạo database multisite và import
```bash
# Tạo database mới
mysql -u root -p -e "CREATE DATABASE multisite_db"

# Chạy migrations
php artisan migrate --database=multisite

# Import dữ liệu với project_id
# (Cần script custom để thêm project_id vào các bảng)
```

### Bước 3: Cập nhật .env
```env
MULTISITE_ENABLED=true
MULTISITE_DB_DATABASE=multisite_db
```

## Commands hữu ích

```bash
# Kiểm tra trạng thái cấu hình
php artisan multisite:manage status

# Test kết nối database
php artisan multisite:manage test

# Hướng dẫn migration
php artisan multisite:manage migrate

# Setup database multisite
php artisan multisite:manage setup
```

## Lưu ý quan trọng

1. **Backup dữ liệu** trước khi chuyển đổi
2. **Test kỹ** trên môi trường development trước
3. **Cập nhật models** để hỗ trợ project scoping nếu cần
4. **Kiểm tra permissions** và access control

## Troubleshooting

### Lỗi kết nối database
```bash
php artisan multisite:manage test
```

### Kiểm tra logs
```bash
tail -f storage/logs/laravel.log
```

### Reset về Legacy Mode
Đặt `MULTISITE_ENABLED=false` trong `.env`