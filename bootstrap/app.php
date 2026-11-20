<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    // biar di rute bisa nulis ->middleware('role:manajer')
    $middleware->alias(['role' => CheckRole::class]);

    $middleware->redirectUsersTo(fn (Request $request) => match ($request->user()->role) {
        'manajer' => route('manajer.bahan.index'),
        'kasir' => route('kasir.menu.index'),
        default => abort(403),
    });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
