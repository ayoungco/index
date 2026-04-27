<?php

namespace App\Http\Middleware;

use App\Support\AuthRedirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVerifiedEmail
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login', [
                'returnTo' => AuthRedirect::resolveTarget($request),
            ], true));
        }

        if (! $user->email_verified_at) {
            return back()->with('error', 'Verified email required to perform this action.');
        }

        return $next($request);
    }
}
