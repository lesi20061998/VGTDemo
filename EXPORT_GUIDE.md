# Hướng dẫn Export Website

## Tính năng Export Website

Hệ thống cho phép export toàn bộ source code Laravel và database của từng project thành file ZIP để triển khai độc lập.

## Nội dung Export

### 1. Full Laravel Source Code (~100-500MB)
- `app/` - Application logic
- `bootstrap/` - Bootstrap files
- `config/` - Configuration files
- `database/` - Migrations & seeders
- `public/` - Public assets
- `resources/` - Views, CSS, JS
- `routes/` - Route definitions
- `storage/` - Storage files

### 2. Database Export
- `data.json` - Project data (JSON format)
- `database/database.sql` - Full SQL dump
- `database/config.php` - Database configuration

### 3. Configuration Files
- `.env` - Environment configuration
- `.env.example` - Example environment file
- `composer.json` - PHP dependencies
- `package.json` - NPM dependencies

### 4. Deployment Files
- `INSTALLATION.md` - Installation guide
- `deploy.sh` - Deployment script
- `.htaccess` - Apache configuration

### 5. Security Files (Optional)
- Security headers configuration
- CSRF protection setup
- Session security settings

## Cách sử dụng

### Từ Multi-Tenancy Dashboard

1. Truy cập `/superadmin/multi-tenancy`
2. Tìm project cần export
3. Click nút "Xuất Website" (icon download màu xanh)
4. Chọn các tùy chọn:
   - ✅ Include Database SQL
   - ✅ Include Security Files
5. Chờ quá trình export (3-8 phút)
6. File ZIP sẽ tự động download

### Thời gian Export

- **Source nhỏ (<100MB)**: ~2-3 phút
- **Source trung bình (100-300MB)**: ~4-6 phút  
- **Source lớn (>300MB)**: ~6-10 phút

## Cài đặt Website đã Export

### Yêu cầu Server
- PHP 8.1+
- Composer
- MySQL/MariaDB 5.7+
- Node.js & NPM
- Apache/Nginx

### Các bước cài đặt

#### 1. Upload files
```bash
# Extract ZIP file
unzip project_code_website_complete.zip
cd project_code_website_complete
```

#### 2. Install dependencies
```bash
# PHP dependencies
composer install --no-dev --optimize-autoloader

# NPM dependencies
npm install
npm run production
```

#### 3. Configure environment
```bash
# Copy environment file
cp .env .env.local

# Edit database credentials
nano .env.local

# Generate application key
php artisan key:generate
```

#### 4. Setup database
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE project_db"

# Import SQL
mysql -u root -p project_db < database/database.sql

# Or run migrations
php artisan migrate --force
php artisan db:seed --force
```

#### 5. Set permissions
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 6. Configure web server

**Apache (.htaccess already included)**
```apache
DocumentRoot /path/to/project/public
```

**Nginx**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/project/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### 7. Optimize for production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Sử dụng Deploy Script

```bash
# Make script executable
chmod +x deploy.sh

# Run deployment
./deploy.sh
```

## Troubleshooting

### Lỗi thường gặp

#### 1. Export timeout
**Nguyên nhân**: Source code quá lớn
**Giải pháp**: 
- Tăng `max_execution_time` trong php.ini
- Tăng `memory_limit` lên 512M hoặc cao hơn

#### 2. ZIP file corrupt
**Nguyên nhân**: Quá trình export bị gián đoạn
**Giải pháp**: Export lại và đảm bảo kết nối ổn định

#### 3. Database import failed
**Nguyên nhân**: SQL syntax hoặc permissions
**Giải pháp**:
```bash
# Check MySQL version compatibility
mysql --version

# Import with verbose mode
mysql -u root -p -v project_db < database/database.sql
```

#### 4. Permission denied
**Nguyên nhân**: Quyền truy cập file/folder
**Giải pháp**:
```bash
# Fix ownership
sudo chown -R www-data:www-data /path/to/project

# Fix permissions
sudo find /path/to/project -type d -exec chmod 755 {} \;
sudo find /path/to/project -type f -exec chmod 644 {} \;
sudo chmod -R 775 storage bootstrap/cache
```

#### 5. Composer install failed
**Nguyên nhân**: Missing PHP extensions
**Giải pháp**:
```bash
# Install required extensions
sudo apt-get install php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-mysql
```

## Best Practices

### 1. Trước khi Export
- ✅ Backup database
- ✅ Test website hoạt động tốt
- ✅ Clear cache và logs
- ✅ Optimize images và assets

### 2. Sau khi Export
- ✅ Test file ZIP có extract được không
- ✅ Verify database SQL file
- ✅ Check file size hợp lý

### 3. Khi Deploy
- ✅ Sử dụng HTTPS
- ✅ Enable caching
- ✅ Setup backup tự động
- ✅ Monitor performance

## API Export (Advanced)

### Export qua API
```bash
curl -X POST http://localhost:8000/superadmin/projects/{projectCode}/export \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "include_database": true,
    "include_security": true
  }' \
  --output website.zip
```

### Response
- Success: Binary ZIP file
- Error: JSON with error message

## Support

Nếu gặp vấn đề, liên hệ:
- Email: support@vnglobaltech.com
- Phone: +84 xxx xxx xxx
- Documentation: https://docs.vnglobaltech.com
