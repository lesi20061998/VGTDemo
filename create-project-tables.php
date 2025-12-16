<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Creating tables for project_sivgt database...\n\n";

try {
    // Switch to project_sivgt database
    DB::statement("USE project_sivgt");
    
    // Create products_enhanced table
    DB::statement("
        CREATE TABLE IF NOT EXISTS products_enhanced (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            project_id INT UNSIGNED NULL,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            short_description TEXT NULL,
            description LONGTEXT NULL,
            sku VARCHAR(100) NULL,
            price DECIMAL(10,2) NULL,
            sale_price DECIMAL(10,2) NULL,
            has_price BOOLEAN DEFAULT 1,
            stock_quantity INT DEFAULT 0,
            manage_stock BOOLEAN DEFAULT 0,
            stock_status VARCHAR(50) DEFAULT 'in_stock',
            featured_image VARCHAR(255) NULL,
            gallery JSON NULL,
            weight DECIMAL(8,2) NULL,
            dimensions VARCHAR(255) NULL,
            product_category_id BIGINT UNSIGNED NULL,
            brand_id BIGINT UNSIGNED NULL,
            status VARCHAR(50) DEFAULT 'draft',
            is_featured BOOLEAN DEFAULT 0,
            badges JSON NULL,
            meta_title VARCHAR(255) NULL,
            meta_description TEXT NULL,
            schema_type VARCHAR(100) NULL,
            canonical_url VARCHAR(255) NULL,
            noindex BOOLEAN DEFAULT 0,
            settings JSON NULL,
            views INT DEFAULT 0,
            rating_average DECIMAL(3,2) DEFAULT 0,
            rating_count INT DEFAULT 0,
            product_type VARCHAR(50) DEFAULT 'simple',
            tenant_id INT UNSIGNED NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_project_id (project_id),
            INDEX idx_category (product_category_id),
            INDEX idx_brand (brand_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✅ products_enhanced table created\n";
    
    // Create other essential tables
    $tables = [
        'product_categories' => "
            CREATE TABLE IF NOT EXISTS product_categories (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                project_id INT UNSIGNED NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description TEXT NULL,
                parent_id BIGINT UNSIGNED NULL,
                status VARCHAR(50) DEFAULT 'active',
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_project_id (project_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ",
        'brands' => "
            CREATE TABLE IF NOT EXISTS brands (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                project_id INT UNSIGNED NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description TEXT NULL,
                logo VARCHAR(255) NULL,
                status VARCHAR(50) DEFAULT 'active',
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_project_id (project_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        "
    ];
    
    foreach ($tables as $tableName => $sql) {
        DB::statement($sql);
        echo "✅ {$tableName} table created\n";
    }
    
    // Update project_id for project SiVGT (ID: 27)
    DB::statement("UPDATE products_enhanced SET project_id = 27 WHERE project_id IS NULL");
    DB::statement("UPDATE product_categories SET project_id = 27 WHERE project_id IS NULL");
    DB::statement("UPDATE brands SET project_id = 27 WHERE project_id IS NULL");
    
    echo "\n✅ Project SiVGT database setup completed!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}