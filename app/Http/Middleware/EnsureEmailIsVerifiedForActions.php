<?php

namespace App\Http\Middleware;

use App\Support\AuthRedirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedForActions
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login', [
                'returnTo' => AuthRedirect::resolveTarget($request),
            ], true));
        }

        if (! $user->email_verified_at) {
            return redirect()
                ->back()
                ->with('status', 'You must verify your email in Auth0 before initializing objects or posting photos.');
        }

        return $next($request);
    }
}
