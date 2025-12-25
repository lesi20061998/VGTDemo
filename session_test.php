<?php
require_once "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

// Test session functionality
if (!session()->isStarted()) {
    session()->start();
}

$testData = [
    "session_id" => session()->getId(),
    "csrf_token" => csrf_token(),
    "session_data" => session()->all(),
    "authenticated" => auth()->check(),
    "user" => auth()->user() ? [
        "id" => auth()->user()->id,
        "username" => auth()->user()->username,
        "role" => auth()->user()->role
    ] : null,
    "config" => [
        "session_driver" => config("session.driver"),
        "session_domain" => config("session.domain"),
        "session_lifetime" => config("session.lifetime"),
        "app_url" => config("app.url")
    ]
];

header("Content-Type: application/json");
echo json_encode($testData, JSON_PRETTY_PRINT);
?>