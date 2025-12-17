<?php

use App\Http\Controllers\Admin\AiController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\ProjectLoginController;
use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Route;

// ============================================
// FRONTEND ROUTES (Website khách hàng)
// URL: /{projectCode}/*
// ============================================
Route::prefix('{projectCode}')
    ->name('project.')
    ->middleware([
        \App\Http\Middleware\ProjectSubdomainMiddleware::class,
        \App\Http\Middleware\SetProjectDatabase::class,
    ])
    ->group(function () {
        // Default routes (no locale prefix - uses default language)
        Route::get('/', [HomeController::class, 'index'])->name('project.home');
        Route::get('/products', [\App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('project.products.index');
        Route::get('/product/{slug}', [\App\Http\Controllers\Frontend\ProductController::class, 'show'])->name('project.products.show');
        Route::get('/blog', [\App\Http\Controllers\Frontend\PostController::class, 'index'])->name('project.posts.index');
        Route::get('/blog/{slug}', [\App\Http\Controllers\Frontend\PostController::class, 'show'])->name('project.posts.show');
        Route::get('/contact', [\App\Http\Controllers\Frontend\PageController::class, 'contact'])->name('project.contact');
        Route::post('/contact', [\App\Http\Controllers\Frontend\PageController::class, 'contactSubmit'])->name('project.contact.submit');
        Route::get('/{slug}', [\App\Http\Controllers\Frontend\PageController::class, 'show'])->name('project.pages.show');

        Route::get('/cart', [\App\Http\Controllers\Frontend\CartController::class, 'index'])->name('project.cart');
        Route::post('/cart/add', [\App\Http\Controllers\Frontend\CartController::class, 'add'])->name('project.cart.add');
        Route::post('/cart/update/{slug}', [\App\Http\Controllers\Frontend\CartController::class, 'update'])->name('project.cart.update');
        Route::delete('/cart/remove/{slug}', [\App\Http\Controllers\Frontend\CartController::class, 'remove'])->name('project.cart.remove');
        Route::get('/checkout', [\App\Http\Controllers\Frontend\CartController::class, 'checkout'])->name('project.checkout');
        Route::post('/checkout/process', [\App\Http\Controllers\Frontend\CartController::class, 'processCheckout'])->name('project.checkout.process');
        Route::get('/order/success', fn () => view('frontend.cart.success'))->name('project.order.success');

        // Localized routes with language prefix
        Route::prefix('{locale}')
            ->where(['locale' => '[a-z]{2}'])
            ->middleware([\App\Http\Middleware\SetLocale::class])
            ->group(function () {
                Route::get('/', [HomeController::class, 'index'])->name('project.home.localized');
                Route::get('/products', [\App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('project.products.index.localized');
                Route::get('/product/{slug}', [\App\Http\Controllers\Frontend\ProductController::class, 'show'])->name('project.products.show.localized');
                Route::get('/blog', [\App\Http\Controllers\Frontend\PostController::class, 'index'])->name('project.posts.index.localized');
                Route::get('/blog/{slug}', [\App\Http\Controllers\Frontend\PostController::class, 'show'])->name('project.posts.show.localized');
                Route::get('/contact', [\App\Http\Controllers\Frontend\PageController::class, 'contact'])->name('project.contact.localized');
                Route::post('/contact', [\App\Http\Controllers\Frontend\PageController::class, 'contactSubmit'])->name('project.contact.submit.localized');
                Route::get('/{slug}', [\App\Http\Controllers\Frontend\PageController::class, 'show'])->name('project.pages.show.localized');
            });
    });

// Dynamic Pages (must be last to avoid conflicts)
Route::get('/{projectCode}/{slug}', [\App\Http\Controllers\Frontend\PageController::class, 'show'])
    ->where('slug', '^(?!admin|login|logout|cart|checkout|products|product|blog|contact).*$')
    ->middleware([\App\Http\Middleware\ProjectSubdomainMiddleware::class])
    ->name('project.pages.show');

// ============================================
// AUTH ROUTES (Đăng nhập CMS)
// URL: /{projectCode}/login
// ============================================
Route::prefix('{projectCode}')
    ->middleware([
        \App\Http\Middleware\ProjectSubdomainMiddleware::class,
        \App\Http\Middleware\SetProjectDatabase::class,
    ])
    ->group(function () {
        Route::get('login', [ProjectLoginController::class, 'showLoginForm'])->name('project.login');
        Route::post('login', [ProjectLoginController::class, 'login'])->name('project.login.post');
        Route::post('logout', [ProjectLoginController::class, 'logout'])->name('project.logout');
    });

// Dynamic CSS & JS per project
Route::get('/{projectCode}/css/custom.css', [\App\Http\Controllers\ThemeController::class, 'projectCustomCss'])->name('project.css.custom');
Route::get('/{projectCode}/js/custom.js', [\App\Http\Controllers\ThemeController::class, 'projectCustomJs'])->name('project.js.custom');

// ============================================
// CMS ADMIN ROUTES (Quản lý nội dung)
// URL: /{projectCode}/admin/*
// Middleware: ProjectSession + auth:project + CheckCmsRole
// ============================================
Route::prefix('{projectCode}/admin')
    ->name('project.admin.')
    ->middleware([
        \App\Http\Middleware\ProjectSubdomainMiddleware::class,
        \App\Http\Middleware\SetProjectDatabase::class,
        \App\Http\Middleware\CheckCmsRole::class,
    ])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'projectDashboard'])->name('dashboard');

        // Products Management
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);

        // Attributes Management
        Route::resource('attributes', \App\Http\Controllers\Admin\AttributeController::class);

        // Orders Management
        Route::get('orders/reports', [OrderController::class, 'reports'])->name('orders.reports');
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'edit', 'update']);
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        // User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

        // Media Management
        Route::get('media/list', [\App\Http\Controllers\Admin\MediaController::class, 'list'])->name('media.list');
        Route::post('media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('media.upload');
        Route::post('media/folder', [\App\Http\Controllers\Admin\MediaController::class, 'createFolder'])->name('media.folder.create');
        Route::delete('media/folder', [\App\Http\Controllers\Admin\MediaController::class, 'deleteFolder'])->name('media.folder.delete');
        Route::post('media/move', [\App\Http\Controllers\Admin\MediaController::class, 'move'])->name('media.move');
        Route::delete('media/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');

        // Settings
        Route::get('settings', [SettingsController::class, 'projectSettings'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'save'])->name('settings.save');

        // AI Management
        Route::post('ai/test', [AiController::class, 'test'])->name('ai.test');
        Route::post('ai/generate', [AiController::class, 'generate'])->name('ai.generate');
        Route::post('ai/list-models', [AiController::class, 'listModels'])->name('ai.list-models');

        // Page Builder
        Route::get('widgets', [\App\Http\Controllers\Admin\WidgetController::class, 'index'])->name('widgets.index');

        // Page Config
        Route::get('page-config', [\App\Http\Controllers\Admin\PageConfigController::class, 'index'])->name('page-config.index');
        Route::get('page-config/{page}', [\App\Http\Controllers\Admin\PageConfigController::class, 'edit'])->name('page-config.edit');
        Route::put('page-config/{page}', [\App\Http\Controllers\Admin\PageConfigController::class, 'update'])->name('page-config.update');

        // Theme Options
        Route::get('theme-options', [\App\Http\Controllers\Admin\ThemeOptionController::class, 'index'])->name('theme-options.index');
        Route::put('theme-options', [\App\Http\Controllers\Admin\ThemeOptionController::class, 'update'])->name('theme-options.update');
        Route::post('widgets', [\App\Http\Controllers\Admin\WidgetController::class, 'store'])->name('widgets.store');
        Route::post('widgets/clear', fn () => \App\Models\Widget::where('area', 'homepage-main')->delete());
        Route::delete('widgets/{widget}', [\App\Http\Controllers\Admin\WidgetController::class, 'destroy'])->name('widgets.destroy');

        // Widget Templates
        Route::get('widget-templates', function () {
            $widgets = \App\Widgets\WidgetRegistry::getByCategory();

            return view('cms.widget-templates.index', compact('widgets'));
        })->name('widget-templates.index');
        Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);
        Route::post('menus/{menu}/items', [\App\Http\Controllers\Admin\MenuController::class, 'storeItem'])->name('menus.items.store');
        Route::put('menus/items/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'updateItem'])->name('menus.items.update');
        Route::delete('menus/items/{item}', [\App\Http\Controllers\Admin\MenuController::class, 'destroyItem'])->name('menus.items.destroy');
        Route::post('menus/{menu}/update-tree', [\App\Http\Controllers\Admin\MenuController::class, 'updateTree'])->name('menus.update-tree');
        // Website Configuration
        Route::get('website-config', [\App\Http\Controllers\Admin\WebsiteConfigController::class, 'index'])->name('website-config.index');
        Route::post('website-config/save', [\App\Http\Controllers\Admin\WebsiteConfigController::class, 'save'])->name('website-config.save');
        Route::get('website-config/preview', [\App\Http\Controllers\Admin\WebsiteConfigController::class, 'preview'])->name('website-config.preview');
        Route::get('theme/dynamic-css', [\App\Http\Controllers\ThemeController::class, 'dynamicCss'])->name('theme.css');
        Route::post('widgets/clear-cache', function () {
            clear_widget_cache();

            return response()->json(['success' => true]);
        })->name('widgets.clear-cache');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::post('scan-translations', [SettingsController::class, 'scanTranslations'])->name('scan-translations');
            Route::get('contact', fn () => view('cms.settings.contact'))->name('contact');
            Route::get('notifications', fn () => view('cms.settings.notifications'))->name('notifications');
            Route::get('fonts', fn () => view('cms.settings.fonts'))->name('fonts');
            Route::get('logs', fn () => view('cms.settings.logs'))->name('logs');
            Route::get('analytics', fn () => view('cms.settings.analytics'))->name('analytics');
            Route::get('watermark', fn () => view('cms.settings.watermark'))->name('watermark');
            Route::get('toc', fn () => view('cms.settings.toc'))->name('toc');
            Route::get('social', fn () => view('cms.settings.social'))->name('social');
            Route::get('payment', fn () => view('cms.settings.payment'))->name('payment');
            Route::get('shipping', fn () => view('cms.settings.shipping'))->name('shipping');
            Route::get('ai', fn () => view('cms.settings.ai'))->name('ai');
            Route::get('reviews', fn () => view('cms.settings.reviews'))->name('reviews');
            Route::get('languages', fn () => view('cms.settings.languages'))->name('languages');
            Route::get('frontend-features', fn () => view('cms.settings.frontend-features'))->name('frontend-features');
            Route::get('forms', fn () => view('cms.settings.forms'))->name('forms');
            Route::get('contact-buttons', fn () => view('cms.settings.contact-buttons'))->name('contact-buttons');
            Route::get('redirects', fn () => view('cms.settings.redirects'))->name('redirects');
            Route::get('seo', fn () => view('cms.settings.seo'))->name('seo');
            Route::get('popups', fn () => view('cms.settings.popups'))->name('popups');
            Route::get('permissions', fn () => view('cms.settings.permissions'))->name('permissions');
            Route::get('fake-notifications', fn () => view('cms.settings.fake-notifications'))->name('fake-notifications');
        });

        // Fonts Management
        Route::post('fonts/store', [\App\Http\Controllers\Admin\FontController::class, 'store'])->name('fonts.store');
        Route::post('fonts/toggle', [\App\Http\Controllers\Admin\FontController::class, 'toggle'])->name('fonts.toggle');
        Route::post('fonts/default', [\App\Http\Controllers\Admin\FontController::class, 'setDefault'])->name('fonts.default');
        Route::delete('fonts/destroy', [\App\Http\Controllers\Admin\FontController::class, 'destroy'])->name('fonts.destroy');

        // Reviews Fake Data
        Route::get('reviews/fake', fn () => view('cms.reviews.fake'))->name('reviews.fake');

    });
