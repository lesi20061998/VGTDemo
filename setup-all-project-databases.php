<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Project;

echo "🔧 Setting up ALL project databases...\n\n";

$projects = Project::all();

foreach ($projects as $project) {
    $dbName = 'project_' . strtolower($project->code);
    echo "📁 Setting up database: {$dbName} for project: {$project->name}\n";
    
    try {
        // Create database if not exists
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Switch to project database
        DB::statement("USE `{$dbName}`");
        
        // Create products_enhanced table
        DB::statement("
            CREATE TABLE IF NOT EXISTS products_enhanced (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                project_id INT UNSIGNED DEFAULT {$project->id},
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
                INDEX idx_project_id (project_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        echo "  ✅ Database {$dbName} setup completed\n\n";
        
    } catch (\Exception $e) {
        echo "  ❌ Error with {$dbName}: " . $e->getMessage() . "\n\n";
    }
}

echo "✅ ALL PROJECT DATABASES SETUP COMPLETED!\n";