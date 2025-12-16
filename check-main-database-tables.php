<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Checking main database tables...\n\n";

$mainDb = config('database.connections.mysql.database');
echo "💾 Main Database: {$mainDb}\n\n";

// Get all tables
$tables = DB::select("SHOW TABLES");

echo "📋 All tables in main database:\n";
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    
    // Get row count
    $count = DB::table($tableName)->count();
    
    echo "  - {$tableName} ({$count} rows)\n";
}

echo "\n\n🔍 Checking project database tables...\n\n";

// Check project database
$projectDb = 'h_ng_thinh_sport';

try {
    DB::statement("USE `{$projectDb}`");
    
    $projectTables = DB::select("SHOW TABLES");
    
    echo "📋 All tables in project database ({$projectDb}):\n";
    foreach ($projectTables as $table) {
        $tableName = array_values((array)$table)[0];
        
        // Get row count
        $count = DB::select("SELECT COUNT(*) as count FROM {$tableName}")[0]->count;
        
        echo "  - {$tableName} ({$count} rows)\n";
    }
    
    // Switch back
    DB::statement("USE `{$mainDb}`");
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}