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

        <main class="app-shell text-sm">
            <section class="app-panel max-w-3xl">
                <p class="app-accent text-xs uppercase tracking-[0.2em]">404</p>
                <h1 class="app-title mt-2">Item Not Found</h1>
                <p class="app-muted mt-2">Tried to resolve: {{ $requestedPath }}</p>

                <div class="app-divider mt-6 border p-4">
                    <p class="app-muted">Log in with Auth0 to initialize this UUID.</p>
                    <a class="app-btn app-btn mt-3" href="{{ $loginUrl }}">Log in</a>
                </div>
            </section>
        </main>
    </body>
</html>
