<?php

// MODIFIED: 2025-12-18 - Converted to Multi-Site Architecture
// All CMS functionality moved to project-specific routes: /{projectCode}/admin/*

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// Super Admin Routes - Global system management only
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Super Admin Dashboard - for managing multiple projects
    Route::get('/', [DashboardController::class, 'superAdminDashboard'])->name('dashboard');

    // Project Management (Super Admin only)
    Route::resource('projects', \App\Http\Controllers\SuperAdmin\ProjectController::class);

    // Global System Settings (Super Admin only) - TODO: Create controllers
    // Route::get('system-settings', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'index'])->name('system.settings');
    // Route::post('system-settings', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'save'])->name('system.settings.save');

    // Global Media Management (if needed) - TODO: Check if MediaController exists
    // Route::get('media/list', [\App\Http\Controllers\Admin\MediaController::class, 'list'])->name('media.list');
    // Route::post('media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('media.upload');
    // Route::post('media/folder', [\App\Http\Controllers\Admin\MediaController::class, 'createFolder'])->name('media.folder.create');
    // Route::delete('media/folder', [\App\Http\Controllers\Admin\MediaController::class, 'deleteFolder'])->name('media.folder.delete');
    // Route::post('media/move', [\App\Http\Controllers\Admin\MediaController::class, 'move'])->name('media.move');
    // Route::delete('media/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');

    // System Logs & Backups (Super Admin only) - TODO: Create SystemController
    // Route::get('logs', [\App\Http\Controllers\Admin\SystemController::class, 'logs'])->name('logs.index');
    // Route::get('backups', [\App\Http\Controllers\Admin\SystemController::class, 'backups'])->name('backups.index');
    // Route::post('backups/create', [\App\Http\Controllers\Admin\SystemController::class, 'createBackup'])->name('backups.create');
});

// NOTE: All content management (products, categories, orders, etc.)
// is now handled through project-specific routes in routes/project.php
// Format: /{projectCode}/admin/{resource}
