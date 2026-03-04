<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => 'index'])
    </head>
    <body>
        <main class="terminal-shell flex min-h-screen items-center">
            <section class="terminal-panel w-full">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <img src="{{ asset('index-v.svg') }}" alt="index" class="logo-adaptive h-16 w-auto">
                    <button type="button" data-theme-toggle class="terminal-btn terminal-btn-accent text-xs">Switch to Dark</button>
                </div>

                <h1 class="terminal-title mt-6">One trusted source.</h1>
                <p class="terminal-muted mt-3 max-w-2xl">
                    Scan an item UUID, post photos from camera, and keep the canonical timeline in one place.
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
