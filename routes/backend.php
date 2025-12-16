<?php
// MODIFIED: 2025-01-21

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SettingsController as Settings;
use App\Http\Controllers\Admin\FontController;
use App\Http\Controllers\FormSubmissionController;

// Admin Dashboard Route
Route::prefix('admin')->name('cms.')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Website Configuration
    Route::get('website-config', [\App\Http\Controllers\Admin\WebsiteConfigController::class, 'index'])->name('website-config.index');
    Route::post('website-config/save', [\App\Http\Controllers\Admin\WebsiteConfigController::class, 'save'])->name('website-config.save');
    Route::get('website-config/preview', [\App\Http\Controllers\Admin\WebsiteConfigController::class, 'preview'])->name('website-config.preview');
    
    // Media Management
    Route::get('media/list', [App\Http\Controllers\Admin\MediaController::class, 'list'])->name('media.list');
    Route::post('media/upload', [App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('media.upload');
    Route::post('media/folder', [App\Http\Controllers\Admin\MediaController::class, 'createFolder'])->name('media.folder.create');
    Route::delete('media/folder', [App\Http\Controllers\Admin\MediaController::class, 'deleteFolder'])->name('media.folder.delete');
    Route::post('media/move', [App\Http\Controllers\Admin\MediaController::class, 'move'])->name('media.move');
    Route::delete('media/{id}', [App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');
});

// CMS Content Management - For CMS users
Route::prefix('cms/admin')->name('cms.')->middleware(['auth', 'cms'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');
    
    // Products Management
    Route::resource('products', ProductController::class);
    Route::get('products/{product}/quick-edit', [ProductController::class, 'quickEdit'])->name('products.quick-edit');
    Route::post('products/{product}/quick-update', [ProductController::class, 'quickUpdate'])->name('products.quick-update');
    Route::post('products/bulk-edit', [ProductController::class, 'bulkEdit'])->name('products.bulk-edit');
    Route::post('products/bulk-update', [ProductController::class, 'bulkUpdate'])->name('products.bulk-update');
    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    
    // Attributes Management
    Route::prefix('attributes')->name('attributes.')->group(function () {
        // Attribute Groups
        Route::get('/groups', [AttributeController::class, 'indexGroups'])->name('groups.index');
        Route::get('/groups/create', [AttributeController::class, 'createGroup'])->name('groups.create');
        Route::post('/groups', [AttributeController::class, 'storeGroup'])->name('groups.store');
        Route::get('/groups/{group}/edit', [AttributeController::class, 'editGroup'])->name('groups.edit');
        Route::put('/groups/{group}', [AttributeController::class, 'updateGroup'])->name('groups.update');
        Route::delete('/groups/{group}', [AttributeController::class, 'destroyGroup'])->name('groups.destroy');
        
        // Product Attributes
        Route::get('/', [AttributeController::class, 'index'])->name('index');
        Route::get('/create', [AttributeController::class, 'create'])->name('create');
        Route::post('/', [AttributeController::class, 'store'])->name('store');
        Route::get('/{attribute}/edit', [AttributeController::class, 'edit'])->name('edit');
        Route::put('/{attribute}', [AttributeController::class, 'update'])->name('update');
        Route::delete('/{attribute}', [AttributeController::class, 'destroy'])->name('destroy');
        
        // Attribute Values
        Route::get('/{attribute}/values', [AttributeController::class, 'indexValues'])->name('values.index');
        Route::get('/{attribute}/values/create', [AttributeController::class, 'createValue'])->name('values.create');
        Route::post('/{attribute}/values', [AttributeController::class, 'storeValue'])->name('values.store');
        Route::get('/{attribute}/values/{value}/edit', [AttributeController::class, 'editValue'])->name('values.edit');
        Route::put('/{attribute}/values/{value}', [AttributeController::class, 'updateValue'])->name('values.update');
        Route::delete('/{attribute}/values/{value}', [AttributeController::class, 'destroyValue'])->name('values.destroy');
    });
    
    // Orders Management
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    Route::post('orders/{order}/notes', [OrderController::class, 'addNote'])->name('orders.add-note');
    Route::get('reports/orders', [OrderController::class, 'reports'])->name('orders.reports');
    
    // Content Management
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
    Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class);
    Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
    
    // Page Config
    Route::get('page-config', [\App\Http\Controllers\Admin\PageConfigController::class, 'index'])->name('page-config.index');
    Route::get('page-config/{page}/edit', [\App\Http\Controllers\Admin\PageConfigController::class, 'edit'])->name('page-config.edit');
    Route::put('page-config/{page}', [\App\Http\Controllers\Admin\PageConfigController::class, 'update'])->name('page-config.update');
    
    // Theme Options
    Route::get('theme-options', [\App\Http\Controllers\Admin\ThemeOptionController::class, 'index'])->name('theme-options.index');
    Route::put('theme-options', [\App\Http\Controllers\Admin\ThemeOptionController::class, 'update'])->name('theme-options.update');
    
    // Media Management
    Route::get('media/list', [App\Http\Controllers\Admin\MediaController::class, 'list'])->name('media.list');
    Route::post('media/upload', [App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('media.upload');
    Route::post('media/folder', [App\Http\Controllers\Admin\MediaController::class, 'createFolder'])->name('media.folder.create');
    Route::delete('media/folder', [App\Http\Controllers\Admin\MediaController::class, 'deleteFolder'])->name('media.folder.delete');
    Route::post('media/move', [App\Http\Controllers\Admin\MediaController::class, 'move'])->name('media.move');
    Route::delete('media/{id}', [App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');
    
    // Marketing
    Route::resource('subscribers', \App\Http\Controllers\Admin\SubscriberController::class);
    Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class);
    Route::resource('feedbacks', \App\Http\Controllers\Admin\FeedbackController::class);
    
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    
    // System Management
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/save', [SettingsController::class, 'save'])->name('settings.save');
    
    // Fonts Management
    Route::get('fonts', [FontController::class, 'index'])->name('fonts.index');
    Route::get('fonts/google', [FontController::class, 'getGoogleFonts'])->name('fonts.google');
    Route::post('fonts', [FontController::class, 'store'])->name('fonts.store');
    Route::post('fonts/toggle', [FontController::class, 'toggle'])->name('fonts.toggle');
    Route::post('fonts/default', [FontController::class, 'setDefault'])->name('fonts.default');
    Route::delete('fonts', [FontController::class, 'destroy'])->name('fonts.destroy');
    
    // AI Management
    Route::post('ai/test', [App\Http\Controllers\Admin\AiController::class, 'test'])->name('ai.test');
    Route::post('ai/generate', [App\Http\Controllers\Admin\AiController::class, 'generate'])->name('ai.generate');
    
    // Settings Modules
    Route::get('test-translation', fn() => view('test-translation'))->name('test-translation');
    
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('debug', fn() => view('cms.settings.debug'))->name('debug');
        Route::post('scan-translations', [SettingsController::class, 'scanTranslations'])->name('scan-translations');
        Route::get('contact', fn() => view('cms.settings.contact'))->name('contact');
        Route::get('notifications', fn() => view('cms.settings.notifications'))->name('notifications');

        Route::get('logs', fn() => view('cms.settings.logs'))->name('logs');
        Route::get('analytics', fn() => view('cms.settings.analytics'))->name('analytics');
        Route::get('watermark', fn() => view('cms.settings.watermark'))->name('watermark');
        Route::get('toc', fn() => view('cms.settings.toc'))->name('toc');
        Route::get('social', fn() => view('cms.settings.social'))->name('social');
        Route::get('payment', fn() => view('cms.settings.payment'))->name('payment');
        Route::get('shipping', fn() => view('cms.settings.shipping'))->name('shipping');
        Route::get('ai', fn() => view('cms.settings.ai'))->name('ai');
        Route::get('reviews', fn() => view('cms.settings.reviews'))->name('reviews');
        Route::get('languages', fn() => view('cms.settings.languages'))->name('languages');
        Route::get('forms', fn() => view('cms.settings.forms'))->name('forms');
        Route::get('contact-buttons', fn() => view('cms.settings.contact-buttons'))->name('contact-buttons');
        Route::get('redirects', fn() => view('cms.settings.redirects'))->name('redirects');
        Route::get('seo', fn() => view('cms.settings.seo'))->name('seo');
        Route::get('popups', fn() => view('cms.settings.popups'))->name('popups');
        Route::get('permissions', fn() => view('cms.settings.permissions'))->name('permissions');
        Route::get('fake-notifications', fn() => view('cms.settings.fake-notifications'))->name('fake-notifications');
    });
    
    // Reviews Management
    Route::get('reviews/fake', fn() => view('cms.reviews.fake'))->name('reviews.fake');
    
    // Form Submissions
    Route::get('form-submissions', [App\Http\Controllers\FormSubmissionController::class, 'index'])->name('form-submissions.index');
    Route::post('form-submissions/{id}/status', [App\Http\Controllers\FormSubmissionController::class, 'updateStatus'])->name('form-submissions.update-status');
    Route::delete('form-submissions/{id}', [App\Http\Controllers\FormSubmissionController::class, 'destroy'])->name('form-submissions.destroy');
    // Page Builder
    Route::get('widgets', [\App\Http\Controllers\Admin\WidgetController::class, 'index'])->name('widgets.index');
    Route::post('widgets', [\App\Http\Controllers\Admin\WidgetController::class, 'store'])->name('widgets.store');
    Route::post('widgets/clear', fn() => \App\Models\Widget::where('area', 'homepage-main')->delete());
    Route::delete('widgets/{widget}', [\App\Http\Controllers\Admin\WidgetController::class, 'destroy'])->name('widgets.destroy');

    Route::get('theme/dynamic-css', [\App\Http\Controllers\ThemeController::class, 'dynamicCss'])->name('theme.css');
    Route::post('widgets/clear-cache', function() {
        clear_widget_cache();
        return response()->json(['success' => true]);
    })->name('widgets.clear-cache');
    
    // System Logs & Backups
    Route::get('logs', [\App\Http\Controllers\Admin\SystemController::class, 'logs'])->name('logs.index');
    Route::get('backups', [\App\Http\Controllers\Admin\SystemController::class, 'backups'])->name('backups.index');
    Route::post('backups/create', [\App\Http\Controllers\Admin\SystemController::class, 'createBackup'])->name('backups.create');
});
