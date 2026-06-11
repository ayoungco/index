<?php

use App\Http\Middleware\EnsureVerifiedEmail;
use App\Providers\AppServiceProvider;
use App\Providers\Auth0ServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        AppServiceProvider::class,
        Auth0ServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth0.verified' => EnsureVerifiedEmail::class,
            'verified.email' => EnsureVerifiedEmail::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            $message = 'Upload rejected because the request was larger than the server allows. Choose a smaller photo and try again.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 413);
            }

            return back()
                ->with('status', $message)
                ->with('statusType', 'critical');
        });

        // Ensure all exceptions get logged to storage/logs/laravel.log for easier debugging
        $exceptions->report(function (Throwable $e) {
            try {
                logger()->error('Unhandled exception', [
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                ]);
            } catch (Throwable) {
                // noop
            }
        });
    })->create();
