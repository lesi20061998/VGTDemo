#!/bin/bash

echo "Starting Core_system Export Process..."
echo

# Mốc 1: Chuẩn bị thư mục export (25%)
echo "[25%] Preparing export directory..."
rm -rf export
mkdir -p export/{app,config,routes,database,public,resources}
echo "Export directory prepared."
echo

# Mốc 2: Copy source code (50%)
echo "[50%] Copying source code..."
cp -r app export/
cp -r config export/
cp -r routes export/
cp -r public export/
cp -r resources export/
cp composer.json export/
cp artisan export/
echo "Source code copied."
echo

# Mốc 3: Export database & migrations (75%)
echo "[75%] Exporting database and migrations..."
cp -r database export/
php artisan schema:dump --database=mysql --path=export/database/schema.sql
echo "Database and migrations exported."
echo

# Mốc 4: Tạo file cấu hình (90%)
echo "[90%] Creating configuration files..."

# Tạo .env file
cat > export/.env << 'EOF'
# Core_system Production Environment
APP_NAME="Core System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=core_system
DB_USERNAME=root
DB_PASSWORD=
EOF

# Tạo deploy.sh
cat > export/deploy.sh << 'EOF'
#!/bin/bash
# Core_system Deployment Script

echo "Starting Core_system deployment..."
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link
echo "Deployment completed!"
EOF

chmod +x export/deploy.sh
echo "Configuration files created."
echo

echo "[100%] Export completed successfully!"
echo "Export location: $(pwd)/export"