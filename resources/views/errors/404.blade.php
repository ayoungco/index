<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        @php
            $requestedPath = urldecode(request()->path());
            $loginUrl = Route::has('login') ? route('login') : '/login';
        @endphp

        <main class="terminal-shell text-sm">
            <div class="mb-3 flex justify-end">
                <x-theme-toggle />
            </div>

            <section class="terminal-panel max-w-3xl">
                <p class="terminal-accent text-xs uppercase tracking-[0.2em]">404</p>
                <h1 class="terminal-title mt-2">Item Not Found</h1>
                <p class="terminal-muted mt-2">Tried to resolve: {{ $requestedPath }}</p>

                <div class="terminal-divider mt-6 border p-4">
                    <p class="terminal-muted">Log in with Auth0 to initialize this UUID.</p>
                    <a class="terminal-btn terminal-btn-accent mt-3" href="{{ $loginUrl }}">Log in</a>
                </div>
            </section>
        </main>
    </body>
</html>
