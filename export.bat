@echo off
echo Starting Core_system Export Process...
echo.

:: Mốc 1: Chuẩn bị thư mục export (25%)
echo [25%%] Preparing export directory...
if exist "export" rmdir /s /q export
mkdir export
mkdir export\app
mkdir export\config
mkdir export\routes
mkdir export\database
mkdir export\public
mkdir export\resources
echo Export directory prepared.
echo.

:: Mốc 2: Copy source code (50%)
echo [50%%] Copying source code...
xcopy /s /e /q app export\app\
xcopy /s /e /q config export\config\
xcopy /s /e /q routes export\routes\
xcopy /s /e /q public export\public\
xcopy /s /e /q resources export\resources\
copy composer.json export\
copy artisan export\
echo Source code copied.
echo.

:: Mốc 3: Export database & migrations (75%)
echo [75%%] Exporting database and migrations...
xcopy /s /e /q database export\database\
php artisan schema:dump --database=mysql --path=export/database/schema.sql
echo Database and migrations exported.
echo.

:: Mốc 4: Tạo file cấu hình (90%)
echo [90%%] Creating configuration files...
call :create_env
call :create_deploy_script
echo Configuration files created.
echo.

echo [100%%] Export completed successfully!
echo Export location: %cd%\export
pause
goto :eof

:create_env
echo # Core_system Production Environment > export\.env
echo APP_NAME="Core System" >> export\.env
echo APP_ENV=production >> export\.env
echo APP_KEY= >> export\.env
echo APP_DEBUG=false >> export\.env
echo APP_URL=https://your-domain.com >> export\.env
echo. >> export\.env
echo DB_CONNECTION=mysql >> export\.env
echo DB_HOST=127.0.0.1 >> export\.env
echo DB_PORT=3306 >> export\.env
echo DB_DATABASE=core_system >> export\.env
echo DB_USERNAME=root >> export\.env
echo DB_PASSWORD= >> export\.env
goto :eof

:create_deploy_script
echo #!/bin/bash > export\deploy.sh
echo # Core_system Deployment Script >> export\deploy.sh
echo. >> export\deploy.sh
echo echo "Starting Core_system deployment..." >> export\deploy.sh
echo composer install --no-dev --optimize-autoloader >> export\deploy.sh
echo php artisan key:generate >> export\deploy.sh
echo php artisan config:cache >> export\deploy.sh
echo php artisan route:cache >> export\deploy.sh
echo php artisan view:cache >> export\deploy.sh
echo php artisan migrate --force >> export\deploy.sh
echo php artisan storage:link >> export\deploy.sh
echo echo "Deployment completed!" >> export\deploy.sh
goto :eof