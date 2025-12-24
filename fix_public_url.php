<?php

// Fix Public URL Issue - Remove /public/ from URLs
echo "=== Fixing Public URL Issue ===\n\n";

try {
    require_once 'vendor/autoload.php';
    
    // Bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "1. Current Configuration...\n";
    echo "   Current URL: " . request()->url() . "\n";
    echo "   APP_URL: " . config('app.url') . "\n";
    echo "   Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo "   Script Path: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
    
    echo "\n2. Analyzing Directory Structure...\n";
    
    $currentDir = getcwd();
    $publicDir = $currentDir . '/public';
    $htaccessPath = $currentDir . '/.htaccess';
    $publicHtaccessPath = $publicDir . '/.htaccess';
    
    echo "   Current directory: {$currentDir}\n";
    echo "   Public directory exists: " . (is_dir($publicDir) ? 'YES' : 'NO') . "\n";
    echo "   Root .htaccess exists: " . (file_exists($htaccessPath) ? 'YES' : 'NO') . "\n";
    echo "   Public .htaccess exists: " . (file_exists($publicHtaccessPath) ? 'YES' : 'NO') . "\n";
    
    echo "\n3. Creating Proper .htaccess Configuration...\n";
    
    // This is the correct .htaccess for Laravel when source is in document root
    $correctHtaccess = '<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect to public folder if accessing Laravel files directly
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ /public/$1 [L]

    # Handle public folder requests
    RewriteCond %{REQUEST_URI} ^/public/
    RewriteRule ^public/(.*)$ /public/$1 [L]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ /public/index.php [L]
</IfModule>

# Security: Deny access to sensitive files
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.lock">
    Order allow,deny
    Deny from all
</Files>

<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Increase PHP limits for export
php_value max_execution_time 120
php_value memory_limit 256M
php_value upload_max_filesize 50M
php_value post_max_size 50M';

    // Backup current .htaccess
    if (file_exists($htaccessPath)) {
        copy($htaccessPath, $htaccessPath . '.backup.' . date('Y-m-d-H-i-s'));
        echo "   ✓ Backed up current .htaccess\n";
    }
    
    // Write new .htaccess
    file_put_contents($htaccessPath, $correctHtaccess);
    echo "   ✓ Created new .htaccess configuration\n";
    
    echo "\n4. Alternative Solution - Symlink Method...\n";
    
    // Create alternative solution using symlinks
    $symlinkSolution = '# Alternative: Create symlinks to remove /public/ from URLs
# Run these commands on your server:

# Method 1: Move public contents to root (RECOMMENDED for shared hosting)
# cp -r public/* ./
# cp public/.htaccess ./
# rm -rf public

# Method 2: Create symlinks (if server supports)
# ln -sf public/index.php index.php
# ln -sf public/.htaccess .htaccess
# ln -sf public/storage storage

# Method 3: Update web server document root (if you have access)
# Point document root to: /home/u712054581/domains/core.vnglobaltech.com/public_html/public
';

    file_put_contents('symlink_solution.txt', $symlinkSolution);
    echo "   ✓ Created symlink_solution.txt with alternatives\n";
    
    echo "\n5. Creating Index.php Redirect...\n";
    
    // Create index.php in root that redirects to public
    $indexRedirect = '<?php
// Redirect to public folder - Laravel Entry Point
require_once __DIR__ . "/public/index.php";
';

    file_put_contents('index.php', $indexRedirect);
    echo "   ✓ Created index.php redirect\n";
    
    echo "\n6. Testing URL Generation...\n";
    
    // Test route generation
    try {
        $testRoutes = [
            'login' => route('login'),
            'project.home' => route('project.home', ['projectCode' => 'SiVGT']),
            'project.admin.dashboard' => route('project.admin.dashboard', ['projectCode' => 'SiVGT'])
        ];
        
        foreach ($testRoutes as $name => $url) {
            echo "   {$name}: {$url}\n";
        }
    } catch (Exception $e) {
        echo "   Error generating routes: " . $e->getMessage() . "\n";
    }
    
    echo "\n7. Creating URL Fix Script...\n";
    
    $urlFixScript = '<?php
// URL Fix Script - Run this to update APP_URL
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

// Get current domain without /public/
$currentDomain = $_SERVER["HTTP_HOST"] ?? "core.vnglobaltech.com";
$protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
$correctUrl = "{$protocol}://{$currentDomain}";

echo "Current APP_URL: " . config("app.url") . "\\n";
echo "Correct URL should be: {$correctUrl}\\n";

// Update .env file
$envPath = base_path(".env");
$envContent = file_get_contents($envPath);
$envContent = preg_replace("/^APP_URL=.*/m", "APP_URL={$correctUrl}", $envContent);
file_put_contents($envPath, $envContent);

// Clear config cache
Artisan::call("config:clear");

echo "✓ Updated APP_URL to: {$correctUrl}\\n";
echo "✓ Config cache cleared\\n";
?>';

    file_put_contents('update_app_url.php', $urlFixScript);
    echo "   ✓ Created update_app_url.php\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Fix Complete ===\n";
echo "\nSOLUTIONS TO REMOVE /public/ FROM URL:\n\n";

echo "OPTION 1 - .htaccess Redirect (DONE):\n";
echo "✓ Updated .htaccess to handle /public/ redirects\n";
echo "✓ Created index.php redirect\n";
echo "✓ URLs should work without /public/ now\n\n";

echo "OPTION 2 - Move Files (RECOMMENDED for shared hosting):\n";
echo "1. Run: cp -r public/* ./\n";
echo "2. Run: rm -rf public\n";
echo "3. Update bootstrap/app.php if needed\n\n";

echo "OPTION 3 - Update APP_URL:\n";
echo "1. Run: php update_app_url.php\n";
echo "2. Test URLs without /public/\n\n";

echo "CURRENT STATUS:\n";
echo "- Your site should now work at: https://core.vnglobaltech.com/SiVGT/admin\n";
echo "- Instead of: https://core.vnglobaltech.com/public/SiVGT/admin\n";
echo "- Test the URLs and let me know if they work!\n";