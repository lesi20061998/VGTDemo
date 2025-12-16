<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Project;

echo "🔧 Creating ALL tables for project databases...\n\n";

$projects = Project::all();

foreach ($projects as $project) {
    $dbName = strtolower($project->name);
    $dbName = preg_replace('/[^a-z0-9_]/', '_', $dbName);
    $dbName = preg_replace('/_+/', '_', $dbName);
    $dbName = trim($dbName, '_');
    
    echo "📁 Processing: {$project->name} → {$dbName}\n";
    
    try {
        DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::statement("USE `{$dbName}`");
        
        // Essential tables
        $tables = [
            'users' => "CREATE TABLE IF NOT EXISTS users (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, username VARCHAR(255) UNIQUE NOT NULL, email VARCHAR(255) UNIQUE NOT NULL, email_verified_at TIMESTAMP NULL, password VARCHAR(255) NOT NULL, role VARCHAR(50) DEFAULT 'cms', level INT DEFAULT 2, remember_token VARCHAR(100) NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'sessions' => "CREATE TABLE IF NOT EXISTS sessions (id VARCHAR(255) NOT NULL PRIMARY KEY, user_id BIGINT UNSIGNED NULL, ip_address VARCHAR(45) NULL, user_agent TEXT NULL, payload LONGTEXT NOT NULL, last_activity INT NOT NULL, INDEX sessions_user_id_index (user_id), INDEX sessions_last_activity_index (last_activity)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'products_enhanced' => "CREATE TABLE IF NOT EXISTS products_enhanced (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, project_id INT UNSIGNED DEFAULT {$project->id}, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, short_description TEXT NULL, description LONGTEXT NULL, sku VARCHAR(100) NULL, price DECIMAL(10,2) NULL, sale_price DECIMAL(10,2) NULL, has_price BOOLEAN DEFAULT 1, stock_quantity INT DEFAULT 0, manage_stock BOOLEAN DEFAULT 0, stock_status VARCHAR(50) DEFAULT 'in_stock', featured_image VARCHAR(255) NULL, gallery JSON NULL, weight DECIMAL(8,2) NULL, dimensions VARCHAR(255) NULL, product_category_id BIGINT UNSIGNED NULL, brand_id BIGINT UNSIGNED NULL, status VARCHAR(50) DEFAULT 'draft', is_featured BOOLEAN DEFAULT 0, badges JSON NULL, meta_title VARCHAR(255) NULL, meta_description TEXT NULL, schema_type VARCHAR(100) NULL, canonical_url VARCHAR(255) NULL, noindex BOOLEAN DEFAULT 0, settings JSON NULL, views INT DEFAULT 0, rating_average DECIMAL(3,2) DEFAULT 0, rating_count INT DEFAULT 0, product_type VARCHAR(50) DEFAULT 'simple', tenant_id INT UNSIGNED NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_project_id (project_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'product_categories' => "CREATE TABLE IF NOT EXISTS product_categories (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, project_id INT UNSIGNED DEFAULT {$project->id}, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description TEXT NULL, parent_id BIGINT UNSIGNED NULL, status VARCHAR(50) DEFAULT 'active', created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_project_id (project_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'brands' => "CREATE TABLE IF NOT EXISTS brands (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, project_id INT UNSIGNED DEFAULT {$project->id}, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description TEXT NULL, logo VARCHAR(255) NULL, status VARCHAR(50) DEFAULT 'active', created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_project_id (project_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'settings' => "CREATE TABLE IF NOT EXISTS settings (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, project_id INT UNSIGNED DEFAULT {$project->id}, setting_key VARCHAR(255) NOT NULL, value LONGTEXT NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_project_id (project_id), UNIQUE KEY settings_key_unique (setting_key)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'menus' => "CREATE TABLE IF NOT EXISTS menus (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, project_id INT UNSIGNED DEFAULT {$project->id}, name VARCHAR(255) NOT NULL, location VARCHAR(100) NULL, status VARCHAR(50) DEFAULT 'active', created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_project_id (project_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            
            'posts' => "CREATE TABLE IF NOT EXISTS posts (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, project_id INT UNSIGNED DEFAULT {$project->id}, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NULL, excerpt TEXT NULL, status VARCHAR(50) DEFAULT 'draft', featured_image VARCHAR(255) NULL, meta_title VARCHAR(255) NULL, meta_description TEXT NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_project_id (project_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
        
        foreach ($tables as $tableName => $sql) {
            DB::statement($sql);
            echo "  ✅ {$tableName}\n";
        }
        
        echo "  🎉 Database {$dbName} completed!\n\n";
        
    } catch (\Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "✅ ALL PROJECT DATABASES SETUP COMPLETED!\n";