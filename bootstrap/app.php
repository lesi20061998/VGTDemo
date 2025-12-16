<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\HideServerSignature::class,
            \App\Http\Middleware\LogVisitor::class,
        ]);
        
        // Chỉ áp dụng TenantMiddleware cho các route không phải admin
        $middleware->group('tenant', [
            \App\Http\Middleware\TenantMiddleware::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'project' => \App\Http\Middleware\ProjectMiddleware::class,
            'cms' => \App\Http\Middleware\CMSMiddleware::class,
            'panel.session' => \App\Http\Middleware\PanelSessionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
