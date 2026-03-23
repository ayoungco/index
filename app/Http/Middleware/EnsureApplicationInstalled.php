<?php

namespace App\Http\Middleware;

use App\Support\SiteSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationInstalled
{
    public function __construct(private readonly SiteSettings $siteSettings) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->siteSettings->isInstalled() || $request->routeIs('install.*')) {
            return $next($request);
        }

        return redirect()->route('install.show');
    }
}
