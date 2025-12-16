<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\TaskController;
use App\Http\Controllers\Employee\ContractController;

Route::middleware(['auth', \App\Http\Middleware\EmployeeMiddleware::class])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
});

