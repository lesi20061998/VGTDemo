# Hướng dẫn Deploy Laravel lên Hostinger

## 1. Chuẩn bị dự án trước khi deploy

### 1.1 Kiểm tra và tối ưu hóa code
```bash
# Format code theo chuẩn Laravel
vendor/bin/pint --dirty

# Chạy tests để đảm bảo code hoạt động
php artisan test

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 1.2 Tối ưu hóa cho production
```bash
# Tạo cache config cho production
php artisan config:cache

# Tạo cache routes
php artisan route:cache

# Tạo cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 1.3 Build assets
```bash
# Build production assets
npm run build
```

## 2. Cấu hình môi trường production

### 2.1 Tạo file .env cho production
Tạo file `.env.production` với các thông tin sau:
```env
APP_NAME="Your App Name"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 2.2 Generate Application Key
```bash
    php artisan key:generate
```

## 3. Chuẩn bị files để upload

### 3.1 Tạo archive
```bash
# Tạo file zip chứa toàn bộ dự án (trừ node_modules, .git)
# Hoặc sử dụng Git để clone trực tiếp trên server
```

### 3.2 Files cần loại trừ khi upload
- `node_modules/`
- `.git/`
- `storage/logs/*`
- `storage/framework/cache/*`
- `storage/framework/sessions/*`
- `storage/framework/views/*`
- `.env` (sẽ tạo mới trên server)

## 4. Cấu hình trên Hostinger

### 4.1 Truy cập hPanel Hostinger
1. Đăng nhập vào hPanel của Hostinger
2. Chọn hosting cần deploy
3. Vào **File Manager**

### 4.2 Cấu hình cấu trúc thư mục
```
public_html/
├── laravel-app/          # Thư mục chứa toàn bộ Laravel app
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   ├── artisan
│   ├── composer.json
│   └── ...
├── index.php             # File redirect từ public_html đến laravel-app/public
├── .htaccess            # File redirect
└── assets/              # Symbolic link hoặc copy từ laravel-app/public
```

### 4.3 Upload files
1. Tạo thư mục `laravel-app` trong `public_html`
2. Upload toàn bộ files Laravel vào `laravel-app/`
3. Copy nội dung từ `laravel-app/public/` ra `public_html/`

### 4.4 Tạo file index.php redirect
Tạo file `public_html/index.php`:
```php
<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/laravel-app/public/index.php';
```

### 4.5 Tạo file .htaccess redirect
Tạo file `public_html/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Handle Angular and Vue.js routes
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /index.php [QSA,L]
</IfModule>
```

## 5. Cấu hình Database

### 5.1 Tạo Database trên Hostinger
1. Vào **Databases** → **MySQL Databases**
2. Tạo database mới
3. Tạo user và gán quyền cho database
4. Ghi nhớ thông tin: database name, username, password

### 5.2 Import database (nếu có)
```bash
# Nếu có database từ local, export trước:
mysqldump -u username -p database_name > database_backup.sql

# Sau đó import trên Hostinger qua phpMyAdmin
```

### 5.3 Chạy migrations
```bash
# SSH vào server và chạy:
cd public_html/laravel-app
php artisan migrate --force
php artisan db:seed --force  # Nếu cần seed data
```

## 6. Cấu hình permissions

### 6.1 Set permissions cho thư mục storage và bootstrap/cache
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 6.2 Đảm bảo web server có quyền ghi
```bash
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

## 7. Cấu hình SSL và Domain

### 7.1 Kích hoạt SSL
1. Vào **SSL/TLS** trong hPanel
2. Kích hoạt **Let's Encrypt SSL**
3. Đợi SSL được cấp phát

### 7.2 Force HTTPS
Thêm vào file `.htaccess`:
```apache
# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## 8. Tối ưu hóa sau khi deploy

### 8.1 Cache optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8.2 Composer optimization
```bash
composer dump-autoload --optimize
```

## 9. Kiểm tra và Debug

### 9.1 Kiểm tra logs
```bash
tail -f storage/logs/laravel.log
```

### 9.2 Common issues và solutions

#### Issue: 500 Internal Server Error
- Kiểm tra file permissions
- Kiểm tra .env file
- Kiểm tra logs trong `storage/logs/`

#### Issue: Database connection error
- Kiểm tra thông tin database trong .env
- Đảm bảo database user có đủ quyền

#### Issue: Assets không load
- Kiểm tra đường dẫn assets
- Chạy `npm run build` lại
- Kiểm tra symbolic links

#### Issue: Session/Cache errors
- Clear cache: `php artisan cache:clear`
- Kiểm tra permissions của thư mục storage

## 10. Maintenance và Monitoring

### 10.1 Backup định kỳ
- Setup backup database hàng ngày
- Backup files quan trọng

### 10.2 Update và Security
- Thường xuyên update Laravel và packages
- Monitor security vulnerabilities
- Setup monitoring cho uptime

### 10.3 Performance optimization
- Enable OPcache
- Setup Redis/Memcached nếu có
- Optimize database queries
- Setup CDN cho static assets

## 11. Troubleshooting Commands

```bash
# Clear all caches
php artisan optimize:clear

# Recreate caches
php artisan optimize

# Check application status
php artisan about

# Run health checks
php artisan health:check

# View current configuration
php artisan config:show

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

## 12. Security Checklist

- [ ] APP_DEBUG=false trong production
- [ ] APP_ENV=production
- [ ] Strong APP_KEY generated
- [ ] Database credentials secure
- [ ] SSL certificate active
- [ ] File permissions correct (755 for directories, 644 for files)
- [ ] .env file không accessible từ web
- [ ] Remove development packages
- [ ] Setup firewall rules
- [ ] Regular security updates

---

**Lưu ý quan trọng:**
- Luôn backup trước khi deploy
- Test trên staging environment trước
- Monitor logs sau khi deploy
- Có plan rollback nếu cần thiết