<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Project;

$projectId = 46;
$project = Project::find($projectId);

$dbName = 'h_ng_thinh_sport';

echo "🔧 Creating missing CMS tables for: {$dbName}\n\n";

try {
    DB::statement("USE `{$dbName}`");
    
    $missingTables = [
        'menu_items' => "CREATE TABLE IF NOT EXISTS menu_items (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, menu_id BIGINT UNSIGNED NOT NULL, parent_id BIGINT UNSIGNED NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NULL, target VARCHAR(50) DEFAULT '_self', icon VARCHAR(100) NULL, order_column INT DEFAULT 0, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX menu_items_menu_id_index (menu_id), INDEX menu_items_parent_id_index (parent_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'product_attributes' => "CREATE TABLE IF NOT EXISTS product_attributes (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, type VARCHAR(50) DEFAULT 'select', is_required BOOLEAN DEFAULT 0, is_filterable BOOLEAN DEFAULT 0, order_column INT DEFAULT 0, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'product_attribute_values' => "CREATE TABLE IF NOT EXISTS product_attribute_values (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_attribute_id BIGINT UNSIGNED NOT NULL, display_name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, order_column INT DEFAULT 0, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX product_attribute_values_attribute_id_index (product_attribute_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'product_attribute_value_mappings' => "CREATE TABLE IF NOT EXISTS product_attribute_value_mappings (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id BIGINT UNSIGNED NOT NULL, product_attribute_id BIGINT UNSIGNED NOT NULL, product_attribute_value_id BIGINT UNSIGNED NOT NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX mappings_product_id_index (product_id), INDEX mappings_attribute_id_index (product_attribute_id), INDEX mappings_value_id_index (product_attribute_value_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'product_variations' => "CREATE TABLE IF NOT EXISTS product_variations (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id BIGINT UNSIGNED NOT NULL, sku VARCHAR(100) NULL, price DECIMAL(10,2) NULL, sale_price DECIMAL(10,2) NULL, stock_quantity INT DEFAULT 0, attributes JSON NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX product_variations_product_id_index (product_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'product_reviews' => "CREATE TABLE IF NOT EXISTS product_reviews (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NULL, customer_name VARCHAR(255) NULL, customer_email VARCHAR(255) NULL, rating INT NOT NULL, title VARCHAR(255) NULL, comment TEXT NULL, status VARCHAR(50) DEFAULT 'pending', created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX product_reviews_product_id_index (product_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'order_items' => "CREATE TABLE IF NOT EXISTS order_items (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id BIGINT UNSIGNED NOT NULL, product_id BIGINT UNSIGNED NOT NULL, product_name VARCHAR(255) NOT NULL, quantity INT NOT NULL, price DECIMAL(10,2) NOT NULL, total DECIMAL(10,2) NOT NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX order_items_order_id_index (order_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'tags' => "CREATE TABLE IF NOT EXISTS tags (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE KEY tags_slug_unique (slug)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'post_tag' => "CREATE TABLE IF NOT EXISTS post_tag (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, post_id BIGINT UNSIGNED NOT NULL, tag_id BIGINT UNSIGNED NOT NULL, INDEX post_tag_post_id_index (post_id), INDEX post_tag_tag_id_index (tag_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'banners' => "CREATE TABLE IF NOT EXISTS banners (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, image VARCHAR(255) NULL, link VARCHAR(255) NULL, position VARCHAR(100) NULL, status VARCHAR(50) DEFAULT 'active', order_column INT DEFAULT 0, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'contact_forms' => "CREATE TABLE IF NOT EXISTS contact_forms (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(50) NULL, subject VARCHAR(255) NULL, message TEXT NOT NULL, status VARCHAR(50) DEFAULT 'new', created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'form_submissions' => "CREATE TABLE IF NOT EXISTS form_submissions (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, form_name VARCHAR(255) NOT NULL, data JSON NOT NULL, ip_address VARCHAR(45) NULL, user_agent TEXT NULL, created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'visitor_logs' => "CREATE TABLE IF NOT EXISTS visitor_logs (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, ip_address VARCHAR(45) NULL, user_agent TEXT NULL, url VARCHAR(255) NULL, referer VARCHAR(255) NULL, visited_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($missingTables as $tableName => $sql) {
        DB::statement($sql);
        echo "  ✅ {$tableName}\n";
    }
    
    echo "\n✅ All missing tables created!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}