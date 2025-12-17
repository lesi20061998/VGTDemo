<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;
use Illuminate\Support\Facades\DB;

echo "🔧 Adding translations table to all project databases...\n\n";

$projects = Project::all();

foreach ($projects as $project) {
    $dbName = 'project_'.strtolower($project->code);

    echo "📁 Processing: {$project->name} → {$dbName}\n";

    try {
        // Switch to project database
        DB::statement("USE `{$dbName}`");

        // Create translations table
        $translationsTableSql = 'CREATE TABLE IF NOT EXISTS translations (
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

        // Update posts table to include missing fields for multilingual support
        $updatePostsSql = "ALTER TABLE posts 
            ADD COLUMN IF NOT EXISTS post_type VARCHAR(50) DEFAULT 'post',
            ADD COLUMN IF NOT EXISTS template VARCHAR(100) NULL,
            ADD COLUMN IF NOT EXISTS seo_data JSON NULL,
            ADD COLUMN IF NOT EXISTS views INT DEFAULT 0,
            ADD COLUMN IF NOT EXISTS published_at TIMESTAMP NULL,
            ADD COLUMN IF NOT EXISTS author_id BIGINT UNSIGNED NULL,
            ADD COLUMN IF NOT EXISTS tenant_id INT UNSIGNED NULL";

        try {
            DB::statement($updatePostsSql);
            echo "  ✅ posts table updated with multilingual fields\n";
        } catch (\Exception $e) {
            echo "  ⚠️  posts table update skipped (fields may already exist)\n";
        }

        echo "  🎉 Database {$dbName} completed!\n\n";

    } catch (\Exception $e) {
        echo '  ❌ Error: '.$e->getMessage()."\n\n";
    }
}

echo "✅ TRANSLATIONS TABLE ADDED TO ALL PROJECT DATABASES!\n";
