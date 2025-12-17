<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;
use Illuminate\Support\Facades\DB;

echo "🔧 Adding translations table to project databases...\n\n";

$projects = Project::all();

foreach ($projects as $project) {
    $dbName = 'project_'.strtolower($project->code);

    echo "📁 Processing: {$project->name} (Code: {$project->code}) → {$dbName}\n";

    try {
        // Check if database exists first
        $databases = DB::select("SHOW DATABASES LIKE '{$dbName}'");
        if (empty($databases)) {
            echo "  ⚠️  Database {$dbName} does not exist, skipping...\n\n";

            continue;
        }

        // Switch to project database
        DB::statement("USE `{$dbName}`");

        // Check if translations table already exists
        $tables = DB::select("SHOW TABLES LIKE 'translations'");
        if (! empty($tables)) {
            echo "  ✅ translations table already exists\n";
        } else {
            // Create translations table
            $translationsTableSql = 'CREATE TABLE translations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                translatable_type VARCHAR(255) NOT NULL,
                translatable_id BIGINT UNSIGNED NOT NULL,
                locale VARCHAR(5) NOT NULL,
                field VARCHAR(255) NOT NULL,
                value LONGTEXT NOT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX translations_translatable_type_translatable_id_index (translatable_type, translatable_id),
                INDEX translations_locale_index (locale),
                UNIQUE KEY translations_unique (translatable_type, translatable_id, locale, field)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

            DB::statement($translationsTableSql);
            echo "  ✅ translations table created\n";
        }

        // Check and update posts table
        $columns = DB::select("SHOW COLUMNS FROM posts LIKE 'post_type'");
        if (empty($columns)) {
            $updatePostsSql = "ALTER TABLE posts 
                ADD COLUMN post_type VARCHAR(50) DEFAULT 'post',
                ADD COLUMN template VARCHAR(100) NULL,
                ADD COLUMN seo_data JSON NULL,
                ADD COLUMN views INT DEFAULT 0,
                ADD COLUMN published_at TIMESTAMP NULL,
                ADD COLUMN author_id BIGINT UNSIGNED NULL,
                ADD COLUMN tenant_id INT UNSIGNED NULL";

            DB::statement($updatePostsSql);
            echo "  ✅ posts table updated with multilingual fields\n";
        } else {
            echo "  ✅ posts table already has multilingual fields\n";
        }

        echo "  🎉 Database {$dbName} completed!\n\n";

    } catch (\Exception $e) {
        echo '  ❌ Error: '.$e->getMessage()."\n\n";
    }
}

echo "✅ TRANSLATIONS TABLE SETUP COMPLETED!\n";
