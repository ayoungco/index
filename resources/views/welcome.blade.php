<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => config('app.name')])
    </head>
    <body>
        <main class="app-shell flex min-h-screen items-center">
            <section class="app-panel w-full">
                <div class="flex flex-wrap items-center gap-3">
                    <img src="{{ app(\App\Support\SiteSettings::class)->logoUrl() }}" alt="{{ config('app.name') }}" class="logo-adaptive h-16 w-auto">
                </div>

                @if (session('status'))
                    <p class="mt-6 border border-emerald-500 bg-emerald-950 p-3 text-emerald-300">{{ session('status') }}</p>
                @endif

                <h1 class="app-title mt-6">{{ $siteSettings['scanner_title'] }}</h1>
                <p class="app-muted mt-3 max-w-2xl">
                    {{ $siteSettings['scanner_tagline'] }}
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a class="app-btn app-btn" href="{{ route('login') }}">Log in</a>
                    @auth
                        <a class="app-btn" href="{{ url('/dashboard') }}">Dashboard</a>
                    @endauth
                </div>
            </section>
        </main>
    </body>
</html>
