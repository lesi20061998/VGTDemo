<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\EmployeeController;
use App\Http\Controllers\SuperAdmin\ContractController;
use App\Http\Controllers\SuperAdmin\TaskController;
use App\Http\Controllers\SuperAdmin\TicketController;
use App\Http\Controllers\SuperAdmin\TenantController;

// SuperAdmin Project Management - For project management
Route::middleware([
    'auth', 
    \App\Http\Middleware\SuperAdminMiddleware::class,
    \App\Http\Middleware\SuperAdminBypassProjectScope::class
])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/multi-tenancy', [DashboardController::class, 'multiTenancy'])->name('multi-tenancy');
    Route::resource('employees', EmployeeController::class)->middleware('can:manage-employees');
    Route::resource('tasks', TaskController::class)->middleware('can:manage-tasks');
    Route::resource('tickets', TicketController::class);
    Route::resource('contracts', ContractController::class)->middleware('can:manage-contracts');
    Route::post('contracts/{contract}/approve', [ContractController::class, 'approve'])->name('contracts.approve');
    Route::post('contracts/{contract}/reject', [ContractController::class, 'reject'])->name('contracts.reject');
    Route::resource('projects', \App\Http\Controllers\SuperAdmin\ProjectController::class)->middleware('can:manage-projects');
    Route::post('projects/{project}/create-website', [\App\Http\Controllers\SuperAdmin\ProjectController::class, 'createWebsite'])->name('projects.create-website');
    Route::get('projects/{project}/config', [\App\Http\Controllers\SuperAdmin\ProjectController::class, 'config'])->name('projects.config');
    Route::post('projects/{project}/config', [\App\Http\Controllers\SuperAdmin\ProjectController::class, 'updateConfig']);
    Route::resource('tenants', TenantController::class);
    Route::post('websites/{tenant}/control', [\App\Http\Controllers\SuperAdmin\WebsiteController::class, 'control'])->name('websites.control');
    Route::post('websites/{tenant}/sync', [\App\Http\Controllers\SuperAdmin\WebsiteController::class, 'updateData'])->name('websites.sync');
    
    // Website Export & CMS Control
    Route::post('projects/{projectCode}/export', [\App\Http\Controllers\SuperAdmin\ProjectExportController::class, 'exportWebsite'])->name('projects.export');
    Route::get('projects/{projectId}/cms-features', [\App\Http\Controllers\SuperAdmin\ProjectExportController::class, 'getCmsFeatures'])->name('projects.cms-features.get');
    Route::put('projects/{projectId}/cms-features', [\App\Http\Controllers\SuperAdmin\ProjectExportController::class, 'updateCmsFeatures'])->name('projects.cms-features.update');
    
    // File Monitor
    Route::get('file-monitor', [\App\Http\Controllers\SuperAdmin\FileMonitorController::class, 'index'])->name('file-monitor');
    Route::get('file-monitor/recent-changes', [\App\Http\Controllers\SuperAdmin\FileMonitorController::class, 'getRecentChanges'])->name('file-monitor.recent-changes');
    
    // Test export routes
    Route::get('test-export/{projectCode}', [\App\Http\Controllers\SuperAdmin\TestExportController::class, 'testExport'])->name('test.export');
    
    // Remote CMS Management - SuperAdmin can manage any project's CMS
    Route::prefix('projects/{projectCode}/cms')->name('projects.cms.')->group(function () {
        Route::get('menus', [\App\Http\Controllers\SuperAdmin\RemoteCmsController::class, 'menus'])->name('menus.index');
        Route::post('menus', [\App\Http\Controllers\SuperAdmin\RemoteCmsController::class, 'storeMenu'])->name('menus.store');
        Route::get('menus/{menu}', [\App\Http\Controllers\SuperAdmin\RemoteCmsController::class, 'showMenu'])->name('menus.show');
        Route::delete('menus/{menu}', [\App\Http\Controllers\SuperAdmin\RemoteCmsController::class, 'destroyMenu'])->name('menus.destroy');
        Route::post('menus/{menu}/items', [\App\Http\Controllers\SuperAdmin\RemoteCmsController::class, 'storeMenuItem'])->name('menus.items.store');
        Route::delete('menus/items/{item}', [\App\Http\Controllers\SuperAdmin\RemoteCmsController::class, 'destroyMenuItem'])->name('menus.items.destroy');
        Route::post('menus/{menu}/update-tree', [\App\Http\Controllers\SuperAdmin\RemoteCmsController::class, 'updateMenuTree'])->name('menus.update-tree');
    });
});

