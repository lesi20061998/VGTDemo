<?php

use App\Http\Controllers\Admin\AiController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CodeWidgetController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FontController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PropertyCategoryController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ThemeOptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteConfigController;
use App\Http\Controllers\Admin\WidgetController;
use App\Http\Controllers\Admin\WidgetTemplateController;
use App\Http\Controllers\Api\RelationshipFieldController;
use App\Http\Controllers\Api\TaxonomyFieldController;
use App\Http\Controllers\Auth\ProjectLoginController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\ThemeController;
use App\Http\Middleware\CheckCmsRole;
use App\Http\Middleware\ProjectSubdomainMiddleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SetProjectDatabase;
use App\Livewire\Admin\CodeWidgetList;
use App\Livewire\Admin\WidgetEditor;
use App\Livewire\Admin\WidgetTemplateBuilder;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

// ============================================
// FRONTEND ROUTES (Website khách hàng)
// URL: /{projectCode}/*
// ============================================
Route::prefix('{projectCode}')
    ->name('project.')
    ->middleware([
        ProjectSubdomainMiddleware::class,
        SetProjectDatabase::class,
    ])
    ->group(function () {
        // Demo Layout Routes
        Route::get('/demo-layout/{type}', function ($projectCode, $type) {
            if (! in_array($type, ['page', 'post', 'product'])) {
                abort(404);
            }

            return view("frontend.demo.{$type}");
        })->name('project.demo-layout');

        // Default routes (no locale prefix - uses default language)
        Route::get('/', [HomeController::class, 'index'])->name('project.home');
        Route::get('/san-pham', [App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('project.products.index');
        Route::get('/san-pham/{slug}', [App\Http\Controllers\Frontend\ProductController::class, 'show'])->name('project.products.show');
        Route::get('/danh-muc/{slug}', [App\Http\Controllers\Frontend\ProductController::class, 'category'])->name('project.products.category');
        Route::get('/products', [App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('project.products.index.en');
        Route::get('/product/{slug}', [App\Http\Controllers\Frontend\ProductController::class, 'show'])->name('project.products.show.en');
        Route::get('/category/{slug}', [App\Http\Controllers\Frontend\ProductController::class, 'category'])->name('project.products.category.en');
        Route::get('/blog', [PostController::class, 'index'])->name('project.posts.index');
        Route::get('/blog/{slug}', [PostController::class, 'show'])->name('project.posts.show');
        Route::get('/contact', [PageController::class, 'contact'])->name('project.contact');
        Route::post('/contact', [PageController::class, 'contactSubmit'])->name('project.contact.submit');

        // Dynamic page route - exclude admin, login, etc.
        Route::get('/{slug}', [PageController::class, 'show'])
            ->where('slug', '^(?!admin|login|logout|cart|checkout|api).*$')
            ->name('project.pages.show');

        Route::get('/cart', [CartController::class, 'index'])->name('project.cart');
        Route::post('/cart/add', [CartController::class, 'add'])->name('project.cart.add');
        Route::post('/cart/update/{slug}', [CartController::class, 'update'])->name('project.cart.update');
        Route::delete('/cart/remove/{slug}', [CartController::class, 'remove'])->name('project.cart.remove');
        Route::get('/checkout', [CartController::class, 'checkout'])->name('project.checkout');
        Route::post('/checkout/process', [CartController::class, 'processCheckout'])->name('project.checkout.process');
        Route::get('/order/success', fn () => view('frontend.cart.success'))->name('project.order.success');

        // Localized routes with language prefix
        Route::prefix('{locale}')
            ->where(['locale' => '[a-z]{2}'])
            ->middleware([SetLocale::class])
            ->group(function () {
                Route::get('/', [HomeController::class, 'index'])->name('project.home.localized');
                Route::get('/products', [App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('project.products.index.localized');
                Route::get('/product/{slug}', [App\Http\Controllers\Frontend\ProductController::class, 'show'])->name('project.products.show.localized');
                Route::get('/blog', [PostController::class, 'index'])->name('project.posts.index.localized');
                Route::get('/blog/{slug}', [PostController::class, 'show'])->name('project.posts.show.localized');
                Route::get('/contact', [PageController::class, 'contact'])->name('project.contact.localized');
                Route::post('/contact', [PageController::class, 'contactSubmit'])->name('project.contact.submit.localized');
                Route::get('/{slug}', [PageController::class, 'show'])->name('project.pages.show.localized');
            });
    });

// Dynamic Pages (must be last to avoid conflicts)
Route::get('/{projectCode}/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin|login|logout|cart|checkout|products|product|blog|contact).*$')
    ->middleware([ProjectSubdomainMiddleware::class])
    ->name('project.pages.show');

// ============================================
// AUTH ROUTES (Đăng nhập CMS)
// URL: /{projectCode}/login
// ============================================
Route::prefix('{projectCode}')
    ->middleware([
        ProjectSubdomainMiddleware::class,
        SetProjectDatabase::class,
    ])
    ->group(function () {
        Route::get('login', [ProjectLoginController::class, 'showLoginForm'])->name('project.login');
        Route::post('login', [ProjectLoginController::class, 'login'])->name('project.login.post');
        Route::post('logout', [ProjectLoginController::class, 'logout'])->name('project.logout');
    });

// Dynamic CSS & JS per project
Route::get('/{projectCode}/css/custom.css', [ThemeController::class, 'projectCustomCss'])->name('project.css.custom');
Route::get('/{projectCode}/js/custom.js', [ThemeController::class, 'projectCustomJs'])->name('project.js.custom');

// ============================================
// CMS ADMIN ROUTES (Quản lý nội dung)
// URL: /{projectCode}/admin/*
// Middleware: ProjectSession + auth:project + CheckCmsRole
// ============================================
Route::prefix('{projectCode}/admin')
    ->name('project.admin.')
    ->middleware([
        ProjectSubdomainMiddleware::class,
        SetProjectDatabase::class,
        CheckCmsRole::class,
    ])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'projectDashboard'])->name('dashboard');

        // Products Management
        Route::post('products/bulk-edit', [ProductController::class, 'bulkEdit'])->name('products.bulk-edit');
        Route::post('products/bulk-update', [ProductController::class, 'bulkUpdate'])->name('products.bulk-update');
        Route::post('products/toggle-badge', [ProductController::class, 'toggleBadge'])->name('products.toggle-badge');
        Route::resource('products', ProductController::class);

        Route::resource('brands', BrandController::class);

        // Category Management - Consistent Routes
        Route::resource('categories', CategoryController::class);
        Route::get('categories/{category}/subcategories', [CategoryController::class, 'getSubcategories'])->name('categories.subcategories');

        // Posts Management (Bài viết)
        Route::resource('posts', App\Http\Controllers\Admin\PostController::class);
        Route::get('posts/create', [App\Http\Controllers\Admin\PostController::class, 'create'])->name('posts.create');

        // Property Categories Management
        Route::resource('property-categories', PropertyCategoryController::class);

        // Pages Management (Trang tĩnh)
        Route::get('pages', [App\Http\Controllers\Admin\PostController::class, 'index'])->name('pages.index')->defaults('post_type', 'page');
        Route::get('pages/create', [App\Http\Controllers\Admin\PostController::class, 'create'])->name('pages.create')->defaults('type', 'page');
        Route::get('pages/{post}', [App\Http\Controllers\Admin\PostController::class, 'show'])->name('pages.show');
        Route::get('pages/{post}/edit', [App\Http\Controllers\Admin\PostController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{post}', [App\Http\Controllers\Admin\PostController::class, 'update'])->name('pages.update');
        Route::delete('pages/{post}', [App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('pages.destroy');

        // Feature Pack Dynamic Post Types
        $featurePostTypes = [
            'properties' => 'property',
            'rooms' => 'room',
            'hotel-bookings' => 'hotel_booking',
            'amenities' => 'amenity',
            'doctors' => 'doctor',
            'patients' => 'patient',
            'prescriptions' => 'prescription',
            'appointments' => 'appointment',
        ];

        foreach ($featurePostTypes as $uri => $type) {
            Route::get($uri, [App\Http\Controllers\Admin\PostController::class, 'index'])->name("{$uri}.index")->defaults('post_type', $type);
            Route::get("{$uri}/create", [App\Http\Controllers\Admin\PostController::class, 'create'])->name("{$uri}.create")->defaults('type', $type);
            Route::get("{$uri}/{post}", [App\Http\Controllers\Admin\PostController::class, 'show'])->name("{$uri}.show");
            Route::get("{$uri}/{post}/edit", [App\Http\Controllers\Admin\PostController::class, 'edit'])->name("{$uri}.edit");
            Route::put("{$uri}/{post}", [App\Http\Controllers\Admin\PostController::class, 'update'])->name("{$uri}.update");
            Route::delete("{$uri}/{post}", [App\Http\Controllers\Admin\PostController::class, 'destroy'])->name("{$uri}.destroy");
        }

        // Attributes Management
        Route::resource('attributes', AttributeController::class);

        // Attribute Groups Management
        Route::get('attributes/groups', [AttributeController::class, 'indexGroups'])->name('attributes.groups.index');
        Route::get('attributes/groups/create', [AttributeController::class, 'createGroup'])->name('attributes.groups.create');
        Route::post('attributes/groups', [AttributeController::class, 'storeGroup'])->name('attributes.groups.store');
        Route::get('attributes/groups/{group}/edit', [AttributeController::class, 'editGroup'])->name('attributes.groups.edit');
        Route::put('attributes/groups/{group}', [AttributeController::class, 'updateGroup'])->name('attributes.groups.update');
        Route::delete('attributes/groups/{group}', [AttributeController::class, 'destroyGroup'])->name('attributes.groups.destroy');

        // Attribute Values Management
        Route::get('attributes/{attribute}/values', [AttributeController::class, 'indexValues'])->name('attributes.values.index');
        Route::get('attributes/{attribute}/values/create', [AttributeController::class, 'createValue'])->name('attributes.values.create');
        Route::post('attributes/{attribute}/values', [AttributeController::class, 'storeValue'])->name('attributes.values.store');
        Route::get('attributes/{attribute}/values/{value}/edit', [AttributeController::class, 'editValue'])->name('attributes.values.edit');
        Route::put('attributes/{attribute}/values/{value}', [AttributeController::class, 'updateValue'])->name('attributes.values.update');
        Route::delete('attributes/{attribute}/values/{value}', [AttributeController::class, 'destroyValue'])->name('attributes.values.destroy');

        // Orders Management
        Route::get('orders/reports', [OrderController::class, 'reports'])->name('orders.reports');
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'edit', 'update']);
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        // User Management
        Route::resource('users', UserController::class);

        // Media Management
        Route::get('media/list', [MediaController::class, 'list'])->name('media.list');
        Route::post('media/upload', [MediaController::class, 'upload'])->name('media.upload');
        Route::post('media/folder', [MediaController::class, 'createFolder'])->name('media.folder.create');
        Route::delete('media/folder', [MediaController::class, 'deleteFolder'])->name('media.folder.delete');
        Route::post('media/move', [MediaController::class, 'move'])->name('media.move');
        Route::delete('media/{id}', [MediaController::class, 'destroy'])->name('media.destroy')->where('id', '.*');

        // Settings
        Route::get('settings', [SettingsController::class, 'projectSettings'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'save'])->name('settings.save');

        // AI Management
        Route::post('ai/test', [AiController::class, 'test'])->name('ai.test');
        Route::post('ai/generate', [AiController::class, 'generate'])->name('ai.generate');
        Route::post('ai/list-models', [AiController::class, 'listModels'])->name('ai.list-models');

        // Debug routes
        Route::get('debug/session', function () {
            return [
                'session' => session()->all(),
                'user_id' => session('project_user_id'),
                'username' => session('project_user_username'),
                'project' => session('current_project'),
                'auth_user' => request()->attributes->get('auth_user'),
                'csrf_token' => csrf_token(),
                'session_token' => session()->token(),
            ];
        })->name('debug.session');

        Route::get('debug/csrf', function () {
            return view('cms.debug.csrf');
        })->name('debug.csrf');

        // Widget Templates (ACF-style builder)
        // Debug route for widget template builder
        Route::get('widget-templates/debug', function () {
            return response()->json([
                'message' => 'Widget Template Builder Debug',
                'livewire_installed' => class_exists(Livewire::class),
                'component_exists' => class_exists(WidgetTemplateBuilder::class),
                'view_exists' => view()->exists('livewire.admin.widget-template-builder'),
                'layout_exists' => view()->exists('cms.layouts.app'),
                'current_project' => session('current_project'),
            ]);
        })->name('widget-templates.debug');

        // Simple test without cms layout
        Route::get('widget-templates/test', function () {
            return view('livewire.admin.widget-template-test');
        })->name('widget-templates.test');

        Route::get('widget-templates', [WidgetTemplateController::class, 'index'])->name('widget-templates.index');
        Route::get('widget-templates/export-all', [WidgetTemplateController::class, 'exportAll'])->name('widget-templates.export-all');
        Route::get('widget-templates/{id}/ export', [WidgetTemplateController::class, 'export'])->name('widget-templates.export');
        Route::post('widget-templates/import', [WidgetTemplateController::class, 'import'])->name('widget-templates.import');
        Route::get('widget-templates/create', function ($projectCode) {
            $currentProject = Project::where('code', $projectCode)->first();

            return view('cms.widget-templates.create', compact('currentProject'));
        })->name('widget-templates.create');

        Route::get('widget-templates/{id}/edit', function ($projectCode, $id) {
            return view('cms.widget-templates.edit', ['id' => $id]);
        })->name('widget-templates.edit');
        Route::delete('widget-templates/{id}', [WidgetTemplateController::class, 'destroy'])->name('widget-templates.destroy');
        Route::post('widget-templates/{type}/preview', [WidgetTemplateController::class, 'preview'])->name('widget-templates.preview');

        // Code-based Widget Editor - use WidgetTemplateBuilder with codeType parameter
        Route::get('code-widgets', CodeWidgetList::class)->name('code-widgets.index');
        Route::get('code-widgets/export-all', [CodeWidgetController::class, 'exportAll'])->name('code-widgets.export-all');
        Route::get('code-widgets/{codeType}/edit', WidgetTemplateBuilder::class)->name('code-widgets.edit');
        Route::get('code-widgets/{type}/export', [CodeWidgetController::class, 'export'])->name('code-widgets.export');

        // Widget Management
        Route::get('widgets', [WidgetController::class, 'index'])->name('widgets.index');
        Route::get('widgets/create', WidgetEditor::class)->name('widgets.create');
        Route::post('widgets', [WidgetController::class, 'store'])->name('widgets.store');
        Route::get('widgets/{id}/edit', WidgetEditor::class)->name('widgets.edit');
        Route::put('widgets/{widget}', [WidgetController::class, 'update'])->name('widgets.update');
        Route::delete('widgets/{widget}', [WidgetController::class, 'destroy'])->name('widgets.destroy');
        Route::post('widgets/save-all', [WidgetController::class, 'saveWidgets'])->name('widgets.save-all');
        Route::post('widgets/preview', [WidgetController::class, 'preview'])->name('widgets.preview');
        Route::post('widgets/toggle', [WidgetController::class, 'toggleWidget'])->name('widgets.toggle');
        Route::post('widgets/clear-cache', [WidgetController::class, 'clearCache'])->name('widgets.clear-cache');
        Route::match(['get', 'post'], 'widgets/fields', [WidgetController::class, 'getFields'])->name('widgets.fields');

        Route::resource('menus', MenuController::class);
        Route::post('menus/{menu}/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
        Route::put('menus/items/{item}', [MenuController::class, 'updateItem'])->name('menus.items.update');
        Route::delete('menus/items/{item}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');
        Route::post('menus/{menu}/update-tree', [MenuController::class, 'updateTree'])->name('menus.update-tree');
        // Website Configuration
        Route::get('website-config', [WebsiteConfigController::class, 'index'])->name('website-config.index');
        Route::post('website-config/save', [WebsiteConfigController::class, 'save'])->name('website-config.save');
        Route::get('website-config/preview', [WebsiteConfigController::class, 'preview'])->name('website-config.preview');

        // Theme Options
        Route::get('theme-options', [ThemeOptionController::class, 'index'])->name('theme-options.index');
        Route::put('theme-options', [ThemeOptionController::class, 'update'])->name('theme-options.update');
        Route::get('theme/dynamic-css', [ThemeController::class, 'dynamicCss'])->name('theme.css');
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
            Route::get('forms', fn () => view('cms.settings.forms'))->name('forms');
            Route::get('contact-buttons', fn () => view('cms.settings.contact-buttons'))->name('contact-buttons');
            Route::get('redirects', fn () => view('cms.settings.redirects'))->name('redirects');
            Route::get('seo', fn () => view('cms.settings.seo'))->name('seo');
            Route::get('popups', fn () => view('cms.settings.popups'))->name('popups');
            Route::get('permissions', fn () => view('cms.settings.permissions'))->name('permissions');
            Route::get('fake-notifications', fn () => view('cms.settings.fake-notifications'))->name('fake-notifications');
        });

        // Fonts Management
        Route::post('fonts/store', [FontController::class, 'store'])->name('fonts.store');
        Route::post('fonts/toggle', [FontController::class, 'toggle'])->name('fonts.toggle');
        Route::post('fonts/default', [FontController::class, 'setDefault'])->name('fonts.default');
        Route::delete('fonts/destroy', [FontController::class, 'destroy'])->name('fonts.destroy');

        // Reviews Fake Data
        Route::get('reviews/fake', fn () => view('cms.reviews.fake'))->name('reviews.fake');

    });

// ============================================
// PROJECT API ROUTES (ACF-like Field APIs)
// URL: /{projectCode}/api/*
// ============================================
Route::prefix('{projectCode}/api')
    ->name('project.api.')
    ->middleware([
        ProjectSubdomainMiddleware::class,
        SetProjectDatabase::class,
    ])
    ->group(function () {
        // Relationship Field API
        Route::get('relationship-field/search', [RelationshipFieldController::class, 'search'])->name('relationship.search');
        Route::get('relationship-field/items', [RelationshipFieldController::class, 'getItems'])->name('relationship.items');

        // Taxonomy Field API
        Route::get('taxonomy-field/list', [TaxonomyFieldController::class, 'list'])->name('taxonomy.list');
    });
