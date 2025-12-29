<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
})->name('home');

// TEMPORARY: Reset password route - DELETE AFTER USE!
Route::get('/reset-pwd-temp-{username}', function ($username) {
    $user = \App\Models\User::where('username', $username)->first();
    if (!$user) {
        return "User not found: {$username}";
    }
    $user->update(['password' => bcrypt('1')]);
    return "Password reset to '1' for user: {$username}";
});

// Protected Media with Watermark - Use /media/* instead of /storage/media/* to avoid symlink conflict
Route::get('/media/{path}', [App\Http\Controllers\WatermarkImageController::class, 'serve'])
    ->where('path', '.*')
    ->name('watermark.image');

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
    
    // ACF-like Field APIs
    Route::get('/relationship-field/search', [App\Http\Controllers\Api\RelationshipFieldController::class, 'search'])->name('relationship.search');
    Route::get('/relationship-field/items', [App\Http\Controllers\Api\RelationshipFieldController::class, 'getItems'])->name('relationship.items');
    Route::get('/taxonomy-field/list', [App\Http\Controllers\Api\TaxonomyFieldController::class, 'list'])->name('taxonomy.list');
});

// Include Frontend Routes
require __DIR__.'/frontend.php';

// Include CMS Routes (Content Management)
require __DIR__.'/backend.php';

// CMS Widget Routes (Non-project context)
Route::prefix('admin')->name('cms.')->middleware(['auth'])->group(function () {
    // Apply bypass middleware for widget routes in local environment
    $middlewares = [];
    if (config('app.env') === 'local') {
        $middlewares[] = 'widget.bypass';
    }
    
    // Widget Templates (ACF-style builder)
    Route::get('widget-templates', [\App\Http\Controllers\Admin\WidgetTemplateController::class, 'index'])->name('widget-templates.index');
    Route::get('widget-templates/create', fn() => view('cms.widget-templates.create'))->name('widget-templates.create');
    Route::get('widget-templates/export-all', [\App\Http\Controllers\Admin\WidgetTemplateController::class, 'exportAll'])->name('widget-templates.export-all');
    Route::get('widget-templates/{id}/export', [\App\Http\Controllers\Admin\WidgetTemplateController::class, 'export'])->name('widget-templates.export');
    Route::post('widget-templates/import', [\App\Http\Controllers\Admin\WidgetTemplateController::class, 'import'])->name('widget-templates.import');
    Route::get('widget-templates/{id}/edit', fn($id) => view('cms.widget-templates.edit', ['id' => $id]))->name('widget-templates.edit');
    Route::delete('widget-templates/{id}', [\App\Http\Controllers\Admin\WidgetTemplateController::class, 'destroy'])->name('widget-templates.destroy');
    Route::post('widget-templates/{type}/preview', [\App\Http\Controllers\Admin\WidgetTemplateController::class, 'preview'])->name('widget-templates.preview');
    
    // Widget Editor (Livewire)
    Route::get('widgets/create', \App\Livewire\Admin\WidgetEditor::class)->name('widgets.create');
    Route::get('widgets/{id}/edit-livewire', \App\Livewire\Admin\WidgetEditor::class)->name('widgets.edit-livewire');
    
    // Code-based Widget Editor - use WidgetTemplateBuilder with codeType parameter
    Route::get('code-widgets', \App\Livewire\Admin\CodeWidgetList::class)->name('code-widgets.index');
    Route::get('code-widgets/export-all', [\App\Http\Controllers\Admin\CodeWidgetController::class, 'exportAll'])->name('code-widgets.export-all');
    Route::get('code-widgets/{codeType}/edit', \App\Livewire\Admin\WidgetTemplateBuilder::class)->name('code-widgets.edit');
    Route::get('code-widgets/{type}/export', [\App\Http\Controllers\Admin\CodeWidgetController::class, 'export'])->name('code-widgets.export');
    
    Route::middleware($middlewares)->group(function () {
        Route::get('widgets', [\App\Http\Controllers\Admin\WidgetController::class, 'index'])->name('widgets.index');
        Route::post('widgets', [\App\Http\Controllers\Admin\WidgetController::class, 'store'])->name('widgets.store');
        Route::post('widgets/save-all', [\App\Http\Controllers\Admin\WidgetController::class, 'saveWidgets'])->name('widgets.save-all');
        Route::post('widgets/clear', [\App\Http\Controllers\Admin\WidgetController::class, 'clearArea'])->name('widgets.clear');
        Route::delete('widgets/{widget}', [\App\Http\Controllers\Admin\WidgetController::class, 'destroy'])->name('widgets.destroy');
        Route::post('widgets/clear-cache', [\App\Http\Controllers\Admin\WidgetController::class, 'clearCache'])->name('widgets.clear-cache');
        Route::post('widgets/preview', [\App\Http\Controllers\Admin\WidgetController::class, 'preview'])->name('widgets.preview');
        Route::get('widgets/discover', [\App\Http\Controllers\Admin\WidgetController::class, 'discover'])->name('widgets.discover');
        Route::match(['get', 'post'], 'widgets/fields', [\App\Http\Controllers\Admin\WidgetController::class, 'getFields'])->name('widgets.fields');
        Route::post('widgets/toggle', [\App\Http\Controllers\Admin\WidgetController::class, 'toggleWidget'])->name('widgets.toggle');
        Route::get('widgets/permissions', [\App\Http\Controllers\Admin\WidgetController::class, 'getPermissions'])->name('widgets.permissions');
        Route::get('widgets/export', [\App\Http\Controllers\Admin\WidgetController::class, 'export'])->name('widgets.export');
        Route::post('widgets/import', [\App\Http\Controllers\Admin\WidgetController::class, 'import'])->name('widgets.import');
        Route::post('widgets/backup', [\App\Http\Controllers\Admin\WidgetController::class, 'createBackup'])->name('widgets.backup');
        Route::get('widgets/backups', [\App\Http\Controllers\Admin\WidgetController::class, 'getBackups'])->name('widgets.backups');
    });
    
    // Development helper route (only in local environment)
    if (config('app.env') === 'local') {
        Route::get('widgets/debug-permission', function() {
            $permissionService = new \App\Services\WidgetPermissionService();
            $allWidgets = \App\Widgets\WidgetRegistry::all();
            
            return response()->json([
                'success' => true,
                'environment' => config('app.env'),
                'debug' => config('app.debug'),
                'user' => auth()->user() ? [
                    'id' => auth()->user()->id,
                    'email' => auth()->user()->email ?? 'N/A',
                    'role' => auth()->user()->role ?? 'N/A',
                    'level' => auth()->user()->level ?? 'N/A'
                ] : 'Not authenticated',
                'permissions' => [
                    'can_manage_widgets' => $permissionService->canManageWidgets(),
                    'can_toggle_widgets' => $permissionService->canToggleWidgets(),
                ],
                'widgets' => [
                    'total_available' => count($allWidgets),
                    'accessible_count' => count($permissionService->getAccessibleWidgets()),
                    'by_category' => $permissionService->getAccessibleWidgetsByCategory()
                ]
            ]);
        })->name('widgets.debug-permission');
        
        Route::get('widgets/test-access', function() {
            return response()->json([
                'success' => true,
                'message' => 'Widget access test successful',
                'environment' => config('app.env'),
                'debug' => config('app.debug'),
                'user' => auth()->user() ? [
                    'id' => auth()->user()->id,
                    'email' => auth()->user()->email ?? 'N/A',
                    'role' => auth()->user()->role ?? 'N/A',
                    'level' => auth()->user()->level ?? 'N/A'
                ] : 'Not authenticated',
                'session' => [
                    'widget_dev_access' => session('widget_dev_access'),
                    'session_id' => session()->getId()
                ]
            ]);
        })->name('widgets.test-access');
        
        Route::get('widgets/dev-access', function() {
            session(['widget_dev_access' => true]);
            return redirect()->route('cms.widgets.index')->with('success', 'Development access granted for this session');
        })->name('widgets.dev-access');
        
        Route::get('widgets/debug/permissions', [\App\Http\Controllers\Admin\WidgetDebugController::class, 'permissions'])->name('widgets.debug.permissions');
        Route::post('widgets/debug/grant-access', [\App\Http\Controllers\Admin\WidgetDebugController::class, 'grantAccess'])->name('widgets.debug.grant-access');
    }
    
    // Page Builder Routes
    Route::prefix('page-builder')->name('page-builder.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PageBuilderController::class, 'index'])->name('index');
        Route::get('pages/{page}/edit', [\App\Http\Controllers\Admin\PageBuilderController::class, 'edit'])->name('edit');
        Route::post('pages/{page}/sections', [\App\Http\Controllers\Admin\PageBuilderController::class, 'addSection'])->name('sections.store');
        Route::put('sections/{section}', [\App\Http\Controllers\Admin\PageBuilderController::class, 'updateSection'])->name('sections.update');
        Route::delete('sections/{section}', [\App\Http\Controllers\Admin\PageBuilderController::class, 'deleteSection'])->name('sections.destroy');
        Route::post('pages/{page}/sections/reorder', [\App\Http\Controllers\Admin\PageBuilderController::class, 'reorderSections'])->name('sections.reorder');
        Route::post('sections/{section}/move-up', [\App\Http\Controllers\Admin\PageBuilderController::class, 'moveSectionUp'])->name('sections.move-up');
        Route::post('sections/{section}/move-down', [\App\Http\Controllers\Admin\PageBuilderController::class, 'moveSectionDown'])->name('sections.move-down');
        Route::get('pages/{page}/preview', [\App\Http\Controllers\Admin\PageBuilderController::class, 'preview'])->name('preview');
        Route::post('sections/preview', [\App\Http\Controllers\Admin\PageBuilderController::class, 'previewSection'])->name('sections.preview');
        Route::post('sections/{section}/duplicate', [\App\Http\Controllers\Admin\PageBuilderController::class, 'duplicateSection'])->name('sections.duplicate');
    });
    
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


// Auth Routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Test routes for development
if (app()->environment(['local', 'testing'])) {
    require __DIR__.'/test.php';
}
