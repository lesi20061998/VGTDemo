<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Check if widgets table exists and get its structure
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('widgets');
    echo "✅ Widgets table columns:\n";
    foreach ($columns as $column) {
        echo "  - $column\n";
    }
    
    // Check if variant column exists
    if (in_array('variant', $columns)) {
        echo "✅ 'variant' column exists\n";
    } else {
        echo "❌ 'variant' column is missing\n";
    }
    
    // Check if metadata column exists
    if (in_array('metadata', $columns)) {
        echo "✅ 'metadata' column exists\n";
    } else {
        echo "❌ 'metadata' column is missing\n";
    }
    
    // Try to get table info using raw query
    $tableInfo = \Illuminate\Support\Facades\DB::select("DESCRIBE widgets");
    echo "\n📋 Full table structure:\n";
    foreach ($tableInfo as $column) {
        echo "  {$column->Field} - {$column->Type} - {$column->Null} - {$column->Default}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}