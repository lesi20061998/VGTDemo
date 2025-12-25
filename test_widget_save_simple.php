<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🔍 Testing widget save functionality...\n\n";
    
    // Check database connection
    $connection = \Illuminate\Support\Facades\DB::connection();
    echo "✅ Database connected: " . $connection->getDatabaseName() . "\n";
    
    // Check if we can query the widgets table
    $count = \Illuminate\Support\Facades\DB::table('widgets')->count();
    echo "✅ Current widgets count: $count\n";
    
    // Test creating a widget using Eloquent
    echo "\n🧪 Testing Eloquent widget creation...\n";
    
    $widget = new \App\Models\Widget();
    $widget->name = 'Test Widget ' . time();
    $widget->type = 'hero';
    $widget->area = 'homepage-main';
    $widget->settings = ['title' => 'Test Title'];
    $widget->sort_order = 0;
    $widget->is_active = true;
    // Don't set variant, let it use the default
    
    echo "Widget data before save:\n";
    echo json_encode($widget->toArray(), JSON_PRETTY_PRINT) . "\n";
    
    $widget->save();
    
    echo "✅ Widget created successfully with ID: " . $widget->id . "\n";
    
    // Test the controller method directly
    echo "\n🧪 Testing controller saveWidgets method...\n";
    
    // Clear existing widgets first
    \Illuminate\Support\Facades\DB::table('widgets')->where('area', 'test-area')->delete();
    
    $testData = [
        'widgets' => [
            [
                'name' => 'Controller Test Widget',
                'type' => 'hero',
                'area' => 'test-area',
                'settings' => ['title' => 'Controller Test'],
                'sort_order' => 0,
                'is_active' => true,
                'variant' => 'default'
            ]
        ]
    ];
    
    // Create a proper request object
    $request = \Illuminate\Http\Request::create('/test', 'POST', $testData);
    $request->headers->set('Content-Type', 'application/json');
    
    $controller = new \App\Http\Controllers\Admin\WidgetController();
    $response = $controller->saveWidgets($request);
    
    echo "Controller response status: " . $response->getStatusCode() . "\n";
    echo "Controller response: " . $response->getContent() . "\n";
    
    // Check if widget was created
    $createdCount = \Illuminate\Support\Facades\DB::table('widgets')->where('area', 'test-area')->count();
    echo "Widgets created in test-area: $createdCount\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}