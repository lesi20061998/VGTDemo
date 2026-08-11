<?php

use App\Models\ThemeSetting;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
);

$watermark = ThemeSetting::where('key', 'watermark')->first();
$watermarkData = $watermark ? $watermark->value : null;

var_dump($watermarkData);

$wmImage = $watermarkData['image'] ?? '';
if (str_starts_with($wmImage, 'http')) {
    $parsedUrl = parse_url($wmImage, PHP_URL_PATH);
    $wmImage = preg_replace('/^\/?storage\//', '', $parsedUrl);
}
$wmImage = urldecode($wmImage);

var_dump($wmImage);
var_dump(Storage::disk('public')->exists($wmImage));
$wmPath = Storage::disk('public')->path($wmImage);
var_dump($wmPath);
var_dump(file_exists($wmPath));

$wmExt = strtolower(pathinfo($wmPath, PATHINFO_EXTENSION));
var_dump($wmExt);

if (in_array($wmExt, ['jpg', 'jpeg'])) {
    $wm = @imagecreatefromjpeg($wmPath);
} elseif ($wmExt === 'png') {
    $wm = @imagecreatefrompng($wmPath);
} elseif ($wmExt === 'webp') {
    $wm = @imagecreatefromwebp($wmPath);
}
var_dump(isset($wm) && $wm !== false);
