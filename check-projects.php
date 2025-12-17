<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;

echo "🔍 Checking existing projects...\n\n";

$projects = Project::all();

foreach ($projects as $project) {
    echo "Project: {$project->name}\n";
    echo "Code: {$project->code}\n";
    echo "ID: {$project->id}\n";
    echo "Database name pattern: project_{$project->code}\n";
    echo "---\n";
}

echo "\nTotal projects: ".$projects->count()."\n";
