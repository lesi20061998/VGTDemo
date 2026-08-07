<?php

// [CLEANED] use App\Http\Controllers\SuperAdmin\ContractController;
use App\Http\Controllers\Admin\PostController;
// [CLEANED] use App\Http\Controllers\SuperAdmin\EmployeeController;
// [CLEANED] use App\Http\Controllers\SuperAdmin\PositionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SuperAdmin\BriefController;
use App\Http\Controllers\SuperAdmin\ContractController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\FeaturePackController;
use App\Http\Controllers\SuperAdmin\FileMonitorController;
use App\Http\Controllers\SuperAdmin\PermissionController;
use App\Http\Controllers\SuperAdmin\ProjectController;
use App\Http\Controllers\SuperAdmin\ProjectExportController;
use App\Http\Controllers\SuperAdmin\RemoteCmsController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\TaskController;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\SuperAdmin\TestExportController;
use App\Http\Controllers\SuperAdmin\TicketController;
use App\Http\Controllers\SuperAdmin\WebsiteController;
use App\Http\Middleware\SuperAdminBypassProjectScope;
use App\Http\Middleware\SuperAdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// SuperAdmin Project Management - For project management
// Uses 'auth:web' to ensure main database authentication
Route::middleware([
    'auth:web',
    SuperAdminMiddleware::class,
    SuperAdminBypassProjectScope::class,
])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/multi-tenancy', [DashboardController::class, 'multiTenancy'])->name('multi-tenancy');

    // Unified Generic Routes
    Route::resource('tasks', TaskController::class);
    Route::resource('tickets', TicketController::class);
    Route::resource('briefs', BriefController::class);
    Route::resource('contracts', ContractController::class);
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('feature-packs', FeaturePackController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('posts', PostController::class);

    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/create-website', [ProjectController::class, 'createWebsite'])
        ->name('projects.create-website');
    Route::post('projects/{project}/update-progress', [ProjectController::class, 'updateProgress'])->name('projects.update-progress');
    Route::get('projects/{project}/config', [ProjectController::class, 'config'])->name('projects.config');
    Route::post('projects/{project}/config', [ProjectController::class, 'updateConfig']);
    Route::post('projects/{project}/reset-admin', [ProjectController::class, 'resetAdminAccount'])->name('projects.reset-admin');
    Route::resource('tenants', TenantController::class);
    Route::post('websites/{tenant}/control', [WebsiteController::class, 'control'])->name('websites.control');
    Route::post('websites/{tenant}/sync', [WebsiteController::class, 'updateData'])->name('websites.sync');

    // Website Export & CMS Control
    Route::post('projects/{projectCode}/export', [ProjectExportController::class, 'exportWebsite'])->name('projects.export');
    Route::get('projects/{projectId}/cms-features', [ProjectExportController::class, 'getCmsFeatures'])->name('projects.cms-features.get');
    Route::put('projects/{projectId}/cms-features', [ProjectExportController::class, 'updateCmsFeatures'])->name('projects.cms-features.update');

    // File Monitor
    Route::get('file-monitor', [FileMonitorController::class, 'index'])->name('file-monitor');
    Route::get('file-monitor/recent-changes', [FileMonitorController::class, 'getRecentChanges'])->name('file-monitor.recent-changes');

    // Test export routes
    Route::get('test-export/{projectCode}', [TestExportController::class, 'testExport'])->name('test.export');

    // Test logging route
    Route::get('test-logging', function () {
        return view('test-logging');
    })->name('test-logging');

    Route::post('test-log', function (Request $request) {
        return response()->json(['message' => 'Test log created', 'data' => $request->all()]);
    })->name('test-log');

    // Debug route to test file monitor API
    Route::get('debug-file-monitor', function (Request $request) {
        $controller = new FileMonitorController;
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        return $controller->index($request);
    })->name('debug-file-monitor');

    // Debug history page
    Route::get('debug-history', function () {
        return view('debug-history');
    })->name('debug-history');

    // Export project config with debug info
    Route::get('projects/{project}/export-config', [ProjectController::class, 'exportConfig'])->name('projects.export-config');
    Route::get('projects/{project}/export-viewer', [ProjectController::class, 'exportViewer'])->name('projects.export-viewer');

    // Remote CMS Management - SuperAdmin can manage any project's CMS
    Route::prefix('projects/{projectCode}/cms')->name('projects.cms.')->group(function () {
        Route::get('menus', [RemoteCmsController::class, 'menus'])->name('menus.index');
        Route::post('menus', [RemoteCmsController::class, 'storeMenu'])->name('menus.store');
        Route::get('menus/{menu}', [RemoteCmsController::class, 'showMenu'])->name('menus.show');
        Route::delete('menus/{menu}', [RemoteCmsController::class, 'destroyMenu'])->name('menus.destroy');
        Route::post('menus/{menu}/items', [RemoteCmsController::class, 'storeMenuItem'])->name('menus.items.store');
        Route::delete('menus/items/{item}', [RemoteCmsController::class, 'destroyMenuItem'])->name('menus.items.destroy');
        Route::post('menus/{menu}/update-tree', [RemoteCmsController::class, 'updateMenuTree'])->name('menus.update-tree');
    });
});
