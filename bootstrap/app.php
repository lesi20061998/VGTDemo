<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Load project routes with isolated session middleware
            Route::middleware('project.web')
                ->group(base_path('routes/project.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\HideServerSignature::class,
            \App\Http\Middleware\LogVisitor::class,
            \App\Http\Middleware\LogFileChanges::class,
            \App\Http\Middleware\HandleDatabaseErrors::class,
        ]);

        // Chỉ áp dụng TenantMiddleware cho các route không phải admin
        $middleware->group('tenant', [
            \App\Http\Middleware\TenantMiddleware::class,
        ]);

        // Project routes group with isolated session - runs BEFORE StartSession
        $middleware->group('project.web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \App\Http\Middleware\ProjectSession::class, // Set session config BEFORE StartSession
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'project' => \App\Http\Middleware\ProjectMiddleware::class,
            'cms' => \App\Http\Middleware\CMSMiddleware::class,
            'panel.session' => \App\Http\Middleware\PanelSessionMiddleware::class,
            'project.session' => \App\Http\Middleware\ProjectSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Xử lý lỗi database QueryException
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            // Xử lý lỗi numeric overflow
            if (str_contains($e->getMessage(), 'Out of range value')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Giá trị nhập vào quá lớn! Vui lòng nhập giá không vượt quá 9,999,999,999,999.99 VNĐ.',
                        'message' => 'Validation Error',
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('alert', [
                        'type' => 'error',
                        'message' => 'Giá trị nhập vào quá lớn! Vui lòng nhập giá không vượt quá 9,999,999,999,999.99 VNĐ.',
                    ]);
            }

            // Xử lý lỗi duplicate entry
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Dữ liệu đã tồn tại trong hệ thống.',
                        'message' => 'Duplicate Entry Error',
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->with('alert', [
                        'type' => 'error',
                        'message' => 'Dữ liệu đã tồn tại trong hệ thống. Vui lòng kiểm tra lại.',
                    ]);
            }

            // Không xử lý ở đây, để Laravel xử lý mặc định
            return null;
        });
    })->create();
