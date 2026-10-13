<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/*
|--------------------------------------------------------------------------
| Bootstrap Aplikasi - JepretYuk
|--------------------------------------------------------------------------
|
| File ini mengonfigurasi:
| 1. Middleware alias untuk role-based access control.
| 2. Custom error handling agar error 404, 403, 500 diarahkan ke
|    view custom (bukan halaman error default Laravel).
|
*/

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan alias middleware untuk role-based access
        $middleware->alias([
            'role.fotografer' => \App\Http\Middleware\RoleFotografer::class,
            'role.klien'      => \App\Http\Middleware\RoleKlien::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ----------------------------------------------------------
        // CUSTOM ERROR HANDLING
        // ----------------------------------------------------------
        // Mengalihkan error HTTP ke view custom di resources/views/errors/
        // agar user tidak melihat halaman error default Laravel.
        // ----------------------------------------------------------

        // Error 404 - Halaman Tidak Ditemukan
        $exceptions->renderable(function (NotFoundHttpException $e) {
            return response()->view('errors.404', [], 404);
        });

        // Error 403 - Akses Ditolak (Forbidden)
        $exceptions->renderable(function (HttpException $e) {
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [], 403);
            }
        });

        // Error 500 - Internal Server Error (hanya di production)
        $exceptions->renderable(function (\Throwable $e) {
            if (!($e instanceof HttpException) && app()->environment('production')) {
                return response()->view('errors.500', [], 500);
            }
        });
    })
    ->create();
