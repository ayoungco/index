<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => config('app.name')])
    </head>
    <body>
        <main class="terminal-shell flex min-h-screen items-center">
            <section class="terminal-panel w-full">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <img src="{{ app(\App\Support\SiteSettings::class)->logoUrl() }}" alt="{{ config('app.name') }}" class="logo-adaptive h-16 w-auto">
                    <button type="button" data-theme-toggle class="terminal-btn terminal-btn-accent text-xs">Switch to Dark</button>
                </div>

                @if (session('status'))
                    <p class="mt-6 border border-emerald-500 bg-emerald-950 p-3 text-emerald-300">{{ session('status') }}</p>
                @endif

                <h1 class="terminal-title mt-6">{{ $siteSettings['scanner_title'] }}</h1>
                <p class="terminal-muted mt-3 max-w-2xl">
                    {{ $siteSettings['scanner_tagline'] }}
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a class="terminal-btn terminal-btn-accent" href="{{ route('login') }}">Log in</a>
                    @auth
                        <a class="terminal-btn" href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a class="terminal-btn" href="{{ route('login') }}">Auth0 Access</a>
                    @endauth
                </div>
            </section>
        </main>
    </body>
</html>
