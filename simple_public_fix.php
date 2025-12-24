<?php
echo "=== Simple Public URL Fix ===\n";

// Method 1: Check current setup
echo "1. Current Setup:\n";
echo "   Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "   Current Script: " . __FILE__ . "\n";
echo "   Public folder exists: " . (is_dir('public') ? 'YES' : 'NO') . "\n";

// Method 2: Create proper .htaccess
echo "\n2. Creating .htaccess fix...\n";

$htaccess = '<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Remove /public/ from URLs
    RewriteCond %{THE_REQUEST} /public/([^\s?]*) [NC]
    RewriteRule ^ /%1 [R=301,L]
    
    # Internal rewrite to public folder
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>

# PHP Settings
php_value max_execution_time 120
php_value memory_limit 256M
php_value upload_max_filesize 50M
php_value post_max_size 50M';

file_put_contents('.htaccess', $htaccess);
echo "   ✓ Created .htaccess\n";

// Method 3: Create index.php redirect
echo "\n3. Creating index.php...\n";
$index = '<?php
// Laravel Entry Point
require_once __DIR__ . "/public/index.php";
';

file_put_contents('index.php', $index);
echo "   ✓ Created index.php\n";

echo "\n=== Fix Complete ===\n";
echo "Now try: https://core.vnglobaltech.com/SiVGT/admin\n";
echo "(without /public/)\n";
?>