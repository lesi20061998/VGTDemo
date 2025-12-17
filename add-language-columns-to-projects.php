<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;
use Illuminate\Support\Facades\DB;

echo "🔧 Adding language columns to project databases...\n\n";

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

        // Add language column to products_enhanced table
        $productsColumns = DB::select("SHOW COLUMNS FROM products_enhanced LIKE 'language'");
        if (empty($productsColumns)) {
            DB::statement("ALTER TABLE products_enhanced ADD COLUMN language VARCHAR(5) DEFAULT 'vi' AFTER tenant_id");
            DB::statement('ALTER TABLE products_enhanced ADD INDEX idx_language (language)');
            echo "  ✅ Added language column to products_enhanced\n";
        } else {
            echo "  ✅ products_enhanced already has language column\n";
        }

        // Add language column to posts table (if exists)
        $tablesResult = DB::select("SHOW TABLES LIKE 'posts'");
        if (! empty($tablesResult)) {
            $postsColumns = DB::select("SHOW COLUMNS FROM posts LIKE 'language'");
            if (empty($postsColumns)) {
                DB::statement("ALTER TABLE posts ADD COLUMN language VARCHAR(5) DEFAULT 'vi' AFTER tenant_id");
                DB::statement('ALTER TABLE posts ADD INDEX idx_language (language)');
                echo "  ✅ Added language column to posts\n";
            } else {
                echo "  ✅ posts already has language column\n";
            }
        } else {
            echo "  ⚠️  posts table does not exist\n";
        }

        echo "  🎉 Database {$dbName} completed!\n\n";

    } catch (\Exception $e) {
        echo '  ❌ Error: '.$e->getMessage()."\n\n";
    }
}

echo "✅ LANGUAGE COLUMNS ADDED TO ALL PROJECT DATABASES!\n";
