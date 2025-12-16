<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Project;

$projectId = 46;
$project = Project::find($projectId);

echo "🔍 Debug login for project: {$project->name} (Code: {$project->code})\n\n";

// Get database name
$dbName = strtolower($project->name);
$dbName = preg_replace('/[^a-z0-9_]/', '_', $dbName);
$dbName = preg_replace('/_+/', '_', $dbName);
$dbName = trim($dbName, '_');

echo "💾 Database: {$dbName}\n";
echo "🔑 Project Password: {$project->project_admin_password}\n\n";

try {
    // Switch to project database
    DB::statement("USE `{$dbName}`");
    
    // Get user
    $user = DB::select("SELECT * FROM users WHERE username = ?", [$project->code]);
    
    if (empty($user)) {
        echo "❌ User not found!\n";
        exit;
    }
    
    $user = $user[0];
    echo "✅ User found:\n";
    echo "  - ID: {$user->id}\n";
    echo "  - Username: {$user->username}\n";
    echo "  - Email: {$user->email}\n";
    echo "  - Role: {$user->role}\n";
    echo "  - Level: {$user->level}\n\n";
    
    // Test password
    echo "🔐 Testing passwords:\n";
    
    $passwords = [
        $project->project_admin_password,
        'fAnWP$GFXe9&',
        'fAnWP$GFXe9&amp;'
    ];
    
    foreach ($passwords as $password) {
        if (password_verify($password, $user->password)) {
            echo "  ✅ Password '{$password}' matches!\n";
        } else {
            echo "  ❌ Password '{$password}' doesn't match\n";
        }
    }
    
    // Check routes
    echo "\n🛣️  Route check:\n";
    echo "  - Login URL: http://localhost:8000/{$project->code}/login\n";
    echo "  - Admin URL: http://localhost:8000/{$project->code}/admin\n";
    
    // Check if route exists
    $routes = \Route::getRoutes();
    $loginRouteExists = false;
    
    foreach ($routes as $route) {
        if (str_contains($route->uri(), $project->code . '/login')) {
            $loginRouteExists = true;
            break;
        }
    }
    
    if ($loginRouteExists) {
        echo "  ✅ Login route exists\n";
    } else {
        echo "  ❌ Login route not found\n";
        echo "  🔧 Available routes with '{$project->code}':\n";
        
        foreach ($routes as $route) {
            if (str_contains($route->uri(), $project->code)) {
                echo "    - {$route->methods()[0]} {$route->uri()}\n";
            }
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}