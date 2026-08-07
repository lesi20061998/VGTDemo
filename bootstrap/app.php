<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\BypassWidgetPermission;
use App\Http\Middleware\CMSMiddleware;
use App\Http\Middleware\HandleDatabaseErrors;
use App\Http\Middleware\HideServerSignature;
use App\Http\Middleware\LogFileChanges;
use App\Http\Middleware\LogVisitor;
use App\Http\Middleware\PanelSessionMiddleware;
use App\Http\Middleware\ProjectMiddleware;
use App\Http\Middleware\ProjectSession;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\TenantMiddleware;
use App\Http\Middleware\VerifyApiToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
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
            SetLocale::class,
            HideServerSignature::class,
            LogVisitor::class,
            LogFileChanges::class,
            HandleDatabaseErrors::class,
        ]);

        // Exclude media upload routes from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'admin/media/upload',
            '*/admin/media/upload',
        ]);

        // Chỉ áp dụng TenantMiddleware cho các route không phải admin
        $middleware->group('tenant', [
            TenantMiddleware::class,
        ]);

        // Project routes group with isolated session - runs BEFORE StartSession
        $middleware->group('project.web', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            ProjectSession::class, // Set session config BEFORE StartSession
            StartSession::class,
            ShareErrorsFromSession::class,
            ValidateCsrfToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth' => Authenticate::class,
            'admin' => AdminMiddleware::class,
            'superadmin' => SuperAdminMiddleware::class,
            'project' => ProjectMiddleware::class,
            'cms' => CMSMiddleware::class,
            'panel.session' => PanelSessionMiddleware::class,
            'project.session' => ProjectSession::class,
            'widget.bypass' => BypassWidgetPermission::class,
            'api.token' => VerifyApiToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Xử lý lỗi database QueryException
        $exceptions->render(function (QueryException $e, $request) {
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
