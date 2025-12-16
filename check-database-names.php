<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;

echo "🔍 Checking database names for all projects...\n\n";

$projects = Project::all();

foreach ($projects as $project) {
    $dbName = strtolower($project->name);
    $dbName = preg_replace('/[^a-z0-9_]/', '_', $dbName);
    $dbName = preg_replace('/_+/', '_', $dbName);
    $dbName = trim($dbName, '_');
    
    echo "📁 Project: {$project->name}\n";
    echo "   Code: {$project->code}\n";
    echo "   Database: {$dbName}\n\n";
}

echo "✅ Done!\n";