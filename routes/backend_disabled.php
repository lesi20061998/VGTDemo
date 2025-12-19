<?php

// BACKUP FILE - CMS routes have been moved to multi-site architecture
// All CMS functionality is now handled through project-specific routes: /{projectCode}/admin/*

// This file contains the original CMS routes for reference only
// DO NOT INCLUDE THIS FILE IN ROUTING

use Illuminate\Support\Facades\Route;

// ORIGINAL CMS ROUTES - NOW DISABLED
// All functionality moved to project-specific routes in routes/project.php

/*
Route::prefix('cms/admin')->name('cms.')->middleware(['auth', 'cms'])->group(function () {
    // All CMS routes were here
    // Now handled by /{projectCode}/admin/* routes
});
*/
