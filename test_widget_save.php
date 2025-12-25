<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Test creating a widget
    $widget = new \App\Models\Widget();
    $widget->name = 'Test Widget';
    $widget->type = 'hero';
    $widget->area = 'homepage-main';
    $widget->settings = ['title' => 'Test Title'];
    $widget->sort_order = 0;
    $widget->is_active = true;
    $widget->variant = 'default';
    
    $widget->save();
    
    echo "✅ Widget created successfully with ID: " . $widget->id . "\n";
    echo "Widget data: " . json_encode($widget->toArray(), JSON_PRETTY_PRINT) . "\n";
    
    // Test the saveWidgets method
    $controller = new \App\Http\Controllers\Admin\WidgetController();
    
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'widgets' => [
            [
                'name' => 'Test Widget 2',
                'type' => 'hero',
                'area' => 'homepage-main',
                'settings' => ['title' => 'Test Title 2'],
                'sort_order' => 1,
                'is_active' => true,
                'variant' => 'default'
            ]
        ]
    ]);
    
    $response = $controller->saveWidgets($request);
    $responseData = json_decode($response->getContent(), true);
    
    echo "✅ SaveWidgets response: " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}