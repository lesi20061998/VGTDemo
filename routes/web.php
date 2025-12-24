<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
})->name('home');

// API Routes - MUST BE FIRST
Route::prefix('api')->name('api.')->middleware('api')->group(function () {
    Route::post('/bridge', [App\Http\Controllers\Api\ProjectBridgeController::class, 'handle'])->name('bridge');
    Route::post('/newsletter/subscribe', [App\Http\Controllers\Api\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::get('/reviews/config', [App\Http\Controllers\Api\ReviewController::class, 'getConfig'])->name('reviews.config');
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

// CMS Widget Routes (Non-project context)
Route::prefix('admin')->name('cms.')->middleware(['auth'])->group(function () {
    Route::get('widgets', [\App\Http\Controllers\Admin\WidgetController::class, 'index'])->name('widgets.index');
    Route::post('widgets', [\App\Http\Controllers\Admin\WidgetController::class, 'store'])->name('widgets.store');
    Route::post('widgets/save-all', [\App\Http\Controllers\Admin\WidgetController::class, 'saveWidgets'])->name('widgets.save-all');
    Route::post('widgets/clear', [\App\Http\Controllers\Admin\WidgetController::class, 'clearArea'])->name('widgets.clear');
    Route::delete('widgets/{widget}', [\App\Http\Controllers\Admin\WidgetController::class, 'destroy'])->name('widgets.destroy');
    Route::post('widgets/clear-cache', [\App\Http\Controllers\Admin\WidgetController::class, 'clearCache'])->name('widgets.clear-cache');
    
    // Theme Options
    Route::get('theme-options', [\App\Http\Controllers\Admin\ThemeOptionController::class, 'index'])->name('theme-options.index');
    Route::put('theme-options', [\App\Http\Controllers\Admin\ThemeOptionController::class, 'update'])->name('theme-options.update');
    
    // Media Management
    Route::get('media/list', [\App\Http\Controllers\Admin\MediaController::class, 'list'])->name('media.list');
    Route::post('media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('media.upload');
    Route::post('media/folder', [\App\Http\Controllers\Admin\MediaController::class, 'createFolder'])->name('media.folder.create');
    Route::delete('media/folder', [\App\Http\Controllers\Admin\MediaController::class, 'deleteFolder'])->name('media.folder.delete');
    Route::post('media/move', [\App\Http\Controllers\Admin\MediaController::class, 'move'])->name('media.move');
    Route::delete('media/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');
    
    // Debug endpoint for media authentication
    Route::get('debug/auth', function() {
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => auth()->user() ? [
                'id' => auth()->user()->id,
                'username' => auth()->user()->username ?? 'N/A',
                'role' => auth()->user()->role ?? 'N/A',
                'level' => auth()->user()->level ?? 'N/A'
            ] : null,
            'session_id' => session()->getId(),
            'csrf_token' => csrf_token(),
            'session_data' => session()->all()
        ]);
    })->name('debug.auth');
});

// Include SuperAdmin Routes (Project Management)
require __DIR__.'/superadmin.php';

// Include Employee Routes
require __DIR__.'/employee.php';

// Project routes are loaded separately in bootstrap/app.php with isolated session

// Robots.txt
Route::get('/robots.txt', function () {
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
Route::get('/lang/{locale}', function ($locale) {
    session(['locale' => $locale]);

    return back();
})->name('lang.switch');

// Test Language
Route::get('/test-lang', function () {
    return view('test-lang');
})->name('test.lang');

// Test Media Manager
Route::get('/test-media', function () {
    return view('test-media');
})->name('test.media')->middleware('auth');

// Auth Routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Test routes for development
if (app()->environment(['local', 'testing'])) {
    require __DIR__.'/test.php';
}
