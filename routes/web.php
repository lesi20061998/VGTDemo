<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
})->name('home');

// API Routes - MUST BE FIRST
Route::prefix('api')->name('api.')->middleware('api')->group(function () {
    Route::post('/bridge', [App\Http\Controllers\Api\ProjectBridgeController::class, 'handle'])->name('bridge');
    Route::post('/newsletter/subscribe', [App\Http\Controllers\Api\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::post('/reviews', [App\Http\Controllers\Api\ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/form-submit', [App\Http\Controllers\FormSubmissionController::class, 'submit'])->name('form.submit');
    
    // Location API
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('provinces', [App\Http\Controllers\Api\LocationController::class, 'provinces'])->name('provinces');
        Route::get('districts/{provinceCode}', [App\Http\Controllers\Api\LocationController::class, 'districts'])->name('districts');
        Route::get('wards/{districtCode}', [App\Http\Controllers\Api\LocationController::class, 'wards'])->name('wards');
    });
});

// Include Frontend Routes
require __DIR__.'/frontend.php';

// Include CMS Routes (Content Management)
require __DIR__.'/backend.php';

// Include SuperAdmin Routes (Project Management)
require __DIR__.'/superadmin.php';

// Include Employee Routes
require __DIR__.'/employee.php';

// Include Project-specific Admin Routes
require __DIR__.'/project.php';

// Robots.txt
Route::get('/robots.txt', function() {
    $content = setting('robots_txt', "User-agent: *\nAllow: /");
    return response($content)->header('Content-Type', 'text/plain');
});

// Sitemap Routes - Professional like Rank Math
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-pages.xml', [App\Http\Controllers\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-products.xml', [App\Http\Controllers\SitemapController::class, 'products'])->name('sitemap.products');
Route::get('/sitemap-categories.xml', [App\Http\Controllers\SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-brands.xml', [App\Http\Controllers\SitemapController::class, 'brands'])->name('sitemap.brands');

// Language Switcher
Route::get('/lang/{locale}', function($locale) {
    session(['locale' => $locale]);
    return back();
})->name('lang.switch');

// Test Language
Route::get('/test-lang', function() {
    return view('test-lang');
})->name('test.lang');

// Test Media Manager
Route::get('/test-media', function() {
    return view('test-media');
})->name('test.media')->middleware('auth');

// Auth Routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');


