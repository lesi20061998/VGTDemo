<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "🔍 Testing controller saveWidgets method in detail...\n\n";
    
    // Clear existing widgets first
    \Illuminate\Support\Facades\DB::table('widgets')->where('area', 'test-area')->delete();
    
    $testData = [
        'widgets' => [
            [
                'name' => 'Controller Test Widget',
                'type' => 'hero',
                'area' => 'test-area',
                'settings' => [
                    'title' => 'Controller Test',
                    'subtitle' => 'Test subtitle',
                    'button_text' => 'Click me',
                    'button_link' => '/test-page'
                ],
                'sort_order' => 0,
                'is_active' => true,
                'variant' => 'default'
            ]
        ]
    ];
    
    echo "Test data:\n";
    echo json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";
    
    // Create a proper request object
    $request = \Illuminate\Http\Request::create('/test', 'POST', $testData);
    $request->headers->set('Content-Type', 'application/json');
    
    echo "Request data:\n";
    echo "Input widgets: " . json_encode($request->input('widgets'), JSON_PRETTY_PRINT) . "\n\n";
    
    $controller = new \App\Http\Controllers\Admin\WidgetController();
    
    // Let's manually test the validation logic
    echo "🧪 Testing widget validation...\n";
    $widgetData = $testData['widgets'][0];
    
    try {
        // Test the validateWidgetData method using reflection
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateWidgetData');
        $method->setAccessible(true);
        
        $validated = $method->invoke($controller, $widgetData);
        echo "✅ Widget validation passed:\n";
        echo json_encode($validated, JSON_PRETTY_PRINT) . "\n\n";
        
    } catch (Exception $e) {
        echo "❌ Widget validation failed: " . $e->getMessage() . "\n\n";
    }
    
    // Now test the full controller method
    echo "🧪 Testing full controller method...\n";
    $response = $controller->saveWidgets($request);
    
    echo "Controller response status: " . $response->getStatusCode() . "\n";
    echo "Controller response: " . $response->getContent() . "\n\n";
    
    // Check if widget was created
    $createdCount = \Illuminate\Support\Facades\DB::table('widgets')->where('area', 'test-area')->count();
    echo "Widgets created in test-area: $createdCount\n";
    
    if ($createdCount > 0) {
        $widgets = \Illuminate\Support\Facades\DB::table('widgets')->where('area', 'test-area')->get();
        echo "Created widgets:\n";
        foreach ($widgets as $widget) {
            echo "- ID: {$widget->id}, Name: {$widget->name}, Type: {$widget->type}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}