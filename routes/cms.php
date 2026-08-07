<?php

use Illuminate\Support\Facades\Route;

// [CLEANED] use App\Http\Controllers\Admin\WidgetController;

// Widget Management Routes (Non-project context)
Route::prefix('admin')->name('cms.')->group(function () {
    // [CLEANED]     Route::get('widgets', [WidgetController::class, 'index'])->name('widgets.index');
    // [CLEANED]     Route::post('widgets', [WidgetController::class, 'store'])->name('widgets.store');
    // [CLEANED]     Route::post('widgets/save-all', [WidgetController::class, 'saveWidgets'])->name('widgets.save-all');
    // [CLEANED]     Route::post('widgets/clear', [WidgetController::class, 'clearArea'])->name('widgets.clear');
    // [CLEANED]     Route::delete('widgets/{widget}', [WidgetController::class, 'destroy'])->name('widgets.destroy');
    // [CLEANED]     Route::post('widgets/clear-cache', [WidgetController::class, 'clearCache'])->name('widgets.clear-cache');
});
