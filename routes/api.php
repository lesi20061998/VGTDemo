<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.token'])->group(function () {
    Route::post('/sync/widgets', [SyncController::class, 'syncWidgets']);
    Route::post('/sync/settings', [SyncController::class, 'syncSettings']);
});
