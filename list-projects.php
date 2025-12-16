<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;

echo "📋 List of all projects:\n\n";

$projects = Project::all();

foreach ($projects as $project) {
    echo "ID: {$project->id} | Code: {$project->code} | Name: {$project->name} | Status: {$project->status}\n";
    if ($project->project_admin_password) {
        echo "  🔑 Has password: {$project->project_admin_password}\n";
    }
    echo "\n";
}

echo "Total: " . $projects->count() . " projects\n";