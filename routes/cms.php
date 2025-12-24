<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\WidgetController;

// Widget Management Routes (Non-project context)
Route::prefix('admin')->name('cms.')->group(function () {
    Route::get('widgets', [WidgetController::class, 'index'])->name('widgets.index');
    Route::post('widgets', [WidgetController::class, 'store'])->name('widgets.store');
    Route::post('widgets/save-all', [WidgetController::class, 'saveWidgets'])->name('widgets.save-all');
    Route::post('widgets/clear', [WidgetController::class, 'clearArea'])->name('widgets.clear');
    Route::delete('widgets/{widget}', [WidgetController::class, 'destroy'])->name('widgets.destroy');
    Route::post('widgets/clear-cache', [WidgetController::class, 'clearCache'])->name('widgets.clear-cache');
});


