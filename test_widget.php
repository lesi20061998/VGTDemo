<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$widget = new \App\Widgets\Groups\SliderWidget([
    'slides' => [
        [
            'title' => 'Test Slider', 
            'image' => 'test.jpg'
        ]
    ]
], 'slider-1');

echo $widget->render();
