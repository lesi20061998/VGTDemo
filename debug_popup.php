<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>Debug Popup Settings</h2>";

// Simulate project context (like frontend)
$project = \App\Models\Project::where('code', 'SiVGT')->first();
if ($project) {
    request()->attributes->set('project', $project);
    echo "<p>Project found: {$project->name} (ID: {$project->id})</p>";
    
    // Clear cache to reload with project context
    \App\Services\SettingsService::getInstance()->clearCache();
}

// Check popup setting in database
$popup = \App\Models\Setting::where('key', 'popup')->first();
echo "<h3>1. Popup setting in DB:</h3>";
echo "<pre>";
var_dump($popup ? $popup->toArray() : null);
echo "</pre>";

// Check via setting() helper
echo "<h3>2. Via setting() helper (with project context):</h3>";
echo "<pre>";
var_dump(setting('popup', []));
echo "</pre>";

// Check all settings for this project
echo "<h3>3. All settings for project {$project->id}:</h3>";
$allSettings = \App\Models\Setting::where('project_id', $project->id)->get();
echo "<pre>";
foreach ($allSettings as $s) {
    echo "Key: {$s->key}\n";
    echo "Payload: " . json_encode($s->payload, JSON_PRETTY_PRINT) . "\n\n";
}
echo "</pre>";
