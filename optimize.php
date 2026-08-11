<?php

use Illuminate\Contracts\Console\Kernel;

/**
 * Chạy file này trên trình duyệt hoặc qua lệnh: php optimize.php
 * File này sẽ tự động tải composer, chạy composer install và gọi các lệnh artisan tối ưu.
 */

// Bỏ giới hạn thời gian và tăng RAM vì composer cài đặt sẽ tốn nhiều tài nguyên
set_time_limit(0);
ini_set('memory_limit', '2G');

$isCli = php_sapi_name() === 'cli';

if (! $isCli) {
    echo "<pre style='background:#1e1e1e; color:#0f0; padding:20px; border-radius:5px;'>";
}

echo "===================================================\n";
echo "       LARAVEL DEPLOY & OPTIMIZE SCRIPT\n";
echo "===================================================\n\n";

// -------------------------------------------------------------
// 1. CHẠY COMPOSER INSTALL
// -------------------------------------------------------------
echo "[1] RUNNING COMPOSER...\n";

// Kiểm tra xem Hosting có cho phép dùng lệnh shell_exec không
if (! function_exists('shell_exec')) {
    echo "=> CẢNH BÁO: Hosting của bạn đã chặn hàm shell_exec(). Không thể chạy Composer qua trình duyệt.\n";
    echo "=> GIẢI PHÁP: Bạn cần nén thư mục 'vendor' từ máy tính của mình và up lên host.\n\n";
} else {
    // Composer cần một thư mục tạm để lưu cache
    putenv('COMPOSER_HOME='.__DIR__.'/.composer');
    if (! is_dir(__DIR__.'/.composer')) {
        mkdir(__DIR__.'/.composer', 0755, true);
    }

    // Tải composer.phar nếu chưa có
    if (! file_exists(__DIR__.'/composer.phar')) {
        echo "=> Đang tải composer.phar...\n";
        file_put_contents(__DIR__.'/composer.phar', file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar'));
    }

    if (file_exists(__DIR__.'/composer.phar')) {
        echo "=> Đang chạy composer install (vui lòng chờ vài phút, không tắt trình duyệt)...\n";
        // Cố gắng tìm đường dẫn PHP, nếu không cứ gọi php mặc định
        $output = shell_exec('php composer.phar install --optimize-autoloader --no-dev 2>&1');
        echo $output."\n";
    } else {
        echo "=> LỖI: Không thể tải được file composer.phar\n\n";
    }
}
echo "---------------------------------------------------\n\n";

// -------------------------------------------------------------
// 2. CHẠY ARTISAN (TỐI ƯU CACHE)
// -------------------------------------------------------------
echo "[2] RUNNING LARAVEL ARTISAN OPTIMIZATION...\n";

if (! file_exists(__DIR__.'/vendor/autoload.php')) {
    exit("=> LỖI NGHIÊM TRỌNG: Chưa có thư mục vendor (Composer cài thất bại hoặc chưa được up lên). Không thể chạy Artisan!\n");
}

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$commands = [
    'optimize:clear',
    'config:cache',
    'route:cache',
    'view:cache',
    'event:cache',
    'optimize',
];

foreach ($commands as $command) {
    echo "=> Running: php artisan {$command} ...\n";
    try {
        $kernel->call($command);
        echo $kernel->output();
    } catch (Exception $e) {
        echo '=> LỖI: '.$e->getMessage()."\n";
    }
}

echo "\n===================================================\n";
echo "[HOÀN TẤT] DỰ ÁN CỦA BẠN ĐÃ ĐƯỢC DEPLOY & TỐI ƯU THÀNH CÔNG!\n";
echo "===================================================\n";

if (! $isCli) {
    echo '</pre>';
}
